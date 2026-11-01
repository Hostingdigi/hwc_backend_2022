# Upgrade notes: Laravel 8 to Laravel 13 (Phases 0–5)

These notes are for whoever deploys and looks after the shop. Part 1 explains
what each phase changed and why. Part 2 is the to-do list before going live:
credentials, server, `.env`, database, cron, admin settings and third parties.
The step-by-step switch-over, with rollback, is in [cutover.md](cutover.md).
The mobile API for the app team is in [api-v2.md](api-v2.md).

| Phase | Commit | In one line |
|---|---|---|
| 0 | `d8963bb` | Closed the worst security holes in the live code |
| 1 | `6cea768` | Safety net: working lockfile, schema baseline, tests that pin current behaviour |
| 2 | `5a78e29` | Laravel 8.83 to Laravel 13.39 on PHP 8.3 |
| 3 | `28dda43` | Cleanup: proper login guards, admin role rights, checkout and pricing bug fixes |
| 4 | `0d694c8` | Mobile API v2 with Sanctum, modern payment flows, two live security fixes |
| 5 | `705c01d` | Cutover tooling: preflight, staging scrub, switch to retire the old app API |

Tests: 265 automated tests pass (`composer test`; needs the `mysql` client on PATH and the `hwc_backend_testing` database).

---

# Part 1: What each phase did

## Phase 0: Security hardening

The live code had several holes anyone could use. Fixed:

- **Leaked and stray files deleted:** `env.html`, `default.php`, `index2.php`, `public/customsql.php` (ran any SQL), `public/hoolah.php`, `public/assets/ninjatracking.php`, and unrelated TCPDF code in `public/pdf/`.
- **Admin pages required no login.** Every `/admin` route now needs an admin session (`EnsureAdminIsLoggedIn`).
- **CSRF protection was switched off.** It's back on. Only punchout setup, gateway callbacks and the old mobile endpoints are exempt.
- **SQL injection** fixed in mobile login, product and brand search, coupon lookup, shipping zones and storefront filters. The `updateLatestProducts` route is removed.
- **The catalogue/order sync API was open to anyone.** It now needs `INTEGRATION_API_KEY`, sent as the `X-Api-Key` header. So does `send-test-email`.
- **Orders were marked paid on the browser's word.** The server now confirms each Stripe, PayPal or Atome payment with the gateway first (`PaymentVerifier`). Unconfirmed orders stay pending. `PAYMENT_VERIFICATION_MODE=log` only logs failures, as a temporary fallback.
- **Uploads** only accept jpg, png, gif, webp and pdf, checked by real file type, with cleaned-up filenames.
- Also committed: the cXML **punchout** integration that was in the working copy (`PunchoutController`, `PunchoutSession`, `BlockPunchoutRoutes`).

## Phase 1: Safety net

- **Dependencies:** a fresh `composer.lock` (the old one was broken), the abandoned faker package swapped for `fakerphp/faker`, and the unused dompdf removed (critical advisories). Routes to a non-existent `Admin\QuoteController` were removed so `route:list` works.
- **Database baseline:** `database/schema/mysql-schema.sql` is the table structure (no data). One guarded migration recreates 12 tables the local copy was missing. It does nothing where they exist, as on production.
- **Tests (127 at the time)** pinning down how the shop prices things today:
  - GST, 5-cent rounding, shipping and fees, coupons, cart totals, order creation
  - plus the Phase 0 fixes
- **Known bugs recorded as tests**, then fixed in Phase 3.

## Phase 2: Laravel 13 on PHP 8.3

- **Framework:** Laravel 8.83 to 13.39 on a fresh skeleton. The kernels, exception handler and providers moved into `bootstrap/app.php`. Middleware, CSRF exceptions and rate limits are carried over unchanged.
- **Config** rebuilt from Laravel 13 with production-compatible defaults: mysql, file cache and sessions, local uploads.
- **Password compatibility:** existing password hashes keep working, because bcrypt verification is relaxed as in Laravel 8.
- **Packages:** fideloper/proxy, fruitcake/cors, laravel/ui, the unused PayPal SDK and dead Auth controllers are dropped. twilio/sdk is now ^8; stripe-php stays on v7. Composer reports no security advisories.
- **Code:**
  - routes converted to class syntax (the route table was checked identical)
  - every mail call ported to the new mailer
  - Eloquent relationships added
  - login guards: `web` for customers, `admin` for admins
