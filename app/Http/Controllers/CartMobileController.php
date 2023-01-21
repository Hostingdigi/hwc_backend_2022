<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Country;
use App\Models\Couponcode;
use App\Models\CouponCodeUsage;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\PaymentMethods;
use App\Models\PaymentSettings;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\ShippingMethods;
use App\Services\CartServices;
use DB;
use Illuminate\Http\Request;
use Mail;
use Session;
use Stripe;

class CartMobileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $cartServices = null;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    protected $orderStatus = [
        '0' => 'Payment Pending',
        '1' => 'Paid, Shipping Pending',
        '2' => 'Shipped',
        '3' => 'Shipped',
        '4' => 'Delivered',
        '5' => 'On The Way To You',
        '6' => 'Partially Delivered',
        '7' => 'Partially Refunded',
        '8' => 'Fully Refunded',
        '9' => 'Ready For Collection',
    ];

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function cartLists(Request $request)
    {
        $data = [];
        $subtotal = $grandtotal = $cartcount = 0;
        $sesid = $request->customerid;

        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $optionprice = 0;
        $prodoption = '';

        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = !empty($orderdetail->prod_option_id) ? $orderdetail->prod_option_id : 0;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {

                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        if ($options) {
                            $optionprice = $options->Price;
                            $optionprice = $price->getOptionPrice($productid, $optionid);
                            $prodoption = $options->Title;
                        }
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    $cart['image'] = ($product->MobileImage != '') ? url('/uploads/product') . '/' . $product->MobileImage : (($product->Image != '') ? url('/uploads/product') . '/' . $product->Image : url('/images/noimage.png'));
                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    $cartdata[$sesid][] = $cart;
                }
            }
            Session::put('cartdata', $cartdata);
        }

        $cart = new \App\Models\Cart();
        $country = $request->has('country_code') && !empty($request->country_code) ? trim($request->country_code) : '';
        $country = empty($country) ? 'SG' : $country;
        $subtotal = $cart->getCartSubTotal($sesid);
        $subtotal = (float) str_replace(',', '', $subtotal);
        //Exclude default tax amount
        $countryDetails = Country::where('countrycode', $country)->first();
        $taxAmount = round((($subtotal * $countryDetails->taxpercentage) / (100 + $countryDetails->taxpercentage)), 2);
        $subtotal = round($subtotal - $taxAmount, 2);

        $gst = $cart->getGST($subtotal, $country);
        $taxDetails = $this->cartServices->getTax($subtotal, $country);
        $gstText = $taxDetails['taxLabel'];
        $gst = $taxDetails['taxTotal'];

        /*if (!empty($country)) {
        $taxes = $cart->getGST($subtotal, $country);
        if ($country != '') {
        $taxvals = @explode("|", $taxes);
        $gstText = $taxvals[0];
        $gst = $taxvals[1];
        } else {
        $gst = $taxes;
        }
        }*/
        $grandtotal = $cart->getGrandTotal($subtotal, $gst);

        return response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'gstText' => $gstText,
            'subtotal_text' => 'w/o ' . $taxDetails['taxLabelOnly'], 'subtotal' => number_format($subtotal, 2), 'gst' => $gst, 'grandtotal' => $grandtotal, 'cartcount' => isset($cartdata[$sesid]) ? count($cartdata[$sesid]) : 0, 'cartdetails' => $cartdata[$sesid] ?? []]);
    }

    public function shoppingcart(Request $request)
    {
        $data = [];
        $cartdata = [];
        $subtotal = $grandtotal = $cartcount = 0;
        $sesid = $request->customerid;

        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';

        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {
                //echo $orderdetail->user_id;

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    //$cart = array();
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option_id;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {

                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $optionprice = $price->getOptionPrice($productid, $optionid);
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    //$cart['image'] = $product->Image;

                    if ($product->MobileImage != '') {
                        $cart['image'] = url('/uploads/product') . '/' . $product->MobileImage;
                    } else {
                        if ($product->Image != '') {
                            $cart['image'] = url('/uploads/product') . '/' . $product->Image;
                        } else {
                            $cart['image'] = url('/images/noimage.png');
                        }
                    }

                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    //$cartdata[$sesid][$cartcount] = $cart;
                    $cartdata[$sesid][] = $cart;
                    //$cartdata[] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        } else {
            $cartdata[$sesid] = [];
            $cartcount = 0;
        }

        $cart = new \App\Models\Cart();
        $subtotal = $cart->getCartSubTotal($sesid);
        $gst = $cart->getGST($subtotal);
        $grandtotal = $cart->getGrandTotal($subtotal, $gst);

        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata, 'subtotal' => $subtotal, 'gst' => $gst, 'grandtotal' => $grandtotal, 'cartcount' => $cartcount]);
        return $data;
    }

    public function addItem(Request $request)
    {

        $productid = $request->productid;
        $optionid = $request->has('optionid') ? (!empty($request->optionid) ? $request->optionid : 0) : 0;
        $qty = $request->has('qty') ? $request->qty : 1;
        $sesid = $request->customerid;

        $cartdata = [];
        $cartcount = $exist = $optionprice = 0;
        $prodoption = '';
        $product = Product::where('Id', $productid)->first();
        $message = $product ? 'Shopping Cart' : 'Product is not found';

        if ($product) {

            $cartdata = [
                'productId' => $productid,
                'productName' => $product->EnName,
                'price' => 0,
                'total' => 0,
                'image' => $product->Image,
                'option' => $prodoption,
                'option_id' => $optionid,
                'qty' => $qty,
                'weight' => $product->Weight,
                'productWeight' => $product->Weight,
                'price' => 0,
                'total' => 0,
                'code' => $product->Code,
                'color' => $product->Color,
                'size' => $product->Size,
                'shippingbox' => $product->ShippingBox,
                'image' => $product->Image ? url('/uploads/product') . '/' . $product->Image : url('/images/noimage.png'),
            ];

            $cartProducts = DB::table('cart_details')->where([['user_id', '=', $sesid], ['prod_id', '=', $productid], ['prod_option_id', '=', $optionid]])->first();

            if ($cartProducts) { //Already product is available

                $qty += $cartProducts->prod_quantity;

                $price = new \App\Models\Price();
                $ses_productprice = $price->getDiscountPrice($productid);

                $optionprice = ($optionid > 0) ? $price->getOptionPrice($productid, $optionid) : 0;
                $ses_productprice = $ses_productprice + $optionprice;

                $cartdata['qty'] = $qty;
                $cartdata['price'] = $ses_productprice;
                $cartdata['total'] = $qty * $ses_productprice;

                DB::table('cart_details')->where([['user_id', '=', $sesid], ['prod_id', '=', $productid]])->update(['prod_quantity' => $qty, 'prod_unit_price' => $ses_productprice]);

            } else { //New product

                $price = new \App\Models\Price();
                $ses_productprice = $price->getDiscountPrice($productid);

                if ($optionid > 0) {
                    $options = ProductOptions::where('Id', $optionid)->first();
                    $optionprice = $price->getOptionPrice($productid, $optionid);
                    $prodoption = $options->Title;
                }

                $ses_productprice = $ses_productprice + $optionprice;
                $ses_total = $qty * $ses_productprice;

                $cartdata['qty'] = $qty;
                $cartdata['price'] = $ses_productprice;
                $cartdata['total'] = $ses_total;
                $cartdata['option'] = $prodoption;

                DB::table('cart_details')->insert(['user_id' => $sesid, 'prod_id' => $productid, 'prod_name' => $product->EnName, 'prod_option' => $prodoption, 'prod_quantity' => $qty, 'prod_unit_price' => $ses_productprice, 'prod_code' => $product->Code, 'Weight' => $product->Weight, 'row_key' => $cartcount, 'prod_option_id' => $optionid]);
            }
        }

        return response()->json(['response' => 'success', 'message' => $message, 'cartdetails' => $cartdata]);
    }

    public function addtoshoppingcart(Request $request)
    {
        $data = [];
        $productid = $request->productid;
        $optionid = $request->optionid;
        $qty = 1;
        if (isset($request->qty)) {
            $qty = $request->qty;
        }

        $cart = $cartdata = [];
        $cartcount = 0;
        $exist = 0;
        $optionprice = 0;
        $prodoption = '';
        $sesid = $request->customerid;
        $product = Product::where('Id', '=', $productid)->first();
        if ($product) {
            if (Session::has('cartdata')) {
                $cartdata = Session::get('cartdata');
                //$cartcount = count($cartdata[$sesid]) + 1;
                if (!empty($cartdata)) {
                    foreach ($cartdata as $key => $val) {
                        if (is_array($val)) {
                            foreach ($val as $datakey => $dataval) {
                                if ($dataval['productId'] == $productid) {
                                    if ($optionid > 0) {
                                        if ($optionid == $dataval['option_id']) {
                                            $exist = 1;
                                            if ($qty >= $cartdata[$key][$datakey]['qty']) {
                                                $cartdata[$key][$datakey]['total'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['price'];
                                                $cartdata[$key][$datakey]['qty'] = $cartdata[$key][$datakey]['qty'] + $qty;
                                                $cartdata[$key][$datakey]['weight'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['weight'];
                                            } else {
                                                $cartdata[$key][$datakey]['total'] = $qty * $cartdata[$key][$datakey]['price'];
                                                $cartdata[$key][$datakey]['qty'] = $qty;
                                                $cartdata[$key][$datakey]['weight'] = $qty * $product->Weight;
                                            }
                                        }
                                    } else {
                                        $exist = 1;
                                        if ($qty >= $cartdata[$key][$datakey]['qty']) {
                                            $cartdata[$key][$datakey]['total'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['price'];
                                            $cartdata[$key][$datakey]['qty'] = $cartdata[$key][$datakey]['qty'] + $qty;
                                            $cartdata[$key][$datakey]['weight'] = ($cartdata[$key][$datakey]['qty'] + $qty) * $cartdata[$key][$datakey]['weight'];
                                        } else {
                                            $cartdata[$key][$datakey]['total'] = $qty * $cartdata[$key][$datakey]['price'];
                                            $cartdata[$key][$datakey]['qty'] = $qty;
                                            $cartdata[$key][$datakey]['weight'] = $qty * $product->Weight;
                                        }
                                    }

                                    DB::table('cart_details')->where('user_id', '=', $sesid)->where('row_key', '=', $datakey)->where('prod_id', '=', $productid)->update(array('prod_quantity' => $qty));
                                }
                            }
                        }
                    }
                }
            }
            if ($exist == 0) {
                $ses_productid = $productid;
                $ses_productname = $product->EnName;
                $ses_qty = $qty;

                $price = new \App\Models\Price();
                //$ses_productprice = $price->getPrice($productid);
                $ses_productprice = $price->getDiscountPrice($productid);

                if ($optionid > 0) {
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
                $cart['option_id'] = $optionid;
                $cart['weight'] = $product->Weight;
                $cart['productWeight'] = $product->Weight;
                $cart['code'] = $product->Code;
                $cart['color'] = $product->Color;
                $cart['size'] = $product->Size;
                $cart['shippingbox'] = $product->ShippingBox;
                if ($product->Image) {
                    $cart['image'] = url('/uploads/product') . '/' . $product->Image;
                } else {
                    $cart['image'] = url('/images/noimage.png');
                }
                $cartdata[$sesid][] = $cart;

                DB::table('cart_details')->insert(array('user_id' => $sesid, 'prod_id' => $productid, 'prod_name' => $ses_productname, 'prod_option' => $prodoption, 'prod_quantity' => $ses_qty, 'prod_unit_price' => $ses_productprice, 'prod_code' => $product->Code, 'Weight' => $product->Weight, 'row_key' => $cartcount, 'prod_option_id' => $optionid));
            }
            Session::put('cartdata', $cartdata);
        }
        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);
        return $data;
    }

    public function removeshoppingcartitem(Request $request)
    {
        $data = [];
        $sesid = $request->customerid;
        $productid = $request->productid;
        $cartdata = Session::get('cartdata');
        if (!empty($cartdata[$sesid])) {
            foreach ($cartdata as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $datakey => $dataval) {
                        if ($dataval['productId'] == $productid) {
                            unset($cartdata[$key][$datakey]);
                        }
                    }
                }
            }
        }

        $whereCondtion = [
            ['user_id', '=', $sesid], ['prod_id', '=', $productid],
        ];

        DB::table('cart_details')->where($whereCondtion)->delete();
        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {
                //echo $orderdetail->user_id;

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    //$cart = array();
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option_id;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {
                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $optionprice = $price->getOptionPrice($productid, $optionid);
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    //$cart['image'] = $product->Image;
                    if ($product->MobileImage != '') {
                        $cart['image'] = url('/uploads/product') . '/' . $product->MobileImage;
                    } else {
                        if ($product->Image != '') {
                            $cart['image'] = url('/uploads/product') . '/' . $product->Image;
                        } else {
                            $cart['image'] = url('/images/noimage.png');
                        }
                    }
                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    //$cartdata[$sesid][$cartcount] = $cart;
                    $cartdata[$sesid][] = $cart;
                    //$cartdata[] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        } else {
            $cartdata[$sesid] = [];
            $cartcount = 0;
        }
        if (!empty($cartdata[$sesid])) {
            $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);
        } else {
            Session::forget('cartdata');
            $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => '']);
        }
        return $data;
    }

    public function updateItem(Request $request)
    {
        $data = [];
        $qtyfield = $itemqty = '';
        $sesid = $request->customerid;
        $productid = $request->products;
        $qty = !empty($request->quantities) ? $request->quantities : 1;
        $optionid = !empty($request->optionid) ? $request->optionid : 0;

        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        $message = 'Shopping Cart';

        $cartProducts = DB::table('cart_details')->where([['user_id', '=', $sesid], ['prod_id', '=', $productid], ['prod_option_id', '=', $optionid]])->first();

        if (empty($cartProducts)) {
            $cartProducts = DB::table('cart_details')->where([['user_id', '=', $sesid], ['prod_id', '=', $productid], ['prod_option_id', '=', null]])->first();
        }

        if ($cartProducts) {

            $price = new \App\Models\Price();
            $ses_productprice = $price->getDiscountPrice($productid);

            $optionprice = ($optionid > 0) ? $price->getOptionPrice($productid, $optionid) : 0;
            $ses_productprice = $ses_productprice + $optionprice;

            DB::table('cart_details')->where('detail_id', $cartProducts->detail_id)->update(['prod_quantity' => $qty, 'prod_unit_price' => $ses_productprice]);
        } else {
            $message = 'Product is not found';
        }

        //List
        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 0;
        $optionprice = 0;
        $prodoption = '';
        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {
                //echo $orderdetail->user_id;

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    //$cart = array();
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option_id;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {
                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $optionprice = $price->getOptionPrice($productid, $optionid);
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;

                    $cart['image'] = $product->MobileImage != '' ? url('/uploads/product') . '/' . $product->MobileImage : ($product->Image != '' ? url('/uploads/product') . '/' . $product->Image : url('/images/noimage.png'));

                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    $cartdata[$sesid][] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        } else {
            $cartdata[$sesid] = [];
        }

        $cart = new \App\Models\Cart();
        $subtotal = $cart->getCartSubTotal($sesid);

        $country = $request->has('country_code') && !empty($request->country_code) ? trim($request->country_code) : '';
        $gstText = 'GST(7%)';
        $gst = $cart->getGST($subtotal, $country);
        if (!empty($country)) {
            $taxes = $cart->getGST($subtotal, $country);
            if ($country != '') {
                $taxvals = @explode("|", $taxes);
                $gstText = $taxvals[0];
                $gst = $taxvals[1];
            } else {
                $gst = $taxes;
            }
        }

        $grandtotal = $cart->getGrandTotal($subtotal, $gst);

        return response()->json(['response' => 'success', 'message' => $message, 'subtotal' => $subtotal, 'gstText' => $gstText, 'gst' => $gst, 'grandtotal' => $grandtotal, 'cartcount' => isset($cartdata[$sesid]) ? count($cartdata[$sesid]) : 0, 'cartdetails' => $cartdata[$sesid]]);
    }

    public function updateshoppingcart(Request $request)
    {
        $data = [];
        $qtyfield = $itemqty = '';
        $products = $request->products;
        if ($products) {
            $products = @explode(",", $products);
        }
        $quantities = $request->quantities;
        if ($quantities) {
            $quantities = @explode(",", $quantities);
        }

        $cartdata = Session::get('cartdata');
        $sesid = $request->customerid;

        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {
                //echo $orderdetail->user_id;

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    //$cart = array();
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option_id;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {
                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $optionprice = $price->getOptionPrice($productid, $optionid);
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    $cart['image'] = $product->Image;
                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    //$cartdata[$sesid][$cartcount] = $cart;
                    $cartdata[$sesid][] = $cart;
                    //$cartdata[] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        }

        if (!empty($cartdata)) {
            $x = 0;
            foreach ($cartdata as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $datakey => $dataval) {
                        if (in_array($dataval['productId'], $products)) {

                            if (isset($quantities[$x])) {

                                $itemqty = $quantities[$x];

                                if ($cartdata[$key][$datakey]['qty'] != $itemqty) {
                                    $cartdata[$key][$datakey]['total'] = $itemqty * $cartdata[$key][$datakey]['price'];
                                    $cartdata[$key][$datakey]['qty'] = $itemqty;
                                    DB::table('cart_details')->where('user_id', '=', $sesid)->where('prod_id', '=', $dataval['productId'])->update(array('prod_quantity' => $itemqty));
                                }
                            }
                        }
                        ++$x;
                    }
                }
            }
        }

        $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

        $cart = $cartdata = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        if (count($cartdetails) > 0) {
            foreach ($cartdetails as $orderdetail) {
                //echo $orderdetail->user_id;

                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    //$cart = array();
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option_id;

                    $price = new \App\Models\Price();
                    $productprice = $price->getDiscountPrice($productid);
                    if ($optionid > 0) {
                        $options = ProductOptions::where('Id', '=', $optionid)->first();
                        $optionprice = $options->Price;
                        $optionprice = $price->getOptionPrice($productid, $optionid);
                        $prodoption = $options->Title;
                    }

                    $productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    //$cart['image'] = $product->Image;
                    if ($product->MobileImage != '') {
                        $cart['image'] = url('/uploads/product') . '/' . $product->MobileImage;
                    } else {
                        if ($product->Image != '') {
                            $cart['image'] = url('/uploads/product') . '/' . $product->Image;
                        } else {
                            $cart['image'] = url('/images/noimage.png');
                        }
                    }
                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    //$cartdata[$sesid][$cartcount] = $cart;
                    $cartdata[$sesid][] = $cart;
                    //$cartdata[] = $cart;
                    ++$cartcount;
                }
            }
            Session::put('cartdata', $cartdata);
        } else {
            $cartdata[$sesid] = [];
            $cartcount = 0;
        }

        Session::put('cartdata', $cartdata);
        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cartdata]);
        return $data;
    }

    public function removeItem(Request $request)
    {
        $optionId = 0;
        $whereCondtion = [
            ['user_id', '=', $request->customerId], ['prod_id', '=', $request->productId],
        ];

        if ($request->has('optionId')) {
            $optionId = !empty($request->optionId) ? $request->optionId : 0;
        }

        $whereCondtion[] = ['prod_option_id', '=', $optionId];

        $sesid = $request->customerId;
        $productid = $request->productId;

        $isProductAvailable = DB::table('cart_details')->where($whereCondtion)->first();
        $message = 'Product is not found';
        $cartdata = [];

        if (empty($isProductAvailable)) {
            $whereCondtion[2] = ['prod_option_id', '=', null];
            $isProductAvailable = DB::table('cart_details')->where($whereCondtion)->first();
        }

        if ($isProductAvailable) {
            DB::table('cart_details')->where('detail_id', $isProductAvailable->detail_id)->delete();
            $message = 'Product has been removed';

            $cartdetails = DB::table('cart_details')->where('user_id', $sesid)->get();

            $optionprice = 0;
            $prodoption = '';

            if (count($cartdetails) > 0) {
                foreach ($cartdetails as $orderdetail) {

                    $product = Product::where('Id', $orderdetail->prod_id)->first();

                    if ($product->Quantity > 0) {

                        $ses_productid = $orderdetail->prod_id;
                        $productid = $orderdetail->prod_id;
                        $ses_productname = $product->EnName;
                        $ses_qty = $orderdetail->prod_quantity;
                        $optionid = $orderdetail->prod_option_id;

                        $price = new \App\Models\Price();
                        $productprice = $price->getDiscountPrice($productid);
                        if ($optionid > 0) {
                            $options = ProductOptions::where('Id', '=', $optionid)->first();
                            $optionprice = $options->Price;
                            $optionprice = $price->getOptionPrice($productid, $optionid);
                            $prodoption = $options->Title;
                        }

                        $productprice = $productprice + $optionprice;

                        $ses_productprice = $productprice;
                        $ses_total = $ses_qty * $productprice;
                        $cart['productId'] = $ses_productid;
                        $cart['productName'] = $ses_productname;
                        $cart['qty'] = $ses_qty;
                        $cart['price'] = $ses_productprice;
                        $cart['total'] = $ses_total;
                        //$cart['image'] = $product->Image;
                        if ($product->MobileImage != '') {
                            $cart['image'] = url('/uploads/product') . '/' . $product->MobileImage;
                        } else {
                            if ($product->Image != '') {
                                $cart['image'] = url('/uploads/product') . '/' . $product->Image;
                            } else {
                                $cart['image'] = url('/images/noimage.png');
                            }
                        }
                        $cart['productoption'] = $prodoption;
                        $cart['option_id'] = $optionid;
                        $cart['weight'] = $product->Weight;
                        $cart['productWeight'] = $product->Weight;
                        $cart['productcode'] = $product->Code;
                        $cart['color'] = $product->Color;
                        $cart['size'] = $product->Size;
                        $cart['shippingbox'] = $product->ShippingBox;
                        $cartdata[] = $cart;
                    }
                }
            }
        }

        return response()->json(['response' => 'success', 'message' => $message, 'cartdetails' => $cartdata]);
    }

    public function clearshoppingcart(Request $request)
    {
        $data = [];
        $sesid = $request->customerid;
        DB::table('cart_details')->where('user_id', '=', $sesid)->delete();
        $cdata[$sesid] = [];
        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cdata]);
        Session::forget('cartdata');
        return $data;
    }

    public function clearcustomershoppingcart(Request $request)
    {
        $data = [];
        $sesid = $request->customerid;
        DB::table('cart_details')->where('user_id', '=', $sesid)->delete();
        $cdata[$sesid] = [];
        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'cartdetails' => $cdata]);
        Session::forget('cartdata');
        return $data;
    }

    public function ListShippingmethods(Request $request)
    {

        $countrycode = $request->countrycode;
        $shippingType = $countrycode != 'SG' ? 1 : 0;

        $shipmethods = ShippingMethods::select(['Id as id', 'EnName as name', 'Price as price', 'FreeShipAvailable as freeshippingavailable', 'FreeShipCost as freeshipcost'])->where([['Status', '=', '1'], ['shipping_type', '=', $shippingType]])->orderBy('DisplayOrder', 'ASC')->get();
        $subTotal = 0;
        if ($countrycode == 'SG') {
            $cartdetails = DB::table('cart_details')->where('user_id', $request->customer_id)->get();
            $price = new \App\Models\Price();

            foreach ($cartdetails as $cartItem) {
                $productprice = $price->getDiscountPrice($cartItem->prod_id);
                $total = $productprice * $cartItem->prod_quantity;

                if ($cartItem->prod_option_id > 0) {
                    $optionprice = $price->getOptionPrice($cartItem->prod_id, $cartItem->prod_option_id) * $cartItem->prod_quantity;
                    $total += $optionprice;
                }
                $subTotal += $total;
            }

            $freeShipmethods = $shipmethods->filter(function ($item) {
                return $item->freeshippingavailable == 1;
            })->values();

            if (!empty($freeShipmethods) && $subTotal > $freeShipmethods[0]->freeshipcost) {
                $shipmethods = $shipmethods->filter(function ($item) {
                    return $item->id != 6;
                })->values();
            } else {
                $shipmethods = $shipmethods->filter(function ($item) {
                    return $item->id != 5;
                })->values();
            }

        }

        return response()->json(['ss' => $subTotal, 'response' => 'success', 'message' => 'Shipping Methods', 'shippingmethods' => $shipmethods]);
    }

    public function shippingmethods(Request $request)
    {
        $countrycode = $request->countrycode;
        $data = $shippingmenthods = [];
        if ($countrycode != 'SG' && $countrycode != '') {
            $shipmethods = ShippingMethods::where('Status', '=', '1')->where('shipping_type', '=', '1')->orderBy('DisplayOrder', 'ASC')->get();
        } else {
            $shipmethods = ShippingMethods::where('Status', '=', '1')->where('shipping_type', '=', '0')->orderBy('DisplayOrder', 'ASC')->get();
        }
        if ($shipmethods) {
            $x = 0;
            foreach ($shipmethods as $shipmethod) {
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

    public function paymethods(Request $request)
    {
        $type = $request->type;
        $data = $paymentmenthods = [];
        if ($type == 2) {
            $paymethods = PaymentMethods::where('status', '=', '1')->whereIn('type', [1, 2])->get();

        } else if ($type == 3) {
            $paymethods = PaymentMethods::where('status', '=', '1')->whereIn('type', [1, 3])->get();

        } else {
            return response()->json(['response' => 'Error', 'message' => 'Type is empty']);
        }

        if ($paymethods) {
            $x = 0;
            foreach ($paymethods as $paymethod) {
                $paymentmenthods[$x]['id'] = $paymethod->Id;
                $paymentmenthods[$x]['name'] = $paymethod->payment_name;
                $paymentmenthods[$x]['mode'] = $paymethod->payment_mode;
                $paymentmenthods[$x]['testing_url'] = $paymethod->testing_url;
                $paymentmenthods[$x]['live_url'] = $paymethod->live_url;
                $paymentmenthods[$x]['api_key'] = $paymethod->api_key;
                $paymentmenthods[$x]['api_signature'] = $paymethod->api_signature;

                if ($paymethod->Id == 3) {
                    $paymentmenthods[$x]['test_api_key'] = $paymethod->test_api_key;
                    $paymentmenthods[$x]['test_api_signature'] = $paymethod->test_api_signature;
                }

                ++$x;
            }
            $data = response()->json(['response' => 'success', 'message' => 'Payment Methods', 'paymentmenthods' => $paymentmenthods]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Payment Methods', 'paymentmenthods' => '']);
        }
        return $data;
    }

    public function createorder(Request $request)
    {
        $data = [];
        $shipcountryname = $billcountryname = '';
        $orderincid = $orderid = $taxValue = 0;
        $orderinfo = $request->orderinfo;

        if ($orderinfo) {
            $orderinfo = $orderinfo[0];
            $userid = $orderinfo['customer_id'];

            $billinginfo = $orderinfo['billing'][0];
            $shippinginfo = $orderinfo['shipping'][0];
            //$carddetails = $orderinfo['carddetails'][0];

            if ($userid <= 0) {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
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
            $deliverynote = $orderinfo['deliverynote'];
            $taxtitle = $deliverytype = '';
            $shipdata = ShippingMethods::where('Id', '=', $orderinfo['shipmethod'])->select('EnName')->first();
            if ($shipdata) {
                $deliverytype = $shipdata->EnName;
            }
            $existorderid = 0;
            if (isset($orderinfo['existorderid'])) {
                $existorderid = $orderinfo['existorderid'];
            }

            $countrydata = Country::where('countrycode', '=', $shippinginfo['ship_country'])->select('countryid', 'taxtitle', 'taxpercentage')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $taxtitle = $countrydata->taxtitle;
                $taxValue = $countrydata->taxpercentage;
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = $orderinfo['shipmethod'];
            $ordermaster['pay_method'] = $orderinfo['paymethod'];
            $ordermaster['shipping_cost'] = str_replace(',', '', $orderinfo['shippingcost']);
            $ordermaster['packaging_fee'] = str_replace(',', '', $orderinfo['packagingfee']);
            $ordermaster['tax_collected'] = str_replace(',', '', $orderinfo['tax_collected']);
            $ordermaster['tax_label'] = trim($taxtitle . ' (' . $taxValue . '%)');
            $ordermaster['tax_percentage'] = $taxValue;
            $ordermaster['discount_amount'] = str_replace(',', '', $discount);
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['payable_amount'] = str_replace(',', '', $orderinfo['payable_amount']);
            $ordermaster['order_status'] = '0';
            $ordermaster['order_from'] = '1';
            $ordermaster['delivery_instructions'] = $deliverynote;
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

            if ($existorderid > 0) {
                $orderid = $existorderid;
                $orderincid = $existorderid;
                $today = date('Y-m-d H:i:s');
                OrderMaster::where('order_id', '=', $orderincid)->update(array('pay_method' => $paymethodname, 'date_entered' => $today));
            } else {

                $ordermaster->save();

                $order = OrderMaster::orderBy('order_id', 'desc')->select('order_id')->first();
                if ($order) {
                    $orderid = $order->order_id;
                    $orderincid = $order->order_id;
                }
            }

            $hoolahitems = [];
            $atomeitems = [];
            $cartitems = $orderinfo['products'];

            $itemdetails = '<table style="width:100%; background:#f1f1f1;">';
            $itemdetails .= '<tr><th>Item</th><th >Qty</th><th style="text-align:right">Price</th><th>Total</th></tr>';

            foreach ($cartitems as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['id'];
                $orderdetails['prod_name'] = $cart['name'];
                $orderdetails['prod_quantity'] = $cart['quantity'];
                $orderdetails['prod_unit_price'] = str_replace(',', '', $cart['price']);
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
                if ($product->Quantity > $cart['quantity']) {
                    $qty = $product->Quantity - $cart['quantity'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['name'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['id'])->update(array('Quantity' => $qty));

                $productname = $cart['name'];

                $sku = $ean = "";
                if ($cart['code']) {
                    $sku = $cart['code'];
                    $ean = $cart['code'];
                }

                $hoolahitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['quantity'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);

                $atomeitems = array("name" => $productname, "description" => $desc, "sku" => $sku, "ean" => $ean, "quantity" => $cart['quantity'], "originalPrice" => $cart['price'], "price" => $cart['price'], "images" => array(array("imageLocation" => $image)), "taxAmount" => "0", "discount" => "0", "detailDescription" => $desc);

                $itemdetails .= '<tr><td>' . $cart['name'] . '</td><td>' . $cart['quantity'] . '</td><td style="text-align:right">$' . number_format($cart['price'], 2) . '</td><td>$' . number_format($cart['total'], 2) . '</td></tr>';
            }

            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Sub Total</td><td>$' . $subtotal . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>' . $taxtitle . '</td><td>$' . $gst . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Shipping (' . $deliverytype . ')</td><td>$' . $deliverycost . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Packing Fee</td><td>$' . $packingfee . '</td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td><b>Grand Total</b></td><td><b>$' . $grandtotal . '</b></td></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';

            $itemdetails .= '</table>';

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $shippinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_address1'], 'cust_address2' => $billinginfo['bill_address2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;
            }

            $billing = '';
            $billing .= '<p>' . $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_address1'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_address2'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_city'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_state'] . ' - ' . $billinginfo['bill_zip'] . '</p>';
            $billing .= '<p>' . $billinginfo['bill_country'] . '</p>';

            $shipping = '';
            $shipping .= '<p>' . $shippinginfo['ship_fname'] . ' ' . $shippinginfo['ship_lname'] . '</p>';
            $shipping .= '<p>' . $shippinginfo['ship_address1'] . '</p>';
            $shipping .= '<p>' . $shippinginfo['ship_address2'] . '</p>';
            $shipping .= '<p>' . $shippinginfo['ship_city'] . '</p>';
            $shipping .= '<p>' . $shippinginfo['ship_state'] . ' - ' . $shippinginfo['ship_zip'] . '</p>';
            $shipping .= '<p>' . $shippinginfo['ship_country'] . '</p>';

            $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
            if ($emailtemplate && 1 == 2) {

                if (strlen($orderid) == 3) {
                    $orderid = date('Ymd') . '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = date('Ymd') . '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = date('Ymd') . '000' . $orderid;
                } else {
                    $orderid = date('Ymd') . $orderid;
                }

                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;
                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';
                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'], $emailcontent);
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

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                //@mail($custemail, $emailsubject, $emailcontent, $headers);

                Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                    $message->to($custemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }

            $grandtotal = str_replace(',', '', $grandtotal);
            $deliverycost = str_replace(',', '', $deliverycost);
            $gst = str_replace(',', '', $gst);
            $discount = str_replace(',', '', $discount);

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            if ($orderinfo['paymethod'] == 'Stripe Pay' || $orderinfo['paymethod'] == 'Native Pay' || $orderinfo['paymethod'] == 'Googlepay' || $orderinfo['paymethod'] == 'Applepay') {

                //OrderMaster::where('order_id', '=', $orderincid)->update(array('order_status' => '0'));
                //$data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid]);

                /*$stripekey = '';
                $paymode = 'live';
                $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%stripe')->first();

                if($paymentmethod) {
                $paymode = $paymentmethod->payment_mode;
                if($paymode == 'live') {
                $stripekey = $paymentmethod->api_key;
                } else {
                $stripekey = $paymentmethod->test_api_key;
                }
                }

                $payurl = url('/').'/api/stripepayment';
                $successurl = url('/').'/successorder?orderid='.$orderincid.'&transactionid=';
                $cancelurl = url('/').'/cancelorder?orderid='.$orderincid.'&transactionid=';
                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'paymenturl' => $payurl, 'successurl' => $successurl, 'cancelurl' => $cancelurl, 'apikey' => $stripekey]);*/

                $stripesignature = '';
                $paymode = 'live';
                $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%Credit Card')->select(['payment_mode', 'api_signature', 'test_api_signature'])->first();

                if ($paymentmethod) {
                    $paymode = $paymentmethod->payment_mode;
                    if ($paymode == 'live') {
                        $stripesignature = $paymentmethod->api_signature;
                    } else {
                        $stripesignature = $paymentmethod->test_api_signature;
                    }
                }

                Stripe\Stripe::setApiKey($stripesignature);

                $token = $orderinfo['token'];

                if (!empty($token)) { // Do payment from backend

                    $stripe = new \Stripe\StripeClient($stripesignature);
                    $response = $stripe->charges->create([
                        'amount' => $grandtotal * 100,
                        'currency' => $currency,
                        'source' => $token,
                        'description' => 'Payment from hardwarecity.com.sg',
                        'metadata' => array("order_id" => $orderid),
                    ]);

                    if ($response) {
                        $transid = $response['id'];
                        OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $transid, 'order_status' => '1'));
                    }

                } else { // Dont do payment, mobile end will do payment

                }

                $successurl = url('/') . '/successorder?orderid=' . $orderincid . '&transactionid=';
                $cancelurl = url('/') . '/cancelorder?orderid=' . $orderincid . '&transactionid=';

                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'successurl' => $successurl, 'cancelurl' => $cancelurl, 'paymenturl' => '', 'apikey' => '']);

            } elseif ($orderinfo['paymethod'] == 'Hoolah') {
                $paymode = 'live';
                $paymentmethod = PaymentMethods::where('id', '=', '4')->orWhere('payment_name', 'LIKE', '%hoolah')->first();

                if ($paymentmethod) {
                    $paymode = $paymentmethod->payment_mode;
                    if ($paymode == 'live') {
                        $apikey = $paymentmethod->api_key;
                        $apisignature = $paymentmethod->api_signature;
                        $paymenturl = $paymentmethod->live_url;
                    } else {
                        $apikey = $paymentmethod->test_api_key;
                        $apisignature = $paymentmethod->test_api_signature;
                        $paymenturl = $paymentmethod->testing_url;
                    }
                }

                $url = $paymenturl . "/auth/login";
                $ch = curl_init($url);
                $payload = json_encode(array("username" => $apikey, "password" => $apisignature));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                if ($result) {
                    $response = json_decode($result);
                    $authtoken = $response->token;

                    if ($authtoken != '') {

                        $url = $paymenturl . "/order/initiate";

                        $billmobile = $billinginfo['bill_mobile'];
                        $orderurl = 'https://demo-js.demo-hoolah.co/';
                        if ($billinginfo['bill_country'] == 'SG') {
                            $billmobile = '+65' . $billmobile;
                            if ($paymode == 'live') {
                                $orderurl = 'https://js.secure-hoolah.co/';
                            }
                        } elseif ($billinginfo['bill_country'] == 'MY') {
                            $billmobile = '+60' . $billmobile;
                            $orderurl = 'https://my.demo-js.demo-hoolah.co/';
                            if ($paymode == 'live') {
                                $orderurl = 'https://my.js.secure-hoolah.co/';
                            }
                        }

                        $billadd2 = $shipadd2 = "";
                        if ($billinginfo['bill_address2']) {
                            $billadd2 = $billinginfo['bill_address2'];
                        }

                        if ($shippinginfo['ship_address2']) {
                            $shipadd2 = $shippinginfo['ship_address2'];
                        }

                        $closeurl = url('/') . '/hoolahcancelpayment?orderid=' . $orderincid;
                        $returnurl = url('/') . '/hoolahsuccess?orderid=' . $orderincid;

                        $ch = curl_init($url);
                        # Setup request to send json via POST.
                        $payload = array("consumerTitle" => "", "consumerFirstName" => $billinginfo['bill_fname'], "consumerLastName" => $billinginfo['bill_lname'], "consumerMiddleName" => "", "consumerEmail" => $billinginfo['bill_email'], "consumerPhoneNumber" => $billmobile, "shippingAddress" => array("line1" => $shippinginfo['ship_address1'], "line2" => $shipadd2, "suburb" => $shipcountryname, "postcode" => $shippinginfo['ship_zip'], "countryCode" => $shippinginfo['ship_country']), "billingAddress" => array("line1" => $billinginfo['bill_address1'], "line2" => $billadd2, "suburb" => $billcountryname, "postcode" => $billinginfo['bill_zip'], "countryCode" => $billinginfo['bill_country']), "items" => array($hoolahitems), "totalAmount" => $grandtotal, "originalAmount" => $grandtotal, "taxAmount" => $gst, "cartId" => $orderid, "orderType" => "ONLINE", "shippingAmount" => $deliverycost, "shippingMethod" => "FREE", "discount" => $discount, "voucherCode" => "", "currency" => $currency, "closeUrl" => $closeurl, "returnToShopUrl" => $returnurl);

                        $payload = json_encode($payload);

                        //print_r($payload); exit;
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        $header_str = "Authorization: Bearer " . $authtoken;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "Content-Type: application/json",
                            "Accept: application/json",
                            $header_str,
                        ));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($ch);
                        curl_close($ch);

                        if ($result) {
                            $response = json_decode($result);

                            $orderContentToken = $response->orderContextToken;
                            $orderid = $response->orderId;
                            $orderuuid = $response->orderUuid;
                            if ($orderContentToken) {
                                OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $orderContentToken));

                                if ($discounttext != '' && $discount != 0) {
                                    CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
                                }

                                $hoolahpaymenturl = $orderurl . '?ORDER_CONTEXT_TOKEN=' . $orderContentToken . '&platform=bespoke&version=1.0.1';

                                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'orderContentToken' => $orderContentToken, 'hoolahpaymenturl' => $hoolahpaymenturl]);

                            }
                        }
                    }
                }
            } elseif ($orderinfo['paymethod'] == 'Paypal') {
                $payurl = url('/') . '/api/paypalpayment';
                $successurl = url('/') . '/successorder?orderid=' . $orderincid . '&transactionid=';
                $cancelurl = url('/') . '/cancelorder?orderid=' . $orderincid . '&transactionid=';
                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'paymenturl' => $payurl, 'successurl' => $successurl, 'cancelurl' => $cancelurl, 'apikey' => '']);
                //return view('public/Payment.paypalapi', compact('orderincid', 'orderinfo'));
            } elseif ($orderinfo['paymethod'] == 'Googlepay-Test' || $orderinfo['paymethod'] == 'Applepay-Test') {
                $payurl = '';
                $successurl = url('/') . '/successorder?orderid=' . $orderincid . '&transactionid=';
                $cancelurl = url('/') . '/cancelorder?orderid=' . $orderincid . '&transactionid=';
                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'paymenturl' => $payurl, 'successurl' => $successurl, 'cancelurl' => $cancelurl, 'apikey' => '']);
                //return view('public/Payment.paypalapi', compact('orderincid', 'orderinfo'));
            } elseif ($orderinfo['paymethod'] == 'Atome') {
                $paymode = 'live';
                $paymentmethod = PaymentMethods::where('id', '=', '7')->orWhere('payment_name', 'LIKE', '%Atome')->first();

                if ($paymentmethod) {
                    $paymode = $paymentmethod->payment_mode;
                    if ($paymode == 'live') {
                        $apikey = $paymentmethod->api_key;
                        $apisignature = $paymentmethod->api_signature;
                        $paymenturl = $paymentmethod->live_url;
                    } else {
                        $apikey = $paymentmethod->test_api_key;
                        $apisignature = $paymentmethod->test_api_signature;
                        $paymenturl = $paymentmethod->testing_url;
                    }
                }

                $url = $paymenturl . "/auth";
                $ch = curl_init($url);
                $auth = base64_encode($apikey . ':' . $apisignature);
                $header_str = "Authorization: Basic " . $auth;
                $payload = json_encode(array("callbackUrl" => "https://hardwarecity.com.sg/atomecallback", "countryCode" => "SG"));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                    $header_str,
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                //print_r($result); exit;

                if ($result) {
                    $response = json_decode($result);

                    //print_r($response);

                    $authtoken = $response->code;

                    if ($authtoken == 'SUCCESS') {

                        $url = $paymenturl . "/payments";

                        $billmobile = $billinginfo['bill_mobile'];

                        if (strlen($billmobile) > 8) {
                            $billmobile = substr($billmobile, -8);
                        }

                        $billadd2 = $shipadd2 = "";
                        if ($billinginfo['bill_address2']) {
                            $billadd2 = $billinginfo['bill_address2'];
                        }

                        if ($shippinginfo['ship_address2']) {
                            $shipadd2 = $shippinginfo['ship_address2'];
                        }

                        $closeurl = url('/') . '/cancelorder?orderid=' . $orderincid;
                        $returnurl = url('/') . '/successorder?orderid=' . $orderincid;

                        $ch = curl_init($url);
                        # Setup request to send json via POST.

                        $payload = json_encode(array(
                            "referenceId" => $orderincid,
                            "currency" => $currency,
                            "amount" => $grandtotal * 100,
                            "callbackUrl" => url('/') . "/atomecallback",
                            "paymentResultUrl" => $returnurl,
                            "paymentCancelUrl" => $closeurl,
                            "merchantReferenceId" => $orderincid,
                            "customerInfo" => [
                                "mobileNumber" => $billmobile,
                                "fullName" => $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'],
                                "email" => $billinginfo['bill_email'],
                            ],
                            "shippingAddress" => [
                                "countryCode" => $shippinginfo['ship_country'],
                                "lines" => [
                                    $shippinginfo['ship_address1'],
                                ],
                                "postCode" => $shippinginfo['ship_zip'],
                            ],
                            "billingAddress" => [
                                "countryCode" => $billinginfo['bill_country'],
                                "lines" => [
                                    $billinginfo['bill_address1'],
                                ],
                                "postCode" => $billinginfo['bill_zip'],
                            ],
                            "taxAmount" => $gst * 100,
                            "shippingAmount" => $deliverycost * 100,
                            "originalAmount" => $grandtotal * 100,
                            "items" => [$atomeitems],
                        ));

                        $url = $paymenturl . '/payments';
                        $ch = curl_init($url);
                        # Setup request to send json via POST.

                        $auth = base64_encode($apikey . ':' . $apisignature);
                        $header_str = "Authorization: Basic " . $auth;
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            "Content-Type: application/json",
                            "Accept: application/json",
                            $header_str,
                        ));
                        # Return response instead of printing.
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        # Send request.
                        $result = curl_exec($ch);
                        curl_close($ch);

                        if ($result) {
                            $response = json_decode($result);
                            //print_r($response);

                            $apiPaymentUrl = $response->appPaymentUrl;

                            if ($apiPaymentUrl) {
                                //OrderMaster::where('order_id', '=', $orderincid)->update(array('trans_id' => $orderContentToken));

                                if ($discounttext != '' && $discount != 0) {
                                    CouponCodeUsage::insert(array('coupon_id' => $couponid, 'customer_id' => $userid, 'order_id' => $orderincid));
                                }

                                $successurl = url('/') . '/successorder?orderid=' . $orderincid . '&transactionid=';
                                $cancelurl = url('/') . '/cancelorder?orderid=' . $orderincid . '&transactionid=';

                                $data = response()->json(['response' => 'success', 'message' => 'Order Created', 'orderid' => $orderincid, 'paymenturl' => $apiPaymentUrl, 'successurl' => $successurl, 'cancelurl' => $cancelurl, 'apikey' => '']);
                            }
                        }
                    }
                }
            }

            DB::table('cart_details')->where('user_id', $userid)->delete();
            //print_r($products);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Order Not Created', 'orderid' => 0, 'orderContentToken' => '', 'hoolahpaymenturl' => '']);
        }
        return $data;
    }

    public function paypalpayment(Request $request)
    {
        $data = $orderdetails = [];

        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';

        $orderid = $request->orderid;
        $userid = $grandtotal = 0;
        //$transactionid = $request->transactionid;
        //OrderMaster::where('order_id', '=', $orderid)->update(array('trans_id' => $transactionid, 'order_status' => '1'));

        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
            $userid = $order->user_id;
            $grandtotal = $order->payable_amount;
        }

        DB::table('cart_details')->where('user_id', '=', $userid)->delete();

        $currency = 'SGD';
        $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
        if ($paysettings) {
            $currency = $paysettings->currency_type;
        }

        $paymode = 'live';
        $payenv = 'production';
        $paymentmethod = PaymentMethods::where('id', '=', '6')->orWhere('payment_name', 'LIKE', '%Paypal')->first();

        if ($paymentmethod) {
            $paymode = $paymentmethod->payment_mode;
            if ($paymode == 'live') {
                $apikey = $paymentmethod->api_key;
                $apisignature = $paymentmethod->api_signature;
                $paymenturl = $paymentmethod->live_url;
                $payenv = 'production';
            } else {
                $apikey = $paymentmethod->test_api_key;
                $apisignature = $paymentmethod->test_api_signature;
                $paymenturl = $paymentmethod->testing_url;
                $payenv = 'sandbox';
            }
        }

        return view('public/Payment.paypalpayment', compact('orderid', 'order', 'orderdetails', 'payenv', 'paymenturl', 'apikey', 'apisignature', 'userid', 'currency', 'grandtotal'));
    }

    public function stripepayment(Request $request)
    {

        $data = $orderdetails = [];

        $authtoken = $username = $password = $paymenturl = $apikey = $apisignature = $billcountryname = $shipcountryname = '';

        $orderid = $request->orderid;
        $userid = $grandtotal = 0;
        $stripekey = $request->apikey;
        $apikey = $request->apikey;

        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
            $userid = $order->user_id;
            $grandtotal = $order->payable_amount;
        }

        DB::table('cart_details')->where('user_id', '=', $userid)->delete();

        $currency = 'SGD';
        $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
        if ($paysettings) {
            $currency = $paysettings->currency_type;
        }

        $apisignature = '';
        $paymode = 'live';
        $paymentmethod = PaymentMethods::where('id', '=', '3')->orWhere('payment_name', 'LIKE', '%Credit Card')->select('api_signature')->first();

        if ($paymentmethod) {
            $paymode = $paymentmethod->payment_mode;
            if ($paymode == 'live') {
                $apisignature = $paymentmethod->api_signature;
            } else {
                $apisignature = $paymentmethod->api_signature;
            }
        }

        Stripe\Stripe::setApiKey($apisignature);

        $token = $orderinfo['token'];

        return view('public/Payment.stripepayment', compact('orderid', 'order', 'orderdetails', 'apikey', 'apisignature', 'userid', 'currency', 'grandtotal'));
    }

    public function hoolahcancelpayment(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        //$data = response()->json(['response' => 'failed', 'message' => 'Your Payment Transaction has been cancelled', 'orderid' => $orderid]);
        //return $data;
        return view('public/Payment.hoolahcancelpayment');
    }

    public function hoolahsuccess(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => '1'));
        return view('public/Payment.hoolahsuccess');
        //$data = response()->json(['response' => 'success', 'message' => 'Your order has been completed', 'orderid' => $orderid]);
        //return $data;
    }

    public function paypalsuccess(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        $transactionid = $request->transactionid;
        OrderMaster::where('order_id', '=', $orderid)->update(array('trans_id' => $transactionid, 'order_status' => '1'));
        $data = response()->json(['response' => 'success', 'message' => 'Your order has been completed', 'orderid' => $orderid]);
        return $data;
    }

    public function successorder(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        $transactionid = $request->transactionid;
        OrderMaster::where('order_id', '=', $orderid)->update(array('trans_id' => $transactionid, 'order_status' => '1'));

        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();

            $logo = url('/') . '/front/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $itemdetails = '<table style="width:100%;" border="0" cellpadding="5" cellspacing="0">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left; border:1px solid #dee2e6;">Item</th><th width="15%" style="text-align:center; border:1px solid #dee2e6;">Quantity</th><th width="25%" style="text-align:right; border:1px solid #dee2e6;">Price</th><th width="20%" style="text-align:right; border:1px solid #dee2e6;">Total</th></tr>';

            $orderid = $order->order_id;
            if (strlen($orderid) == 3) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '0' . $orderid;
            } elseif (strlen($orderid) == 2) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '00' . $orderid;
            } elseif (strlen($orderid) == 1) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '000' . $orderid;
            } else {
                $orderid = date('Ymd', strtotime($order->date_entered)) . $orderid;
            }

            $tax = '';
            $taxes = Country::where('countrycode', '=', $order->ship_country)->first();
            if ($taxes) {
                $tax = $taxes->taxtitle . ' - ' . $taxes->taxpercentage;
            }

            if ($orderdetails) {
                foreach ($orderdetails as $orderdetail) {
                    $itemdetails .= '<tr><td style="border:1px solid #dee2e6;">' . $orderdetail->prod_name;
                    if ($orderdetail->prod_option != '') {
                        $itemdetails .= '<span style="color: #6c757d !important">Option: ' . $orderdetail->prod_option . '</span>';
                    }
                    $itemdetails .= '</td><td style="text-align:center; border:1px solid #dee2e6;">' . $orderdetail->prod_quantity . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . $orderdetail->prod_unit_price . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) . '</td></tr>';
                }
            }

            $itemdetails .= '<tr><td colspan="4" style="border:1px solid #dee2e6;">&nbsp;</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Sub Total</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->payable_amount - ($order->shipping_cost + $order->packaging_fee + $order->tax_collected), 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Tax (' . $tax . '%)</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->tax_collected, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Shipping</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->shipping_cost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Packaging Fee</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->packaging_fee, 2) . '</td></tr>';
            if ($order->discount_amount != '0.00') {
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Discount</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->discount_amount, 2) . '</td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;"><b>Grand Total</b></td><td style="text-align:right; border:1px solid #dee2e6;"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr>';

            $emailsubject = $emailcontent = '';

            $companyname = $adminemail = $ccemail = $customername = $customeremail = $companydetails = $statusdetails = '';
            $shippinginfo = $billinginfo = '';
            $shipmethod = '';

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;

                $companydetails .= '<table><tr><td>' . nl2br($setting->company_address) . '</td></tr>';
                $companydetails .= '<tr><td>Fax: ' . $setting->company_fax . '</td></tr>';
                $companydetails .= '<tr><td>GST No: ' . $setting->GST_res_no . '</td></tr></table>';

                $statusdetails .= '<table><tr><td style="vertical-align:top">Date: ' . date("d/m/Y", strtotime($order->date_entered)) . '</td></tr>';

                if ($order->order_status == 0) {
                    $statusdetails .= '<tr><td>Status: Payment Pending</td></tr>';
                } elseif ($order->order_status == 1) {
                    $statusdetails .= '<tr><td>Status: Paid, Shipping Pending</td></tr>';
                }

                $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                if ($shipping) {
                    $statusdetails .= '<tr><td>Delivery Method: ' . $shipping->EnName . '</td></tr>';
                    $shipmethod = $shipping->EnName;
                }

                if ($order->if_items_unavailabel == 1) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Call Me</td></tr>';
                } elseif ($order->if_items_unavailabel == 2) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Do Not Replace</td></tr>';
                } else {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Replace</td></tr>';
                }

                if (stripos($shipmethod, 'Self Collect') !== false) {
                    $shippinginfo .= '<table><tr><td>' . $shipmethod . '</td></tr></table>';
                } else {
                    $shippinginfo .= '<table><tr><td>' . $order->ship_fname . ' ' . $order->ship_lname . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_ads1 . '</td></tr>';
                    if ($order->ship_ads2) {
                        $shippinginfo .= '<tr><td>' . $order->ship_ads2 . '</td></tr>';
                    }
                    $shippinginfo .= '<tr><td>' . $order->ship_city . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_state . ' - ' . $order->ship_zip . '</td></tr>';
                    $shippinginfo .= '<tr><td>Email: ' . $order->ship_email . '</td></tr>';
                    $shippinginfo .= '<tr><td>Mobile: ' . $order->ship_mobile . '</td></tr>';
                    $shippinginfo .= '</table>';
                }
                $billinginfo .= '<table><tr><td>' . $order->bill_fname . ' ' . $order->bill_lname . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_ads1 . '</td></tr>';
                if ($order->bill_ads2) {
                    $billinginfo .= '<tr><td>' . $order->bill_ads2 . '</td></tr>';
                }
                $billinginfo .= '<tr><td>' . $order->bill_city . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_state . ' - ' . $order->bill_zip . '</td></tr>';
                $billinginfo .= '<tr><td>Email: ' . $order->bill_email . '</td></tr>';
                $billinginfo .= '<tr><td>Mobile: ' . $order->bill_mobile . '</td></tr>';
                $billinginfo .= '</table>';

                $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
                if ($emailtemplate) {
                    $emailsubject = $emailtemplate->subject;
                    $emailcontent = $emailtemplate->content;
                }

                $customer = Customer::where('cust_id', '=', $order->user_id)->first();
                if ($customer) {
                    $customername = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                    $customeremail = $customer->cust_email;
                }

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{status}', 'payment is successful', $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $customername, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', $companydetails, $emailcontent);
                $emailcontent = str_replace('{statusdetails}', $statusdetails, $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billinginfo, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shippinginfo, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $order->pay_method, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                //@mail($customeremail, $emailsubject, $emailcontent, $headers);
                Mail::send([], [], function ($message) use ($customeremail, $adminemail, $emailsubject, $emailcontent) {
                    $message->to([$customeremail, $adminemail])
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });

                //@mail($adminemail, $emailsubject, $emailcontent, $headers);
            }
        }

        $data = response()->json(['response' => 'success', 'message' => 'Your order has been completed', 'orderid' => $orderid]);
        return $data;
    }
    public function cancelorder(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;

        $order = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($order) {
            if ($order->order_type == 2) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => '1'));
            }
            $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();

            $logo = url('/') . '/front/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $itemdetails = '<table style="width:100%;" border="0" cellpadding="5" cellspacing="0">';
            $itemdetails .= '<tr><th width="40%" style="text-align:left; border:1px solid #dee2e6;">Item</th><th width="15%" style="text-align:center; border:1px solid #dee2e6;">Quantity</th><th width="25%" style="text-align:right; border:1px solid #dee2e6;">Price</th><th width="20%" style="text-align:right; border:1px solid #dee2e6;">Total</th></tr>';

            $orderid = $order->order_id;
            if (strlen($orderid) == 3) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '0' . $orderid;
            } elseif (strlen($orderid) == 2) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '00' . $orderid;
            } elseif (strlen($orderid) == 1) {
                $orderid = date('Ymd', strtotime($order->date_entered)) . '000' . $orderid;
            } else {
                $orderid = date('Ymd', strtotime($order->date_entered)) . $orderid;
            }

            $tax = '';
            $taxes = Country::where('countrycode', '=', $order->ship_country)->first();
            if ($taxes) {
                $tax = $taxes->taxtitle . ' - ' . $taxes->taxpercentage;
            }

            if ($orderdetails) {
                foreach ($orderdetails as $orderdetail) {
                    $itemdetails .= '<tr><td style="border:1px solid #dee2e6;">' . $orderdetail->prod_name;
                    if ($orderdetail->prod_option != '') {
                        $itemdetails .= '<span style="color: #6c757d !important">Option: ' . $orderdetail->prod_option . '</span>';
                    }
                    $itemdetails .= '</td><td style="text-align:center; border:1px solid #dee2e6;">' . $orderdetail->prod_quantity . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . $orderdetail->prod_unit_price . '</td>';
                    $itemdetails .= '<td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format(($orderdetail->prod_quantity * $orderdetail->prod_unit_price), 2) . '</td></tr>';
                }
            }

            $itemdetails .= '<tr><td colspan="4" style="border:1px solid #dee2e6;">&nbsp;</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Sub Total</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->payable_amount - ($order->shipping_cost + $order->packaging_fee + $order->tax_collected), 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Tax (' . $tax . '%)</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->tax_collected, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Shipping</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->shipping_cost, 2) . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Packaging Fee</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->packaging_fee, 2) . '</td></tr>';
            if ($order->discount_amount != '0.00') {
                $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;">Discount</td><td style="text-align:right; border:1px solid #dee2e6;">S$' . number_format($order->discount_amount, 2) . '</td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2" style="border:1px solid #dee2e6;">&nbsp;</td><td style="border:1px solid #dee2e6;"><b>Grand Total</b></td><td style="text-align:right; border:1px solid #dee2e6;"><b>S$' . number_format($order->payable_amount, 2) . '</b></td></tr>';

            $emailsubject = $emailcontent = '';

            $companyname = $adminemail = $ccemail = $customername = $customeremail = $companydetails = $statusdetails = '';
            $shippinginfo = $billinginfo = '';
            $shipmethod = '';

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;

                $companydetails .= '<table><tr><td>' . nl2br($setting->company_address) . '</td></tr>';
                $companydetails .= '<tr><td>Fax: ' . $setting->company_fax . '</td></tr>';
                $companydetails .= '<tr><td>GST No: ' . $setting->GST_res_no . '</td></tr></table>';

                $statusdetails .= '<table><tr><td style="vertical-align:top">Date: ' . date("d/m/Y", strtotime($order->date_entered)) . '</td></tr>';

                if ($order->order_status == 0) {
                    $statusdetails .= '<tr><td>Status: Payment Pending</td></tr>';
                } elseif ($order->order_status == 1) {
                    $statusdetails .= '<tr><td>Status: Paid, Shipping Pending</td></tr>';
                }

                $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                if ($shipping) {
                    $statusdetails .= '<tr><td>Delivery Method: ' . $shipping->EnName . '</td></tr>';
                    $shipmethod = $shipping->EnName;
                }

                if ($order->if_items_unavailabel == 1) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Call Me</td></tr>';
                } elseif ($order->if_items_unavailabel == 2) {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Do Not Replace</td></tr>';
                } else {
                    $statusdetails .= '<tr><td>If item(s) unavailable: Replace</td></tr>';
                }

                if (stripos($shipmethod, 'Self Collect') !== false) {
                    $shippinginfo .= '<table><tr><td>' . $shipmethod . '</td></tr></table>';
                } else {
                    $shippinginfo .= '<table><tr><td>' . $order->ship_fname . ' ' . $order->ship_lname . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_ads1 . '</td></tr>';
                    if ($order->ship_ads2) {
                        $shippinginfo .= '<tr><td>' . $order->ship_ads2 . '</td></tr>';
                    }
                    $shippinginfo .= '<tr><td>' . $order->ship_city . '</td></tr>';
                    $shippinginfo .= '<tr><td>' . $order->ship_state . ' - ' . $order->ship_zip . '</td></tr>';
                    $shippinginfo .= '<tr><td>Email: ' . $order->ship_email . '</td></tr>';
                    $shippinginfo .= '<tr><td>Mobile: ' . $order->ship_mobile . '</td></tr>';
                    $shippinginfo .= '</table>';
                }
                $billinginfo .= '<table><tr><td>' . $order->bill_fname . ' ' . $order->bill_lname . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_ads1 . '</td></tr>';
                if ($order->bill_ads2) {
                    $billinginfo .= '<tr><td>' . $order->bill_ads2 . '</td></tr>';
                }
                $billinginfo .= '<tr><td>' . $order->bill_city . '</td></tr>';
                $billinginfo .= '<tr><td>' . $order->bill_state . ' - ' . $order->bill_zip . '</td></tr>';
                $billinginfo .= '<tr><td>Email: ' . $order->bill_email . '</td></tr>';
                $billinginfo .= '<tr><td>Mobile: ' . $order->bill_mobile . '</td></tr>';
                $billinginfo .= '</table>';

                $emailtemplate = EmailTemplate::where('template_type', '=', '2')->where('status', '=', '1')->first();
                if ($emailtemplate) {
                    $emailsubject = $emailtemplate->subject;
                    $emailcontent = $emailtemplate->content;
                }

                $customer = Customer::where('cust_id', '=', $order->user_id)->first();
                if ($customer) {
                    $customername = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                    $customeremail = $customer->cust_email;
                }

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{status}', 'payment not successful', $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{customername}', $customername, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', $companydetails, $emailcontent);
                $emailcontent = str_replace('{statusdetails}', $statusdetails, $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billinginfo, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shippinginfo, $emailcontent);
                $emailcontent = str_replace('{paymentmethod}', $order->pay_method, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                //@mail($customeremail, $emailsubject, $emailcontent, $headers);

                //@mail($adminemail, $emailsubject, $emailcontent, $headers);
                Mail::send([], [], function ($message) use ($customeremail, $adminemail, $emailsubject, $emailcontent) {
                    $message->to([$customeremail, $adminemail])
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }
        }

        $data = response()->json(['response' => 'failed', 'message' => 'Your Payment Transaction has been cancelled', 'orderid' => $orderid]);
        return $data;

    }

    public function updateorder(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        $transactionid = $request->transactionid;
        $orderstatus = $request->orderstatus;
        OrderMaster::where('order_id', '=', $orderid)->update(array('trans_id' => $transactionid, 'order_status' => $orderstatus));
        $data = response()->json(['response' => 'success', 'message' => 'Order Update', 'orderid' => $orderid]);
        return $data;
    }

    public function getShippingPackagePrice(Request $request)
    {

        $data = [];
        $packagingprice = $shippingprice = $subtotal = $deliverycost = $packingfee = $grandtotal = 0;
        $orderinfo = $request->orderinfo;
        $gstText = 'GST(7%)';

        if ($orderinfo) {
            $orderinfo = $orderinfo[0];
            $country = $orderinfo['country'];

            $cartdetails = DB::table('cart_details')->where('user_id', $orderinfo['customer_id'])->get();
            $price = new \App\Models\Price();
            foreach ($cartdetails as $cartItem) {
                $productprice = $price->getDiscountPrice($cartItem->prod_id);
                $total = $productprice * $cartItem->prod_quantity;
                $productDeails = Product::find($cartItem->prod_id);
                $prodOptionDetails = ProductOptions::where([['Prod', '=', $cartItem->prod_id], ['Id', '=', $cartItem->prod_option_id]])->first();

                if ($cartItem->prod_option_id > 0 && !empty($prodOptionDetails)) {
                    $optionprice = $price->getOptionPrice($cartItem->prod_id, $cartItem->prod_option_id) * $cartItem->prod_quantity;
                    $total += $optionprice;
                    $cartItem->shippingbox = $prodOptionDetails->ShippingBox;
                    $cartItem->productWeight = $prodOptionDetails->Weight;
                } else {
                    $cartItem->shippingbox = $productDeails->ShippingBox;
                    $cartItem->productWeight = $productDeails->Weight;
                }
                $subtotal += $total;
            }

            $cart = new \App\Models\Cart();

            //Exclude default tax amount
            $countryDetails = Country::where('countrycode', $country)->first();
            $taxAmount = round((($subtotal * $countryDetails->taxpercentage) / (100 + $countryDetails->taxpercentage)), 2);
            $subtotal = round($subtotal - $taxAmount, 2);

            $gst = $cart->getGST($subtotal, $country);
            $taxDetails = $this->cartServices->getTax($subtotal, $country);
            $gstText = $taxDetails['taxLabel'];
            $gst = $taxDetails['taxTotal'];

            $taxes = $cart->getGST($subtotal, $country);

            /*if ($country != '') {
            $taxvals = @explode("|", $taxes);
            $gstText = $taxvals[0];
            $gst = $taxvals[1];
            } else {
            $gst = $taxes;
            }*/

            $grandtotal = $cart->getGrandTotal($subtotal, $gst);
            $deliverymethod = $orderinfo['shipmethod'];

            $couponcode = $orderinfo['couponcode'];

            if ($country != 'SG') {
                $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
                $packingfee = $settings->min_package_fee;
            }

            $boxfees = 0;
            $totalweight = $deliverycosttotalitem = $totaldeliverycost = 0;

            foreach ($cartdetails as $item) {

                $shippingbox = $item->shippingbox;
                if ($shippingbox == "L" || $shippingbox == "XL" || $shippingbox == "XXL" || $shippingbox == "P") {
                    $deliverycostperitem = $cart->shippingCost($country, $deliverymethod, $shippingbox, $item->prod_quantity, $subtotal, $gst, $item->productWeight);

                    $deliverycosttotalitem += $deliverycostperitem * $item->prod_quantity;
                } else {
                    $product_weight = $item->productWeight * $item->prod_quantity;
                    $totalweight += $product_weight;
                }
                $quantity = 1; //$item->prod_quantity;
                $boxfees += $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);

            }

            if ($totalweight > 0) {
                #$boxfees = $cart->getPackagingFee($country, $totalweight, $subtotal, $gst, $deliverymethod, $shippingbox, $quantity);
                $totaldeliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst, $totalweight);
            }

            $deliverycost = $deliverycosttotalitem + $totaldeliverycost;
            $packingfee = number_format(($packingfee + $boxfees), 2);

            #$deliverycost = $cart->shippingCost($country, $deliverymethod, $shippingbox, $quantity, $subtotal, $gst);
            #$packingfee = number_format(($packingfee + $boxfees), 2);

            $discounttype = $disamount = 0;
            $discounttext = '';
            if (isset($orderinfo['couponcode']) && !empty($orderinfo['couponcode'])) {
                $coupondata = Couponcode::where('coupon_code', '=', trim($orderinfo['couponcode']))->where('status', '=', '1')->first();
                if ($coupondata) {
                    $discounttext = 'Coupon discount(' . ($coupondata->discount_type == 1 ? $coupondata->discount . '%)' : '$' . $coupondata->discount . ')');
                    $discounttype = $coupondata->discount_type;
                    $disamount = $coupondata->discount;
                }
            }

            $discount = $cart->getDiscount($subtotal, $gst, $deliverycost, $packingfee, $discounttype, $disamount);
            $grandtotal = $cart->getGrandTotal($subtotal, $gst, $deliverycost, $packingfee, $discount);

        }

        return response()->json(['response' => 'success', 'message' => 'Shipping & Packaing Price', 'gstText' => $gstText, 'gst' => $gst, 'packagingprice' => $packingfee, 'shippingprice' => $deliverycost,
            'grandtotal' => $grandtotal, 'deliverycosttotalitem' => $deliverycosttotalitem, 'totaldeliverycost' => $totaldeliverycost, 'totalweight' => $totalweight, 'subtotal_text' => 'w/o ' . $taxDetails['taxLabelOnly'], 'subtotal' => $subtotal, 'discount' => $discount, 'discount_text' => $discounttext]);
    }

    public function getshipandpackingprice(Request $request)
    {
        $data = [];
        $packagingprice = $shippingprice = $deliverycost = $packingfee = $grandtotal = 0;
        $orderinfo = $request->orderinfo;

        if ($orderinfo) {
            $orderinfo = $orderinfo[0];
            $subtotal = $orderinfo['subtotal'];
            $gst = $orderinfo['tax_collected'];
            $deliverymethod = $orderinfo['shipmethod'];
            $country = $orderinfo['country'];
            $couponcode = $orderinfo['couponcode'];

            $cartitems = $orderinfo['products'];

            $settings = PaymentSettings::where('Id', '=', '1')->select('min_package_fee')->first();
            if ($country != 'SG') {
                $packingfee = $settings->min_package_fee;
            }

            $boxfees = $totalweight = 0;

            $cart = new Cart();

            foreach ($cartitems as $cartitem) {
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

        return response()->json(['response' => 'success', 'message' => 'Shipping & Packaing Price', 'packagingprice' => $packingfee, 'shippingprice' => $deliverycost, 'grandtotal' => $grandtotal]);
    }

    public function discountcoupon(Request $request)
    {
        $data = [];

        $orderinfo = $request->orderinfo;
        $usage = 0;
        $countryid = $allowapply = 0;
        $chkcategory = $chkbrand = 1;
        $country = $response = '';
        if ($orderinfo) {
            $orderinfo = $orderinfo[0];
            $customerid = $orderinfo['customer_id'];
            $couponcode = $orderinfo['couponcode'];
            $cartitems = $orderinfo['products'];

            $cust = Customer::where('cust_id', '=', $customerid)->first();

            $coupondata = Couponcode::where('coupon_code', '=', $couponcode)->where('status', '=', '1')->first();

            if (!empty($coupondata)) {
                $date = date('Y-m-d');

                if (strtotime($coupondata->validity) >= strtotime($date)) {

                    if ($cartitems) {
                        if ($coupondata->category_id > 0) {
                            foreach ($cartitems as $cartitem) {
                                $chkproduct = Product::where('Id', '=', $cartitem['id'])->where('Types', '=', $coupondata->category_id)->where('ProdStatus', '=', '1')->first();
                                if (!$chkproduct) {
                                    $chkcategory = 0;
                                    break;
                                }
                            }
                        }
                        if ($coupondata->brand_id > 0) {
                            foreach ($cartitems as $cartitem) {
                                $chkproduct = Product::where('Id', '=', $cartitem[id])->where('Brand', '=', $coupondata->brand_id)->where('ProdStatus', '=', '1')->first();
                                if (!$chkproduct) {
                                    $chkbrand = 0;
                                    break;
                                }
                            }
                        }

                        if ($coupondata->customer_id > 0) {
                            if ($customer == $coupondata->customer_id) {
                                if ($coupondata->customer_type == 1) {
                                    $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
                                } elseif ($coupondata->customer_type == 2) {
                                    $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
                                }
                                if ($coupondata->nooftimes > $usage) {
                                    $allowapply = 1;
                                } else {
                                    $response = 'Coupon Code Usage Limit Exceeded!';
                                }
                            } else {
                                $allowapply = 0;
                                $response = 'Invalid Coupon Code!';
                            }
                        } else {
                            if ($coupondata->customer_type == 3) {
                                if ($cust->group_admin == 1) {
                                    $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
                                    if ($coupondata->nooftimes > $usage) {
                                        $allowapply = 1;
                                    } else {
                                        $response = 'Coupon Code Usage Limit Exceeded!';
                                    }
                                } else {
                                    $allowapply = 0;
                                }
                            } else {
                                if ($coupondata->customer_type == 1) {
                                    $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->where('customer_id', '=', $customerid)->count();
                                } elseif ($coupondata->customer_type == 2) {
                                    $usage = CouponCodeUsage::where('coupon_id', '=', $coupondata->id)->count();
                                }
                                if ($coupondata->nooftimes > $usage) {
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

            if ($allowapply == 1 && $chkbrand == 1 && $chkcategory == 1) {
                $discounttext = '';
                if ($coupondata->discount_type == 1) {
                    $discounttext = $coupondata->discount . '%';
                } else {
                    $discounttext = '$' . $coupondata->discount;
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

    public function iOSCreateStripePaymentIntend(Request $request)
    {
        $data = ['status' => false];
        $paymentmethod = PaymentMethods::where('id', 3)->orWhere('payment_name', 'LIKE', '%Credit Card')->select(['payment_mode', 'api_signature', 'test_api_signature'])->first();

        if ($paymentmethod) {

            $stripesignature = $paymentmethod->payment_mode == 'live' ? $paymentmethod->api_signature : $paymentmethod->test_api_signature;

            Stripe\Stripe::setApiKey($stripesignature);

            try {

                $shippingAddress = $request->has('shipping') ? $request->shipping[0] : [];

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => ($request['orderinfo'][0]['amount'] * 100),
                    'currency' => 'sgd',
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                    "description" => "Payment from Ios Application and payment method is - " . ($request->has('payment_method') ? $request->payment_method : '') . ".",
                    "metadata" => ["order_id" => $request->has('order_id') ? $request->order_id : ''],
                    "shipping" => [
                        'name' => !empty($shippingAddress) ? $shippingAddress['ship_fname'] . ' ' . $shippingAddress['ship_lname'] : '-',
                        'address' => !empty($shippingAddress) ? [
                            "line1" => $shippingAddress['ship_address1'], "line2" => $shippingAddress['ship_address2'],
                            "city" => $shippingAddress['ship_city'],
                            "state" => $shippingAddress['ship_state'], "postal_code" => $shippingAddress['ship_zip'],
                            "country" => $shippingAddress['ship_country'],
                        ] : [],
                    ],
                ]);

                $data['status'] = true;
                $data['clientSecret'] = $paymentIntent->client_secret;

            } catch (Exception $e) {
                $data['message'] = $e->getMessage();
            }

        } else {
            $data['message'] = 'Payment method is not found';
        }

        return response()->json($data);
    }

    public function createquotation(Request $request)
    {
        $data = [];
        $shipcountryname = $billcountryname = '';
        $orderincid = $orderid = $taxValue = 0;
        $orderinfo = $request->orderinfo;

        if ($orderinfo) {
            $orderinfo = $orderinfo[0];
            $userid = $orderinfo['customer_id'];

            $billinginfo = $orderinfo['billing'][0];
            $shippinginfo = $orderinfo['shipping'][0];
            //$carddetails = $orderinfo['carddetails'][0];

            if ($userid <= 0) {
                $chkcustomer = Customer::where('cust_email', '=', $billinginfo['bill_email'])->select('cust_id')->first();
                if (!$chkcustomer) {
                    Customer::insert(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_email' => $billinginfo['bill_email'], 'cust_address1' => $billinginfo['bill_ads1'], 'cust_address2' => $billinginfo['bill_ads2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile'], 'cust_status' => 0));
                    $cust = Customer::where('cust_id', '>', '0')->orderBy('cust_id', 'desc')->select('cust_id')->first();
                    if ($cust) {
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
            $deliverynote = $orderinfo['deliverynote'];
            $taxtitle = $deliverytype = '';
            $shipdata = ShippingMethods::where('Id', '=', $orderinfo['shipmethod'])->select('EnName')->first();
            if ($shipdata) {
                $deliverytype = $shipdata->EnName;
            }

            $countrydata = Country::where('countrycode', '=', $shippinginfo['ship_country'])->select('countryid', 'taxtitle', 'taxpercentage')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $taxtitle = $countrydata->taxtitle;
                $taxValue = $countrydata->taxpercentage;
            }

            $ordermaster = new OrderMaster;
            $ordermaster['user_id'] = $userid;
            $ordermaster['ship_method'] = $orderinfo['shipmethod'];
            $ordermaster['pay_method'] = $orderinfo['paymethod'];
            $ordermaster['shipping_cost'] = str_replace(',', '', $orderinfo['shippingcost']);
            $ordermaster['packaging_fee'] = str_replace(',', '', $orderinfo['packagingfee']);
            $ordermaster['tax_label'] = trim($taxtitle . ' (' . $taxValue . '%)');
            $ordermaster['tax_percentage'] = $taxValue;
            $ordermaster['tax_collected'] = str_replace(',', '', $orderinfo['tax_collected']);
            $ordermaster['discount_amount'] = str_replace(',', '', $discount);
            $ordermaster['discount_id'] = $couponid;
            $ordermaster['payable_amount'] = str_replace(',', '', $orderinfo['payable_amount']);
            $ordermaster['order_status'] = '0';
            $ordermaster['order_from'] = '1';
            $ordermaster['order_type'] = '2';
            $ordermaster['delivery_instructions'] = $deliverynote;
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
            if ($order) {
                $orderid = $order->order_id;
                $orderincid = $order->order_id;
                $quotationid = $order->order_id;
            }

            $hoolahitems = [];
            $atomeitems = [];
            $cartitems = $orderinfo['products'];

            $itemdetails = '<table style="width:100%; background:#f1f1f142; padding:10px;">';
            $itemdetails .= '<tr><th width="40%">Item</th><th width="15%" style="text-align:center;">Quantity</th><th width="25%" style="text-align:right">Price</th><th width="20%" style="text-align:right">Total</th></tr>';
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';

            foreach ($cartitems as $cart) {
                $orderdetails = new OrderDetails;
                $orderdetails['order_id'] = $orderid;
                $orderdetails['prod_id'] = $cart['id'];
                $orderdetails['prod_name'] = $cart['name'];
                $orderdetails['prod_quantity'] = $cart['quantity'];
                $orderdetails['prod_unit_price'] = str_replace(',', '', $cart['price']);
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
                if ($product->Quantity > $cart['quantity']) {
                    $qty = $product->Quantity - $cart['quantity'];
                    $desc = $product->EnShortDesc;
                    if ($desc == '') {
                        $desc = $cart['name'];
                    }
                    $image = url('/') . '/uploads/product/' . $product->Image;
                }
                Product::where('Id', '=', $cart['id'])->update(array('Quantity' => $qty));

                $productname = $cart['name'];

                $sku = $ean = "";
                if ($cart['code']) {
                    $sku = $cart['code'];
                    $ean = $cart['code'];
                }

                $itemdetails .= '<tr><td>' . $cart['name'] . '</td><td style="text-align:center;">' . $cart['quantity'] . '</td><td style="text-align:right">$' . number_format($cart['price'], 2) . '</td><td style="text-align:right">$' . number_format($cart['total'], 2) . '</td></tr>';

            }

            $countrydata = Country::where('countrycode', '=', $billinginfo['bill_country'])->select('countryid', 'countryname')->first();
            if ($countrydata) {
                $countryid = $countrydata->countryid;
                $billcountryname = $countrydata->countryname;
            }

            $shipcountrydata = Country::where('countrycode', '=', $shippinginfo['ship_country'])->select('countryid', 'countryname')->first();
            if ($shipcountrydata) {
                $shipcountryname = $shipcountrydata->countryname;
            }

            Customer::where('cust_id', '=', $userid)->update(array('cust_firstname' => $billinginfo['bill_fname'], 'cust_lastname' => $billinginfo['bill_lname'], 'cust_address1' => $billinginfo['bill_address1'], 'cust_address2' => $billinginfo['bill_address2'], 'cust_city' => $billinginfo['bill_city'], 'cust_state' => $billinginfo['bill_state'], 'cust_country' => $countryid, 'cust_zip' => $billinginfo['bill_zip'], 'cust_phone' => $billinginfo['bill_mobile']));

            $setting = Settings::where('id', '=', '1')->first();
            if ($setting) {
                $companyname = $setting->company_name;
                $adminemail = $setting->admin_email;
                $ccemail = $setting->cc_email;
            }

            $currency = 'SGD';
            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
            if ($paysettings) {
                $currency = $paysettings->currency_type;
            }

            //Start
            $itemdetails .= '<tr><td colspan="4"><hr></td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Sub Total</td><td style="text-align:right;">$' . $subtotal . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>' . $taxtitle . '</td><td style="text-align:right;">$' . $gst . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Shipping (' . $deliverytype . ')</td><td style="text-align:right;">$' . $deliverycost . '</td></tr>';
            $itemdetails .= '<tr><td colspan="2"></td><td>Packing Fee</td><td style="text-align:right;">$' . $packingfee . '</td></tr>';
            if ($discounttext != '' && $discount != 0) {
                $itemdetails .= '<tr><td colspan="2"></td><td>Discount(' . $discounttext . ')</td><td style="text-align:right;">$' . $discount . '</td></tr>';
            }
            $itemdetails .= '<tr><td colspan="2"></td><td><b>Grand Total</b></td><td style="text-align:right;"><b>$' . $grandtotal . '</b></td></tr>';
            $itemdetails .= '</table>';

            $billing = '';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_fname'] . ' ' . $billinginfo['bill_lname'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_address1'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_address2'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_city'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_state'] . ' - ' . $billinginfo['bill_zip'] . '</p>';
            $billing .= '<p style="margin:0;">' . $billinginfo['bill_country'] . '</p>';

            $shipping = '';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_fname'] . ' ' . $shippinginfo['ship_lname'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_address1'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_address2'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_city'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_state'] . ' - ' . $shippinginfo['ship_zip'] . '</p>';
            $shipping .= '<p style="margin:0;">' . $shippinginfo['ship_country'] . '</p>';

            $emailtemplate = EmailTemplate::where('template_type', '=', '10')->where('status', '=', '1')->first();
            if ($emailtemplate) {

                if (strlen($orderid) == 3) {
                    $orderid = '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = '000' . $orderid;
                }
                $orderid = 'Q' . date('Ymd') . $orderid;

                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;
                $logo = url('/') . '/front/img/logo.png';
                $logo = '<img src="' . $logo . '">';
                $emailsubject = str_replace('{companyname}', $setting->company_name, $emailsubject);
                $emailsubject = str_replace('{date}', date("d-M-Y"), $emailsubject);
                $emailcontent = str_replace('{companyname}', $setting->company_name, $emailcontent);
                $emailcontent = str_replace('{companyaddress}', nl2br($setting->company_address), $emailcontent);
                $emailcontent = str_replace('{companyfax}', $setting->company_fax, $emailcontent);
                $emailcontent = str_replace('{companygstno}', $setting->GST_res_no, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{email}', $adminemail, $emailcontent);
                $emailcontent = str_replace('{invoiceno}', $orderid, $emailcontent);
                $emailcontent = str_replace('{date}', date("d-M-Y"), $emailcontent);
                $emailcontent = str_replace('{orderstatus}', 'Payment Pending', $emailcontent);
                $emailcontent = str_replace('{billinginfo}', $billing, $emailcontent);
                $emailcontent = str_replace('{shippinginfo}', $shipping, $emailcontent);
                $emailcontent = str_replace('{deliverymethod}', $deliverytype, $emailcontent);
                $emailcontent = str_replace('{orderdetails}', $itemdetails, $emailcontent);

                $custemail = $billinginfo['bill_email'];

                $headers = 'From: ' . $setting->company_name . ' ' . $setting->admin_email . '' . "\r\n";
                $headers .= 'Reply-To: ' . $setting->admin_email . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($custemail, $emailsubject, $emailcontent, $headers);
                Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                    $message->to($custemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });

                //@mail($setting->admin_email, $emailsubject, $emailcontent, $headers);
            }
            //End

            $data = response()->json(['response' => 'success', 'message' => 'Quotation Created', 'orderid' => $orderincid]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Quotation Not Created', 'orderid' => 0]);
        }
        return $data;
    }

    public function makerepayment(Request $request)
    {
        $data = [];
        $orderid = $request->orderid;
        $orders = OrderMaster::where('order_id', '=', $orderid)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
        $paymentmethods = PaymentMethods::where('status', '=', '1')->get();

        $cart = $cartdata = $cartdataMobile = $quotionDetails = [];
        $cartcount = 1;
        $optionprice = 0;
        $prodoption = '';
        $sesid = $request->customerid;
        $ifItemsUnavailabel = [
            '1' => 'Call me',
            '2' => 'Do not replace',
            '3' => 'Replace',
        ];

        if ($orderdetails) {

            $quotionDetails[0]['order_id'] = $orders->order_id;
            $quotionDetails[0]['payable_amount'] = $orders->payable_amount;
            $quotionDetails[0]['paymethod'] = $orders->pay_method;
            $quotionDetails[0]['order_status'] = $this->orderStatus[$orders->order_status] ?? $this->orderStatus['0'];
            $quotionDetails[0]['order_date'] = $orders->date_entered;
            $quotionDetails[0]['deliveryMethod'] = $orders->ship_method;
            $quotionDetails[0]['deliveryNotes'] = $orders->delivery_instructions;
            $quotionDetails[0]['ifItemsUnavailabel'] = $ifItemsUnavailabel[$orders->if_items_unavailabel] ?? '';

            $quotionDetails[0]['billingAddress'][] = [
                'first_name' => $orders->bill_fname,
                'last_name' => $orders->bill_lname,
                'address_line_one' => $orders->bill_ads1,
                'address_line_two' => $orders->bill_ads2,
                'city' => $orders->bill_city,
                'state' => $orders->bill_state,
                'country' => $orders->bill_country,
                'zipCode' => $orders->bill_zip,
                'ipAddress' => $orders->bill_ipaddress,
                'companyName' => $orders->bill_compname,
                'nameOnCard' => $orders->bill_name_oncard,
                'email' => $orders->bill_email,
                'mobile' => $orders->bill_mobile,
                'fax' => $orders->bill_fax,
                'landLine' => $orders->bill_landline,
            ];
            $quotionDetails[0]['shippingAddres'][] = [
                'first_name' => $orders->ship_fname,
                'last_name' => $orders->ship_lname,
                'address_line_one' => $orders->ship_ads1,
                'address_line_two' => $orders->ship_ads2,
                'city' => $orders->ship_city,
                'state' => $orders->ship_state,
                'country' => $orders->ship_country,
                'zipCode' => $orders->ship_zip,
                'ipAddress' => $orders->ship_ipaddress,
                'companyName' => $orders->ship_compname,
                'nameOnCard' => $orders->ship_name_oncard,
                'email' => $orders->ship_email,
                'mobile' => $orders->ship_mobile,
                'fax' => $orders->ship_fax,
                'landLine' => $orders->ship_landline,
            ];

            DB::table('cart_details')->where('user_id', '=', $sesid)->delete();
            foreach ($orderdetails as $orderdetail) {
                $product = Product::where('Id', '=', $orderdetail->prod_id)->first();

                if ($product->Quantity > 0) {
                    $ses_productid = $orderdetail->prod_id;
                    $productid = $orderdetail->prod_id;
                    $ses_productname = $product->EnName;
                    $ses_qty = $orderdetail->prod_quantity;
                    $optionid = $orderdetail->prod_option;

                    //$price = new \App\Models\Price();
                    //$productprice = $price->getPrice($orderdetail->prod_id);

                    if ($orders->order_type == 1) {

                        $price = new \App\Models\Price();
                        $productprice = $price->getDiscountPrice($productid);
                        if ($optionid > 0) {
                            $options = ProductOptions::where('Id', '=', $optionid)->first();
                            $optionprice = $options->Price;
                            $optionprice = $price->getOptionPrice($productid, $optionid);
                            $prodoption = $options->Title;
                            //$optionid = $options->Id;
                        }

                        $productprice = $productprice + $optionprice;

                    } else {
                        $productprice = $orderdetail->prod_unit_price;
                    }

                    /*if($optionid > 0) {
                    $options = ProductOptions::where('Id', '=', $optionid)->first();
                    $optionprice = $options->Price;
                    $prodoption = $options->Title;
                    }*/

                    //$productprice = $productprice + $optionprice;

                    $ses_productprice = $productprice;
                    $ses_total = $ses_qty * $productprice;
                    $cart['productId'] = $ses_productid;
                    $cart['productName'] = $ses_productname;
                    $cart['qty'] = $ses_qty;
                    $cart['price'] = $ses_productprice;
                    $cart['total'] = $ses_total;
                    $cart['image'] = $product->Image;
                    $cart['productoption'] = $prodoption;
                    $cart['option_id'] = $optionid;
                    $cart['weight'] = $product->Weight;
                    $cart['productWeight'] = $product->Weight;
                    $cart['productcode'] = $product->Code;
                    $cart['color'] = $product->Color;
                    $cart['size'] = $product->Size;
                    $cart['shippingbox'] = $product->ShippingBox;
                    $cartdata[$sesid][] = $cart;
                    $cartdataMobile[] = $cart;
                    ++$cartcount;

                    DB::table('cart_details')->insert(array('user_id' => $sesid, 'prod_id' => $productid, 'prod_name' => $ses_productname, 'prod_option' => $prodoption, 'prod_quantity' => $ses_qty, 'prod_unit_price' => $ses_productprice, 'prod_code' => $product->Code, 'Weight' => $product->Weight, 'row_key' => $cartcount, 'prod_option_id' => $optionid));
                }
            }

            Session::put('cartdata', $cartdata);
        }

        $data = response()->json(['response' => 'success', 'message' => 'Shopping Cart', 'subtotal' => (!empty($orders) ? (string) ($orders->payable_amount - $orders->tax_collected) : 0), 'gst' => $orders->tax_collected ?? 0, 'grandtotal' => $orders->payable_amount ?? 0, 'cartcount' => count($cartdataMobile), 'quotionDetails' => $quotionDetails, 'cartdetails' => $cartdataMobile]);
        return $data;
    }
}
