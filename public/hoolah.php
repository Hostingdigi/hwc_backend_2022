<?php

$authtoken = '';
$url = "https://demo-merchant-service.demo-hoolah.co/merchant/auth/login";
$ch = curl_init( $url );
$payload = json_encode( array("username" => "1e218c1c-f593-4eb5-93f1-9bc273ef788b", "password" => "TK_A8C8C5DDD239A70572"));
curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$result = curl_exec($ch);
curl_close($ch);
if($result) {
	$response = json_decode($result);
	$authtoken = $response->token;
}

if($authtoken != '') {

$url = "https://demo-merchant-service.demo-hoolah.co/merchant/order/initiate";

$ch = curl_init( $url );
# Setup request to send json via POST.
//$payload = json_encode( array("consumerTitle" => "Mr", "consumerFirstName" => "Bala", "consumerLastName" => "K", "consumerMiddleName" => "", "consumerEmail" => "balamurugan.sk@gmail.com", "consumerPhoneNumber" => "+6588889999", "shippingAddress" => array("line1" => "North Street", "line2" => "", "suburb" => "Singapore", "postcode" => "038989", "countryCode" => "SG"), "billingAddress" => array("line1" => "North Street", "line2" => "", "suburb" => "Singapore", "postcode" => "038989", "countryCode" => "SG"), "items" => array(array("name" => "Test Product", "description" => "test product", "sku" => "123456", "ean" => "12345678", "quantity" => "1", "originalPrice" => "100", "price" => "100", "images" => array(array("imageLocation" => "http://demo.hardwarecity.asia/uploads/product/products_20200217142220_1.jpg")), "taxAmount" => "0", "discount" => "0", "detailDescription" => "test product")), "totalAmount" => "100", "originalAmount" => "100", "taxAmount" => "0", "cartId" => "123", "orderType" => "ONLINE", "shippingAmount" => "0", "shippingMethod" => "FREE", "discount" => "0", "voucherCode" => "", "currency" => "SGD", "closeUrl" => "http://demo.hardwarecity.asia/", "returnToShopUrl" => "http://demo.hardwarecity.asia/success" ));

//print_r($payload); exit;

$payload = '{"consumerTitle":"","consumerFirstName":"Bala","consumerLastName":"K","consumerMiddleName":"","consumerEmail":"balamurugan.sk@gmail.com","consumerPhoneNumber":"+6588889999","shippingAddress":{"line1":"North Street","line2":"","suburb":"Singapore","postcode":"626101","countryCode":"SG"},"billingAddress":{"line1":"North Street","line2":"","suburb":"Singapore","postcode":"626101","countryCode":"SG"},"items":[{"name":"King Tony 1\/2in Drive Torque Wrench 42Nm-210Nm","description":"King Tony 1\/2in Drive Torque Wrench 42Nm-210Nm","sku":"","ean":"","quantity":1,"originalPrice":80,"price":80,"images":[{"imageLocation":"http:\/\/demo.hardwarecity.asia\/uploads\/product\/products_20160211151624_kttorque.jpg"}],"taxAmount":"0","discount":"0","detailDescription":"King Tony 1\/2in Drive Torque Wrench 42Nm-210Nm"}],"totalAmount":"85.60","originalAmount":"85.60","taxAmount":"5.60","cartId":"202102050030","orderType":"ONLINE","shippingAmount":"0","shippingMethod":"FREE","discount":"0.00","voucherCode":"","currency":"SGD","closeUrl":"http:\/\/demo.hardwarecity.asia\/cancelpayment","returnToShopUrl":"http:\/\/demo.hardwarecity.asia\/success?orderid=30"}';

curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
$header_str = "Authorization: bearer ".$authtoken;
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Accept: application/json",
  $header_str
));
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$result = curl_exec($ch);
curl_close($ch);

if($result) {
	$response = json_decode($result);
	$orderContentToken = $response->orderContextToken;
	$orderid = $response->orderId;
	$orderuuid = $response->orderUuid;	
	if($orderContentToken) {
		header('location:https://demo-js.demo-hoolah.co/?ORDER_CONTEXT_TOKEN='.$orderContentToken.'&platform=bespoke&version=1.0.1');
		exit;
	}
}

echo "<pre>$result</pre>";

}

?>