- **Front end:** Laravel Mix replaced by **Vite** (`npm run build` writes `public/build`).
- **Linux fix:** `app/Models/category.php` renamed to `Category.php` (commit `f387636`). It would have failed on a case-sensitive server.

## Phase 3: Cleanup, auth, role rights, bug fixes

- **Logins:** customers and admins sign in through Laravel's guards, and the session id is regenerated on login.
- **Admin role rights are enforced on the server.** Before, they only hid menu links (`AuthorizeAdminModule`).
- **55 admin actions that changed data over GET links** are now POST requests with a CSRF token.
- **Admin list filters** are safe from injection.
- **Checkout and pricing:**
  - **The mobile app's order total is checked** against a server-side quote (`MOBILE_PRICING_MODE=enforce|log`).
  - The iOS Stripe payment charges the stored order total.
  - **Checkout retries** no longer duplicate order lines or deduct stock twice, and a same-second retry no longer fails.
  - New orders and customers use their real inserted id, not "highest id", which could mix customers up. Mobile sign-up no longer returns another customer's data.
  - Mobile guest checkout, brand coupons and customer coupons no longer crash.
  - **Shipping zones** match the exact country. Some countries had been priced with another zone's rates.
- **Punchout:** the cXML is stored, setup needs `PUNCHOUT_SHARED_SECRET`, and bad input gets 400/401 instead of 500.
- **Infrastructure:**
  - all emails go through one queued `TemplateMail`
  - no `env()` calls left outside config, so `config:cache` is safe
  - uploads go through the `uploads` disk
  - `route:cache` works
  - 16 view names fixed for Linux file case
  - dead code removed

## Phase 4: Mobile API v2, payments, security

- **Two live security holes fixed:**
  - **Account takeover through password reset.** Reset links were just base64 of the email, and the reset form accepted any customer id. Resets now use real single-use tokens that expire after 60 minutes, for both the web and the old app.
  - **`/api/paymethods` returned the gateways' secret keys** to anyone. Secrets now always come back empty.
- **Mobile API v2** (`/api/v2`, 42 endpoints, documented in `docs/api-v2.md`):
  - **Accounts:** a Sanctum login token identifies the customer (90 days); no customer id is trusted from the app.
  - **Server-side pricing:** cart, quote, coupons and orders are priced on the server. A stale price gets a 409 with the new quote.
  - **Covers** profile, account deletion, catalogue, payments, wishlist, reviews and feedback.
  - **Limits:** rate limits are per customer.
- **Payments:**
  - **Stripe** uses PaymentIntents plus a signed webhook (`/api/webhooks/stripe`).
  - **PayPal** moved to Orders v2 with the JS SDK button, on the web checkout and the old app's web view; the old checkout.js is deprecated by PayPal.
  - **Atome** has a create/confirm flow for v2.
  - **One idempotent "mark paid" path** (`OrderPayments`): status, coupon use, cart and email.
- **Hoolah removed everywhere:** checkout, the app's method list, product pages and code.
- **Queue:** emails go on a database queue, drained every minute by the scheduler from cPanel cron.
- **Not changed:** the old app's `/api/...` responses are identical.

## Phase 5: Cutover tooling

- **Old app API switch:** all original app endpoints, under `/api/...` and their copies at the site root, sit in one `legacy.mobile` group.
  - **While on:** responses are unchanged, but they carry `Deprecation` and `Sunset` headers, and calls are counted per day (`php artisan api:legacy-usage`).
  - **When off (`LEGACY_MOBILE_API=off`):** they answer 410 "please update the app". `mobileversions`, the sync partner API and the PayPal button keep working.
- **`php artisan app:preflight`:** a server health check covering config, database, migrations, cron, gateway modes and keys, and the queue. It exits non-zero on a failure.
- **`php artisan app:scrub-staging`:** makes a copy of production safe for staging. Gateways go to test mode, customer emails become `@example.invalid`, and tokens and jobs are cleared. It refuses to run in production.
- **Twilio SID and token** were hardcoded in `app/Models/SMS.php`. They now come from `.env`. SMS sending itself is still commented out, as before.
- **Runbook:** `docs/cutover.md`.

---

# Part 2: Before you deploy

Work through this list on **staging first**. Then follow
[cutover.md](cutover.md), which covers the actual switch, smoke tests and
rollback.

## 1. Rotate every credential that has been exposed

All of these are in git history or were publicly readable, so treat them as seen. New values go in `.env` or the admin, never in code.

