# HardwareCity Mobile API v2

Base URL: `https://<your-domain>/api/v2`

The v2 API replaces the original `/api/...` endpoints used by the current app.
Those keep working during the switch-over; nothing changes for existing app
versions until you retire them (see [Moving from v1](#moving-from-v1)).

## Basics

- **JSON in, JSON out.** Send `Content-Type: application/json` and
  `Accept: application/json`. Every response, including errors, is JSON.
- **Authentication.** Sign in or register to get a token, then send it on every
  account request: `Authorization: Bearer <token>`. Tokens last 90 days; on `401`
  sign the customer in again. The server works out who the customer is from
  the token. Never send a customer id.
- **Money.** Amounts are numbers in SGD. Item prices include GST.
- **Server prices.** The server prices everything (items, shipping, tax,
  coupons). The app shows what the server returns; it never sends prices.
- **Lists** are paginated: `?page=2`. Responses include `data`, `links` and `meta`
  (`current_page`, `last_page`, `total`, ...).
- **Rate limits.** 120 requests a minute per signed-in customer (per IP address
  when signed out); sign-in, sign-up and password reset are limited to 10 a minute.
  Over the limit you get `429`; wait and retry.

### Errors

| Status | Meaning | Body |
|---|---|---|
| 401 | Missing, expired or revoked token | `{"message": "Unauthenticated."}` |
| 402 | Payment not confirmed by the gateway | `{"message": ..., "reason": "payment_not_confirmed"}` |
| 404 | Not found, or not yours | `{"message": ...}` |
| 409 | Conflict: prices changed, or order already paid | `{"message": ..., "reason": ..., "quote": {...}}` |
| 422 | Validation failed | `{"message": ..., "errors": {"field": ["..."]}}` |
| 429 | Rate limited | |
| 502 | A payment gateway is unavailable | `{"message": "PayPal is not available right now..."}` |

## Account

### Register `POST /auth/register`
```json
{ "first_name": "Tan", "last_name": "Ah Kow", "email": "tan@example.com",
  "password": "at-least-8", "password_confirmation": "at-least-8",
  "phone": "81234567", "company": "", "newsletter": false, "device_name": "Tan's iPhone" }
```
`201` → `{ "token": "1|abc...", "token_type": "Bearer", "customer": { ...Customer } }`

### Sign in `POST /auth/login`
```json
{ "login": "tan@example.com", "password": "...", "device_name": "Tan's iPhone" }
```
`login` is the email or username. `200` → same shape as register. Wrong details
or a deactivated account → `422`.

### Sign out `POST /auth/logout` 🔒
Revokes the token used for the request.

### Forgot password `POST /auth/forgot-password`
`{ "email": "..." }` → always `200` with the same message (it does not reveal
whether an account exists). The customer gets an email with a link that is valid
for 60 minutes.

### Profile 🔒
- `GET /me` → `{ "data": Customer }`
- `PATCH /me`: any of `first_name, last_name, phone, company, address1, address2, city, state, postcode, country` (2-letter code)
- `PUT /me/password`: `{ "current_password", "password", "password_confirmation" }`. Signs out the customer's other devices.

**Customer**
```json
{ "id": 123, "first_name": "Tan", "last_name": "Ah Kow", "email": "tan@example.com",
  "username": "tan@example.com", "phone": "81234567", "company": "",
  "address": { "address1": "", "address2": "", "city": "", "state": "", "postcode": "", "country_id": 189 },
  "is_group_admin": false }
```

### Delete account 🔒 (required by the App Store / Play Store)
1. `POST /me/deactivation` (optional `{ "reason": "..." }`) emails a 6-digit code.
2. `POST /me/deactivation/confirm` `{ "otp": "123456" }` (within 1 hour) deactivates
   the account and revokes all its tokens.

## Catalogue (no sign-in needed)

| Endpoint | Notes |
|---|---|
| `GET /categories` | Top-level categories with `children` |
| `GET /brands?popular=1&letter=B` | `letter=0-9` for brands starting with a digit |
| `GET /products` | Filters: `search`, `category` (includes its sub-categories), `brand`, `promotion=1`, `new=1`. `sort`: `featured` (default), `newest`, `name_asc`, `name_desc`, `price_asc`, `price_desc`. 20 per page. |
| `GET /products/{id}` | Detail with `options`, `gallery`, `rating`, approved `reviews`, data sheets |
| `GET /banners` | `{ home: [...], ads: [...], announcements: [...] }` |
| `GET /countries` | Code, tax label/percentage |
| `GET /pages/{key}` | `terms-and-conditions`, `faq`, `privacy-policy`, ... |
| `GET /app/version` | Latest app version (see [force update](#force-update)) |
| `GET /shipping-methods?country=SG` | Methods for the destination. Send the token if signed in: Singapore swaps in free delivery above the free-shipping amount. |
| `GET /payment-methods` | `id`, `name`, `public_key` (Stripe publishable key or PayPal client id), `mode` (`live`/`test`). Never secrets. |
| `POST /feedback` | `{ name, email, phone?, message }` |

**Product (list)**
```json
{ "id": 26864, "name": "...", "slug": "...", "code": "DR-1", "price": 54.5, "regular_price": 60.0,
  "currency": "SGD", "in_stock": true, "is_promotion": false, "is_new": false,
  "image": "https://.../uploads/product/....jpg", "category_id": 11, "brand_id": 7 }
```
`regular_price` is set only when the customer pays less than it (a discount).

## Cart 🔒

The cart is stored on the server, so the same cart appears on every device.

- `GET /cart`
- `POST /cart/items`: `{ "product_id": 26864, "option_id": null, "quantity": 1 }` (adds to an existing line)
- `PATCH /cart/items/{lineId}`: `{ "quantity": 3 }`
- `DELETE /cart/items/{lineId}`
- `DELETE /cart`: empties it

All return the cart:
```json
{ "data": { "items": [ { "id": 55, "product_id": 26864, "name": "...", "option": null, "image": "...",
  "quantity": 1, "unit_price": 54.5, "line_total": 54.5, "in_stock": true } ],
  "item_count": 1, "subtotal": 54.5, "currency": "SGD" } }
```

### Quote `POST /checkout/quote` 🔒
`{ "country": "SG", "shipping_method_id": 2, "coupon_code": "SAVE10" }`
```json
{ "data": { "subtotal_excl_tax": 50.0, "tax": 4.5, "tax_label": "GST (9.00%)", "shipping": 8.0,
  "fuel_charges": 0, "handling_fee": 0, "packaging_fee": 0, "discount": 6.25,
  "discount_label": "Coupon discount(10.00%)", "coupon_error": null, "total": 56.25, "currency": "SGD" } }
```
Show this `total` to the customer and send it back as `expected_total` when
placing the order. If the coupon can't be used, `coupon_error` says why and the
quote is without the discount.

### Check a coupon `POST /coupons/check` 🔒
`{ "coupon_code": "SAVE10" }` → `200` with `{ code, type: "percent"|"fixed", value }`, or `422` with the reason.

## Orders 🔒

### Place an order `POST /orders`
```json
{
  "shipping": { "first_name": "Tan", "last_name": "Ah Kow", "email": "tan@example.com", "phone": "81234567",
                "company": "", "address1": "1 Test Road", "address2": "", "city": "Singapore",
                "state": "", "postcode": "123456", "country": "SG" },
  "billing": null,
  "shipping_method_id": 2,
  "payment_method_id": 3,
  "coupon_code": "SAVE10",
  "delivery_note": "Leave at door",
  "if_items_unavailable": 1,
  "expected_total": 56.25,
  "type": "order",
  "platform": "ios"
}
```
- `billing` defaults to `shipping` when left out.
- `type`: `order` (default) or `quotation`.
- `201` → `{ "data": Order }`, unpaid (`is_paid: false`). Stock is reserved now.
  The cart is emptied once the payment is confirmed.
- Failure `reason` codes: `cart_empty`, `out_of_stock`, `coupon_invalid`,
  `payment_method_unavailable` (422), `price_changed` (409, includes the current
  `quote`: show it to the customer and place the order again with the new
  `expected_total`).

### List / view
- `GET /orders?type=order|quotation`: newest first, 20 per page
- `GET /orders/{id}`: includes `lines`

**Order**
```json
{ "id": 1234, "number": "202610311234", "type": "order", "status": 1, "status_label": "Paid, Shipping Pending",
  "is_paid": true, "payment_method": "Credit Card", "placed_at": "2026-10-31T10:00:00+08:00",
  "totals": { "shipping": 8, "fuel_charges": 0, "handling_fee": 0, "packaging_fee": 0, "tax": 4.5,
              "tax_label": "GST (9.00%)", "discount": 0, "total": 62.5, "currency": "SGD" },
  "shipping_address": { ... }, "billing_address": { ... }, "tracking_number": null,
  "lines": [ { "product_id": 26864, "name": "...", "option": null, "code": "DR-1", "quantity": 1,
               "unit_price": 54.5, "total": 54.5 } ] }
```
Status codes: 0 Payment Pending, 1 Paid/Shipping Pending, 2-3 Shipped,
4 Delivered, 5 On The Way, 6 Partially Delivered, 7 Partially Refunded,
8 Fully Refunded, 9 Ready For Collection.

## Paying for an order 🔒

Every payment is for the order's stored `totals.total`. An order is only marked
paid after the gateway confirms the money; the confirm calls return the updated
Order (`is_paid: true`) or `402`.

### Card, Apple Pay, Google Pay (Stripe)
1. `POST /orders/{id}/payments/stripe` → `{ payment_intent_id, client_secret }`
2. Present Stripe's PaymentSheet with the `client_secret` and the publishable key
   from `GET /payment-methods`.
3. On success: `POST /orders/{id}/payments/stripe/confirm` `{ "payment_intent_id": "pi_..." }`.
   (A Stripe webhook also confirms it, so the order is paid even if the app
   closes first.)

### PayPal
1. `POST /orders/{id}/payments/paypal` → `{ paypal_order_id, approve_url }`
2. Use `paypal_order_id` with the PayPal mobile SDK, or open `approve_url` in a
   browser tab. After approval PayPal redirects to
   `/api/v2/payments/paypal/return`; watch for that URL.
3. `POST /orders/{id}/payments/paypal/capture` `{ "paypal_order_id": "..." }`

### Atome
1. `POST /orders/{id}/payments/atome` → `{ payment_url }`. Open it.
2. Atome redirects to `/api/v2/payments/atome/return?order={id}` (or `/cancel`).
3. `POST /orders/{id}/payments/atome/confirm`

Already paid → `409`. A gateway outage → `502`; offer another payment method.

## Wishlist and reviews 🔒
- `GET /wishlist` · `POST /wishlist` `{ "product_id": 1 }` · `DELETE /wishlist/{productId}`
- `POST /products/{id}/reviews` `{ "rating": 1-5, "comment": "..." }`. Shown after admin approval; a new review replaces the customer's earlier one.
- `POST /products/{id}/questions` `{ "question": "..." }`. Emailed to the store.

## Force update
`GET /app/version` returns the newest version an admin has added in the
`mobileversions` table. Compare it with the installed version and ask the
customer to update when it's newer.

## Moving from v1

| v1 | v2 |
|---|---|
| `customerlogin`, `storecustomer` (returned customer data, no token) | `auth/login`, `auth/register` (return a token) |
| Any endpoint taking `customerid` / `customer_id` | The token identifies the customer |
| `cart/add_item`, `cart/list`, `cart/update`, `cart/remove` | `cart`, `cart/items` |
| `get-shipping-package-price` | `checkout/quote` |
| `discountcoupon` | `coupons/check` (and `coupon_code` in quote/order) |
| `createorder` (app sent all prices) | `orders` (server prices; optional `expected_total`) |
| `successorder`, `paypalsuccess`, `ios-create-payment-intend` | `orders/{id}/payments/...` |
| `orderlist`, `orderinfo` | `orders`, `orders/{id}` |
| `mywishlist`, `addwishlist`, `removefromwishlist` | `wishlist` |
| `customerdata`, `updatecustomerdata`, `changecustomerpassword` | `me`, `me/password` |
| `request-account-deactivate`, `verify-account-deactivate` | `me/deactivation`, `me/deactivation/confirm` |
| `paymethods` (included gateway secrets) | `payment-methods` (public keys only) |
| `hoolah*` | Removed |

Once the v2 app is live and most customers have updated, raise `mobileversions`
to force the rest, then the v1 endpoints can be removed.
