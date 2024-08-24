<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bannerads;
use App\Models\Country;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\FavouriteProducts;
use App\Models\Order;
use App\Models\OrderDeliveryDetails;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderDetails;
use App\Models\OrderMaster;
use App\Models\PageContent;
use App\Models\PaymentSettings;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\UserLoggedDevices;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mail;
use PDF;
use Session;

class CustomerController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return view('public/Customer.register', compact('countries'));
    }
    public function create()
    {
        $countries = Country::all();
        return view('Customer.create', compact('countries'));
    }
    public function store(Request $request)
    {
        $customer = new Customer;
        $customer->cust_firstname = $request->cust_firstname;
        $customer->cust_lastname = $request->cust_lastname;
        $customer->cust_email = $request->cust_email;
        $customer->cust_username = $request->cust_username;
        $customer->cust_password = Hash::make($request->cust_password);
        $customer->cust_address1 = $request->cust_address1;
        $customer->cust_address2 = $request->cust_address2;
        $customer->cust_zip = $request->cust_zip;
        $customer->cust_phone = $request->cust_countrycode . $request->cust_phone;
        $customer->cust_newsletter = $request->cust_newsletter;
        $customer->howyouknow = $request->howyouknow;
        $customer->cust_terms_agreed = $request->cust_terms_agreed;
        $customer->cust_fax = $request->cust_fax;
        $customer->cust_countrycode = $request->cust_countrycode;
        $customer->cust_status = 1;
        $customer->cust_company = $request->cust_company;
        $customer->cust_type = 1;
        $customer->save();
        $customerid = 0;
        $cust = Customer::orderBy('cust_id', 'desc')->select('cust_id')->first();
        if ($cust) {
            $customerid = $cust->cust_id;
        }

        Session::put('customer_id', $customerid);
        Session::put('customer_name', $request->cust_firstname . ' ' . $request->cust_lastname);

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

        /*$emailids = $custemail;

        $data = array( 'replytoemail' => $replyto, 'subject' => $subject, 'content' => $content);

        Mail::send('public/Emails.verify', $data, function ($m) use ($data, $emailids, $companyname, $custname)  {
        $m->from($data['replytoemail'], $companyname);
        $m->replyTo($data['replytoemail'], $companyname);
        //$m->bcc('balamurugan@webneo.in', '');
        $m->to($emailids, $custname);
        $m->subject($data['subject']);
        }); */

        return redirect('/customer/dashboard')->with('success', 'Account Successfully Created!');

    }
    public function edit($id)
    {
        $countries = Country::all();
        $customer = Customer::where('cust_id', '=', $id)->first();
        return view('Customer.edit', compact('customer', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;

        $phone = $request->cust_countrycode . $request->cust_phone;

        Customer::where('cust_id', '=', $id)->update(array('cust_firstname' => $request->cust_firstname, 'cust_lastname' => $request->cust_lastname, 'cust_email' => $request->cust_email, 'cust_username' => $request->cust_username, 'cust_password' => Hash::make($request->cust_password), 'cust_address1' => $request->cust_address1, 'cust_zip' => $request->cust_zip, 'cust_phone' => $phone, 'cust_newsletter' => $request->cust_newsletter, 'cust_status' => $request->cust_status, 'cust_countrycode' => $request->cust_countrycode));

        return redirect('/customer')->with('success', 'Customer Successfully Updated!');
    }

    public function destroy($id)
    {
        Customer::where('cust_id', '=', $id)->delete();
        return redirect('/customer')->with('success', 'Customer Successfully Deleted!');
    }

    public function chkcustomerexist(Request $request)
    {
        $email = $request->email;
        $exist = 0;
        $customer = Customer::where('cust_email', '=', $email)->get();
        if (count($customer) > 0) {
            $exist = 1;
        }
        echo $exist;
    }

    public function show($id)
    {
        $orders = [];
        $customerid = Session::get('customer_id');
        $groupadmin = Session::get('group_admin');
        $customertype = Session::get('customer_type');
        if ($customerid > 0) {
            if ($id == 'dashboard' || $id == 'personalinfo') {
                $customer = Customer::where('cust_id', '=', $customerid)->first();
                return view('public/Customer.dashboard', compact('customer', 'id'));
            } else if ($id == 'address') {
                $customer = Customer::where('cust_id', '=', $customerid)->first();
                $countries = Country::where('country_status', '=', '1')->where('phonecode', '>', '0')->orderBy('countryname', 'ASC')->get();
                return view('public/Customer.address', compact('customer', 'id', 'countries'));
            } else if ($id == 'myorders') {
                $orders = OrderMaster::where('user_id', '=', $customerid)->where('order_status', '>', '0')->orderBy('order_id', 'desc')->paginate(10);
                return view('public/Customer.myorders', compact('id', 'orders'));
            } else if ($id == 'pendingorders') {
                $orders = OrderMaster::where('user_id', '=', $customerid)->where('order_status', '=', '0')->orderBy('order_id', 'desc')->paginate(10);
                return view('public/Customer.pendingorders', compact('id', 'orders'));
            } else if ($id == 'quotations') {
                $groupcustomers[] = $customerid;
                if ($groupadmin == 1) {
                    $groupmembers = Customer::where('cust_type', '=', $customertype)->where('cust_id', '!=', $customerid)->where('cust_status', '=', '1')->select('cust_id')->get();
                    if ($groupmembers) {
                        foreach ($groupmembers as $groupmember) {
                            $groupcustomers[] = $groupmember->cust_id;
                        }
                    }
                }

                $orders = OrderMaster::whereIn('user_id', $groupcustomers)->where('order_type', '=', '2')->orderBy('order_id', 'desc')->paginate(10);
                return view('public/Customer.quotations', compact('id', 'orders', 'groupadmin'));
            } else if ($id == 'changepassword') {
                return view('public/Customer.changepassword', compact('id'));
            } else if ($id == 'favouriteproducts') {
                $productids = $products = [];
                $favproducts = FavouriteProducts::where('cust_id', '=', $customerid)->get();
                if ($favproducts) {
                    foreach ($favproducts as $favproduct) {
                        $productids[] = $favproduct->prod_id;
                    }
                }
                if (is_array($productids) && !empty($productids)) {
                    $products = Product::whereIn('Id', $productids)->where('ProdStatus', '=', '1')->get();
                }
                return view('public/Customer.favouriteproducts', compact('products'));
            }
        } else {
            return redirect('/login');
        }
    }

    /*public function dashboard() {
    $customerid = Session::get('customer_id');
    $customer = Customer::where('cust_id', '=', $customerid)->first();
    return view('public/Customer.dashboard', compact('customer'));
    }*/

    public function login()
    {
        return view('public/Customer.login');
    }

    public function logincheck(Request $request)
    {
        $username = $request->username;
        $customer = Customer::where('cust_username', '=', $username)->orWhere('cust_email', '=', $username)->first();
        if ($customer) {
            $password = Hash::check($request->password, $customer->cust_password);
            if ($password) {
                Session::put('customer_id', $customer->cust_id);
                Session::put('customer_name', $customer->cust_firstname . ' ' . $customer->cust_lastname);
                Session::put('customer_email', $customer->cust_email);
                Session::put('customer_phone', $customer->cust_phone);
                Session::put('group_admin', $customer->group_admin);
                Session::put('customer_type', $customer->cust_type);

                $sesid = Session::get('_token');

                DB::table('cart_details')->where('user_token', '=', $sesid)->update(array('user_id' => $customer->cust_id));

                $cartdetails = DB::table('cart_details')->where('user_id', '=', $customer->cust_id)->get();

                $cart = $cartdata = [];
                $cartcount = 1;

                $prodoption = '';

                //create entry in log device table
                UserLoggedDevices::firstOrCreate([
                    'user_id' => $customer->cust_id,
                    'session_id' => $request->session()->getId(),
                ]);

                if (count($cartdetails) > 0) {
                    foreach ($cartdetails as $orderdetail) {
                        $product = Product::where('Id', '=', $orderdetail->prod_id)->first();
                        $optionprice = 0;
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
                                $ShippingBox = $options->ShippingBox;
                                $Weight = $options->Weight;
                            } else {
                                $ShippingBox = $product->ShippingBox;
                                $Weight = $product->Weight;
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
                            $cart['weight'] = $Weight;
                            $cart['productWeight'] = $Weight;
                            $cart['productcode'] = $product->Code;
                            $cart['color'] = $product->Color;
                            $cart['size'] = $product->Size;
                            $cart['shippingbox'] = $ShippingBox;
                            $cartdata[$sesid][$cartcount] = $cart;
                            ++$cartcount;
                        }
                    }
                    Session::put('cartdata', $cartdata);
                }

                //return redirect('/customer/myorders');
                if (Session::has('returnurl')) {
                    $returnurl = Session::get('returnurl');
                    Session::forget('returnurl');
                    return redirect('/' . $returnurl);
                } else {
                    if (Session::has('cartdata')) {
                        return redirect('/cart');
                    } else {
                        return redirect('/customer/myorders');
                    }
                }
            } else {
                return redirect()->back()->withInput($request->only('username', 'remember'))->with('message', 'Invalid Login Credentials!');
            }
        } else {
            return redirect()->back()->withInput($request->only('username', 'remember'))->with('message', 'Invalid Login Credentials!');
        }
    }

    public function register()
    {
        $countries = Country::all();
        return view('public/Customer.register', compact('countries'));
    }

    public function logout(Request $request)
    {
        //remove entry in log device table
        if(Session::has('customer_id')){
            UserLoggedDevices::where([
                ['user_id', '=', Session::get('customer_id')],
                ['session_id', '=', $request->session()->getId()],
            ])->delete();
        }

        //Session::flash();
        Session::forget('customer_id');
        Session::forget('customer_name');
        Session::forget('cartdata');
        Session::forget('deliverymethod');
        Session::forget('if_unavailable');
        Session::forget('billinginfo');
        Session::forget('paymentmethod');
        Session::forget('discount');
        Session::forget('discounttext');
        Session::forget('couponcode');
        Session::forget('discounttype');
        Session::forget('old_order_id');
        return redirect('/');
    }

    public function updatepersonalinfo(Request $request)
    {
        $custid = Session::get('customer_id');
        $firstname = $request->cust_firstname;
        $lastname = $request->cust_lastname;
        $dobdate = $request->dob_date;
        $dobmonth = $request->dob_month;
        $dobyear = $request->dob_year;
        $dob = '';
        if ($dobdate != '' && $dobmonth != '' && $dobyear != '') {
            $dob = $dobyear . '-' . $dobmonth . '-' . $dobdate;
        }

        Customer::where('cust_id', '=', $custid)->update(array('cust_firstname' => $firstname, 'cust_lastname' => $lastname, 'cust_dob' => $dob));
        return redirect('/customer/dashboard')->with('success', 'Personal Info Successfully Updated!');
    }

    public function updateprofile(Request $request)
    {
        $id = Session::get('customer_id');

        $phone = $request->cust_countrycode . $request->cust_phone;

        Customer::where('cust_id', '=', $id)->update(array('cust_firstname' => $request->cust_firstname, 'cust_lastname' => $request->cust_lastname, 'cust_address1' => $request->cust_address1, 'cust_address2' => $request->cust_address2, 'cust_city' => $request->cust_city, 'cust_state' => $request->cust_state, 'cust_country' => $request->cust_country, 'cust_zip' => $request->cust_zip, 'cust_phone' => $phone, 'cust_company' => $request->cust_company, 'cust_countrycode' => $request->cust_countrycode));

        return redirect('/customer/address')->with('success', 'Customer Details Successfully Updated!');
    }

    public function updatepassword(Request $request)
    {
        $customerid = Session::get('customer_id');
        $customer = Customer::where('cust_id', '=', $customerid)->first();
        if ($customer) {
            $password = Hash::check($request->old_password, $customer->cust_password);
            if ($password) {
                Customer::where('cust_id', '=', $customerid)->update(array('cust_password' => Hash::make($request->new_password)));

                //Update entry in other logged devices
                UserLoggedDevices::where([
                    ['user_id', '=', $customerid],
                    ['session_id', '!=', $request->session()->getId()],
                ])->update(['is_log_off' => 1]);
                
                return redirect('/customer/changepassword')->with('success', 'Password Successfully Changed!');
            } else {
                return redirect()->back()->withInput($request->only('old_password', 'new_password', 'confirm_password'))->with('message', 'Invalid Old Password!');
            }
        } else {
            return redirect('/');
        }
    }

    public function forgotpassword()
    {
        return view('public/Customer.forgotpassword');
    }

    public function sendforgotemail(Request $request)
    {
        $adminemail = $companyname = '';
        $email = $request->cust_email;
        $customer = Customer::where('cust_username', '=', $email)->orWhere('cust_email', '=', $email)->first();
        if ($customer) {
            if ($customer->password_reset_count >= 3) {
                return redirect('/forgotpassword')->with('message', 'You have been tried more than 3 times. Please contact our support team at it@hardwarecity.com.sg');
            } else {
                $password_reset_count = $customer->password_reset_count + 1;

                Customer::where('cust_id', '=', $customer->cust_id)->update(array('password_reset_count' => $password_reset_count));
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

                    #@mail($custemail, $emailsubject, $emailcontent, $headers);

                    Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                        $message->to($custemail)
                            ->subject($emailsubject)
                            ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                            ->setBody($emailcontent, 'text/html');
                    });
                }

                return redirect('/forgotpassword')->with('success', 'Password rest link successully sent to registered email!');
            }
        } else {
            return redirect()->back()->withInput($request->only('cust_email'))->with('message', 'Invalid Username / Email!');
        }
    }

    public function resetpassword($username)
    {
        $username = base64_decode($username);
        $customer = Customer::where('cust_username', '=', $username)->orWhere('cust_email', '=', $username)->first();
        if ($customer) {
            $customerid = $customer->cust_id;
            return view('public/Customer.resetpassword', compact('customerid'));
        } else {
            return redirect('/');
        }
    }

    public function updateresetpassword(Request $request)
    {
        $customerid = $request->id;
        $customer = Customer::where('cust_id', '=', $customerid)->first();
        if ($customer) {
            Customer::where('cust_id', '=', $customerid)->update(array('cust_password' => Hash::make($request->new_password)));
            return redirect('/login')->with('success', 'Password Successfully Changed! Please login and continue!');
        } else {
            return redirect('/');
        }
    }

    public function addtofavorite(Request $request)
    {
        $customerid = 0;
        $productid = $request->prodid;
        if (Session::has('customer_id')) {
            $customerid = Session::get('customer_id');
        }
        if ($customerid > 0) {
            $chkexist = FavouriteProducts::where('cust_id', '=', $customerid)->where('prod_id', '=', $productid)->first();
            if (empty($chkexist)) {
                FavouriteProducts::insert(array('cust_id' => $customerid, 'prod_id' => $productid));
            }
            echo 'Success';
        } else {
            echo 'Failed';
        }
    }

    public function removefavproduct($id)
    {
        $customerid = 0;
        if (Session::has('customer_id')) {
            $customerid = Session::get('customer_id');
        }
        if ($customerid > 0) {
            FavouriteProducts::where('cust_id', '=', $customerid)->where('prod_id', '=', $id)->delete();
            return redirect('/customer/favouriteproducts')->with('success', 'Item successfully removed from your favourite list!');
        } else {
            return redirect('/');
        }
    }

    public function orderdetails($orderid)
    {
        $id = 'myorders';
        if (Session::has('customer_id')) {
            $orders = OrderMaster::where('order_id', '=', $orderid)->first();
            //echo Session::get('customer_id');
            if ($orders->user_id == Session::get('customer_id') || Session::get('customer_id') == 30197) {
                $shipCountryData = Country::where('countrycode', $orders->ship_country)->first();
                $billCountryData = Country::where('countrycode', $orders->bill_country)->first();
                $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
                return view('public/Customer.orderdetails', compact('orders', 'id', 'orderdetails', 'shipCountryData', 'billCountryData'));
            } else {
                $staticpage = PageContent::where('UniqueKey', '=', 'page-not-found')->first();
                $bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'page-not-found')->orderBy('display_order', 'asc')->get();
                return view('public.staticpages', compact('staticpage', 'bannerads'));
            }
        } else {
            return redirect('/login');
        }
    }

    public function quotationdetails($orderid)
    {
        $id = 'quotations';
        $showapprove = $showproceed = $showbtn = 1;
        $customerid = Session::get('customer_id');
        $groupadmin = Session::get('group_admin');
        $customertype = Session::get('customer_type');

        $orders = OrderMaster::where('order_id', '=', $orderid)->first();
        if ($orders->user_id == $customerid && $groupadmin == 0) {

            $chkcusttype = Customer::where('cust_type', '=', $customertype)->where('group_admin', '>', '0')->first();
            if ($chkcusttype) {
                if ($orders->group_admin_approval == 1) {
                    $showapprove = 0;
                    $showproceed = 1;
                } else {
                    $showapprove = 0;
                    $showproceed = 0;
                }
            } else {
                $showapprove = 0;
                $showproceed = 1;
            }
        } elseif ($orders->user_id != $customerid && $groupadmin == 1) {
            if ($orders->group_admin_approval == 0) {
                $showapprove = 1;
                $showproceed = 1;
            }
        } else {
            $showapprove = 0;
            $showproceed = 1;
        }

        $quoteexpiry = 0;
        $settings = PaymentSettings::first();
        if ($settings) {
            $quoteexpiry = $settings->quotation_expiry_day;
        }

        $curdate = date('Y-m-d H:i:s');
        $expiredate = date('Y-m-d H:i:s', strtotime($orders->created_at . '+' . $quoteexpiry . ' days'));

        if (strtotime($expiredate) < strtotime($curdate)) {
            $showbtn = 0;
        } else if (strtotime($orders->created_at) < strtotime('2022-12-31 00:00:01')) {
            $showbtn = 0;
        }

        $shipCountryData = Country::where('countrycode', $orders->ship_country)->first();
        $billCountryData = Country::where('countrycode', $orders->bill_country)->first();

        $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
        return view('public/Customer.quotationdetails', compact('orders', 'id', 'orderdetails', 'showapprove', 'showproceed', 'showbtn', 'expiredate', 'shipCountryData', 'billCountryData'));
    }

    public function approvequotation($orderid)
    {
        $id = 'quotations';
        OrderMaster::where('order_id', '=', $orderid)->update(array('group_admin_approval' => 1, 'approved_dt' => date('Y-m-d H:i:s')));
        return redirect('quotationdetails/' . $orderid)->with('success', 'Quotation Successfully Approved!');
    }

    public function downloadquotation($orderid)
    {
        $id = 'quotations';
        return PDF::loadFile(public_path() . '/customsql.php')->save('/my_stored_file.pdf')->stream('download.pdf');
    }

    public function trackorder($orderid, $trackingno)
    {
        $trackingnumber = $authkey = '';
        $id = 'myorders';
        $orderdate = '';

        if (isset($trackingno)) {
            $trackingnumber = $trackingno;

            $chktracking = OrderDeliveryInfo::where('ship_tracking_number', '=', $trackingnumber)->count();
            $ninja = new Order;
            if ($chktracking > 0) {
                if (Session::has('ninja_authkey')) {
                    $authkey = Session::get('ninja_authkey');
                } else {
                    $authkey = $ninja->getAuthKey();
                }

                if ($authkey) {
                    $ninja->TrackOrder($authkey, $trackingnumber);
                }
            } else {
                return redirect('trackorder/' . $orderid)->with('message', 'Invalid Tracking Number');
            }
        }

        $orders = OrderMaster::where('order_id', '=', $orderid)->select('created_at')->first();
        if ($orders) {
            $orderdate = $orders->created_at;
        }
        return view('public/Customer.trackorder', compact('orderid', 'id', 'orderdate'));
    }

    public function orderdeliveryinfo($orderid)
    {
        $orderdate = '';
        $orders = OrderMaster::where('order_id', '=', $orderid)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $orderid)->get();
        $orderdeliverydetails = OrderDeliveryDetails::where('order_id', '=', $orderid)->get();
        if ($orders) {
            $orderdate = $orders->created_at;
        }
        $id = 'myorders';
        return view('public/Customer.orderdeliveryinfo', compact('orders', 'id', 'orderdetails', 'orderid', 'orderdeliverydetails', 'orderdate'));
    }

    public function encryptpassword()
    {
        //$customers = Customer::where('cust_id', '>', '19099')->get();
        $customers = Customer::where('cust_id', '=', '25096')->get();
        if ($customers) {
            foreach ($customers as $customer) {
                $encpassword = Hash::make($customer->cust_password);
                Customer::where('cust_id', '=', $customer->cust_id)->update(array('cust_password' => $encpassword));
                echo $customer->cust_id . '=' . $customer->cust_email;
                echo '<br>';
            }
        }
    }

}
