# Cutover runbook: Laravel 13 app and mobile API v2

This runbook moves the shop from the old Laravel 8 app to this one on shared
cPanel hosting, then retires the original mobile API. There are four stages.
Each stage can be undone until the next one starts.

| Stage | What happens | Undo |
|---|---|---|
| A. Staging | This app runs on a staging subdomain against a scrubbed copy of production | Delete the subdomain |
| B. App release | The mobile app built on `/api/v2` ships to the stores | Old app versions keep working |
| C. Production switch | The domain points at this app | Point it back (see [Rollback](#rollback)) |
| D. Retire v1 | The original `/api/...` endpoints answer 410 | `LEGACY_MOBILE_API=on` |

Three commands support the cutover:

- `php artisan app:preflight` checks one server: PHP, `.env`, database, migrations, cron, gateway modes and keys, and the queue. It exits non-zero when a check fails.
- `php artisan app:scrub-staging` makes a production copy safe. It puts every gateway in test mode, replaces customer emails with `@example.invalid` addresses, and clears tokens and queued jobs. It refuses to run when `APP_ENV=production`.
- `php artisan api:legacy-usage` shows daily calls to the old mobile API.

---

## Before you start

- **Rotate credentials that were in the code.** The Twilio SID and token in `app/Models/SMS.php` are in git history. Revoke them in the Twilio console. Then do the same for the credentials found in Phase 0. New values go in `.env` only.
- **PHP 8.3** must be available in cPanel (MultiPHP Manager). Leave production on its current PHP version until stage C.
- **Composer.** If the host has no SSH or Composer, build `vendor/` locally with PHP 8.3 using `composer install --no-dev --optimize-autoloader`, then upload it. Build `public/build` locally too, with `npm ci && npm run build`.

## A. Staging

1. **Subdomain.** Create `staging.<domain>` in cPanel and set its document root to `<app folder>/public`. Set its PHP version to 8.3. Protect it with cPanel *Directory Privacy* (a password), so customers and search engines can't reach it.
2. **Database.** Create a new database and user. In phpMyAdmin, export production and import the dump into the new database. **Never point staging at the production database.**
3. **Code.** Upload this repository (with `vendor/` and `public/build`) to a folder outside `public_html`.
4. **`.env`.** Start from `.env.example`, then set the following:
   ```
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://staging.<domain>
   DB_DATABASE / DB_USERNAME / DB_PASSWORD = the staging database
   MAIL_MAILER=log
   QUEUE_CONNECTION=database
   PAYMENT_VERIFICATION_MODE=enforce
   ```
   Run `php artisan key:generate`. Staging gets its own key, and nothing encrypted by production needs to be read on staging.
5. **Scrub before anything else** (cPanel *Terminal*, or a one-off cron job):
   ```
   php artisan app:scrub-staging --force
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```
   The database now holds production data with test gateways. Customer addresses can no longer receive mail. The migrations only add tables and missing columns, and skip anything that already exists.
6. **Gateway test keys.** In the staging admin (*Settings > Payment Methods*), make sure each gateway has its **test** keys. In the Stripe dashboard (test mode), add the webhook `https://staging.<domain>/api/webhooks/stripe` for `payment_intent.succeeded`. Put its signing secret in `STRIPE_WEBHOOK_SECRET`, then run `php artisan config:cache`.
7. **Cron** (cPanel *Cron Jobs*, every minute):
   ```
   * * * * * cd /home/<user>/<app folder> && /usr/local/bin/ea-php83 artisan schedule:run >> /dev/null 2>&1
   ```
   The PHP path depends on the host. cPanel shows it under *MultiPHP Manager* or *Cron Jobs*.
8. **Preflight.** Wait two minutes for the cron, then run `php artisan app:preflight`. Every row should be OK. On staging, the expected warnings are a missing `PUNCHOUT_SHARED_SECRET` (unless you test punchout) and the config cache.
9. **Test.** Register a new customer (real customers can't sign in by email after the scrub), then work through this list:
   - [ ] Web: browse, search, category and promotion pages, cart, coupon, checkout, and an overseas address
   - [ ] Web payments, one each: card (Stripe), PayPal (sandbox account), Atome, GrabPay
   - [ ] The order shows as paid in the admin, and the confirmation email appears in `storage/logs/laravel.log`
   - [ ] Admin: sign in, change an order's status, products, coupons, payment methods, and role rights for a non-super admin
   - [ ] Password reset (the link appears in the log)
   - [ ] Mobile v2: a development build of the new app against `https://staging.<domain>/api/v2`, covering sign-up, cart, quote, order, each payment, and wishlist
   - [ ] Catalogue sync partner: point their test system at staging with a staging `INTEGRATION_API_KEY`
   - [ ] Punchout (if used): a setup request from the buyer's test system
   - [ ] `storage/logs`: no `PAYMENT VERIFICATION FAILED`, and no errors you can't explain

   If a payment check turns out wrong for a gateway, `PAYMENT_VERIFICATION_MODE=log` lets orders through while logging the failure. Fix the check before production.

## B. Mobile app release on v2

1. The app team builds against `docs/api-v2.md` and tests against staging.
2. Release the new version to both stores. **Don't force the update yet.** Old versions keep working on the original endpoints, which now send `Deprecation` headers and are counted.
3. Add the new version to the `mobileversions` table, so `GET /api/v2/app/version` reports it.

## C. Production switch

Pick a quiet hour, and keep the old app's folder untouched for rollback.

1. **Back up.** Take a full database export plus a copy of the old app folder and its `.env`.
2. **Upload** this app to a **new** folder next to the old one, for example `~/hwc_l13`. Copy the old `.env` across. Add the settings from `.env.example` that are new since Laravel 8, set `APP_ENV=production` and `APP_DEBUG=false`, and point `MAIL_MAILER` at the real SMTP server. Keep the production `APP_KEY`.
3. **Maintenance on.** Run `php artisan down` in the **old** app folder.
4. **Migrate.** Run `php artisan migrate --force` in the new folder. The changes only add tables and columns, so the old app still runs on this database if you roll back.
5. **Switch.** In cPanel, set the domain's PHP version to 8.3 and its document root to `~/hwc_l13/public`. If the domain is the account's main domain, `public_html` can't be re-pointed: rename it, then symlink `public_html` to `~/hwc_l13/public`.
6. **Finish:**
   ```
   php artisan storage:link
   php artisan optimize
   php artisan app:preflight
   ```
   Preflight must show no FAIL rows. Gateways must be **live**, and `STRIPE_WEBHOOK_SECRET` must be set.
7. **Stripe live webhook.** Add `https://<domain>/api/webhooks/stripe` (`payment_intent.succeeded`) in live mode, put its secret in `.env`, then run `php artisan config:cache`.
8. **Cron.** Change the existing cron line to the new folder and PHP 8.3, as in A7.
9. **Smoke test.** Open the home page, a product, the cart, and an admin sign-in. Then place one real order with a small card payment and refund it from the Stripe dashboard.
10. **Watch for the first day.** Check `storage/logs` (especially `PAYMENT VERIFICATION FAILED`), `php artisan queue:failed`, and new orders in the admin.

### Rollback

1. In cPanel, set the document root back to the old folder (or undo the `public_html` rename) and the PHP version back to what it was.
2. Point the cron line back at the old folder, and run `php artisan up` there.
3. Leave the database as it is. Orders taken by the new app stay valid. New-app items that the old app ignores: password-reset links (they stop working), v2 app sign-ins (the v2 app stops working until you switch forward again), and jobs left in the `jobs` table. The old app has no queue worker, so either clear the `jobs` table or switch forward again soon, because those emails won't be sent.

## D. Retire the original mobile API

The original endpoints (`/api/customerlogin`, `/api/createorder`, and so on, plus their copies at the site root) live in the `legacy.mobile` route group.

1. **Watch usage** with `php artisan api:legacy-usage --days=30`. Calls fall as customers update.
2. **Announce a date.** Set `LEGACY_MOBILE_API_SUNSET=YYYY-MM-DD` and run `php artisan config:cache`. Old-app responses then carry a `Sunset` header.
3. **Prompt the stragglers.** Add a newer version to `mobileversions`, so old apps that check `mobileversions` prompt for the update.
4. **Switch off.** When usage is down to a trickle (for example, a few customers a day for two weeks), set `LEGACY_MOBILE_API=off` and run `php artisan config:cache`. The old endpoints then answer `410` with `{"response":"failed","message":"This version of the app is no longer supported..."}`. These keep working:
   - `mobileversions`, so old apps can still prompt for the update
   - the key-protected sync partner endpoints
   - the PayPal button endpoints used by the web checkout
5. **Undo** if needed by setting `LEGACY_MOBILE_API=on` and running `config:cache`.
6. **Remove the code** after a few weeks with no complaints: the `legacy.mobile` route groups in `routes/api.php` and `routes/web.php`, `CustomerMobileController`, `CartMobileController`, `CommonMobileController`, `ProductMobileController`, their CSRF exceptions in `bootstrap/app.php`, and their tests.
