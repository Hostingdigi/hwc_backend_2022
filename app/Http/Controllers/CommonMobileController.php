<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Country;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BrandPrice;
use App\Models\MastheadImage;
use App\Models\Promotions;
use Illuminate\Support\Facades\Hash;
use Session;
use App\Models\Menu;
use App\Models\PageContent;
use App\Models\Settings;
use App\Models\PaymentSettings;
use App\Models\EmailTemplate;
use App\Models\Announcements;

class CommonMobileController extends Controller
{
    
	/* for Mobile App */
	
	public function menus() {
		$data = [];
		$menus = Menu::where('status', '=', '1')->orderBy('display_order', 'asc')->get();
		if($menus) {
			$x = 0;
			foreach($menus as $menu) {
				$menudata[$x]['menu_url'] = $menu->menu_url;
				$menudata[$x]['menu_name'] = $menu->menu_name;
				++$x;
			}
		}
		$data = response()->json(['menudata' => $menudata]);
		return $data;
	}
	
	public function allcategory() {
		$data = $allcategories = [];
		$categories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->get();
		if($categories) {
			$x = 0;
			foreach($categories as $category) {
				$allcategories[$x]['category_name'] = $category->EnName;
				$allcategories[$x]['category_id'] = $category->TypeId;
				$allcategories[$x]['url_key'] = $category->UniqueKey;
				if($category->MobileImage) {
					$allcategories[$x]['image'] = url('/').'/uploads/category/'.$category->MobileImage;
				} else {
					$allcategories[$x]['image'] = '';
				}
				$allcategories[$x]['meta_title'] = $category->meta_title;
				$allcategories[$x]['meta_keywords'] = $category->meta_keywords;
				$allcategories[$x]['meta_description'] = $category->meta_description;
				
				/*$subcategories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', $category->TypeId)->orderBy('DisplayOrder', 'asc')->get();
				if($subcategories) {
					foreach($subcategories as $subcategory) {
						$allcategories[$category->TypeId]['category_name'] = $subcategory->EnName;
						$allcategories[$category->TypeId]['category_id'] = $subcategory->TypeId;
						$allcategories[$category->TypeId]['url_key'] = $subcategory->UniqueKey;
						if($subcategory->Image) {
							$allcategories[$category->TypeId]['image'] = url('/').'/uploads/category/'.$subcategory->Image;
						} else {
							$allcategories[$category->TypeId]['image'] = '';
						}
						$allcategories[$category->TypeId]['meta_title'] = $subcategory->meta_title;
						$allcategories[$category->TypeId]['meta_keywords'] = $subcategory->meta_keywords;
						$allcategories[$category->TypeId]['meta_description'] = $subcategory->meta_description;
						
						$subchildcategories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', $subcategory->TypeId)->orderBy('DisplayOrder', 'asc')->get();
						if($subchildcategories) {
							foreach($subchildcategories as $subchildcategory) {
								$allcategories[$subcategory->TypeId]['category_name'] = $subchildcategory->EnName;
								$allcategories[$subcategory->TypeId]['category_id'] = $subchildcategory->TypeId;
								$allcategories[$subcategory->TypeId]['url_key'] = $subchildcategory->UniqueKey;
								if($subchildcategory->Image) {
									$allcategories[$subcategory->TypeId]['image'] = url('/').'/uploads/category/'.$subchildcategory->Image;
								} else {
									$allcategories[$subcategory->TypeId]['image'] = '';
								}
								$allcategories[$subcategory->TypeId]['meta_title'] = $subchildcategory->meta_title;
								$allcategories[$subcategory->TypeId]['meta_keywords'] = $subchildcategory->meta_keywords;
								$allcategories[$subcategory->TypeId]['meta_description'] = $subchildcategory->meta_description;
							}
						}
					}
				}*/
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'All Category', 'categories' => $allcategories]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Categories Found!', 'categories' => '']);
		}
		
		return $data;
	}
	
