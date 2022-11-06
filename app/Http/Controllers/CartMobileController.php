<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Promotions;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Price;
use App\Models\Cart;
use Session;
use DB;
use App\Models\ShippingMethods;
use App\Models\PaymentMethods;
use App\Models\OrderMaster;
use App\Models\OrderDetails;
use App\Models\Customer;
use App\Models\Country;
use App\Models\Settings;
use App\Models\EmailTemplate;
use App\Models\PaymentSettings;
use App\Models\Couponcode;
use App\Models\CouponCodeUsage;
use Stripe;


class CartMobileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->middleware('auth');
    }*/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function shoppingcart(Request $request)
    {		
		$data = [];
		$cartdata = [];
		$subtotal = $grandtotal = 0;
		$sesid = $request->customerid;
		if(Session::has('cartdata')) {
			$cartdata = Session::get('cartdata');
		}
		$cart = new \App\Models\Cart();		
		$subtotal = $cart->getCartSubTotal($sesid);
		$gst = $cart->getGST($subtotal);
		$grandtotal = $cart->getGrandTotal($subtotal, $gst);
        
		$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata, 'subtotal' => $subtotal, 'gst' => $gst, 'grandtotal' => $grandtotal]);	
		return $data;
    }	
		
	public function addtoshoppingcart(Request $request) {
		$data = [];
		$productid = $request->productid;
		$optionid = $request->optionid;
		$qty = 1;
		if(isset($request->qty)) {
			$qty = $request->qty;
		}
				
		$cart = $cartdata = [];
		$cartcount = 0;
		$exist = 0;
		$optionprice = 0;
		$prodoption = '';
		$sesid = $request->customerid;
		$product = Product::where('Id', '=', $productid)->first();
		if($product) {			
			if(Session::has('cartdata')) {
				$cartdata = Session::get('cartdata');
				//$cartcount = count($cartdata[$sesid]) + 1;
				if(!empty($cartdata)) {
					foreach($cartdata as $key => $val) {						
						if(is_array($val)) {
							foreach($val as $datakey => $dataval) {									
								if($dataval['productId'] == $productid) {
									$exist = 1;									
									$cartdata[$key][$datakey]['total'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['price'];
									$cartdata[$key][$datakey]['qty'] = $cartdata[$key][$datakey]['qty'] + $qty;
								}
							}
						}
					}
				}
			}
			if($exist == 0) {
				$ses_productid = $productid;
				$ses_productname = $product->EnName;
				$ses_qty = $qty;
				
				$price = new \App\Models\Price();
				//$ses_productprice = $price->getPrice($productid);
				$ses_productprice = $price->getDiscountPrice($productid);
				
				if($optionid > 0) {
					$options = ProductOptions::where('Id', '=', $optionid)->first();
					//$oprice = new \App\Models\Price();
					$optionprice = $options->Price;
					$optionprice = $price->getOptionPrice($productid, $optionid);
										
					$prodoption = $options->Title;
				}
				
				$ses_productprice = $ses_productprice + $optionprice;
				$ses_total = $qty * $ses_productprice;
				$cart['productId'] = $ses_productid;
				$cart['productName'] = $ses_productname;
				$cart['qty'] = $ses_qty;
				$cart['price'] = $ses_productprice;
				$cart['total'] = $ses_total;
				$cart['image'] = $product->Image;
				$cart['option'] = $prodoption;				
				$cart['weight'] = $product->Weight;
				$cart['productWeight'] = $product->Weight;
				$cart['code'] = $product->Code;
				$cart['color'] = $product->Color;
				$cart['size'] = $product->Size;
				$cart['shippingbox'] = $product->ShippingBox;
				if($product->Image) {
					$cart['image'] = url('/uploads/product').'/'.$product->Image;
				} else {
					$cart['image'] = url('/images/noimage.png');
				}
				$cartdata[$sesid][] = $cart;
			} 
			Session::put('cartdata', $cartdata);
		} 
		$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);	
		return $data;
	}
	
	public function removeshoppingcartitem(Request $request) {
		$data = [];
		$sesid = $request->customerid;
		$productid = $request->productid;
		$cartdata = Session::get('cartdata');
		if(!empty($cartdata[$sesid])) {
			foreach($cartdata as $key => $val) {						
				if(is_array($val)) {
					foreach($val as $datakey => $dataval) {									
						if($dataval['productId'] == $productid) {														
							unset($cartdata[$key][$datakey]);
						}
					}
				}
			}			
		}
		Session::put('cartdata', $cartdata);
		if(!empty($cartdata[$sesid])) {
			$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);
		} else {
			Session::forget('cartdata');
			$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => '']);
		}
		return $data;
	}

	public function updateshoppingcart(Request $request) {
		$data = [];
		$qtyfield = $itemqty = '';
		$products = $request->products;
		if($products) {
			$products = @explode(",", $products);
		}
		$quantities = $request->quantities;
		if($quantities) {
			$quantities = @explode(",", $quantities);
		}
		
		$cartdata = Session::get('cartdata');	
		
		if(!empty($cartdata)) {
			$x = 0;
			foreach($cartdata as $key => $val) {						
				if(is_array($val)) {
					foreach($val as $datakey => $dataval) {									
						if(in_array($dataval['productId'], $products)) {
							
							if(isset($quantities[$x])) {	
								$itemqty = $quantities[$x];
								if($cartdata[$key][$datakey]['qty'] != $itemqty) {
									$cartdata[$key][$datakey]['total'] = $itemqty * $cartdata[$key][$datakey]['price'];
									$cartdata[$key][$datakey]['qty'] = $itemqty;
								}
							}
						}
						++$x;
					}
				}				
			}
		}			
		
		
		Session::put('cartdata', $cartdata);
		$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);	
		return $data;
	}	
		
	public function clearshoppingcart(Request $request) {
		$data = [];
		Session::forget('cartdata');
		$data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => '']);	
		return $data;
	}
	
	public function shippingmethods() {
		$data = $shippingmenthods = [];
		$shipmethods = ShippingMethods::where('Status', '=', '1')->orderBy('DisplayOrder', 'ASC')->get();
		if($shipmethods) {
			$x = 0;
			foreach($shipmethods as $shipmethod) {
				$shippingmenthods[$x]['id'] = $shipmethod->Id;
				$shippingmenthods[$x]['name'] = $shipmethod->EnName;
				$shippingmenthods[$x]['price'] = $shipmethod->Price;
				$shippingmenthods[$x]['freeshippingavailable'] = $shipmethod->FreeShipAvailable;
				$shippingmenthods[$x]['freeshipcost'] = $shipmethod->FreeShipCost;
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Shipping Methods', 'shippingmethods' => $shippingmenthods]);	
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Shipping Methods', 'shippingmethods' => '']);	
		}
		return $data;
	}
	
	public function paymethods() {
		$data = $paymentmenthods = [];
		$paymethods = PaymentMethods::where('status', '=', '1')->get();
		if($paymethods) {
			$x = 0;
			foreach($paymethods as $paymethod) {
				$paymentmenthods[$x]['id'] = $paymethod->id;
				$paymentmenthods[$x]['name'] = $paymethod->payment_name;
				$paymentmenthods[$x]['mode'] = $paymethod->payment_mode;
				$paymentmenthods[$x]['testing_url'] = $paymethod->testing_url;
				$paymentmenthods[$x]['live_url'] = $paymethod->live_url;
				$paymentmenthods[$x]['api_key'] = $paymethod->api_key;
				$paymentmenthods[$x]['api_signature'] = $paymethod->api_signature;
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Payment Methods', 'paymentmenthods' => $paymentmenthods]);	
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Payment Methods', 'paymentmenthods' => '']);	
		}
		return $data;
	}
	
	public function createorder(Request $request) {
		$data = [];
		$shipcountryname = $billcountryname = '';
		$orderincid = $orderid = 0;
		$orderinfo = $request->orderinfo;
				
		if($orderinfo) {
			$orderinfo = $orderinfo[0];
			$userid = $orderinfo['customer_id'];
			
			$billinginfo = $orderinfo['billing'][0];
			$shippinginfo = $orderinfo['shipping'][0];
			//$carddetails = $orderinfo['carddetails'][0];
			
			if($userid <= 0) {				
				$chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
				if(!$chkcustomer) {
					Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'],'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
					$cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
					if($cust) {
						$userid = $cust->cust_id;
					}
				} else {
					$userid = $chkcustomer->cust_id;
				}
			}
			
			$subtotal = $orderinfo['subtotal'];
			$gst = $orderinfo['tax_collected'];
			$packingfee = $orderinfo['packagingfee'];
			$deliverycost = $orderinfo['shippingcost'];
			$grandtotal = $orderinfo['payable_amount'];
			$paymethodname = $orderinfo['paymethod'];
			$discount = $orderinfo['discount'];
			$couponid = $orderinfo['couponid'];
			$discounttext = $orderinfo['discounttext'];
			$taxtitle = $deliverytype = '';
			$shipdata = ShippingMethods::where('Id', '=', $orderinfo['shipmethod'])->select('EnName')->first();
			if($shipdata) {
				$deliverytype = $shipdata->EnName;
			}
			
			$ordermaster = new OrderMaster;
			$ordermaster['user_id'] = $userid;
			$ordermaster['ship_method'] = $orderinfo['shipmethod'];
			$ordermaster['pay_method'] = $orderinfo['paymethod'];
			$ordermaster['shipping_cost'] = $orderinfo['shippingcost'];
			$ordermaster['packaging_fee'] = $orderinfo['packagingfee'];
			$ordermaster['tax_collected'] = $orderinfo['tax_collected'];
			$ordermaster['discount_amount'] = $discount;
			$ordermaster['discount_id'] = $couponid;
			$ordermaster['payable_amount'] = $orderinfo['payable_amount'];
			$ordermaster['order_status'] = '0';
			$ordermaster['if_items_unavailabel'] = $orderinfo['if_items_unavailabel'];
			$ordermaster['bill_fname'] = $billinginfo['bill_fname'];
			$ordermaster['bill_lname'] = $billinginfo['bill_lname'];
			$ordermaster['bill_email'] = $billinginfo['bill_email'];
			$ordermaster['bill_mobile'] = $billinginfo['bill_mobile'];
			$ordermaster['bill_compname'] = $billinginfo['bill_compname'];
			$ordermaster['bill_ads1'] = $billinginfo['bill_address1'];
			$ordermaster['bill_ads2'] = $billinginfo['bill_address2'];
			$ordermaster['bill_city'] = $billinginfo['bill_city'];
			$ordermaster['bill_state'] = $billinginfo['bill_state'];
			$ordermaster['bill_zip'] = $billinginfo['bill_zip'];
			$ordermaster['bill_country'] = $billinginfo['bill_country'];
			$ordermaster['ship_fname'] = $shippinginfo['ship_fname'];
			$ordermaster['ship_lname'] = $shippinginfo['ship_lname'];
			$ordermaster['ship_email'] = $shippinginfo['ship_email'];
			$ordermaster['ship_mobile'] = $shippinginfo['ship_mobile'];
			$ordermaster['ship_ads1'] = $shippinginfo['ship_address1'];
			$ordermaster['ship_ads2'] = $shippinginfo['ship_address2'];
			$ordermaster['ship_country'] = $shippinginfo['ship_country'];
			$ordermaster['ship_city'] = $shippinginfo['ship_city'];
			$ordermaster['ship_state'] = $shippinginfo['ship_state'];
			$ordermaster['ship_zip'] = $shippinginfo['ship_zip'];
			$ordermaster->save();
			
			$order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
			if($order) {
				$orderid = $order->order_id;
				$orderincid = $order->order_id;
			}
			
			$countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'taxtitle')->first();
			if($countrydata) {
				$countryid = $countrydata->countryid;
				$taxtitle = $countrydata->taxtitle;
			}
			$hoolahitems = [];
			$cartitems = $orderinfo['products'];
			
			$itemdetails = '<table style="width:100%; background:#f1f1f1;">';
			$itemdetails .= '<tr><th>Item</th><th >Qty</th><th style="text-align:right">Price</th><th>Total</th></tr>'; 
									
			foreach($cartitems as $cart) {
				$orderdetails = new OrderDetails;
				$orderdetails['order_id'] = $orderid;
				$orderdetails['prod_id'] = $cart['id'];
				$orderdetails['prod_name'] = $cart['name'];
				$orderdetails['prod_quantity'] = $cart['quantity'];
				$orderdetails['prod_unit_price'] = $cart['price'];
				$orderdetails['prod_option'] = $cart['option'];
				$orderdetails['Weight'] = $cart['weight'];
				$orderdetails['prod_code'] = $cart['code'];
				$orderdetails->save();
				/*$qty = 0;
				$product = Product::where('Id', '=', $cart['id'])->select('Quantity')->first();
				if($product->Quantity > $cart['quantity']) {
					$qty = $product->Quantity - $cart['quantity'];
				}
				Product::where('Id', '=', $cart['id'])->update(array('Quantity' => $qty));*/
				
				$desc = $image = '';
				
				$qty = 0;
				$product = Product::where('Id', '=', $cart['id'])->select('Quantity', 'Image', 'EnShortDesc')->first();
				if($product->Quantity > $cart['quantity']) {
					$qty = $product->Quantity - $cart['quantity'];
					$desc = $product->EnShortDesc;
					if($desc == '') {
						$desc = $cart['name'];
					}
					$image = url('/').'/uploads/product/'.$product->Image;
				}
				Product::where('Id', '=', $cart['id'])->update(array('Quantity' => $qty));
				
				$productname = $cart['name'];
				
				$sku = $ean = "";
				if($cart['code']) {
					$sku = $cart['code'];
					$ean = $cart['code'];
				}
				
				$hoolahitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['quantity'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);
				
				$itemdetails .= '<tr><td>'.$cart['name'].'</td><td>'.$cart['quantity'].'</td><td style="text-align:right">$'.number_format($cart['price'], 2).'</td><td>$'.number_format($cart['total'], 2).'</td></tr>';
			}
			
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			$itemdetails .= '<tr><td colspan="2"></td><td>Sub Total</td><td>$'.$subtotal.'</td></tr>';
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			$itemdetails .= '<tr><td colspan="2"></td><td>'.$taxtitle.'</td><td>$'.$gst.'</td></tr>';
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			$itemdetails .= '<tr><td colspan="2"></td><td>Shipping ('.$deliverytype.')</td><td>$'.$deliverycost.'</td></tr>';
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			$itemdetails .= '<tr><td colspan="2"></td><td>Packing Fee</td><td>$'.$packingfee.'</td></tr>';
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			$itemdetails .= '<tr><td colspan="2"></td><td><b>Grand Total</b></td><td><b>$'.$grandtotal.'</b></td></tr>';
			$itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
			
			$itemdetails .= '</table>';
			
			$countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
			if($countrydata) {
				$countryid = $countrydata->countryid;
				$billcountryname = $countrydata->countryname;
			}
			
			$shipcountrydata = Country::where('countrycode', '=', $shippinginfo['ship_country'])->select('countryid', 'countryname')->first();
			if($shipcountrydata) {				
				$shipcountryname = $shipcountrydata->countryname;
			}

			
			Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_address1'], 'cust_address2' => $billinginfo['bill_address2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));
			
			$setting = Settings::where('id', '=', '1')->first();
			if($setting) {
				$companyname = $setting->company_name;
				$adminemail = $setting->admin_email;
				$ccemail = $setting->cc_email;
			}
			
			$billing = '';
			$billing .= '<p>'.$billinginfo['bill_fname'].' '.$billinginfo['bill_lname'].'</p>';
			$billing .= '<p>'.$billinginfo['bill_address1'].'</p>';
			$billing .= '<p>'.$billinginfo['bill_address2'].'</p>';
			$billing .= '<p>'.$billinginfo['bill_city'].'</p>';
			$billing .= '<p>'.$billinginfo['bill_state'].' - '.$billinginfo['bill_zip'].'</p>';
			$billing .= '<p>'.$billinginfo['bill_country'].'</p>';
			
			$shipping = '';
			$shipping .= '<p>'.$shippinginfo['ship_fname'].' '.$shippinginfo['ship_lname'].'</p>';
			$shipping .= '<p>'.$shippinginfo['ship_address1'].'</p>';
			$shipping .= '<p>'.$shippinginfo['ship_address2'].'</p>';
			$shipping .= '<p>'.$shippinginfo['ship_city'].'</p>';
			$shipping .= '<p>'.$shippinginfo['ship_state'].' - '.$shippinginfo['ship_zip'].'</p>';
			$shipping .= '<p>'.$shippinginfo['ship_country'].'</p>';
			
			$emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
			if($emailtemplate) {
				
				if(strlen($orderid) == 3) {
					$orderid = date('Ymd').'0'.$orderid;
				} elseif(strlen($orderid) == 2) {
					$orderid = date('Ymd').'00'.$orderid;
				} elseif(strlen($orderid) == 1) {
					$orderid = date('Ymd').'000'.$orderid;
				} else {
					$orderid = date('Ymd').$orderid;
				}
				
				$emailsubject = $emailtemplate->subject;
				$emailcontent = $emailtemplate->content;
				$logo = url('/').'/front/img/logo.png';
				$logo = '<img src="'.$logo.'">';
				$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
				$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
				$emailcontent = str_replace('{customername}', $billinginfo['bill_fname'].' '.$billinginfo['bill_lname'], $emailcontent);
				$emailcontent = str_replace('{logo}', $logo, $emailcontent);
				$emailcontent = str_replace('{email}', $adminemail, $emailcontent);
				$emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
				$emailcontent = str_replace('{datetime}', date("M d Y H:i A"), $emailcontent);
				$emailcontent = str_replace('{billinginfo}', $billing, $emailcontent);
				$emailcontent = str_replace('{shippinginfo}', $shipping, $emailcontent);
				$emailcontent = str_replace('{paymentmethod}', $paymethodname, $emailcontent);
				$emailcontent = str_replace('{shippingmethod}', $deliverytype, $emailcontent);
				$emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);
				
				$custemail = $billinginfo['bill_email'];
				
				$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
				$headers .='Reply-To: '. $adminemail . "\r\n" ;
				$headers .='X-Mailer: PHP/' . phpversion();
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
						
				@mail($custemail, $emailsubject, $emailcontent, $headers);
			}
			
			
			$currency = 'SGD';
			$paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
			if($paysettings) {
				$currency = $paysettings->currency_type;
			}
			
			if($orderinfo['paymethod'] == 'Stripe Pay') {
				
				

				$stripesignature = '';
				$paymode = 'live';
				$paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%Credit Card')->select('api_signature')->first();
				
				if($paymentmethod) {
					$paymode = $paymentmethod->payment_mode;
					if($paymode == 'live') {
						$stripesignature = $paymentmethod->api_signature;
					} else {
						$stripesignature = $paymentmethod->api_signature;
					}
				}
				
				Stripe\Stripe::setApiKey($stripesignature);
				
				$token = $orderinfo['token'];
				
				
				if($token) {
					
					$stripe = new \Stripe\StripeClient($stripesignature);
					$response = $stripe->charges->create([
					  'amount' => $grandtotal * 100,
					  'currency' => $currency,
					  'source' => $token,
					  'description' => 'Payment from hardwarecity.com.sg',
					  'metadata' => array("order_id" => $orderid)
					]);
					
					/*$response = Stripe\Charge::create ([
						"amount" => $grandtotal * 100,
						"currency" => $currency,
						"source" => $token,
						"description" => "Payment from hardwarecity.com.sg",
						"metadata" => array("order_id" => $orderid)
					]);*/
					
					if($response) {
						$transid = $response['id'];
						OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $transid, 'order_status' => '1'));
					}
					
					$data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid]);
				}
				
				/*$source = \Stripe\Source::create([
				  "type" => "card",
				  "currency" => $currency,
				  "card" => [
					"number" => $carddetails['cardnumber'],
					"cvc" => $carddetails['cvc'],
					"exp_month" => $carddetails['exp_month'],
					"exp_year" => $carddetails['exp_year'],
					],
				  "owner" => [
					"email" => $billinginfo['bill_email']
				  ]
				]);
						
				$stripecustomer = \Stripe\Customer::create([
					'name' => $billinginfo['bill_fname'].' '.$billinginfo['bill_lname'],
					'email' => $billinginfo['bill_email'],
					'address' => [
						'line1' => $billinginfo['bill_address1'],
						'city' => $billinginfo['bill_city'],
						'state' => $billinginfo['bill_state'],
						'postal_code' => $billinginfo['bill_zip'],
						'country' => $billinginfo['bill_country'],
					],
				]);
				
				if($stripecustomer) {
					$stripe = new \Stripe\StripeClient($stripesignature);	
					$token = $stripe->tokens->create([
						'card' => [
							"number" => $carddetails['cardnumber'],
							"cvc" => $carddetails['cvc'],
							"exp_month" => $carddetails['exp_month'],
							"exp_year" => $carddetails['exp_year'],
						],
					]);
					if($token) {
						$response = Stripe\Charge::create ([
							"amount" => $grandtotal * 100,
							"currency" => $currency,
							"source" => $token,
							"description" => "Payment from hardwarecity.com.sg",
							"metadata" => array("order_id" => $orderid),	
							"source" => $source['id'],	
							"customer" => $stripecustomer['id']
						]);
						
						if($response) {
							$transid = $response['id'];
							OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $transid, 'order_status' => '1'));
						}
						
						$data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid]);
					}	
				}*/
			} elseif($orderinfo['paymethod'] == 'Hoolah') {
				$paymode = 'live';
				$paymentmethod = PaymentMethods::where('id', '=', '4')->orWhere('payment_name', 'LIKE', '%hoolah')->first();
				
				if($paymentmethod) {
					$paymode = $paymentmethod->payment_mode;
					if($paymode == 'live') {
						$apikey = $paymentmethod->api_key;
						$apisignature = $paymentmethod->api_signature;
						$paymenturl = $paymentmethod->live_url;
					} else {
						$apikey = $paymentmethod->test_api_key;
						$apisignature = $paymentmethod->test_api_signature;
						$paymenturl = $paymentmethod->testing_url;
					}
				}
				
				$url = $paymenturl."/auth/login";
				$ch = curl_init( $url );
				$payload = json_encode( array("username" => $apikey, "password" => $apisignature));
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				$result = curl_exec($ch);
				curl_close($ch);
				if($result) {
					$response = json_decode($result);
					$authtoken = $response->token;
					
					if($authtoken != '') {
					
						$url = $paymenturl."/order/initiate";
						
						$billmobile = $billinginfo['bill_mobile'];
						$orderurl = 'https://demo-js.demo-hoolah.co/';
						if($billinginfo['bill_country'] == 'SG') {
							$billmobile = '+65'.$billmobile;
							if($paymode == 'live') {
								$orderurl = 'https://js.secure-hoolah.co/';
							}
						} elseif($billinginfo['bill_country'] == 'MY') {
							$billmobile = '+60'.$billmobile;
							$orderurl = 'https://my.demo-js.demo-hoolah.co/';
							if($paymode == 'live') {
								$orderurl = 'https://my.js.secure-hoolah.co/';
							}
						}
						
						$billadd2 = $shipadd2 = "";
						if($billinginfo['bill_address2']) {
							$billadd2 = $billinginfo['bill_address2'];
						}
						
						if($shippinginfo['ship_address2']) {
							$shipadd2 = $shippinginfo['ship_address2'];
						}
						
						$closeurl = url('/').'/hoolahcancelpayment?orderid='.$orderincid;
						$returnurl = url('/').'/hoolahsuccess?orderid='.$orderincid;

						$ch = curl_init( $url );
						# Setup request to send json via POST.
						$payload = array("consumerTitle" => "", "consumerFirstName" => $billinginfo['bill_fname'], "consumerLastName" => $billinginfo['bill_lname'], "consumerMiddleName" => "", "consumerEmail" => $billinginfo['bill_email'], "consumerPhoneNumber" => $billmobile, "shippingAddress" => array("line1" => $shippinginfo['ship_address1'], "line2" => $shipadd2, "suburb" => $shipcountryname, "postcode" => $shippinginfo['ship_zip'], "countryCode" => $shippinginfo['ship_country']), "billingAddress" => array("line1" => $billinginfo['bill_address1'], "line2" => $billadd2, "suburb" => $billcountryname, "postcode" => $billinginfo['bill_zip'], "countryCode" => $billinginfo['bill_country']), "items" => array($hoolahitems), "totalAmount" => $grandtotal, "originalAmount" => $grandtotal, "taxAmount" => $gst, "cartId" => $orderid, "orderType" => "ONLINE", "shippingAmount" => $deliverycost, "shippingMethod" => "FREE", "discount" => $discount, "voucherCode" => "", "currency" => $currency,  "closeUrl" => $closeurl, "returnToShopUrl" => $returnurl );
						
						
						$payload = json_encode($payload);
						
						//print_r($payload); exit;
						curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
						$header_str = "Authorization: Bearer ".$authtoken;
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
								OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $orderContentToken));
						
								if($discounttext != '' && $discount != 0) {
									CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
								}
								
								$hoolahpaymenturl = $orderurl.'?ORDER_CONTEXT_TOKEN='.$orderContentToken.'&platform=bespoke&version=1.0.1';
								
								$data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'orderContentToken' => $orderContentToken, 'hoolahpaymenturl' => $hoolahpaymenturl]);
																
							}
						}
					}
				}
			}
						
			
			//print_r($products);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Order Not Created', 'orderid' => 0, 'orderContentToken' => '', 'hoolahpaymenturl' => '']);
		}
		return $data;
	}
	
	public function hoolahcancelpayment(Request $request) {
		$data = [];
		$orderid = $request->orderid;
		$data = response()->json(['response' => 'failed', 'message' => 'Your Payment Transaction has been cancelled', 'orderid' => $orderid]);
		return $data;
	}
	
	public function hoolahsuccess(Request $request) {
		$orderid = $request->orderid;
		OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => '1'));
		$data = response()->json(['response' => 'success', 'message' => 'Your order has been completed', 'orderid' => $orderid]);
		return $data;
	}
	
	public function updateorder(Request $request) {
		$data = [];
		$orderid = $request->orderid;
		$transactionid = $request->transactionid;
		$orderstatus = $request->orderstatus;
		OrderMaster::where('order_id', '=', $orderid)->update(array('trans_id' => $transactionid, 'order_status' => $orderstatus));
		$data = response()->json(['response' => 'success', 'message' => 'Order Update', 'orderid' => $orderid]);
		return $data;
	}
	
	public function getshipandpackingprice(Request $request) {
		$data = [];
		$packagingprice = $shippingprice = $deliverycost = $packingfee = $grandtotal = 0;
		$orderinfo = $request->orderinfo;
				
		if($orderinfo) {
			$orderinfo = $orderinfo[0];
			$subtotal = $orderinfo['subtotal'];
			$gst = $orderinfo['tax_collected'];
			$deliverymethod = $orderinfo['shipmethod'];
			$country = $orderinfo['country'];
			$couponcode = $orderinfo['couponcode'];
			
			$cartitems = $orderinfo['products'];
			
			$settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
			if($country != 'SG') {
				$packingfee = $settings->min_package_fee;
			}
			$boxfees = $totalweight = 0;
			
			$cart = new Cart();
									
			foreach($cartitems as $cartitem) {
				$totalweight = $totalweight + $cartitem['weight'];
				$shippingbox = $cartitem['shippingbox'];	
				$quantity = $cartitem['quantity'];
				$deliverycost += $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
				$boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
			}
			
			$packingfee = number_format(($packingfee + $boxfees), 2);
						
			$discounttype = $disamount = 0;
			$discounttext = '';
			
			$discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);				
						
			$grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);
			
		} 
		$data = response()->json(['response' => 'success', 'message' => 'Shipping & Packaing Price', 'packagingprice' => $packingfee, 'shippingprice' => $deliverycost, 'grandtotal' => $grandtotal]);
		return $data;	
	}
	
	public function discountcoupon(Request $request) {
		$data = [];
		
		$orderinfo = $request->orderinfo;
		$usage = 0;
		$countryid = $allowapply = 0;
		$chkcategory = $chkbrand = 1;
		$country = $response = '';
		if($orderinfo) {
			$orderinfo = $orderinfo[0];
			$customerid = $orderinfo['customer_id'];
			$couponcode = $orderinfo['couponcode'];
			$cartitems = $orderinfo['products'];
			
			$cust = Customer::where('cust_id', '=', $customerid)->first();
			
			$coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();
			
			if(!empty($coupondata)) {
				$date = date('Y-m-d');
							
				if(strtotime($coupondata->validity) >= strtotime($date)) {
					
					if($cartitems) {
						if($coupondata->category_id > 0) {
							foreach($cartitems as $cartitem) {
								$chkproduct = Product::where('Id', '=', $cartitem['id'])->where('Types', '=', $coupondata->category_id)->where('ProdStatus', '=', '1')->first();
								if(!$chkproduct) {
									$chkcategory = 0;
									break;	
								}
							}
						}
						if($coupondata->brand_id > 0) {
							foreach($cartitems as $cartitem) {
								$chkproduct = Product::where('Id', '=', $cartitem[id])->where('Brand', '=', $coupondata->brand_id)->where('ProdStatus', '=', '1')->first();
								if(!$chkproduct) {
									$chkbrand = 0;
									break;	
								}
							}
						}
						
						if($coupondata->customer_id > 0) {
							if($customer == $coupondata->customer_id) {
								if($coupondata->customer_type == 1) {
									$usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
								} elseif($coupondata->customer_type == 2) {
									$usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
								}
								if($coupondata->nooftimes > $usage) {
									$allowapply = 1;
								} else {													
									$response = 'Coupon Code Usage Limit Exceeded!';								
								}
							} else {
								$allowapply = 0;
								$response = 'Invalid Coupon Code!';
							}
						} else {
							if($coupondata->customer_type == 3) {
								if($cust->group_admin == 1) {
									$usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
									if($coupondata->nooftimes > $usage) {
										$allowapply = 1;
									} else {													
										$response = 'Coupon Code Usage Limit Exceeded!';								
									}
								} else {
									$allowapply = 0;
								}		
							} else {
								if($coupondata->customer_type == 1) {								
									$usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
								} elseif($coupondata->customer_type == 2) {
									$usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
								}
								if($coupondata->nooftimes > $usage) {
									$allowapply = 1;
								} else {													
									$response = 'Coupon Code Usage Limit Exceeded!';								
								}
							}
						}
						
					}
					
				} else {
					$response = 'Coupon Code Expired!';
				}
			} else {
				$response = 'Invalid Coupon Code!';
			}
			
			if($allowapply == 1 && $chkbrand == 1 && $chkcategory == 1) {
				$discounttext = '';
				if($coupondata->discount_type == 1) {
					$discounttext = $coupondata->discount.'%';
				} else {
					$discounttext = '$'.$coupondata->discount;
				}
				$data = response()->json(['response' => 'success', 'message' => 'Valid Coupon Code', 'discount' => $coupondata->discount, 'discounttext' => $discounttext, 'couponid' => $coupondata->id]);
			} else {
				$data = response()->json(['response' => 'success', 'message' => $response, 'discount' => '', 'discounttext' => '', 'couponid' => '']);
			}
		} else {
			$data = response()->json(['response' => 'success', 'message' => $response, 'discount' => '', 'discounttext' => '', 'couponid' => '']);
		}
		return $data;
	}
	
}