- [ ] **Database password**: production MySQL user
- [ ] **Stripe, PayPal, Atome (and GrabPay) secret keys**: they were returned by `/api/paymethods`. Change them under admin, *Settings > Payment Methods*.
- [ ] **Twilio SID and token**: revoke them in the Twilio console; they were hardcoded in `SMS.php`.
- [ ] **NinjaVan credentials**: these were in the deleted `ninjatracking.php`.
- [ ] **Hoolah credentials**: close or revoke the account, since the gateway is removed.
- [ ] **`APP_KEY`**: was exposed by `env.html`. Rotating it signs everyone out. If you rotate it, do so at the production switch.
- [ ] Optional: purge the old secrets from git history. Rotating them is what actually protects you.

## 2. Server (cPanel)

- [ ] **PHP 8.3** via MultiPHP Manager. Extensions: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `dom`/`xml` (punchout cXML), `fileinfo` (upload type check), `ctype`, `tokenizer`. These are usually on by default.
- [ ] **The document root must point at `public/`.** The old root `.htaccess` sends requests to `server.php`, which Laravel 13 no longer has, so the site would break. A root document root would also expose `.env`. If the domain is the account's main domain, symlink `public_html` to the app's `public/` folder (see cutover.md, C5).
- [ ] **Build files.** If there's no SSH or Composer, build locally on PHP 8.3 and upload `vendor/` and `public/build/`:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
  ```bash
  npm ci && npm run build
  ```
- [ ] **Don't upload** `.env`, `.env.smoke`, `.env.testing`, `node_modules/` or `tests/`.
- [ ] **Writable folders:** `storage/` and `bootstrap/cache/` must be writable by PHP.

## 3. `.env` changes

Start from the **current production `.env`** and change it as below.

**Keep as they are:** `APP_NAME`, `APP_KEY` (unless rotating), `APP_URL`, `DB_*`, `MAIL_HOST/PORT/USERNAME/PASSWORD/FROM_*`, `IMG_URL`, `FUEL_SUBCHARGE`, `HANDLING_PER`, `GOOGLE_CAPTCHA_SITE_KEY`, `GOOGLE_CAPTCHA_SECRET_KEY`.
`IMG_URL`, `FUEL_SUBCHARGE`, `HANDLING_PER` and the captcha keys are still read but are missing from `.env.example`, so don't drop them.

**Renamed in Laravel 13** (the old name is ignored):

| Old | New |
|---|---|
| `CACHE_DRIVER=file` | `CACHE_STORE=file` |
| `BROADCAST_DRIVER=log` | `BROADCAST_CONNECTION=log` |
| `MAIL_ENCRYPTION=ssl` / `tls` | `MAIL_SCHEME=smtps` for port 465; leave it empty for 587 (STARTTLS is automatic) |
| `MIX_PUSHER_APP_KEY` / `MIX_PUSHER_APP_CLUSTER` | `VITE_PUSHER_APP_KEY` / `VITE_PUSHER_APP_CLUSTER` (only if Pusher is used) |

**No longer used** (safe to delete): `GST_PER`, `PAYPAL_CLIENT_ID`, `PAYPAL_SECRET`, `PAYPAL_MODE`. PayPal keys live in the admin.

**Change or add** (production values):

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<your-domain>
LOG_LEVEL=warning

QUEUE_CONNECTION=database          # emails are queued; needs the cron entry (step 5)
CACHE_STORE=file
SESSION_DRIVER=file

# Shared key for the catalogue/order sync partner (header X-Api-Key). Long random value.
INTEGRATION_API_KEY=
# cXML punchout SharedSecret agreed with the buyer; setup is refused while empty
PUNCHOUT_SHARED_SECRET=

# Server-side checks: keep "enforce". "log" only logs (a temporary fallback)
PAYMENT_VERIFICATION_MODE=enforce
MOBILE_PRICING_MODE=enforce

# Stripe dashboard > Developers > Webhooks, event payment_intent.succeeded,
# URL https://<your-domain>/api/webhooks/stripe
STRIPE_WEBHOOK_SECRET=

# Mobile API v2 login lifetime in minutes (90 days)
SANCTUM_TOKEN_EXPIRATION=129600

# Twilio: new (rotated) values; SMS sending is still disabled in code
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_MESSAGING_SERVICE=

