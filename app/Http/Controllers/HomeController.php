<?php

namespace App\Http\Controllers;

use App\Models\Bannerads;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\Menu;
use App\Models\PageContent;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Models\Settings;
use App\Models\Subscriber;
use App\Models as HWCModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;
use App\Mail\DynamicEmail;
use Illuminate\Support\Facades\Log;

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

        return view('frontend.index', compact('favproducts', 'bannerads', 'promoitems', 'homecategories', 'homebrands', 'homeban2', 'welcomecontent'));
    }

    public function staticpages($urlkey)
	{
		$staticpage = PageContent::where('UniqueKey', $urlkey)->first();

		if($staticpage)
		{
			if(in_array($urlkey,['contact-us', 'contact']))
			{
				$bannerads = Bannerads::where(['ban_status' => 1, 'PageId' => 'contact-us'])->orderBy('display_order')->get();
				$bladeFile = 'frontend.contact-us';
				$compactData = compact('bannerads');
			} elseif($urlkey == 'feedback') {
				$bannerads = Bannerads::where(['ban_status' => 1, 'PageId' => 'feedback'])->orderBy('display_order')->get();
				$bladeFile = 'frontend.feedback';
				$compactData = compact('bannerads');
			} else {
				$bannerads = Bannerads::where(['ban_status' => 1, 'PageId' => $urlkey])->orderBy('display_order')->get();
				$bladeFile = 'frontend.staticpages';
				$compactData = compact('staticpage', 'bannerads');
			}

			return view($bladeFile, $compactData);
		}

		$chkmenu = Menu::where('menu_url', $urlkey)->first();
		if($chkmenu) return view('frontend.emptypage');
		
		$staticpage = PageContent::where('UniqueKey', 'page-not-found')->first();
		$bannerads = Bannerads::where(['ban_status' => '1', 'PageId' => 'page-not-found'])->orderBy('display_order')->get();
		return view('frontend.staticpages', compact('staticpage', 'bannerads'));
	}

    public function newslettersubscribe(Request $request)
    {
		if(!$request->filled('email')) return response()
			->json(['status' => false, 'message' => __('messages.email_required'), 'data' => null]);

		$email = $request->email;
		
		$chkexist = Subscriber::where(['email' => $email, 'status' => 1])->first();
			
		if($chkexist) return response()->json(['status' => false, 'message' => 'Email address is already subscribed.', 'data' => null]);
            				
		Subscriber::create(['email' => $email]);
		$settings = Settings::where('id', 1)->first();

		$adminemail = $settings->admin_email;
		$companyname = $settings->company_name;
		
		$logo = '<img src="'.url('img/logo.png').'">';		
		$unsubscribeurl = '<a href="'.url('unsubscribe/'.base64_encode($email)).'">Unsubscribe</a>';
		
		$emailsubject = $emailcontent = '';
		$emailtemplate = EmailTemplate::where(['template_type' => 8, 'status' => 1])->first();
		
		if($emailtemplate)
		{
			$emailsubject = $emailtemplate->subject;
			$emailcontent = $emailtemplate->content;
			
			$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
			$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
			$emailcontent = str_replace('{logo}', $logo, $emailcontent);					
			$emailcontent = str_replace('{unsubscribeurl}', $unsubscribeurl, $emailcontent);

			try {
				Mail::to($email)->send(new DynamicEmail($emailsubject,$emailcontent));
			} catch (\Throwable $e) {
				Log::error('Newsletter Subscription Email Error: ' . $e->getMessage());
			}
		}
		
		return response()->json(['status' => true, 'message' => 'Thanks for your subscription!.', 'data' => null]);
	}

    public function unsubscribe($email)
    {
	    if($email) Subscriber::where('email', base64_decode($email))->update(['status' => '0']);
	    return redirect('/')->with('success', 'Successfully Unsubscribed!');
	}

    public function contactus(Request $request)
    {
		// Google reCaptcha secret key
        if (app()->environment(['production','staging']) && !empty(config('app.google_captcha_secret'))) {
            if (empty($request->captcha_response)) return redirect('contact-us')->with('message', 'Captcha verification failed, please try again.');
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . config('app.google_captcha_secret') . '&response=' . $request->captcha_response);
            $responseData = json_decode($verifyResponse);
            if (empty($responseData->success)) return redirect('contact-us')->with('message', 'Captcha verification failed, please try again.');
        }
		
		$email = $request->email;
		$name = $request->name;
		$phone = $request->phone;
		$salutation = $request->salutation;
		$enquiry_type = $request->enquiry_type;
		$message = $request->message;

		if($request->filled('email'))
        {
			$settings = HWCModel\Settings::select('admin_email','company_name','cc_email')->where('id',1)->first();
			$adminemail = $settings?->admin_email;
			$companyname = $settings?->company_name;
			$ccemail = $settings?->cc_email;
						
			$logo = url('frontend/images/logo.png');
			$logo = '<img src="'.$logo.'">';
						
			$emailsubject = $emailcontent = '';
			$emailtemplate = HWCModel\EmailTemplate::select('subject','content')->where(['template_type' => 9, 'status' => 1])->first();
			
            if($emailtemplate)
            {
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
				
                try{
                    Mail::send([],[], function($message) use ($adminemail, $emailsubject, $emailcontent) {
                        $message->to($adminemail)
                                ->subject($emailsubject)
                                ->from(config('mail.mailers.smtp.username'), config('app.name'))
                                ->setBody($emailcontent, 'text/html');
                    });
                }catch(Throwable $e){
                    Log::info('Contact US email failed to admin.');
                    Log::error($e->getMessage());
                    Log::info('User data:');
                    Log::info($request->all());
                }
                
                try{
                    if($ccemail != '') Mail::send([],[], function($message) use ($ccemail, $emailsubject, $emailcontent) {
                        $message->to($ccemail)
                                ->subject($emailsubject)
                                ->from(config('mail.mailers.smtp.username'), config('app.name'))
                                ->setBody($emailcontent, 'text/html');
                    });
                }catch(Throwable $e){
                    Log::info('Contact US email failed to ccemail.');
                    Log::error($e->getMessage());
                    Log::info('User data:');
                    Log::info($request->all());
                }
			}
			return redirect('contact-us')->with('success', 'Thank you for contacting us.');
		}
		return redirect('contact-us')->with('message', 'Please try again. Something went wrong.');
	}

    public function submitfeedback(Request $request)
    {
		// Google reCaptcha secret key
        if (app()->environment(['production','staging']) && !empty(config('app.google_captcha_secret'))) {
            if (empty($request->captcha_response)) return redirect('feedback')->with('message', 'Captcha verification failed, please try again.');
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . config('app.google_captcha_secret') . '&response=' . $request->captcha_response);
            $responseData = json_decode($verifyResponse);
            if (empty($responseData->success)) return redirect('feedback')->with('message', 'Captcha verification failed, please try again.');
        }

		$email = $request->email;
		$name = $request->name;
		$phone = $request->phone;		
		$message = $request->message;

		if($request->filled('email'))
        {
			$settings = HWCModel\Settings::select('admin_email','company_name','cc_email')->where('id', 1)->first();
			$adminemail = $settings?->admin_email;
			$companyname = $settings?->company_name;
			$ccemail = $settings?->cc_email;
						
			$logo = url('frontend/images/logo.png');
			$logo = '<img src="'.$logo.'">';
						
			$emailsubject = $emailcontent = '';
			$emailtemplate = HWCModel\EmailTemplate::select('subject','content')->where('template_type', '=', '11')->where('status', '=', '1')->first();
			if($emailtemplate)
            {
				$emailsubject = $emailtemplate?->subject;
				$emailcontent = $emailtemplate?->content;
				
				$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);				
				$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
				$emailcontent = str_replace('{logo}', $logo, $emailcontent);					
				$emailcontent = str_replace('{name}', $name, $emailcontent);
				$emailcontent = str_replace('{email}', $email, $emailcontent);
				$emailcontent = str_replace('{phone}', $phone, $emailcontent);				
				$emailcontent = str_replace('{message}', $message, $emailcontent);
								
                try
                {
                    Mail::send([],[], function($message) use ($adminemail, $emailsubject, $emailcontent) {
                        $message->to($adminemail)
                                ->subject($emailsubject)
                                ->from(config('mail.mailers.smtp.username'), config('app.name'))
                                ->setBody($emailcontent, 'text/html');
                    });
                }catch(Throwable $e){
                    Log::info('Feedback email failed to admin.');
                    Log::error($e->getMessage());
                    Log::info('User data:');
                    Log::info($request->all());
                }
				
                try{
                    Mail::send([],[], function($message) use ($ccemail, $emailsubject, $emailcontent) {
                        $message->to($ccemail)
                                ->subject($emailsubject)
                                ->from(config('mail.mailers.smtp.username'), config('app.name'))
                                ->setBody($emailcontent, 'text/html');
                    });
                }catch(Throwable $e){
                    Log::info('Feedback email failed to ccemail.');
                    Log::error($e->getMessage());
                    Log::info('User data:');
                    Log::info($request->all());
                }
			}
		}
		return redirect('feedback')->with('success', 'Thank you for your feedback!');
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
