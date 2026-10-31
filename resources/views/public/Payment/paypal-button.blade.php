{{--
    PayPal JS SDK button (PayPal Orders v2). Replaces the deprecated checkout.js.
    The server creates the PayPal order for the stored order total and captures it
    (App\Http\Controllers\PayPalButtonController); the browser never sets the amount.

    Expects: $apikey (PayPal client id), $currency, $orderId, $return ("web" or "app"), $cancelUrl
--}}
<div id="paypal-error" class="alert alert-danger" style="display:none"></div>
<script src="https://www.paypal.com/sdk/js?client-id={{ urlencode($apikey) }}&currency={{ urlencode($currency ?: 'SGD') }}&intent=capture"></script>
<script>
(function () {
    var base = @json(url('/api/paypal/orders/' . $orderId));
    var post = function (url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body || {})
        }).then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); });
    };
    var fail = function (message) {
        var box = document.getElementById('paypal-error');
        box.textContent = message || 'The PayPal payment could not be completed. Please try again.';
        box.style.display = 'block';
    };

    paypal.Buttons({
        createOrder: function () {
            return post(base).then(function (res) {
                if (!res.ok) { fail(res.data.message); throw new Error('create failed'); }
                return res.data.id;
            });
        },
        onApprove: function (data) {
            return post(base + '/capture/' + @json($return), { paypal_order_id: data.orderID }).then(function (res) {
                if (!res.ok) { fail(res.data.message); return; }
                window.location = res.data.redirect;
            });
        },
        onCancel: function () { window.location = @json($cancelUrl); },
        onError: function () { fail(); }
    }).render('#paypal-button');
})();
</script>