# Old mobile app endpoints: "on" until the v2 app has replaced it (Phase 5 / cutover.md D)
LEGACY_MOBILE_API=on
LEGACY_MOBILE_API_SUNSET=
```

To generate a random key for `INTEGRATION_API_KEY`:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

**Staging differs** in these values: `APP_ENV=staging`, `APP_URL=https://staging.<domain>`, its own `DB_*` and `APP_KEY`, `MAIL_MAILER=log`, Stripe **test** webhook secret.

After every `.env` change on the server, refresh the cached config:

```bash
php artisan config:cache
```

## 4. Database

- [ ] **Back up** first: a full export from phpMyAdmin.
- [ ] **Run the migrations:**
  ```bash
  php artisan migrate --force
  ```
  They only add things, and each one skips anything that already exists, so the old app still runs on the same database if you roll back. They add:
  - `personal_access_tokens` (v2 logins)
  - `jobs`, `job_batches`, `failed_jobs` (queue)
  - `legacy_api_hits` (old-API usage counts)
  - `password_resets`, if missing
  - `order_master.mobile_platform`, if missing
  - any of the 12 legacy tables that don't exist yet

  Production already has an `admins` table without a row in `migrations`, so
  `migrate` used to stop there with "table already exists". The three original
  Laravel 8 create-table migrations (`password_resets`, `admins`, `categories`)
  now skip tables that exist, like the newer ones. A structure-only copy of
  `hwc_new` now migrates cleanly (8 pending, then 0).
- [ ] **On staging only**, right after importing the production copy:
  ```bash
  php artisan app:scrub-staging
  ```

## 5. Cron (cPanel > Cron Jobs, every minute)

```
* * * * * cd /home/<user>/<app-folder> && /usr/local/bin/ea-php83 artisan schedule:run >> /dev/null 2>&1
```

The PHP path differs by host. Without this line queued emails are never sent (order confirmations, password resets). It also prunes expired tokens and failed jobs, and records the heartbeat preflight looks for.

## 6. Admin and gateway settings

- [ ] **Payment Methods** (admin): enter the **rotated** keys. Production must be in **live** mode and staging in **test** mode (preflight checks this).
- [ ] **Hoolah:** you can set its row inactive. The code already hides it.
- [ ] **Stripe webhook:** add `https://<domain>/api/webhooks/stripe` for `payment_intent.succeeded`, and put the signing secret in `STRIPE_WEBHOOK_SECRET`. Use a separate test-mode webhook for staging.
- [ ] **PayPal:** the new JS SDK button uses the same client id and secret from Payment Methods. Test it with a sandbox account on staging; so far it has only been tested against simulated PayPal responses.

## 7. Tell third parties

- [ ] **Catalogue/order sync partner:** they must send `X-Api-Key: <INTEGRATION_API_KEY>` on every call, or get 401.
- [ ] **Punchout buyer:** agree a SharedSecret and set `PUNCHOUT_SHARED_SECRET`. Setup requests are refused until it's set.
- [ ] **App team:** build the new app on `docs/api-v2.md`. Old app versions keep working until you switch v1 off (cutover.md, stage D).

## 8. Final checks after deploying

Run these on the server:

```bash
php artisan storage:link
```
```bash
php artisan optimize
```
```bash
php artisan app:preflight
```

- [ ] Preflight shows **no FAIL** rows.
- [ ] Work through the test checklist in cutover.md, A9: web checkout with every gateway, admin, password reset, the v2 app, sync partner, punchout.
- [ ] **Check these pages on staging:** category, promotions and search. MySQL 8's `ONLY_FULL_GROUP_BY` threw errors on them locally. That behaviour predates the upgrade and depends on the server's MySQL version.
- [ ] Watch `storage/logs/laravel.log` for `PAYMENT VERIFICATION FAILED`, and check `php artisan queue:failed`, for the first day.

---

# Known issues not fixed (your decision)

These predate the upgrade. Behaviour is unchanged and pinned by tests.

- **1-cent rounding:** the quote takes GST out and adds it back, so two S$1.00 items come to S$1.99. Affects web and app.
- **Abandoned unpaid orders keep their stock reserved.** There's no "cancelled" status to release it. A scheduled cleanup would need that status first.
- **Mobile percentage coupons** count fuel and handling charges twice in the discount base.
- **Old API rate limit:** the original `/api` endpoints share one 60-per-minute bucket for all clients. API v2 has per-customer limits, so this goes away when v1 is retired.
- **SMS sending** is commented out in `App\Models\SMS`. Order status changes send email only.
