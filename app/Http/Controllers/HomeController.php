<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Promotions;
use App\Models\Menu;
use App\Models\PageContent;
use App\Models\Price;
use App\Models\FavouriteProducts;
use App\Models\Subscriber;
use App\Models\Settings;
use App\Models\EmailTemplate;
use App\Models\Bannerads;
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
		$favproducts = [];
		if(Session::has('customer_id')) {
			$customerid = Session::get('customer_id');
			$fproducts = FavouriteProducts::where('cust_id', '=', $customerid)->select('prod_id')->get();
			if($fproducts) {
				foreach($fproducts as $fproduct) {
					$favproducts[] = $fproduct->prod_id;
				}
			}
		}
		$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'home')->orderBy('display_order', 'asc')->get();
		
        return view('welcome', compact('favproducts','bannerads'));
    }
	
	public function staticpages($urlkey) {
		$staticpage = PageContent::where('UniqueKey', '=', $urlkey)->first();
		if($staticpage) {
			if($urlkey == 'contact-us' || $urlkey == 'contact') {
				$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'contact-us')->orderBy('display_order', 'asc')->get();
				return view('public.contact-us', compact('bannerads'));
			} elseif($urlkey == 'feedback') {
				$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'feedback')->orderBy('display_order', 'asc')->get();
				return view('public.feedback', compact('bannerads'));
			} else {
				$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', $urlkey)->orderBy('display_order', 'asc')->get();
				return view('public.staticpages', compact('staticpage', 'bannerads'));
			}
		} else {
			$chkmenu = Menu::where('menu_url', '=', $urlkey)->first();
			if($chkmenu) {				
				return view('public.emptypage');
			} else {
				$staticpage = PageContent::where('UniqueKey', '=', 'page-not-found')->first();
				$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'page-not-found')->orderBy('display_order', 'asc')->get();
				return view('public.staticpages', compact('staticpage', 'bannerads'));
			}
		}
		
	}
	
	public function newslettersubscribe(Request $request) {
		$subscribestatus = '';
		$email = $request->email;
		if($email) {
			$chkexist = Subscriber::where('email', '=', $email)->get();
			
			if(count($chkexist) > 0) {
				$subscribestatus = 'Already Exist';
			} else {
				
				Subscriber::insert(array('email' => $email));
				
				$settings = Settings::where('id', '=', '1')->first();
				$adminemail = $settings->admin_email;
				$companyname = $settings->company_name;
				
				$unsubscribeurl = url('/').'/unsubscribe/'.base64_encode($email);
				$logo = url('/').'/img/logo.png';
				$logo = '<img src="'.$logo.'">';
				
				$unsubscribeurl = '<a href="'.$unsubscribeurl.'">Unsubscribe</a>';
				
				$emailsubject = $emailcontent = '';
				$emailtemplate = EmailTemplate::where('template_type', '=', '8')->where('status', '=', '1')->first();
				if($emailtemplate) {
					$emailsubject = $emailtemplate->subject;
					$emailcontent = $emailtemplate->content;
					
					$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
					$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
					$emailcontent = str_replace('{logo}', $logo, $emailcontent);					
					$emailcontent = str_replace('{unsubscribeurl}', $unsubscribeurl, $emailcontent);
									
					$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
					$headers .='Reply-To: '. $adminemail . "\r\n" ;
					$headers .='X-Mailer: PHP/' . phpversion();
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
							
					@mail($email, $emailsubject, $emailcontent, $headers);
				}			
				
				$subscribestatus = 'Success';
			}
		}
		echo $subscribestatus;
	}
	
	public function unsubscribe($email) {
		if($email) {
			$email = base64_decode($email);
			Subscriber::where('email', '=', $email)->update(array('status' => '0'));
		}
		return redirect('/')->with('success', 'Successfully Unsubscribed!');
	}
	
	public function contactus(Request $request) {
		$subscribestatus = '';
		$email = $request->email;
		$name = $request->name;
		$phone = $request->phone;
		$salutation = $request->salutation;
		$enquiry_type = $request->enquiry_type;
		$message = $request->message;
		if($email) {
			$settings = Settings::where('id', '=', '1')->first();
			$adminemail = $settings->admin_email;
			$companyname = $settings->company_name;
			$ccemail = $settings->cc_email;
						
			$logo = url('/').'/img/logo.png';
			$logo = '<img src="'.$logo.'">';
						
			$emailsubject = $emailcontent = '';
			$emailtemplate = EmailTemplate::where('template_type', '=', '9')->where('status', '=', '1')->first();
			if($emailtemplate) {
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
								
				$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
				$headers .='Reply-To: '. $adminemail . "\r\n" ;
				$headers .='X-Mailer: PHP/' . phpversion();
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
						
				@mail($adminemail, $emailsubject, $emailcontent, $headers);
				if($ccemail != '') {
					@mail($adminemail, $emailsubject, $emailcontent, $headers);
				}
			}	
		}
		return redirect('/contact-us')->with('success', 'Thank you for contacting us.');
	}
	
	public function submitfeedback(Request $request) {
		$subscribestatus = '';
		$email = $request->email;
		$name = $request->name;
		$phone = $request->phone;		
		$message = $request->message;
		if($email) {
			$settings = Settings::where('id', '=', '1')->first();
			$adminemail = $settings->admin_email;
			$companyname = $settings->company_name;
			$ccemail = $settings->cc_email;
						
			$logo = url('/').'/img/logo.png';
			$logo = '<img src="'.$logo.'">';
						
			$emailsubject = $emailcontent = '';
			$emailtemplate = EmailTemplate::where('template_type', '=', '11')->where('status', '=', '1')->first();
			if($emailtemplate) {
				$emailsubject = $emailtemplate->subject;
				$emailcontent = $emailtemplate->content;
				
				$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);				
				$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
				$emailcontent = str_replace('{logo}', $logo, $emailcontent);					
				$emailcontent = str_replace('{name}', $name, $emailcontent);
				$emailcontent = str_replace('{email}', $email, $emailcontent);
				$emailcontent = str_replace('{phone}', $phone, $emailcontent);				
				$emailcontent = str_replace('{message}', $message, $emailcontent);
								
				$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
				$headers .='Reply-To: '. $adminemail . "\r\n" ;
				$headers .='X-Mailer: PHP/' . phpversion();
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
						
				@mail('balamurugan.sk@gmail.com', $emailsubject, $emailcontent, $headers);
				/*if($ccemail != '') {
					@mail($adminemail, $emailsubject, $emailcontent, $headers);
				}*/
			}	
		}
		return redirect('/feedback')->with('success', 'Thank you for your feedback!');
	}
}
