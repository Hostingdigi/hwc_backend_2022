<?php

namespace App\Http\Controllers;

use App\Models\Bannerads;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\FavouriteProducts;
use App\Models\Menu;
use App\Models\PageContent;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\Subscriber;
use App\Models as HWCModel;
use Illuminate\Http\Request;
use Mail;
use Session;

class HomeController extends Controller
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
    public function index()
    {
        $favproducts = getFavProductsIds();
        $bannerads = HWCModel\Bannerads::select('Video','ban_link','EnBanimage','ban_name')->where(['ban_status' => '1', 'PageId' => 'home'])->orderBy('display_order')->get();
		$promoitems = HWCModel\Product::select('Id','EnName','Image','Price','Quantity','UniqueKey')->where(['IsPromotion' => '1','ProdStatus' => '1'])->where('Quantity', '>', '0')->orderByRaw('RAND()')->take(12)->get();
	    $homecategories = HWCModel\Category::select('TypeId','UniqueKey','EnName','Image')
            ->where(['TypeStatus' => '1','offerCategory' => '1','ParentLevel' => '0'])
            ->withCount(['products' => fn ($q) => $q->where('ProdStatus', '1')])
            ->with(['children' => function ($query) {
                    $query->select('TypeId', 'ParentLevel')
                        ->where('TypeStatus', 1)
                        ->withCount(['products' => fn ($q) => $q->where('ProdStatus', '1')]);
            }])
            ->orderBy('DisplayOrder')->skip(0)->take(8)->get();
	    $homebrands = HWCModel\Brand::select('UniqueKey','Image', 'EnName')->where(['BrandStatus' => '1','ParentLevel' => '0','PopularBrand' => '1'])->orderBy('DisplayOrder')->skip(0)->take(18)->get();
	    $homeban2 = HWCModel\MastheadImage::select('ban_link','EnBanimage','ban_name')->where('ban_status', 1)->orderBy('display_order')->skip(1)->take(1)->first();
		$welcomecontent = HWCModel\PageContent::select('banner_image', 'ChContent')->where(['page_link' => 'about-hardwarecity','display_status' => '1'])->first();

        return view('welcome', compact('favproducts', 'bannerads', 'promoitems', 'homecategories', 'homebrands', 'homeban2', 'welcomecontent'));
    }

    public function staticpages($urlkey)
    {
        $staticpage = PageContent::where('UniqueKey', '=', $urlkey)->first();
        if ($staticpage) {
            if ($urlkey == 'contact-us' || $urlkey == 'contact') {
                $bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'contact-us')->orderBy('display_order', 'asc')->get();
                return view('public.contact-us', compact('bannerads'));
            } elseif ($urlkey == 'feedback') {
                $bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'feedback')->orderBy('display_order', 'asc')->get();
                return view('public.feedback', compact('bannerads'));
            } else {
                $bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', $urlkey)->orderBy('display_order', 'asc')->get();
                return view('public.staticpages', compact('staticpage', 'bannerads'));
            }
        } else {
            $chkmenu = Menu::where('menu_url', '=', $urlkey)->first();
            if ($chkmenu) {
                return view('public.emptypage');
            } else {
                abort(404);
                /*$staticpage = PageContent::where('UniqueKey', '=', 'page-not-found')->first();
                $bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'page-not-found')->orderBy('display_order', 'asc')->get();
                return view('public.staticpages', compact('staticpage', 'bannerads'));*/
            }
        }

    }

    public function newslettersubscribe(Request $request)
    {
        $subscribestatus = '';
        $email = $request->email;
        if ($email) {
            $chkexist = Subscriber::where('email', '=', $email)->get();

            if (count($chkexist) > 0) {
                $subscribestatus = 'Already Exist';
            } else {

                Subscriber::insert(array('email' => $email));

                $settings = Settings::where('id', '=', '1')->first();
                $adminemail = $settings->admin_email;
                $companyname = $settings->company_name;

                $unsubscribeurl = url('/') . '/unsubscribe/' . base64_encode($email);
                $logo = url('/') . '/img/logo.png';
                $logo = '<img src="' . $logo . '">';

                $unsubscribeurl = '<a href="' . $unsubscribeurl . '">Unsubscribe</a>';

                $emailsubject = $emailcontent = '';
                $emailtemplate = EmailTemplate::where('template_type', '=', '8')->where('status', '=', '1')->first();
                if ($emailtemplate) {
                    $emailsubject = $emailtemplate->subject;
                    $emailcontent = $emailtemplate->content;

                    $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                    $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                    $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                    $emailcontent = str_replace('{unsubscribeurl}', $unsubscribeurl, $emailcontent);

                    $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                    $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                    $headers .= 'X-Mailer: PHP/' . phpversion();
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                    #@mail($email, $emailsubject, $emailcontent, $headers);
                    Mail::to($email)->send(new \App\Mail\TemplateMail($emailsubject, $emailcontent));
                }

                $subscribestatus = 'Success';
            }
        }
        echo $subscribestatus;
    }

    public function unsubscribe($email)
    {
        if ($email) {
            $email = base64_decode($email);
            Subscriber::where('email', '=', $email)->update(array('status' => '0'));
        }
        return redirect('/')->with('success', 'Successfully Unsubscribed!');
    }

    public function contactus(Request $request)
    {
        // Google reCaptcha secret key
        $secretKey = config('services.recaptcha.secret', '');

        if (!empty($secretKey)) {

            if (empty($request->captcha_response)) {
                return redirect('contact-us')->with('message', 'Captcha verification failed, please try again.');
            }

            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $request->captcha_response);
            $responseData = json_decode($verifyResponse);
            if (empty($responseData->success)) {
                return redirect('contact-us')->with('message', 'Captcha verification failed, please try again.');
            }
        }

        $subscribestatus = '';
        $email = $request->email;
        $name = $request->name;
        $phone = $request->phone;
        $salutation = $request->salutation;
        $enquiry_type = $request->enquiry_type;
        $message = $request->message;
        if ($email) {
            $settings = Settings::where('id', '=', '1')->first();
            $adminemail = $settings->admin_email;
            $companyname = $settings->company_name;
            $ccemail = $settings->cc_email;

            $logo = url('/') . '/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $emailsubject = $emailcontent = '';
            $emailtemplate = EmailTemplate::where('template_type', '=', '9')->where('status', '=', '1')->first();
            if ($emailtemplate) {
                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{enquiry_type}', $enquiry_type, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{name}', $name, $emailcontent);
                $emailcontent = str_replace('{email}', $email, $emailcontent);
                $emailcontent = str_replace('{phone}', $phone, $emailcontent);
                $emailcontent = str_replace('{salutation}', $salutation, $emailcontent);
                $emailcontent = str_replace('{enquiry_type}', $enquiry_type, $emailcontent);
                $emailcontent = str_replace('{message}', $message, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                Mail::to($adminemail)->send(new \App\Mail\TemplateMail($emailsubject, $emailcontent));

                #@mail($adminemail, $emailsubject, $emailcontent, $headers);
                if ($ccemail != '') {
                    Mail::to($ccemail)->send(new \App\Mail\TemplateMail($emailsubject, $emailcontent));
                    #@mail($ccemail, $emailsubject, $emailcontent, $headers);
                }
            }
            return redirect('/contact-us')->with('success', 'Thank you for contacting us.');
        }
        return redirect('contact-us')->with('message', 'Please try again. Something went wrong.');

    }

    public function submitfeedback(Request $request)
    {
        $subscribestatus = '';
        $email = $request->email;
        $name = $request->name;
        $phone = $request->phone;
        $message = $request->message;
        if ($email) {
            $settings = Settings::where('id', '=', '1')->first();
            $adminemail = $settings->admin_email;
            $companyname = $settings->company_name;
            $ccemail = $settings->cc_email;

            $logo = url('/') . '/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $emailsubject = $emailcontent = '';
            $emailtemplate = EmailTemplate::where('template_type', '=', '11')->where('status', '=', '1')->first();
            if ($emailtemplate) {
                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{name}', $name, $emailcontent);
                $emailcontent = str_replace('{email}', $email, $emailcontent);
                $emailcontent = str_replace('{phone}', $phone, $emailcontent);
                $emailcontent = str_replace('{message}', $message, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($adminemail, $emailsubject, $emailcontent, $headers);
                Mail::to($adminemail)->send(new \App\Mail\TemplateMail($emailsubject, $emailcontent));
                if ($ccemail != '') {
                    #@mail($ccemail, $emailsubject, $emailcontent, $headers);
                    Mail::to($ccemail)->send(new \App\Mail\TemplateMail($emailsubject, $emailcontent));
                }
            }
        }
        return redirect('/feedback')->with('success', 'Thank you for your feedback!');
    }

    public function prodPriceUpdate(Request $request)
    {
        $percent = Country::where('countryid', 189)->first()->taxpercentage;
        $percent = ($percent + 100) / 100;

        $allProducts = Product::where([['is_inclusive_tax', '=', 0], ['Price', '!=', 0]])->count();

        if ($allProducts == 0) {

            echo 'Done!';
            die();
        }

        $rang = range(1, ($allProducts + 1000), 1000);

        print_r($rang);

        foreach ($rang as $rk => $r) {
            if (!empty($rang[$rk + 1])) {
                $products = Product::where('is_inclusive_tax', 0)->take($rang[$rk + 1])->get();

                foreach ($products as $prod) {
                    Product::where('Id', $prod->Id)->update([
                        'is_inclusive_tax' => 1,
                        'Price' => number_format(($prod->oldPrice * $percent), 2, '.', ''),
                    ]);
                }
            }
        }

        if (Product::where([['is_inclusive_tax', '=', 0], ['Price', '!=', 0]])->count() == 0) {
            echo 'Done!';
        } else {
            echo 'Not Completed. Try again';
        }
    }

    public function prodOptionPriceUpdate(Request $request)
    {
        $percent = Country::where('countryid', 189)->first()->taxpercentage;
        $percent = ($percent + 100) / 100;

        $allProducts = ProductOptions::where([['is_inclusive_tax', '=', 0], ['Price', '!=', 0]])->count();

        if ($allProducts == 0) {
            echo 'Done!';
            die();
        }

        $rang = range(1, ($allProducts + 1000), 1000);

        foreach ($rang as $rk => $r) {
            if (!empty($rang[$rk + 1])) {
                $products = ProductOptions::where('is_inclusive_tax', 0)->take($rang[$rk + 1])->get();

                foreach ($products as $prod) {
                    ProductOptions::where('Id', $prod->Id)->update([
                        'is_inclusive_tax' => 1,
                        'Price' => number_format(($prod->oldPrice * $percent), 2, '.', ''),
                    ]);
                }
            }
        }

        if (ProductOptions::where([['is_inclusive_tax', '=', 0], ['Price', '!=', 0]])->count() == 0) {
            echo 'Done!';
        } else {
            echo 'Not Completed. Try again';
        }
    }
}