	public function childcategories(Request $request) {
		$data = $childcategories = [];
		$category = $request->category;
		$categories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', $category)->orderBy('DisplayOrder', 'asc')->get();
		if($categories) {
			$x = 0;
			foreach($categories as $category) {
				$childcategories[$x]['category_name'] = $category->EnName;
				$childcategories[$x]['category_id'] = $category->TypeId;
				$childcategories[$x]['url_key'] = $category->UniqueKey;
				if($category->MobileImage) {
					$childcategories[$x]['image'] = url('/').'/uploads/category/'.$category->MobileImage;
				} else {
					$childcategories[$x]['image'] = '';
				}
				$childcategories[$x]['meta_title'] = $category->meta_title;
				$childcategories[$x]['meta_keywords'] = $category->meta_keywords;
				$childcategories[$x]['meta_description'] = $category->meta_description;				
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Child Category', 'childcategories' => $childcategories]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Categories Found!', 'childcategories' => '']);
		}
		
		return $data;
	}
	
	public function allbrands() {
		$data = $allbrands = [];
		$brands = Brand::where('BrandStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		if($brands) {
			$x = 0;
			foreach($brands as $brand) {
				$allbrands[$x]['brand_id'] = $brand->BrandId;
				$allbrands[$x]['brand_name'] = $brand->EnName;
				$allbrands[$x]['url_key'] = $brand->UniqueKey;
				if($brand->MobileImage != '') {
					$allbrands[$x]['image'] = url('/').'/uploads/brands/'.$brand->MobileImage;
				} else {
					$allbrands[$x]['image'] = '';
				}
				$allbrands[$x]['meta_title'] = $brand->meta_title;
				$allbrands[$x]['meta_keywords'] = $brand->meta_keywords;
				$allbrands[$x]['meta_description'] = $brand->meta_description;				
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'All Brands', 'brands' => $allbrands]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Brand Found!', 'brands' => '']);
		}
		
		return $data;
	}
	
	public function allbrandswithorder() {
		$data = $allbrands = $allbrandsdata = [];
		$alphas = ['0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
		$a = 0;
		foreach($alphas as $key => $alpha) {
			if($alpha == '0-9') {
				$brands = Brand::where('BrandStatus', '=', '1')->where('EnName', 'LIKE', '0%')->orWhere('EnName', 'LIKE', '1%')->orWhere('EnName', 'LIKE', '2%')->orWhere('EnName', 'LIKE', '3%')->orWhere('EnName', 'LIKE', '4%')->orWhere('EnName', 'LIKE', '5%')->orWhere('EnName', 'LIKE', '6%')->orWhere('EnName', 'LIKE', '7%')->orWhere('EnName', 'LIKE', '8%')->orWhere('EnName', 'LIKE', '9%')->orderBy('EnName', 'ASC')->get();
			} else {	
				$brands = Brand::where('BrandStatus', '=', '1')->whereRaw('EnName LIKE "'.$alpha.'%"')->orderBy('EnName', 'asc')->get();
			}
			if($brands) {
				$allbrands[$a]['startchar'] = $alpha;
				$x = 0;
				foreach($brands as $brand) {
					$allbrandsdata[$x]['brand_id'] = $brand->BrandId;
					$allbrandsdata[$x]['brand_name'] = $brand->EnName;
					$allbrandsdata[$x]['url_key'] = $brand->UniqueKey;
					if($brand->MobileImage != '') {
						$allbrandsdata[$x]['image'] = url('/').'/uploads/brands/'.$brand->MobileImage;
					} else {
						$allbrandsdata[$x]['image'] = '';
					}
					$allbrandsdata[$x]['meta_title'] = $brand->meta_title;
					$allbrandsdata[$x]['meta_keywords'] = $brand->meta_keywords;
					$allbrandsdata[$x]['meta_description'] = $brand->meta_description;	
					++$x;
				}				
			} 
			$allbrands[$a]['data'] = $allbrandsdata;
			++$a;
		}	
		$data = response()->json(['response' => 'success', 'message' => 'All Brands', 'brands' => $allbrands]);
		
		return $data;
	}
	
	public function popularbrands(Request $request) {
		$data = $allbrands = [];
		$settings = PaymentSettings::where('id', '=', '1')->first();
		$globaldis = $settings->discount_percentage;
		$customerid = $request->customerid;
		$brands = Brand::where('BrandStatus', '=', '1')->where('PopularBrand', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		if($brands) {
			$x = 0;
			foreach($brands as $brand) {
				$allbrands[$x]['brand_id'] = $brand->BrandId;
				$allbrands[$x]['brand_name'] = $brand->EnName;
				$allbrands[$x]['url_key'] = $brand->UniqueKey;
				if($brand->MobileImage != '') {
					$allbrands[$x]['image'] = url('/').'/uploads/brands/'.$brand->MobileImage;
				} else {
					$allbrands[$x]['image'] = '';
				}
				$allbrands[$x]['meta_title'] = $brand->meta_title;
				$allbrands[$x]['meta_keywords'] = $brand->meta_keywords;
				$allbrands[$x]['meta_description'] = $brand->meta_description;	
				
				$customergroup = 1;
				if($customerid > 0) {
					$customer = Customer::where('cust_id', '=', $customerid)->select('cust_type')->first();
					if($customer) {
						if($customer->cust_type > 0) {
							$customergroup = $customer->cust_type;
						}
					}
				}
				$brandprice = BrandPrice::where('Brand', '=', $brand->BrandId)->where('GroupId', '=', $customergroup)->where('type', '=', '2')->where('Status', '=', '1')->first();
				
				if($brandprice && $brand->dis_type == 1 && $brand->exclude_global_dis == 1) {
					$allbrands[$x]['discount_percentage'] = $brandprice->Price;
				} else {
					$allbrands[$x]['discount_percentage'] = 0;
				}
									
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Popular Brands', 'brands' => $allbrands]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Popular Brand Found!', 'brands' => '']);
		}
		return $data;
	}
	
	public function brandbyfilter($filter) {
		$data = $allbrands = [];
		if($filter == '0-9') {
			$brands = Brand::where('BrandStatus', '=', '1')->where('EnName', 'LIKE', '0%')->orWhere('EnName', 'LIKE', '1%')->orWhere('EnName', 'LIKE', '2%')->orWhere('EnName', 'LIKE', '3%')->orWhere('EnName', 'LIKE', '4%')->orWhere('EnName', 'LIKE', '5%')->orWhere('EnName', 'LIKE', '6%')->orWhere('EnName', 'LIKE', '7%')->orWhere('EnName', 'LIKE', '8%')->orWhere('EnName', 'LIKE', '9%')->orderBy('EnName', 'ASC')->get();
		} else {
			$brands = Brand::where('BrandStatus', '=', '1')->whereRaw('EnName LIKE "'.$filter.'%"')->orderBy('DisplayOrder', 'asc')->get();
		}
		if($brands) {
			$x = 0;
			foreach($brands as $brand) {
				$allbrands[$x]['brand_id'] = $brand->BrandId;
				$allbrands[$x]['brand_name'] = $brand->EnName;
				$allbrands[$x]['url_key'] = $brand->UniqueKey;
				if($brand->MobileImage != '') {
					$allbrands[$x]['image'] = url('/').'/uploads/brands/'.$brand->MobileImage;
				} else {
					$allbrands[$x]['image'] = '';
				}
				$allbrands[$x]['meta_title'] = $brand->meta_title;
				$allbrands[$x]['meta_keywords'] = $brand->meta_keywords;
				$allbrands[$x]['meta_description'] = $brand->meta_description;				
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Brands', 'brands' => $allbrands]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Brand Found!', 'brands' => '']);
		}
		return $data;
	}
	
	public function offerCategories() {
		$data = $allcategories = [];
		$categories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->where('offerCategory', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		$categorydiscount = 0;
		$settings = PaymentSettings::where('id', '=', '1')->first();
		$globaldis = $settings->discount_percentage;
		if($categories) {
			$x = 0;
			foreach($categories as $category) {
				$allcategories[$x]['category_name'] = $category->EnName;
				$allcategories[$x]['category_id'] = $category->TypeId;
				$allcategories[$x]['url_key'] = $category->UniqueKey;
				if($category->MobileImage) {
					$allcategories[$x]['image'] = url('/').'/uploads/category/'.$category->MobileImage;
				} else {
					$allcategories[$x]['image'] = '';
				}
				$allcategories[$x]['meta_title'] = $category->meta_title;
				$allcategories[$x]['meta_keywords'] = $category->meta_keywords;
				$allcategories[$x]['meta_description'] = $category->meta_description;
				if($category->dis_type == 1) {
					$allcategories[$x]['discount_percentage'] = $globaldis;
				} else {
					$allcategories[$x]['discount_percentage'] = 0;
				}					
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Offer Category', 'categories' => $allcategories]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Categories Found!', 'categories' => '']);
		}
		
		return $data;
	}
	
	public function homebanners() {
		$data = $allbanners = [];
		$banners = Promotions::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		if($banners) {
			$x = 0;
			foreach($banners as $banner) {
				$allbanners[$x]['ban_name'] = $banner->ban_name;
				$allbanners[$x]['ban_link'] = $banner->ban_link;
				$allbanners[$x]['ban_caption'] = strip_tags($banner->ban_caption);
				if($banner->EnBanMobileimage) {
					$allbanners[$x]['image'] = url('/').'/uploads/promotionsbanner/'.$banner->EnBanMobileimage;
				} else {
					$allbanners[$x]['image'] = '';
				}
				
								
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Home Banners', 'banners' => $allbanners]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Banners Found!', 'banners' => '']);
		}
		
		return $data;
	}
	
	public function terms() {
		$data =  $terms = [];
		$pagecontent = PageContent::where('UniqueKey', '=', 'terms-and-conditions')->first();
		
		$x = 0;
		if($pagecontent) {			
			$terms[$x]['termsandconditions'] = $pagecontent->ChContent;
			$data = response()->json(['response' => 'success', 'message' => 'Terms and Conditions', 'terms' => $terms]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Terms and Conditions', 'terms' => '']);
		}
		return $terms;
	}
	
	public function allcountries() {
		$data = $countries = [];
		$allcountries = Country::where('country_status', '=', '1')->orderBy('countryname', 'asc')->get();
		if($allcountries) {
			$x = 0;
			foreach($allcountries as $allcountry) {
				$countries[$x]['countryid'] = $allcountry->countryid;
				$countries[$x]['countryname'] = $allcountry->countryname;
				$countries[$x]['countrycode'] = $allcountry->countrycode;
				$countries[$x]['taxtitle'] = $allcountry->taxtitle;
				$countries[$x]['taxpercentage'] = $allcountry->taxpercentage;
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Countries', 'countries' => $countries]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'Countries', 'countries' => '']);
		}
		
		return $data;
	}
	
	public function sendfeedback(Request $request) {
		$data = [];
		$name = $request->name;
		$email = $request->email;
		$phone = $request->phone;
		$message = $request->message;
		
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
			
			$data = response()->json(['response' => 'success', 'message' => 'Feedback Successfully Sent!']);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Feedback Not Send!']);
		}
		
		return $data;
	}
	
	public function offermessages() {
		$data = $offers = [];
		$offermessage = '';
		$alloffers = Announcements::where('status', '=', '1')->orderBy('display_order', 'asc')->get();
		if($alloffers) {
			$x = 0;
			foreach($alloffers as $alloffer) {
				/*$offers[$x]['id'] = $alloffer->id;
				$offers[$x]['message'] = $alloffer->message;				
				++$x;*/
				if($offermessage != '') {
					$offermessage = $offermessage.' '.strip_tags($alloffer->message);
				} else {
					$offermessage = strip_tags($alloffer->message);
				}
			}
			$offers[$x]['message'] = $offermessage;
			$data = response()->json(['response' => 'success', 'message' => 'Offer Messages', 'offers' => $offers]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'Offer Messages', 'offers' => '']);
		}
		
		return $data;
	}
	
	public function adbanners() {
		$data = $adbanners = [];
		$banners = MastheadImage::where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		if($banners) {
			$x = 0;
			foreach($banners as $banner) {
				$adbanners[$x]['ban_name'] = $banner->ban_name;
				$adbanners[$x]['ban_link'] = $banner->ban_link;
				$adbanners[$x]['ban_caption'] = $banner->ban_caption;
				if($banner->EnBanMobileimage) {
					$adbanners[$x]['image'] = url('/').'/uploads/bannermaster/'.$banner->EnBanMobileimage;
				} else {
					$adbanners[$x]['image'] = '';
				}
				
								
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Ad Banners', 'adbanners' => $adbanners]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Banners Found!', 'adbanners' => '']);
		}
		
		return $data;
	}

}
