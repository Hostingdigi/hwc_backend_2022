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
use App\Models\ProductGallery;
use App\Models\ProductReviews;
use App\Models\EmailTemplate;
use App\Models\Settings;
use App\Models\FavouriteProducts;
use App\Models\PaymentSettings;
use App\Models\BrandPrice;
use App\Models\Bannerads;
use Session;
use DB;
use App\Models\PageContent;
use Mail;

class ProductController extends Controller
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
    public function index($category, Request $request)
    {		
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		$categoryid = $parentlevel = $parentcategoryid = $grandparentcategoryid = 0;
		$products = $brands = $categories = $brandids = [];
		$catids = $producttags = [];
		$parentname = $parenturl = $categoryname = $urlkey = $grandparentname = $grandparenturl = '';
		$categorydata = Category::where('UniqueKey', '=', $category)->first();
		if($categorydata) {
			$categoryid = $categorydata->TypeId;
			$parentlevel = $categorydata->ParentLevel;
			$categoryname = $categorydata->EnName;
			$urlkey = $categorydata->UniqueKey;
			$catids[] = $categoryid;
			if($categorydata->ParentLevel > 0) {				
				$parentcategory = Category::where('TypeId', '=', $categorydata->ParentLevel)->first();
				if($parentcategory) {
					$parentname = $parentcategory->EnName;
					$parenturl = $parentcategory->UniqueKey;
					$parentcategoryid = $categorydata->ParentLevel;
					$catids[] = $parentcategoryid;
					if($parentcategory->ParentLevel > 0) {
						$grandparentcategory = Category::where('TypeId', '=', $parentcategory->ParentLevel)->first();
						if($grandparentcategory) {
							$grandparentname = $grandparentcategory->EnName;
							$grandparenturl = $grandparentcategory->UniqueKey;
							$grandparentcategoryid = $parentcategory->ParentLevel;
							$catids[] = $grandparentcategoryid;
						}
					}
				}				
			} else {
				//$parents = Category::where('TypeId', '=', $categorydata->ParentLevel)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
				$parentname = $categorydata->EnName;
				$parenturl = $categorydata->UniqueKey;
				$categoryname = $categorydata->EnName;				
			}
			$bid = '';
			$join = '';
			
			if(isset($request->bid)) {
				$bid = $request->bid;
				if($join != '') {
					$join .= ' AND Brand IN ('.$bid.')';
				} else {
					$join .= 'Brand IN ('.$bid.')';
				}
			}
			if($join != '') {
				$products = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'desc')->paginate($show);

				$producttags = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
			} else {
				$products = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);
				$producttags = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
			}
			if($catids) {
				$catproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '=', '1')->select('Brand')->get();
				if($catproducts) {
					foreach($catproducts as $catproduct) {
						$brandids[] = $catproduct->Brand;
					}
				}					
				if($brandids) {
					$brandids = array_unique($brandids);
					$brands = Brand::where('BrandStatus', '=', '1')->whereIn('BrandId', $brandids)->orderBy('DisplayOrder', 'asc')->get();
				}
			}	
			if($grandparentcategoryid > 0) {
				$categories = Category::where('TypeStatus', '=', '1')->where('TypeId', '=', $grandparentcategoryid)->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->get();
			} elseif($parentcategoryid > 0) {
				$categories = Category::where('TypeStatus', '=', '1')->where('TypeId', '=', $parentcategoryid)->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->get();
			} else {
				$categories = Category::where('TypeStatus', '=', '1')->where('TypeId', '=', $categoryid)->where('ParentLevel', '=', '0')->orderBy('DisplayOrder', 'asc')->get();
			}
		}
		
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
		
		$adbanners = Bannerads::where('PageId', '=', 'types')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
				
        return view('public/Product.index', compact('products', 'parentname', 'parenturl', 'parentlevel', 'categoryname', 'grandparenturl', 'grandparentname', 'page', 'show', 'sortby', 'urlkey', 'brands', 'categories', 'bid', 'favproducts', 'producttags', 'adbanners', 'categoryid'));
    }
	
	public function types($category, Request $request) {
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		$categoryid = $parentlevel = 0;
		$products = $types = $brands = $subcategories = $subsubcategories = $categories = [];
		$brandids = $categoryids = [0];
		$parentname = $parenturl = $categoryname = $urlkey = '';
		$categorydata = Category::where('UniqueKey', '=', $category)->first();
		if($categorydata) {			
			$parentlevel = $categorydata->ParentLevel;
			$categoryname = $categorydata->EnName;
			$urlkey = $categorydata->UniqueKey;			
			
			
			$parentcategory = Category::where('TypeId', '=', $parentlevel)->where('TypeStatus', '=', '1')->first();
			if($parentcategory) {
				$parentname = $parentcategory->EnName;
				$parenturl = $parentcategory->UniqueKey;
			}
			
			$types = Category::where('ParentLevel', '=', $categorydata->TypeId)->where('TypeStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);
			$categories = Category::where('ParentLevel', '=', $categorydata->TypeId)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'desc')->get();
			
			$categoryids[] = $categorydata->TypeId;
			$subcategories = Category::where('ParentLevel', '=', $categorydata->TypeId)->where('TypeStatus', '=', '1')->get();
			if($subcategories) {
				foreach($subcategories as $subcategory) {
					$categoryids[] = $subcategory->TypeId;
					$subsubcategories = Category::where('ParentLevel', '=', $subcategory->TypeId)->where('TypeStatus', '=', '1')->get();
					if($subsubcategories) {
						foreach($subsubcategories as $subsubcategory) {
							$categoryids[] = $subsubcategory->TypeId;
						}
					}
				}
			}
			
			/*if($categoryids) {
				$products = Product::whereIn('Types', $categoryids)->where('ProdStatus', '=', '1')->get();
				if($products) {
					foreach($products as $product) {
						$brandids[] = $product->Brand;
					}
				}					
				if($brandids) {
					$brandids = array_unique($brandids);
					$brands = Brand::where('BrandStatus', '=', '1')->whereIn('BrandId', $brandids)->orderBy('DisplayOrder', 'asc')->get();
				}
			}*/
		}
		
		if($types) {	
			$adbanners = Bannerads::where('PageId', '=', 'types')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		
			return view('public/Product.types', compact('types', 'categoryname', 'page', 'show', 'sortby', 'urlkey', 'parentlevel', 'products', 'brands', 'categories', 'parenturl', 'parentname', 'adbanners'));
		} else {
			return redirect('/category/'.$category);
		}
	}
	
	public function alltypes(Request $request) {
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		$categoryid = $parentlevel = 0;
		
		
		$types = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy($sortby, 'asc')->paginate($show);			
			
		return view('public/Product.alltypes', compact('types', 'page', 'show', 'sortby'));
		
	}
	
	public function gettypeslist(Request $request) {
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		$categoryid = $parentlevel = 0;
		
		$content = '';
		
		$types = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', '0')->orderBy($sortby, 'asc')->paginate($show);	

		if($types) {
			foreach($types as $type) {
				$productscount = 0;
				$catproducts = Product::where('Types', '=', $type->TypeId)->where('ProdStatus', '=', '1')->count();
				$productscount = $productscount + $catproducts;
				$subtypes = Category::where('ParentLevel', '=', $type->TypeId)->where('TypeStatus', '=', '1')->get();
				if(count($subtypes) > 0) {
					foreach($subtypes as $subtype) {
						$subcatproducts = Product::where('Types', '=', $subtype->TypeId)->where('ProdStatus', '=', '1')->count();
						$productscount = $productscount + $subcatproducts;
						$subsubcats = [];
						$subsubcats = Category::where('ParentLevel','=',$subtype->TypeId)->orderBy('EnName', 'ASC')->get();
						if(count($subsubcats) > 0) {
							foreach($subsubcats as $subsubcat) {								
								$subsubcatproducts = Product::where('Types', '=', $subsubcat->TypeId)->where('ProdStatus', '=', '1')->count();
								$productscount = $productscount + $subsubcatproducts;
							}		
						}		
					}
				}
				$content .= '<div class="col-lg-3 col-md-6"><div class="tab-item"><div class="tab-img">';
				
				$typeurl = url('/type/'.$type->UniqueKey);
				$categoryurl = url('/category/'.$type->UniqueKey);
				
				if(count($subtypes) > 0) {
					$content .= '<a href="'.$typeurl.'">';
				} else {
					$content .= '<a href="'.$categoryurl.'">';
				}
				
				$typeimage = url('/uploads/category/'.$type->Image);
				$noimage = url('/images/noimage.png');
				if($type->Image != '' && file_exists(public_path('/uploads/category/'.$type->Image))) {
					$content .= '<img class="main-img img-fluid" src="'.$typeimage.'"  alt="'.$type->EnName.'">';
					$content .= '<img class="sec-img img-fluid" src="'.$typeimage.'"  alt="'.$type->EnName.'">';
				} else {
					$content .= '<img class="main-img img-fluid" src="'.$noimage.'" alt="'.$type->EnName.'" >';
				}
				$content .= '</a></div><div class="caption">';
				
				if(count($subtypes) > 0) {
					$content .= '<a href="'.$typeurl.'">';
				} else {
					$content .= '<a href="'.$categoryurl.'">';
				}
				
				$content .= '<h4 class="pt-3 pb-1">'.$type->EnName.'</h4></a><p>'.$productscount.' Products</p>';

				$content .= '</div></div></div>';
				
			}
		}
		echo $content;		
	}
	
	public function brands(Request $request) {
		$filter = $brandname = '';
		if(isset($request->filter)) {
			$filter = $request->filter;
		}
		if(isset($request->brandname)) {
			$brandname = $request->brandname;
		}
		return view('public.Product/brands', compact('filter', 'brandname'));
	}
	
	public function branditems($brand, Request $request) {
		
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		
		$join = '';
		
		$parentlevel = $catid = $bid = '';
		$products = $categories = $categoryids = $producttags = $parentcats = $subcats = [];
		
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		
		if(isset($request->cid)) {
			$catid = $request->cid;		
			if($catid > 0) {
				if($join != '') {
					$join .= ' AND Types IN ('.$catid.')';
				} else {
					$join .= 'Types IN ('.$catid.')';
				}
			}
		}
				
		$branddata = Brand::where('UniqueKey', '=', $brand)->first();
		if($branddata) {
			$bid = $branddata->BrandId;
			if($join != '') {
				
				$products = Product::where('Brand', '=', $bid)->whereRaw($join)->where('ProdStatus', '=', '1')->orderBy($sortby, 'DESC')->paginate($show);
				$producttags = Product::where('Brand', '=', $bid)->whereRaw($join)->where('ProdStatus', '=', '1')->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'ASC')->get();
			} else {
				
				$products = Product::where('Brand', '=', $bid)->where('ProdStatus', '=', '1')->orderBy($sortby, 'DESC')->paginate($show);
				$producttags = Product::where('Brand', '=', $bid)->where('ProdStatus', '=', '1')->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'ASC')->get();
			}
			$prodcategories = Product::where('Brand', '=', $bid)->select('Types')->get();			
			if($prodcategories) {
				foreach($prodcategories as $prodcategory) {
					
					$categoryids[] = $prodcategory->Types;
					
					/*$parentcats = Category::where('TypeId', '=', $prodcategory->Types)->where('TypeStatus', '=', '1')->select('ParentLevel')->first();					
					if($parentcats) {
						$parentcatid = $parentcats->ParentLevel;
						$subcats = Category::where('TypeId', '=', $parentcatid)->where('TypeStatus', '=', '1')->select('ParentLevel')->first(); 						
						if($subcats) {
							if($subcats->ParentLevel > 0) {
								$categoryids[] = $subcats->ParentLevel;
							} else {
								$categoryids[] = $parentcats->ParentLevel;
							}
						} else {
							$categoryids[] = $parentcats->ParentLevel;
						}
					}*/
					
				}
			}
			
			if(!empty($categoryids) && count($categoryids) > 0) {
				$categories = Category::whereIn('TypeId', $categoryids)->where('TypeStatus', '=', '1')->get();
			}
		}
		
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
		
		$adbanners = Bannerads::where('PageId', '=', 'brands')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		
		return view('public/Product.branditems', compact('products', 'categories', 'brand', 'sortby', 'show', 'page', 'catid', 'bid', 'favproducts', 'adbanners', 'producttags'));		
	}
	
	
	
	public function productdetails($product, Request $request) {
		if($product) {
									
			$productdetail = Product::where('UniqueKey', '=', $product)->first();

			if(!empty($productdetail)) {		
			
				$category = $categoryurl = $subategory = $subcategoryurl = $childcategory = $childcategoryurl = '';
				$types = $subtypes = $childtypes = $relatedproducts = [];
				$typeid = $productdetail->Types;
				$types = Category::where('TypeId', '=', $typeid)->first();
				if($types) {
					
					if($types->ParentLevel > 0) {
						$subtypes = Category::where('TypeId', '=', $types->ParentLevel)->first();
						if($subtypes) {
							if($subtypes->ParentLevel > 0) {
								$childtypes = Category::where('TypeId', '=', $subtypes->ParentLevel)->first();
								if($childtypes) {
									$category = $childtypes->EnName;
									$categoryurl = $childtypes->UniqueKey;
									$subcategory = $subtypes->EnName;
									$subcategoryurl = $subtypes->UniqueKey;
									$childcategory = $types->EnName;
									$childcategoryurl = $types->UniqueKey;
									
								}
							} else {
								$category = $subtypes->EnName;
								$categoryurl = $subtypes->UniqueKey;
								$subcategory = $types->EnName;
								$subcategoryurl = $types->UniqueKey;
								$childcategory = '';
								$childcategoryurl = '';
								
							}
						}
					} else {
						$category = $types->EnName;
						$categoryurl = $types->UniqueKey;
						$subcategory = '';
						$subcategoryurl = '';
						$childcategory = '';
						$childcategoryurl = '';
						
					}
				}
				
				$options = ProductOptions::where('Prod', '=', $productdetail->Id)->where('Status', '=', '1')->get();
				
				$galleries = ProductGallery::where('ProdId', '=', $productdetail->Id)->where('Status', '=', '1')->orderBy('DisplayOrder', 'ASC')->get();
				
				$reviews = ProductReviews::where('ProdId', '=', $productdetail->Id)->orderBy('created_at', 'desc')->get();
				$totalreviews = $rating = 0;
				if($reviews) {
					foreach($reviews as $review) {
						$totalreviews = $totalreviews + (int)$review->rating;
					}
					if($totalreviews > 0) {
						$rating = round(($totalreviews * 5) / 100);
					}
				}
				
				$relatedproducts = Product::where('Types', '=', $typeid)->where('UniqueKey', '!=', $product)->where('ProdStatus', '=', '1')->orderByRaw('RAND()')->take(6)->get();
				return view('public/Product.productdetails', compact('productdetail', 'category', 'categoryurl', 'subcategory', 'subcategoryurl', 'childcategory', 'childcategoryurl', 'relatedproducts', 'options', 'galleries', 'reviews', 'rating'));
			} else {
				$staticpage = PageContent::where('UniqueKey', '=', 'page-not-found')->first();
				$bannerads = Bannerads::where('ban_status', '=', '1')->where('PageId', '=', 'page-not-found')->orderBy('display_order', 'asc')->get();
				return view('public.staticpages', compact('staticpage', 'bannerads'));
			}
		}
	}
	
	public function promotions(Request $request) {
		
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		
		$categories = $brands = $adbanners = [];
		$catid = $bid = '';
		$parentid = 0;
				
		$typeproducts = DB::table('products')->select('Types')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->groupBy('Types');

		$typecategories = DB::table('types')			
			->joinSub($typeproducts, 'products', function ($join) {
				$join->on('types.TypeId', '=', 'products.Types');
			})->select('types.TypeId', 'types.ParentLevel')->get();
			
		$catids = [0];	
		if($typecategories) {
			foreach($typecategories as $typecategory) {
				/*if($typecategory->ParentLevel == 0) {
					$catids[] = $typecategory->TypeId;					
				} else {
					$topcategory = Category::where('TypeId', '=', $typecategory->ParentLevel)->where('TypeStatus', '=', '1')->select('TypeId', 'ParentLevel')->first();
					if($topcategory) {
						$grandtopcategory = Category::where('TypeId', '=', $topcategory->ParentLevel)->where('TypeStatus', '=', '1')->select('TypeId', 'ParentLevel')->first();
						if($grandtopcategory) {
							$catids[] = $grandtopcategory->TypeId;							
						} else {
							$catids[] = $topcategory->TypeId;							
						}
					}
				}*/
				$catids[] = $typecategory->TypeId;
			}
		}			
		
		if($catids) {
			$categories = Category::where('TypeStatus', '=', '1')->whereIn('TypeId', $catids)->orderBy('DisplayOrder', 'asc')->get();
			$typebrands = DB::table('products')->select('Brand')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereIn('Types', $catids)->groupBy('Brand');
		} else {
			$typebrands = DB::table('products')->select('Brand')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->groupBy('Brand');
		}
				
		
		$brands = DB::table('brands')						
			->joinSub($typebrands, 'products', function ($join) {
				$join->on('brands.BrandId', '=', 'products.Brand');
			})->get();
		
		$join = $parentcategory = '';
		$producttags = [];
		
		/*if(isset($request->cid)) {
			$catid = $request->cid;		
			$join .= 'Types = '.$catid;
			
			$pcategory = Category::where('TypeId', '=', $catid)->select('TypeId','ParentLevel')->first();
			if($pcategory->ParentLevel == 0) {
				$parentcategory = $catid;
			} else {
				$scategory = Category::where('TypeId', '=', $pcategory->ParentLevel)->select('TypeId','ParentLevel')->first();
				if($scategory) {
					$sscategory = Category::where('TypeId', '=', $scategory->ParentLevel)->select('TypeId','ParentLevel')->first();
					if($sscategory) {
						$parentcategory = $sscategory->TypeId;
					} else {
						$parentcategory = $scategory->TypeId;
					}
				} 
			}
		}*/
		
		if(isset($request->cid)) {
			$catid = $request->cid;		
			if($join != '') {
				$join .= ' AND Types IN ('.$catid.')';
			} else {
				$join .= 'Types IN ('.$catid.')';
			}
		}
		
		if(isset($request->bid)) {
			$bid = $request->bid;
			if($join != '') {
				$join .= ' AND Brand IN ('.$bid.')';
			} else {
				$join .= 'Brand IN ('.$bid.')';
			}
		}
		
		
		if($join) {
			
			$producttags = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'asc')->get();
			
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'desc')->paginate($show);
		}  else {
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);
			
			$producttags = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'asc')->get();
		}
		
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
		
		$adbanners = Bannerads::where('PageId', '=', 'promotions')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		
		return view('public/Product.promotions', compact('promoproducts', 'categories', 'brands', 'page', 'show', 'sortby', 'catid', 'bid', 'parentcategory', 'favproducts', 'producttags', 'adbanners'));
	}
	
	public function newarrivals(Request $request) {		
		$show = 60;
		$page = 1;
		$sortby = 'Id';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		
		$categories = $brands = $adbanners = [];
		$catid = $bid = '';
		$parentid = 0;
				
		$typeproducts = DB::table('products')->select('Types')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->groupBy('Types');

		$typecategories = DB::table('types')			
			->joinSub($typeproducts, 'products', function ($join) {
				$join->on('types.TypeId', '=', 'products.Types');
			})->select('types.TypeId', 'types.ParentLevel')->get();
			
		$catids = [0];	
		if($typecategories) {
			foreach($typecategories as $typecategory) {
				
				$catids[] = $typecategory->TypeId;
			}
		}			
		
		if($catids) {
			$categories = Category::where('TypeStatus', '=', '1')->whereIn('TypeId', $catids)->orderBy('DisplayOrder', 'asc')->get();
			$typebrands = DB::table('products')->select('Brand')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereIn('Types', $catids)->groupBy('Brand');
		} else {
			$typebrands = DB::table('products')->select('Brand')->where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->groupBy('Brand');
		}
				
		
		$brands = DB::table('brands')						
			->joinSub($typebrands, 'products', function ($join) {
				$join->on('brands.BrandId', '=', 'products.Brand');
			})->get();
		
		$join = $parentcategory = '';
		$producttags = [];
		
		
		
		if(isset($request->cid)) {
			$catid = $request->cid;		
			if($join != '') {
				$join .= ' AND Types IN ('.$catid.')';
			} else {
				$join .= 'Types IN ('.$catid.')';
			}
		}
		
		if(isset($request->bid)) {
			$bid = $request->bid;
			if($join != '') {
				$join .= ' AND Brand IN ('.$bid.')';
			} else {
				$join .= 'Brand IN ('.$bid.')';
			}
		}
		
		
		if($join) {			
			$producttags = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'asc')->get();
			
			$newproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'desc')->paginate($show);
		}  else {
			$newproducts = Product::where('ProdStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);			
			$producttags = Product::where('ProdStatus', '=', '1')->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'asc')->get();
		}
		
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
		
		$adbanners = Bannerads::where('PageId', '=', 'newarrivals')->where('ban_status', '=', '1')->orderBy('display_order', 'asc')->get();
		
		return view('public/Product.newarrivals', compact('newproducts', 'categories', 'brands', 'page', 'show', 'sortby', 'catid', 'bid', 'parentcategory', 'favproducts', 'producttags', 'adbanners'));
	}
	
	public function getproductslist(Request $request) {
		$join = '';
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		
		$isPromotion = 0;
		if(isset($request->IsPromotion)) {
			$isPromotion = $request->IsPromotion;
			if($isPromotion == 1) {
				if($join != '') {
					$join .= ' AND IsPromotion = '.$isPromotion;
				} else {
					$join .= 'IsPromotion = '.$isPromotion;
				}
			}
		}
		if(isset($request->cid)) {
			$catid = $request->cid;				
			if($join != '') {
				$join .= ' AND Types IN ('.$catid.')';
			} else {
				$join .= 'Types IN ('.$catid.')';
			}			
		}
		
		$searchkey = '';
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
			if($searchkey != '') {
				if($join != '') {
					$join .= ' AND EnName LIKE "%'.$searchkey.'%"';
				} else {
					$join .= 'EnName LIKE "%'.$searchkey.'%"';
				}
			}
		}
		
		if(isset($request->bid)) {
			$bid = $request->bid;
			if($join != '') {
				$join .= ' AND Brand IN ('.$bid.')';
			} else {
				$join .= 'Brand IN ('.$bid.')';
			}			
		}
		
		//echo $join;
		if($join) {			
			$products = Product::where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'desc')->paginate($show);			
		}  else {			
			$products = Product::where('ProdStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);
		}
		
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
		
		$content = '';
		if($products) {
			$roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
			
			foreach($products as $product) {
				$content .= '<div class="col-lg-3 col-md-6 col-6"><div class="tab-item"><div class="tab-img">';
				if($product->Quantity <= 0) {
					$content .= '<div class="box-no-product"><span class="boxAdjest">Out of Stock</span></div>';
				}
				$content .= '<div class="box-Added-product" id="success"'.$product->Id.'" style="display:none;"><span class="boxAdjest">Item has been Added into Your Cart</span></div>';
				$content .= '<div class="box-Added-product" id="setfav"'.$product->Id.'" style="display:none;"><span class="boxAdjest">Item has been Added into Your Favourite List</span></div>';
				$content .=	'<div class="box-no-product" id="setfaverror"'.$product->Id.'" style="display:none;"><span class="boxAdjest">Please Login to Add Favourite</span></div>';
				$url = url('/prod/'.$product->UniqueKey);
				$image = url('/uploads/product/'.$product->Image);
				$noimage = url('/images/noimage.png');
				$favimage = url('/images/it-fav.png');
				$content .= '<a href="'.$url.'">';
				if($product->Image != '') {
					$content .= '<img class="main-img img-fluid" src="'.$image.'" alt="'.$product->EnName.'">';
					$content .= '<img class="sec-img img-fluid" src="'.$image.'" alt="'.$product->EnName.'">';
				} else {
					$content .= '<img class="main-img img-fluid" src="'.$noimage.'" style="height:286px; width:100%;" alt="'.$product->EnName.'">';															
				}
				$content .= '</a>';
				
				if(in_array($product->Id, $favproducts)) {
					$content .= '<div class="heartIcon"><i class="fa fa-heart-o" aria-hidden="true" style="padding: 10px 15px; color: #999; font-size: 21px;"></i></div>';
				} else {
					$content .= '<div class="layer-box" id="heart'.$product->Id.'">';
					$content .= '<a href="javascript:void(0);" class="it-fav" data-toggle="tooltip" data-placement="left" title="Favourite" onclick="setfavorite('.$product->Id.')"><img src="'.$favimage.'" alt=""></a></div>';
				}
				$content .= '</div>';	
				$content .= '<div class="tab-heading">';
				$content .= '<p><a href="'.$url.'">';
				if(strlen($product->EnName) > 32) {
					$content .= substr($product->EnName, 0, 32).'...';
				} else {
					$content .= $product->EnName;
				}
				$content .= '</a></p>';
				$content .= '</div>';
				
				$content .= '<div class="img-content d-flex justify-content-between"><div class="wid_full">';
				
				$content .= '<ul class="list-unstyled list-inline price"><li class="list-inline-item">';
				
				$displayprice = $product->Price;
				$price = new \App\Models\Price();
				//$displayprice = $price->getPrice($product->Id);
				$actualprice = $price->getGroupPrice($product->Id);
				$displayprice = $price->getDiscountPrice($product->Id);
				$installmentPrice = $price->getInstallmentPrice($displayprice);
				
				$actualprice = $roundObj->roundDecimal($price->getGSTPrice($actualprice, 'SG'));
				$displayprice = $roundObj->roundDecimal($price->getGSTPrice($displayprice, 'SG'));
				$installmentPrice = $roundObj->roundDecimal($price->getInstallmentPrice($displayprice));
				
				$content .= 'S$'.number_format($displayprice, 2).'</li>';
				
				if($displayprice < $actualprice) {
					$content .= '<li class="list-inline-item strikeoutprice">S$'.number_format($actualprice, 2).'</li>';
				}
				
				$hoolahimg = url('/images/8dc4dab.png');
				
				$content .= '</ul>';
				$content .= '<div class="text-center mt-3">or installments of S$'.number_format($installmentPrice, 2).' with <img src="'.$hoolahimg.'" style="width:80px; vertical-align: middle;"></div>';
				$content .= '<div class="text-center mt-3">';
				
				if($product->Quantity > 0) {
					$options = \App\Models\ProductOptions::where('Prod', '=', $product->Id)->count();
					
					if($options > 0) {
						$content .= '<a href="'.$url.'" class="site-btn bg-dark mx-auto textyellow">More Details</a>';
					} else {
						$content .= '<a href="javascript:void(0);" onclick="addtocart('.$product->Id.');" class="site-btn bg-dark mx-auto textyellow">Add to Cart</a>';
					}
				} else {
					$content .= '<a href="javascript:void(0);" class="site-btn bg-dark mx-auto textyellow">Out of Stock</a>';
				}
				
				$content .= '</div></div>';
				$content .= '</div></div></div>';
			}
		}
		echo $content;
	}
	
	
	public function setOptionPrice(Request $request) {
		$productid = $request->prodid;
		$optionid = $request->optionid;	
		$displayprice = $request->displayprice;
		$oprice = new \App\Models\Price();
		$gstprice = $oprice->getGSTPrice($displayprice, 'SG');
		$oldprice = $oldgstprice = 0;
		$optionprice = 0;
		$isdiscount = 0;
		$option = ProductOptions::where('Id', '=', $optionid)->first();
		$roundObj = new \App\Services\OrderServices(new \App\Services\CartServices());
		if($option) {
			$optionprice = $option->Price;			
			
			$optionprice = $oprice->getOptionPrice($productid, $optionid);			
			$displayprice = $optionprice + $displayprice;
			$gstprice = $oprice->getGSTPrice($displayprice, 'SG');
			
			
			$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
			if($globalsettings) {
				$globaldiscount = $globalsettings->discount_percentage;
				$promodiscount = $globalsettings->promo_discount_percentage;
			}
		
			$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Types', 'Brand', 'Price')->first();
			
			$brand_general_dis_included = 1;
			$brand_allow_group_price = 1;
			
			if($product->Brand > 0) {
				$brand = Brand::where('BrandId', '=', $product->Brand)->select('exclude_global_dis', 'dis_type')->first();
				if($brand) {
					if($brand->exclude_global_dis == 1) {
						$brand_general_dis_included = 0;
					}
					if($brand->dis_type == 0) {
						$brand_allow_group_price = 0;
					}
				}
			}
			
			$brandgroup = BrandPrice::where('Brand', '=', $product->Brand)->where('GroupId', '=', '1')->where('Status', '=', '1')->first();
			
			if((!empty($brandgroup) && $brand_allow_group_price == 1) || ($globaldiscount > 0 && $brand_general_dis_included == 1) || ($promodiscount > 0 && $brand_general_dis_included == 1 && $product->IsPromotion == 1)) {
				$isdiscount = 1;
				$optionprices = ProductOptions::where('Id', '=', $optionid)->where('Prod', '=', $productid)->select('Price')->first();
				if($optionprices) {
					$oldprice = $product->Price + $optionprices->Price;
					$oldgstprice = $oprice->getGSTPrice($oldprice, 'SG');
				}	
			} 
			
		}		
		echo number_format($roundObj->roundDecimal($displayprice), 2)."#".number_format($gstprice, 2)."#".$isdiscount."#".number_format($oldprice, 2)."#".number_format($oldgstprice, 2);
	}
	
	public function search(Request $request) {
		$searchkey = $request->searchkey;
		if($searchkey != '') {
		$show = 20;
		$page = 1;
		$sortby = 'DisplayOrder';
		if(isset($request->page)) {
			$page = $request->page;
		}
		if(isset($request->show)) {
			$show = $request->show;
		}
		if(isset($request->sortby)) {
			$sortby = $request->sortby;
		}
		
		$categories = $brands = $producttags = [];
		$catid = $bid = '';
		$parentid = 0;
		
		$search = '';
		if($searchkey) {
			
			$search = '(EnName LIKE "%'.addslashes($searchkey).'%" OR ProdCode LIKE "%'.addslashes($searchkey).'%")';			
			
		}
		
		
		$prod = new Product();
		$catids = $prod->getcategories($searchkey, 0);
		
		if($catids) {
			$categories = Category::where('TypeStatus', '=', '1')->whereIn('TypeId', $catids)->orderBy('DisplayOrder', 'asc')->get();
			$typebrands = DB::table('products')->select('Brand')->whereRaw($search)->where('ProdStatus', '=', '1')->whereIn('Types', $catids)->groupBy('Brand'); 
		} else {
			$typebrands = DB::table('products')->select('Brand')->whereRaw($search)->where('ProdStatus', '=', '1')->groupBy('Brand'); 
		}
		
		$brands = DB::table('brands')						
			->joinSub($typebrands, 'products', function ($join) {
				$join->on('brands.BrandId', '=', 'products.Brand');
			})->get();
		
		$join = $parentcategory = '';
		
		if(isset($request->cid)) {
			$catid = $request->cid;		
			if($join != '') {
				$join .= ' AND Types IN ('.$catid.')';
			} else {
				$join .= 'Types IN ('.$catid.')';
			}
		}
		
		if(isset($request->bid)) {
			$bid = $request->bid;
			if($join != '') {
				$join .= ' AND Brand IN ('.$bid.')';
			} else {
				$join .= 'Brand IN ('.$bid.')';
			}
		}
		
		if($join) {
			$promoproducts = Product::whereRaw($search)->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'desc')->paginate($show);
			
			$producttags = Product::whereRaw($search)->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
		}  else {
			$promoproducts = Product::whereRaw($search)->where('ProdStatus', '=', '1')->orderBy($sortby, 'desc')->paginate($show);
			
			$producttags = Product::whereRaw($search)->where('ProdStatus', '=', '1')->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
		}
		
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
		
		return view('public/Product.search', compact('promoproducts', 'categories', 'brands', 'page', 'show', 'sortby', 'catid', 'bid', 'parentcategory', 'searchkey', 'favproducts', 'producttags'));
		} else {
			return redirect('/');
		}
	}
	
	public function productrating(Request $request) {
		$custid = $request->custid;
		$prodid = $request->prodid;
		$rating = $request->rating;
		$comments = $request->comments;
		$chkexist = ProductReviews::where('ProdId', '=', $prodid)->where('CustomerId', '=', $custid)->first();
		if($chkexist) {
			ProductReviews::where('ProdId', '=', $prodid)->where('CustomerId', '=', $custid)->update(array('rating' => $rating, 'comments' => $comments));
		} else {
			$review = new ProductReviews;
			$review->ProdId = $prodid;
			$review->CustomerId = $custid;
			$review->rating = $rating;
			$review->comments = $comments;
			$review->status = 1;
			$review->save();
		}
		echo 'Success';
	}	
	
	public function enquiryemailus(Request $request) {
		$name = $request->name;
		$email = $request->email;
		$phone =$request->phone;
		$message = $request->message;
		$productname = $request->productname;
		
		$settings = Settings::where('id', '=', '1')->first();
		$adminemail = $settings->admin_email;
		$companyname = $settings->company_name;
		$ccemail = $settings->cc_email;
		
		$logo = url('/').'/img/logo.png';	
		$logo = '<img src="'.$logo.'">';
		
		$emailsubject = $emailcontent = '';
		$emailtemplate = EmailTemplate::where('template_type', '=', '7')->where('status', '=', '1')->first();
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
			$emailcontent = str_replace('{productdetails}', $productname, $emailcontent);
			
			$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
			$headers .='Reply-To: '. $adminemail . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
			#@mail($adminemail, $emailsubject, $emailcontent, $headers);
			#@mail($ccemail, $emailsubject, $emailcontent, $headers);
			Mail::send([],[], function($message) use ($adminemail, $ccemail, $emailsubject, $emailcontent) {
                $message->to($adminemail)
                        ->cc($ccemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
            });
		}
		echo 'Success';
	}
	
	public function submitqa(Request $request) {
		$name = $request->name;
		$email = $request->email;
		$question = $request->question;
		$productcode = $request->productcode;
		$productname = $request->productname;
		
		$settings = Settings::where('id', '=', '1')->first();
		$adminemail = $settings->admin_email;
		$companyname = $settings->company_name;
		$ccemail = $settings->cc_email;
		
		$logo = url('/').'/img/logo.png';	
		$logo = '<img src="'.$logo.'">';
		
		$emailsubject = $emailcontent = '';
		$emailtemplate = EmailTemplate::where('template_type', '=', '15')->where('status', '=', '1')->first();
		if($emailtemplate) {
			$emailsubject = $emailtemplate->subject;
			$emailcontent = $emailtemplate->content;
			
			$emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
			$emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
			$emailcontent = str_replace('{logo}', $logo, $emailcontent);
			$emailcontent = str_replace('{name}', $name, $emailcontent);
			$emailcontent = str_replace('{email}', $email, $emailcontent);			
			$emailcontent = str_replace('{question}', $question, $emailcontent);
			$emailcontent = str_replace('{productname}', $productname, $emailcontent);
			$emailcontent = str_replace('{productcode}', $productcode, $emailcontent);
			
			$headers = 'From: '.$companyname.' '.$adminemail.'' . "\r\n" ;
			$headers .='Reply-To: '. $adminemail . "\r\n" ;
			$headers .='X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
			//@mail('balamurugan.sk@gmail.com', $emailsubject, $emailcontent, $headers);
			#@mail($adminemail, $emailsubject, $emailcontent, $headers);
			#@mail($ccemail, $emailsubject, $emailcontent, $headers);
			Mail::send([],[], function($message) use ($adminemail, $ccemail, $emailsubject, $emailcontent) {
                $message->to($adminemail)
                        ->cc($ccemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
            });
		}
		echo 'Success';
	}
	
	public function updateLatestProducts($tablename) {
		$tabledata = DB::select('SELECT * FROM '.$tablename);
		if($tabledata) {
			foreach($tabledata as $data) {
				Product::where('Id', '=', $data->Id)->update(array('ProdType' => $data->ProdType, 'UniqueKey' => $data->UniqueKey, 'EnName' => $data->EnName, 'SKU' => $data->SKU, 'Code' => $data->Code, 'Size' => $data->Size, 'EnShortDesc' => $data->EnShortDesc, 'Color' => $data->Color, 'Specs' => $data->Specs, 'StandardPrice' => $data->StandardPrice, 'Price' => $data->Price, 'Vendor' => $data->Vendor, 'Supplier' => $data->Supplier, 'Quantity' => $data->Quantity, 'cust_qty_per_day' => $data->cust_qty_per_day, 'ShippingBox' => $data->ShippingBox, 'Weight' => $data->Weight, 'Dimension' => $data->Dimension, 'MOQ' => $data->MOQ, 'unspsc' => $data->unspsc, 'gebiz_item' => $data->gebiz_item, 'ProdStatus' => $data->ProdStatus, 'Types' => $data->Types, 'Brand' => $data->Brand, 'Image' => $data->Image, 'LargeImage' => $data->LargeImage, 'MobileImage' => $data->MobileImage, 'MobileLargeImage' => $data->MobileLargeImage, 'Tds' => $data->Tds, 'Sds' => $data->Sds, 'IsFeatured' => $data->IsFeatured, 'IsPromotion' => $data->IsPromotion, 'IsOverseasShippingTrue' => $data->IsOverseasShippingTrue, 'Video' => $data->Video, 'Created' => $data->Created, 'Modified' => $data->Modified, 'DisplayOrder' => $data->DisplayOrder, 'EnLongDesc' => $data->EnLongDesc, 'EnInfo' => $data->EnInfo, 'MetaTitle' => $data->MetaTitle, 'MetaKey' => $data->MetaKey, 'MetaDesc' => $data->MetaDesc, 'ProdCode' => $data->ProdCode, 'ProdTags' => $data->ProdTags));
				echo $data->Id.' updated<br>';
			}
		}
	}
	
}
