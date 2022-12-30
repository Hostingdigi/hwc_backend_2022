<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AccountDeactiveRequest;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\FavouriteProducts;
use App\Models\OrderDeliveryDetails;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\ShippingMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use Session;

class CustomerMobileController extends Controller
{

    /* for Mobile App */

    public function storecustomer(Request $request)
    {
        $customer = new Customer;
        $customer->cust_firstname = $request->cust_firstname;
        $customer->cust_lastname = $request->cust_lastname;
        $customer->cust_email = $request->cust_email;
        $customer->cust_username = $request->cust_email;
        $customer->cust_password = Hash::make($request->cust_password);
        $customer->cust_address1 = $request->cust_address1;
        $customer->cust_address2 = $request->cust_address2;
        $customer->cust_zip = $request->cust_zip;
        $customer->cust_phone = $request->cust_phone;
        $customer->cust_fax = $request->cust_fax;
        $customer->cust_newsletter = $request->cust_newsletter;
        $customer->cust_status = 1;
        $customer->cust_type = 1;
        $customer->cust_company = $request->cust_company;
        $customer->howyouknow = $request->howyouknow;
        $customer->cust_terms_agreed = $request->cust_terms_agreed;
        $customer->cust_countrycode = $request->cust_countrycode;
        $customer->save();

        $data = $customerdata = [];
        $newcustomers = Customer::where('cust_status', '=', '1')->orderBy('cust_id', 'desc')->skip(0)->take(1)->get();
        if ($newcustomers) {
            foreach ($newcustomers as $newcustomer) {
                $customerdata['cust_id'] = $newcustomer->cust_id;
                $customerdata['cust_firstname'] = $newcustomer->cust_firstname;
                $customerdata['cust_lastname'] = $newcustomer->cust_lastname;
                $customerdata['cust_email'] = $newcustomer->cust_email;
                $customerdata['cust_company'] = $newcustomer->cust_company;
                $customerdata['cust_phone'] = $newcustomer->cust_phone;
                $customerdata['cust_address1'] = $newcustomer->cust_address1;
                $customerdata['cust_address2'] = $newcustomer->cust_address2;
                $customerdata['cust_city'] = $newcustomer->cust_city;
                $customerdata['cust_state'] = $newcustomer->cust_state;
                $customerdata['cust_country'] = $newcustomer->cust_country;
                $customerdata['cust_zip'] = $newcustomer->cust_zip;
                $customerdata['cust_fax'] = $newcustomer->cust_fax;
                $customerdata['cust_countrycode'] = $newcustomer->cust_countrycode;
            }

            $settings = Settings::where('id', '=', '1')->first();
            $adminemail = $settings->admin_email;
            $companyname = $settings->company_name;

            $replyto = $adminemail;
            $subject = "Forgot Password | " . $companyname;

            $logo = url('/') . '/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $myaccounturl = url('/') . '/customer/personalinfo';
            $faqurl = url('/') . '/faq';

            $emailsubject = $emailcontent = '';
            $emailtemplate = EmailTemplate::where('template_type', '=', '1')->where('status', '=', '1')->first();

            if ($emailtemplate) {
                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{customername}', $request->cust_firstname . ' ' . $request->cust_lastname, $emailcontent);
                $emailcontent = str_replace('{email}', $request->cust_email, $emailcontent);
                $emailcontent = str_replace('{myaccounturl}', $myaccounturl, $emailcontent);
                $emailcontent = str_replace('{faqurl}', $faqurl, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($request->cust_email, $emailsubject, $emailcontent, $headers);
                $custemail = $request->cust_email;
                Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                    $message->to($custemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }
        }

        //$data = json_encode(['response' => 'success', 'message' => 'Customer Successfully Created!', 'customerdata' => $customerdata]);
        $data = response()->json(['response' => 'success', 'message' => 'Customer Successfully Created!', 'customerdata' => $customerdata]);
        return $data;
    }

    public function chkcustomeravailable(Request $request)
    {
        $email = $request->email;

        $exist = 0;
        $customer = Customer::where('cust_email', '=', $email)->get();
        if (count($customer) > 0) {
            $exist = 1;
        }

        if ($exist == 0) {
            $data = response()->json(['response' => 'success', 'message' => 'Email Available']);
        } else {
            $data = response()->json(['response' => 'failed', 'message' => 'Email Already Exist']);
        }
        return $data;
    }

    public function requestAccountDeactivate(Request $request)
    {
        $data = [
            'response' => 'success', 'message' => 'Successfully request sent, check your inbox.',
        ];
        $user = Customer::where([['cust_id', '=', $request->customerid], ['cust_status', '=', 1]])->first();

        if ($user) {

            AccountDeactiveRequest::where([['user_id', '=', $request->customerid], ['status', '=', '1']])->update(['status' => '0']);

            $otp = random_int(100000, 999999);
            AccountDeactiveRequest::create([
                'user_id' => $request->customerid,
                'date_time' => date('Y-m-d H:i:s'),
                'otp_number' => $otp,
                'description' => $request->has('description') ? trim($request->description) : '',
            ]);

            $uemail = $user->cust_email;
            $html = '<p>Hi ' . trim($user->cust_firstname . ' ' . $user->cust_lastname) . ',</p>';
            $html .= '<p>OTP is <b>' . $otp . '</b>.</p>';

            Mail::html($html, function ($message) use ($uemail) {
                $message->to($uemail)->subject('OTP for account deactivation')
                    ->from(env('MAIL_USERNAME'), env('APP_NAME'));
            });

        } else {

            $data = [
                'response' => 'failed', 'message' => 'Invalid User.',
            ];
        }

        return response()->json($data);
    }

    public function verifyAccountDeactivate(Request $request)
    {
        $data = [
            'response' => 'success', 'message' => 'Successfully account has been deactivated',
        ];
        $user = Customer::where([['cust_id', '=', $request->customerid], ['cust_status', '=', 1]])->first();

        if ($user) {

            $otpVerify = AccountDeactiveRequest::where([['user_id', '=', $request->customerid], ['otp_number', '=', $request->otp], ['status', '=', '1']])->first();

            if ($otpVerify) {
                Customer::where('cust_id', $user->cust_id)->update(['cust_status' => 0]);
                AccountDeactiveRequest::where([['user_id', '=', $user->cust_id], ['status', '=', '1']])->update(['status' => '2']);

                $uemail = $user->cust_email;
                $html = '<p>Hi ' . trim($user->cust_firstname . ' ' . $user->cust_lastname) . '</p>';
                $html .= '<p>Your account has been deactivated.</p>';

                Mail::html($html, function ($message) use ($uemail) {
                    $message->to($uemail)->subject('Account Deactivated')
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'));
                });

            } else {
                $data = [
                    'response' => 'failed', 'message' => 'Invalid OTP.',
                ];
            }

            //$user->cust_email;
        } else {

            $data = [
                'response' => 'failed', 'message' => 'Invalid User.',
            ];
        }

        return response()->json($data);
    }

    public function customerlogin(Request $request)
    {
        $data = $customerdata = [];
        $username = $request->username;
        $customer = Customer::whereRaw("(cust_username = '" . $username . "' OR cust_email = '" . $username . "')")
            ->where('cust_status', 1)->first();
        if ($customer) {
            $password = Hash::check($request->password, $customer->cust_password);
            if ($password) {
                Session::put('customer_id', $customer->cust_id);
                Session::put('customer_name', $customer->cust_firstname . ' ' . $customer->cust_lastname);
                if ($customer) {
                    $customerdata['cust_id'] = $customer->cust_id;
                    $customerdata['cust_firstname'] = $customer->cust_firstname;
                    $customerdata['cust_lastname'] = $customer->cust_lastname;
                    $customerdata['cust_email'] = $customer->cust_email;
                    $customerdata['cust_company'] = $customer->cust_company;
                    $customerdata['cust_phone'] = $customer->cust_phone;
                    $customerdata['cust_address1'] = $customer->cust_address1;
                    $customerdata['cust_address2'] = $customer->cust_address2;
                    $customerdata['cust_city'] = $customer->cust_city;
                    $customerdata['cust_state'] = $customer->cust_state;
                    $customerdata['cust_country'] = $customer->cust_country;
                    $customerdata['cust_zip'] = $customer->cust_zip;
                    $customerdata['cust_fax'] = $customer->cust_fax;
                }
                $data = response()->json(['response' => 'success', 'message' => 'successfully login', 'customerdata' => $customerdata]);
            } else {
                $data = response()->json(['response' => 'failed', 'message' => 'Invalid Login Credentials', 'customerdata' => (object) $customerdata]);
            }
        } else {
            $data = response()->json(['response' => 'failed', 'message' => 'Invalid Login Credentials', 'customerdata' => (object) $customerdata]);
        }
        return $data;
    }

    public function customerdata(Request $request)
    {
        $data = $customerdata = [];
        $id = $request->id;
        $customer = Customer::where('cust_id', '=', $id)->first();
        if ($customer) {
            $customerdata['cust_id'] = $customer->cust_id;
            $customerdata['cust_firstname'] = $customer->cust_firstname;
            $customerdata['cust_lastname'] = $customer->cust_lastname;
            $customerdata['cust_email'] = $customer->cust_email;
            $customerdata['cust_company'] = $customer->cust_company;
            $customerdata['cust_phone'] = $customer->cust_phone;
            $customerdata['cust_address1'] = $customer->cust_address1;
            $customerdata['cust_address2'] = $customer->cust_address2;
            $customerdata['cust_city'] = $customer->cust_city;
            $customerdata['cust_state'] = $customer->cust_state;
            $customerdata['cust_country'] = $customer->cust_country;
            $customerdata['cust_zip'] = $customer->cust_zip;
            $customerdata['cust_fax'] = $customer->cust_fax;
            $customerdata['cust_countrycode'] = $customer->cust_countrycode;
            $data = response()->json(['response' => 'success', 'customerdata' => $customerdata]);
        } else {
            $data = response()->json(['response' => 'failed', 'customerdata' => '']);
        }
        return $data;
    }

    public function updatecustomerdata(Request $request)
    {
        $data = $customerdata = [];
        $id = $request->id;
        Customer::where('cust_id', '=', $id)->update(array('cust_firstname' => $request->cust_firstname, 'cust_lastname' => $request->cust_lastname, 'cust_address1' => $request->cust_address1, 'cust_address2' => $request->cust_address2, 'cust_phone' => $request->cust_phone, 'cust_company' => $request->cust_company, 'cust_zip' => $request->cust_zip, 'cust_fax' => $request->cust_fax, 'cust_countrycode' => $request->cust_countrycode));
        $customer = Customer::where('cust_id', '=', $id)->first();
        if ($customer) {
            $customerdata['cust_id'] = $customer->cust_id;
            $customerdata['cust_firstname'] = $customer->cust_firstname;
            $customerdata['cust_lastname'] = $customer->cust_lastname;
            $customerdata['cust_email'] = $customer->cust_email;
            $customerdata['cust_company'] = $customer->cust_company;
            $customerdata['cust_phone'] = $customer->cust_phone;
            $customerdata['cust_address1'] = $customer->cust_address1;
            $customerdata['cust_address2'] = $customer->cust_address2;
            $customerdata['cust_city'] = $customer->cust_city;
            $customerdata['cust_state'] = $customer->cust_state;
            $customerdata['cust_country'] = $customer->cust_country;
            $customerdata['cust_zip'] = $customer->cust_zip;
            $customerdata['cust_fax'] = $customer->cust_fax;
            $customerdata['cust_countrycode'] = $customer->cust_countrycode;
            $data = response()->json(['response' => 'success', 'message' => 'Customer Details Successfully Updated', 'customerdata' => $customerdata]);
        } else {
            $data = response()->json(['response' => 'failed', 'message' => '', 'customerdata' => '']);
        }
        return $data;
    }

    public function addwishlist(Request $request)
    {
        $data = $favitems = [];
        $productids = $products = [];
        $customerid = $request->customerid;
        $productid = $request->productid;
        if ($customerid && $productid) {
            FavouriteProducts::insert(array('cust_id' => $customerid, 'prod_id' => $productid));
            $favproducts = FavouriteProducts::where('cust_id', '=', $customerid)->get();
            if ($favproducts) {
                foreach ($favproducts as $favproduct) {
                    $productids[] = $favproduct->prod_id;
                }
            }
            if (is_array($productids) && !empty($productids)) {
                $products = Product::whereIn('Id', $productids)->where('ProdStatus', '=', '1')->get();
            }

            if ($products) {
                $x = 0;
                foreach ($products as $product) {
                    $favitems[$x]['id'] = $product->Id;
                    $favitems[$x]['urlkey'] = $product->UniqueKey;
                    $favitems[$x]['name'] = $product->EnName;
                    $favitems[$x]['size'] = $product->Size;
                    $favitems[$x]['shortdesc'] = $product->EnShortDesc;
                    $favitems[$x]['color'] = $product->Color;
                    $favitems[$x]['specification'] = $product->Specs;
                    /*$favitems[$x]['standardprice'] = $product->StandardPrice;

                    $price = new \App\Models\Price();
                    $productprice = $price->getPrice($product->Id);
                    $favitems[$x]['price'] = $productprice;*/

                    $price = new \App\Models\Price();

                    $productprice = $product->Price;
                    $actualprice = $price->getGroupPrice($product->Id);
                    $productprice = $price->getDiscountPrice($product->Id);
                    $installmentPrice = $price->getInstallmentPrice($productprice);

                    $favitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                    $favitems[$x]['price'] = number_format($productprice, 2, '.', '');
                    $favitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');

                    $favitems[$x]['qty'] = $product->Quantity;
                    $favitems[$x]['cust_qty'] = $product->cust_qty_per_day;
                    $favitems[$x]['shippingbox'] = $product->ShippingBox;
                    $favitems[$x]['weight'] = $product->Weight;
                    $favitems[$x]['dimension'] = $product->Dimension;
                    $favitems[$x]['categoryid'] = $product->Types;
                    $favitems[$x]['brand'] = $product->Brand;
                    if ($product->Image != '') {
                        $favitems[$x]['image'] = url('/uploads/product') . '/' . $product->Image;
                    } else {
                        $favitems[$x]['image'] = url('/images/noimage.png');
                    }
                    if ($product->LargeImage != '') {
                        $favitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $product->LargeImage;
                    } else {
                        $favitems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                    $favitems[$x]['video'] = $product->Video;
                    $favitems[$x]['description'] = $product->EnInfo;

                    $options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();

                    $favitems[$x]['optionscount'] = count($options);

                    if ($options) {
                        $o = 0;
                        foreach ($options as $option) {
                            $favitems[$x]['options'][$o]['name'] = $option->Title;
                            $favitems[$x]['options'][$o]['price'] = $option->Price;
                            $favitems[$x]['options'][$o]['qty'] = $option->Quantity;
                            $favitems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
                            $favitems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
                            $favitems[$x]['options'][$o]['weight'] = $option->Weight;
                            ++$o;
                        }
                    }
                    ++$x;
                }
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => $favitems]);
            } else {
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => '']);
            }
        }
        return $data;
    }

    public function mywishlist(Request $request)
    {
        $customerid = $request->customerid;
        $data = $favitems = [];
        $productids = $products = [];
        if ($customerid > 0) {
            $favproducts = FavouriteProducts::where('cust_id', '=', $customerid)->get();
            if ($favproducts) {
                foreach ($favproducts as $favproduct) {
                    $productids[] = $favproduct->prod_id;
                }
            }
            if (is_array($productids) && !empty($productids)) {
                $products = Product::whereIn('Id', $productids)->where('ProdStatus', '=', '1')->get();
            }

            if ($products) {
                $x = 0;
                foreach ($products as $product) {
                    $favitems[$x]['id'] = $product->Id;
                    $favitems[$x]['urlkey'] = $product->UniqueKey;
                    $favitems[$x]['name'] = $product->EnName;
                    $favitems[$x]['size'] = $product->Size;
                    $favitems[$x]['shortdesc'] = $product->EnShortDesc;
                    $favitems[$x]['color'] = $product->Color;
                    $favitems[$x]['specification'] = $product->Specs;
                    /*$favitems[$x]['standardprice'] = $product->StandardPrice;

                    $price = new \App\Models\Price();
                    $productprice = $price->getPrice($product->Id);
                    $favitems[$x]['price'] = $productprice;*/

                    $price = new \App\Models\Price();

                    $productprice = $product->Price;
                    $actualprice = $price->getGroupPrice($product->Id);
                    $productprice = $price->getDiscountPrice($product->Id);
                    $installmentPrice = $price->getInstallmentPrice($productprice);

                    $favitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                    $favitems[$x]['price'] = number_format($productprice, 2, '.', '');
                    $favitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');

                    $favitems[$x]['qty'] = $product->Quantity;
                    $favitems[$x]['cust_qty'] = $product->cust_qty_per_day;
                    $favitems[$x]['shippingbox'] = $product->ShippingBox;
                    $favitems[$x]['weight'] = $product->Weight;
                    $favitems[$x]['dimension'] = $product->Dimension;
                    $favitems[$x]['categoryid'] = $product->Types;
                    $favitems[$x]['brand'] = $product->Brand;
                    if ($product->Image != '') {
                        $favitems[$x]['image'] = url('/uploads/product') . '/' . $product->Image;
                    } else {
                        $favitems[$x]['image'] = url('/images/noimage.png');
                    }
                    if ($product->LargeImage != '') {
                        $favitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $product->LargeImage;
                    } else {
                        $favitems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                    $favitems[$x]['video'] = $product->Video;
                    $favitems[$x]['description'] = $product->EnInfo;

                    $options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();

                    $favitems[$x]['optionscount'] = count($options);

                    if ($options) {
                        $o = 0;
                        foreach ($options as $option) {
                            $favitems[$x]['options'][$o]['name'] = $option->Title;
                            $favitems[$x]['options'][$o]['price'] = $option->Price;
                            $favitems[$x]['options'][$o]['qty'] = $option->Quantity;
                            $favitems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
                            $favitems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
                            $favitems[$x]['options'][$o]['weight'] = $option->Weight;
                            ++$o;
                        }
                    }
                    ++$x;
                }
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => $favitems]);
            } else {
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => '']);
            }
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => '']);
        }
        return $data;
    }

    public function removefromwishlist(Request $request)
    {
        $data = $favitems = [];
        $productids = $products = [];
        $customerid = $request->customerid;
        $productid = $request->productid;
        if ($customerid && $productid) {
            FavouriteProducts::where('cust_id', '=', $customerid)->where('prod_id', '=', $productid)->delete();
            $favproducts = FavouriteProducts::where('cust_id', '=', $customerid)->get();
            if ($favproducts) {
                foreach ($favproducts as $favproduct) {
                    $productids[] = $favproduct->prod_id;
                }
            }
            if (is_array($productids) && !empty($productids)) {
                $products = Product::whereIn('Id', $productids)->where('ProdStatus', '=', '1')->get();
            }

            if ($products) {
                $x = 0;
                foreach ($products as $product) {
                    $favitems[$x]['id'] = $product->Id;
                    $favitems[$x]['urlkey'] = $product->UniqueKey;
                    $favitems[$x]['name'] = $product->EnName;
                    $favitems[$x]['size'] = $product->Size;
                    $favitems[$x]['shortdesc'] = $product->EnShortDesc;
                    $favitems[$x]['color'] = $product->Color;
                    $favitems[$x]['specification'] = $product->Specs;
                    /*$favitems[$x]['standardprice'] = $product->StandardPrice;

                    $price = new \App\Models\Price();
                    $productprice = $price->getPrice($product->Id);
                    $favitems[$x]['price'] = $productprice;*/

                    $price = new \App\Models\Price();

                    $productprice = $product->Price;
                    $actualprice = $price->getGroupPrice($product->Id);
                    $productprice = $price->getDiscountPrice($product->Id);
                    $installmentPrice = $price->getInstallmentPrice($productprice);

                    $favitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                    $favitems[$x]['price'] = number_format($productprice, 2, '.', '');
                    $favitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');

                    $favitems[$x]['qty'] = $product->Quantity;
                    $favitems[$x]['cust_qty'] = $product->cust_qty_per_day;
                    $favitems[$x]['shippingbox'] = $product->ShippingBox;
                    $favitems[$x]['weight'] = $product->Weight;
                    $favitems[$x]['dimension'] = $product->Dimension;
                    $favitems[$x]['categoryid'] = $product->Types;
                    $favitems[$x]['brand'] = $product->Brand;
                    if ($product->Image != '') {
                        $favitems[$x]['image'] = url('/uploads/product') . '/' . $product->Image;
                    } else {
                        $favitems[$x]['image'] = url('/images/noimage.png');
                    }
                    if ($product->LargeImage != '') {
                        $favitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $product->LargeImage;
                    } else {
                        $favitems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                    $favitems[$x]['video'] = $product->Video;
                    $favitems[$x]['description'] = $product->EnInfo;

                    $options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();

                    $favitems[$x]['optionscount'] = count($options);

                    if ($options) {
                        $o = 0;
                        foreach ($options as $option) {
                            $favitems[$x]['options'][$o]['name'] = $option->Title;
                            $favitems[$x]['options'][$o]['price'] = $option->Price;
                            $favitems[$x]['options'][$o]['qty'] = $option->Quantity;
                            $favitems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
                            $favitems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
                            $favitems[$x]['options'][$o]['weight'] = $option->Weight;
                            ++$o;
                        }
                    }
                    ++$x;
                }
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => $favitems]);
            } else {
                $data = response()->json(['response' => 'success', 'message' => 'Favourite Products', 'favitems' => '']);
            }
        }
        return $data;
    }

    public function orderlist(Request $request)
    {
        $data = $orderlist = $orderdetails = [];
        $customerid = $request->customerid;
        $filterCondition = "";

        if ($request->has('filter') && !empty($request->filter)) {
            switch ($request->filter) {
                case 'pending':
                    $filterCondition = "order_status=0";
                    break;
                case 'paid':
                    $filterCondition = "order_status!=0";
                    break;
                /*case 'shipped':
                $filterCondition = "(order_status=2 OR order_status=3)";
                break;
                case 'delivered':
                $filterCondition = "order_status=4";
                break;*/
                default:
                    $filterCondition = "";
            }

        }

        $orders = !empty($filterCondition) ? OrderMaster::where('user_id', $customerid)->whereRaw($filterCondition)->orderBy('order_id', 'desc')->get() : [];

        if ($orders) {
            $o = 0;
            foreach ($orders as $order) {

                $orderdeliveryinfo = OrderDeliveryInfo::where('order_id', '=', $order->order_id)->orderBy('delivery_id', "desc")->first();

                $orderlist[$o]['order_id'] = $order->order_id;
                /*$orderlist[$o]['shippingcost'] = $order->shipping_cost;
                $orderlist[$o]['packagingfee'] = $order->packaging_fee;
                $orderlist[$o]['shipmethod'] = '';
                $orderlist[$o]['tax_collected'] = $order->tax_collected;*/
                $orderlist[$o]['payable_amount'] = $order->payable_amount;
                $orderlist[$o]['paymethod'] = $order->pay_method;
                $orderstatus = 'Payment Pending';
                if ($order->order_status == 0) {
                    $orderstatus = 'Payment Pending';
                } elseif ($order->order_status == 1) {
                    $orderstatus = 'Paid, Shipping Pending';
                } elseif ($order->order_status == 2) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 3) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 4) {
                    $orderstatus = 'Delivered';
                } elseif ($order->order_status == 5) {
                    $orderstatus = 'On The Way To You';
                } elseif ($order->order_status == 6) {
                    $orderstatus = 'Partially Delivered';
                } elseif ($order->order_status == 7) {
                    $orderstatus = 'Partially Refunded';
                } elseif ($order->order_status == 8) {
                    $orderstatus = 'Fully Refunded';
                } elseif ($order->order_status == 9) {
                    $orderstatus = 'Ready For Collection';
                }
                $orderlist[$o]['order_status'] = $orderstatus;
                $orderlist[$o]['order_date'] = $order->date_entered;
                $orderlist[$o]['shipping_date'] = $orderdeliveryinfo ? $orderdeliveryinfo->delivered_date : '';
                /*$orderlist[$o]['bill_fname'] = $order->bill_fname;
                $orderlist[$o]['bill_lname'] = $order->bill_lname;
                $orderlist[$o]['bill_address1'] = $order->bill_ads1;
                $orderlist[$o]['bill_address2'] = $order->bill_ads2;
                $orderlist[$o]['bill_city'] = $order->bill_city;
                $orderlist[$o]['bill_state'] = $order->bill_state;
                $orderlist[$o]['bill_country'] = $order->bill_country;
                $orderlist[$o]['bill_zip'] = $order->bill_zip;
                $orderlist[$o]['ship_fname'] = $order->ship_fname;
                $orderlist[$o]['ship_lname'] = $order->ship_lname;
                $orderlist[$o]['ship_address1'] = $order->ship_address1;
                $orderlist[$o]['ship_address2'] = $order->ship_address2;
                $orderlist[$o]['ship_city'] = $order->ship_city;
                $orderlist[$o]['ship_state'] = $order->ship_state;
                $orderlist[$o]['ship_country'] = $order->ship_country;
                $orderlist[$o]['ship_zip'] = $order->ship_zip;*/

                $orderdetails = OrderDetails::where('order_id', '=', $order->order_id)->get();
                if ($orderdetails) {
                    $p = 0;
                    foreach ($orderdetails as $orderdetail) {
                        $orderlist[$o]['products'][$p]['id'] = $orderdetail->prod_id;
                        $orderlist[$o]['products'][$p]['name'] = $orderdetail->prod_name;
                        $orderlist[$o]['products'][$p]['option'] = $orderdetail->prod_option;
                        $orderlist[$o]['products'][$p]['quantity'] = $orderdetail->prod_quantity;
                        $orderlist[$o]['products'][$p]['price'] = $orderdetail->prod_unit_price;
                        $orderlist[$o]['products'][$p]['weight'] = $orderdetail->prod_weight;
                        $orderlist[$o]['products'][$p]['code'] = $orderdetail->prod_code;

                        $prod = Product::where('Id', '=', $orderdetail->prod_id)->select('Image')->first();
                        if ($prod) {
                            if ($prod->Image) {
                                $orderlist[$o]['products'][$p]['image'] = url('/') . '/uploads/product/' . $prod->Image;
                            } else {
                                $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                            }
                        } else {
                            $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                        }

                        ++$p;
                    }
                }
                ++$o;
            }
            $data = response()->json(['response' => 'success', 'message' => 'Order List', 'orderlist' => $orderlist]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Order List', 'orderlist' => '']);
        }
        return $data;
    }

    public function orderinfo(Request $request)
    {
        $orderid = $request->orderid;
        $data = $orderlist = $orderdetails = $shipping = [];
        $orders = OrderMaster::where('order_id', '=', $orderid)->get();
        if ($orders) {
            $o = 0;

            $orderdeliveryinfo = OrderDeliveryInfo::where('order_id', '=', $orderid)->orderBy('delivery_id', "desc")->first();

            foreach ($orders as $order) {
                $orderlist[$o]['order_id'] = $order->order_id;
                $orderlist[$o]['shippingcost'] = $order->shipping_cost;
                $orderlist[$o]['packagingfee'] = $order->packaging_fee;
                $shipmethod = '';
                $shipping = ShippingMethods::where('Id', '=', $order->ship_method)->select('EnName')->first();
                if ($shipping) {
                    $shipmethod = $shipping->EnName;
                }
                $orderlist[$o]['shipmethod'] = $shipmethod;
                $orderlist[$o]['tax_collected'] = $order->tax_collected;
                $orderlist[$o]['payable_amount'] = $order->payable_amount;
                $orderlist[$o]['paymethod'] = $order->pay_method;
                $orderstatus = 'Payment Pending';
                if ($order->order_status == 0) {
                    $orderstatus = 'Payment Pending';
                } elseif ($order->order_status == 1) {
                    $orderstatus = 'Paid, Shipping Pending';
                } elseif ($order->order_status == 2) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 3) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 4) {
                    $orderstatus = 'Delivered';
                } elseif ($order->order_status == 5) {
                    $orderstatus = 'On The Way To You';
                } elseif ($order->order_status == 6) {
                    $orderstatus = 'Partially Delivered';
                } elseif ($order->order_status == 7) {
                    $orderstatus = 'Partially Refunded';
                } elseif ($order->order_status == 8) {
                    $orderstatus = 'Fully Refunded';
                } elseif ($order->order_status == 9) {
                    $orderstatus = 'Ready For Collection';
                }
                $orderlist[$o]['order_status'] = $orderstatus;
                $orderlist[$o]['order_date'] = $order->date_entered;
                $orderlist[$o]['shipping_date'] = $orderdeliveryinfo ? $orderdeliveryinfo->delivered_date : '';
                $orderlist[$o]['company_name'] = empty($order->bill_compname) ? '' : trim($order->bill_compname);
                $orderlist[$o]['delivery_note'] = empty($order->delivery_instructions) ? '' : trim($order->delivery_instructions);
                $orderlist[$o]['bill_fname'] = $order->bill_fname;
                $orderlist[$o]['bill_lname'] = $order->bill_lname;
                $orderlist[$o]['bill_address1'] = $order->bill_ads1;
                $orderlist[$o]['bill_address2'] = $order->bill_ads2;
                $orderlist[$o]['bill_city'] = $order->bill_city;
                $orderlist[$o]['bill_state'] = $order->bill_state;
                $orderlist[$o]['bill_country'] = $order->bill_country;
                $orderlist[$o]['bill_zip'] = $order->bill_zip;
                $orderlist[$o]['ship_fname'] = $order->ship_fname;
                $orderlist[$o]['ship_lname'] = $order->ship_lname;
                $orderlist[$o]['ship_address1'] = $order->ship_ads1;
                $orderlist[$o]['ship_address2'] = $order->ship_ads2;
                $orderlist[$o]['ship_city'] = $order->ship_city;
                $orderlist[$o]['ship_state'] = $order->ship_state;
                $orderlist[$o]['ship_country'] = $order->ship_country;
                $orderlist[$o]['ship_zip'] = $order->ship_zip;

                $orderdetails = OrderDetails::where('order_id', '=', $order->order_id)->get();
                if ($orderdetails) {
                    $p = 0;
                    foreach ($orderdetails as $orderdetail) {
                        $orderlist[$o]['products'][$p]['id'] = $orderdetail->prod_id;
                        $orderlist[$o]['products'][$p]['name'] = $orderdetail->prod_name;
                        $orderlist[$o]['products'][$p]['option'] = $orderdetail->prod_option;
                        $orderlist[$o]['products'][$p]['quantity'] = $orderdetail->prod_quantity;
                        $orderlist[$o]['products'][$p]['price'] = $orderdetail->prod_unit_price;
                        $orderlist[$o]['products'][$p]['weight'] = $orderdetail->prod_weight;
                        $orderlist[$o]['products'][$p]['code'] = $orderdetail->prod_code;

                        $prod = Product::where('Id', '=', $orderdetail->prod_id)->select('Image')->first();
                        if ($prod) {
                            if ($prod->Image) {
                                $orderlist[$o]['products'][$p]['image'] = env('IMG_URL') . '/uploads/product/' . $prod->Image;
                            } else {
                                $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                            }
                        } else {
                            $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                        }

                        ++$p;
                    }
                }

                $orderdeliverydetails = OrderDeliveryDetails::where('order_id', '=', $orderid)->get();
                if ($orderdeliverydetails) {
                    $d = 0;
                    foreach ($orderdeliverydetails as $orderdeliverydetail) {
                        $productname = '';
                        $prod = Product::where('Id', '=', $orderdeliverydetail->prod_id)->select('EnName')->first();
                        if ($prod) {
                            $productname = $prod->EnName;
                        }
                        $orderlist[$o]['deliverydetails'][$d]['productname'] = $productname;
                        $orderlist[$o]['deliverydetails'][$d]['quantity'] = $orderdeliverydetail->quantity;
                        if ($orderdeliverydetail->status == 1) {
                            $orderlist[$o]['deliverydetails'][$d]['status'] = 'Delivered';
                        } elseif ($orderdeliverydetail->status == 2) {
                            $orderlist[$o]['deliverydetails'][$d]['status'] = 'Refunded';
                        }
                        $deliveryinfo = OrderDeliveryInfo::where('delivery_id', '=', $orderdeliverydetail->delivery_info_id)->where('order_id', '=', $orderdeliverydetail->order_id)->select('next_delivery_date', 'shipping_by', 'ship_tracking_number')->first();
                        $nextdeliverydate = $shippingby = $trackingnumber = '';
                        if ($deliveryinfo) {
                            $orderlist[$o]['deliverydetails'][$d]['nextdeliverydate'] = $deliveryinfo->next_delivery_date;
                            $orderlist[$o]['deliverydetails'][$d]['shippingby'] = $deliveryinfo->shipping_by;
                            $orderlist[$o]['deliverydetails'][$d]['trackingnumber'] = $deliveryinfo->ship_tracking_number;
                        } else {
                            $orderlist[$o]['deliverydetails'][$d]['nextdeliverydate'] = '';
                            $orderlist[$o]['deliverydetails'][$d]['shippingby'] = '';
                            $orderlist[$o]['deliverydetails'][$d]['trackingnumber'] = '';
                        }
                        ++$d;
                    }
                }
                ++$o;
            }
            $data = response()->json(['response' => 'success', 'message' => 'Order Info', 'orderinfo' => $orderlist]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Order Info', 'orderinfo' => '']);
        }
        return $data;
    }

    public function customerforgotpassword(Request $request)
    {

        $data = [];
        $email = $request->email;
        $customer = Customer::where('cust_username', '=', $email)->orWhere('cust_email', '=', $email)->first();
        if ($customer) {
            $custemail = $customer->cust_email;
            $custname = $customer->cust_firstname . ' ' . $customer->cust_lastname;
            $settings = Settings::where('id', '=', '1')->first();
            $adminemail = $settings->admin_email;
            $companyname = $settings->company_name;

            $replyto = $adminemail;

            $url = url('/') . '/resetpassword/' . base64_encode($custemail);

            $reseturl = '<a href="' . $url . '">Reset Password</a>';

            $logo = url('/') . '/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $emailsubject = $emailcontent = '';
            $emailtemplate = EmailTemplate::where('template_type', '=', '6')->where('status', '=', '1')->first();
            if ($emailtemplate) {
                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{customername}', $custname, $emailcontent);
                $emailcontent = str_replace('{resetpasswordlink}', $reseturl, $emailcontent);

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
            $data = response()->json(['response' => 'success', 'message' => 'Reset password Link sent']);
        } else {
            $data = response()->json(['response' => 'failed', 'message' => 'Invalid Email']);
        }
        return $data;
    }

    public function updatecustomerpassword(Request $request)
    {
        $customerid = $request->id;
        $customer = Customer::where('cust_id', '=', $customerid)->first();
        if ($customer) {
            Customer::where('cust_id', '=', $customerid)->update(array('cust_password' => Hash::make($request->new_password)));
            $data = response()->json(['response' => 'success', 'message' => 'Password updated']);
        } else {
            $data = response()->json(['response' => 'failed', 'message' => '']);
        }
        return $data;
    }

    public function changecustomerpassword(Request $request)
    {
        $customerid = $request->customerid;
        $customer = Customer::where('cust_id', '=', $customerid)->first();
        if ($customer) {
            $password = Hash::check($request->old_password, $customer->cust_password);
            if ($password) {
                Customer::where('cust_id', '=', $customerid)->update(array('cust_password' => Hash::make($request->new_password)));
                $data = response()->json(['response' => 'success', 'message' => 'Password Changed']);
            } else {
                $data = response()->json(['response' => 'failed', 'message' => 'Invalid Old Password']);
            }
        } else {
            $data = response()->json(['response' => 'failed', 'message' => 'Invalid']);
        }
        return $data;
    }

    public function quotationlist(Request $request)
    {
        $data = $orderlist = $orderdetails = [];
        $customerid = $request->customerid;
        $orders = OrderMaster::where('user_id', '=', $customerid)->where('order_type', '=', '2')->orderBy('order_id', 'desc')->get();

        if ($orders) {
            $o = 0;
            foreach ($orders as $order) {
                $orderlist[$o]['order_id'] = $order->order_id;
                /*$orderlist[$o]['shippingcost'] = $order->shipping_cost;
                $orderlist[$o]['packagingfee'] = $order->packaging_fee;
                $orderlist[$o]['shipmethod'] = '';
                $orderlist[$o]['tax_collected'] = $order->tax_collected;*/
                $orderlist[$o]['payable_amount'] = $order->payable_amount;
                $orderlist[$o]['paymethod'] = $order->pay_method;
                $orderstatus = 'Payment Pending';
                if ($order->order_status == 0) {
                    $orderstatus = 'Payment Pending';
                } elseif ($order->order_status == 1) {
                    $orderstatus = 'Paid, Shipping Pending';
                } elseif ($order->order_status == 2) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 3) {
                    $orderstatus = 'Shipped';
                } elseif ($order->order_status == 4) {
                    $orderstatus = 'Delivered';
                } elseif ($order->order_status == 5) {
                    $orderstatus = 'On The Way To You';
                } elseif ($order->order_status == 6) {
                    $orderstatus = 'Partially Delivered';
                } elseif ($order->order_status == 7) {
                    $orderstatus = 'Partially Refunded';
                } elseif ($order->order_status == 8) {
                    $orderstatus = 'Fully Refunded';
                } elseif ($order->order_status == 9) {
                    $orderstatus = 'Ready For Collection';
                }
                $orderlist[$o]['order_status'] = $orderstatus;
                $orderlist[$o]['order_date'] = $order->date_entered;
                /*$orderlist[$o]['bill_fname'] = $order->bill_fname;
                $orderlist[$o]['bill_lname'] = $order->bill_lname;
                $orderlist[$o]['bill_address1'] = $order->bill_ads1;
                $orderlist[$o]['bill_address2'] = $order->bill_ads2;
                $orderlist[$o]['bill_city'] = $order->bill_city;
                $orderlist[$o]['bill_state'] = $order->bill_state;
                $orderlist[$o]['bill_country'] = $order->bill_country;
                $orderlist[$o]['bill_zip'] = $order->bill_zip;
                $orderlist[$o]['ship_fname'] = $order->ship_fname;
                $orderlist[$o]['ship_lname'] = $order->ship_lname;
                $orderlist[$o]['ship_address1'] = $order->ship_address1;
                $orderlist[$o]['ship_address2'] = $order->ship_address2;
                $orderlist[$o]['ship_city'] = $order->ship_city;
                $orderlist[$o]['ship_state'] = $order->ship_state;
                $orderlist[$o]['ship_country'] = $order->ship_country;
                $orderlist[$o]['ship_zip'] = $order->ship_zip;*/

                $orderdetails = OrderDetails::where('order_id', '=', $order->order_id)->get();
                if ($orderdetails) {
                    $p = 0;
                    foreach ($orderdetails as $orderdetail) {
                        $orderlist[$o]['products'][$p]['id'] = $orderdetail->prod_id;
                        $orderlist[$o]['products'][$p]['name'] = $orderdetail->prod_name;
                        $orderlist[$o]['products'][$p]['option'] = $orderdetail->prod_option;
                        $orderlist[$o]['products'][$p]['quantity'] = $orderdetail->prod_quantity;
                        $orderlist[$o]['products'][$p]['price'] = $orderdetail->prod_unit_price;
                        $orderlist[$o]['products'][$p]['weight'] = $orderdetail->prod_weight;
                        $orderlist[$o]['products'][$p]['code'] = $orderdetail->prod_code;

                        $prod = Product::where('Id', '=', $orderdetail->prod_id)->select('Image')->first();
                        if ($prod) {
                            if ($prod->Image) {
                                $orderlist[$o]['products'][$p]['image'] = url('/') . '/uploads/product/' . $prod->Image;
                            } else {
                                $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                            }
                        } else {
                            $orderlist[$o]['products'][$p]['image'] = url('/') . '/images/noimage.png';
                        }

                        ++$p;
                    }
                }
                ++$o;
            }
            $data = response()->json(['response' => 'success', 'message' => 'Quotation List', 'quotionlist' => $orderlist]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Quotation List', 'quotionlist' => '']);
        }
        return $data;
    }
}
