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
use App\Models\Bannerads;
use Session;
use DB;

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
				$products = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'asc')->paginate($show);

				$producttags = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
			} else {
				$products = Product::where('Types', '=', $categoryid)->where('ProdStatus', '=', '1')->orderBy($sortby, 'asc')->paginate($show);
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
				
        return view('public/Product.index', compact('products', 'parentname', 'parenturl', 'parentlevel', 'categoryname', 'grandparenturl', 'grandparentname', 'page', 'show', 'sortby', 'urlkey', 'brands', 'categories', 'bid', 'favproducts', 'producttags', 'adbanners'));
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
			
			$types = Category::where('ParentLevel', '=', $categorydata->TypeId)->where('TypeStatus', '=', '1')->orderBy($sortby, 'asc')->paginate($show);
			$categories = Category::where('ParentLevel', '=', $categorydata->TypeId)->where('TypeStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
			
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
		
		$parentlevel = $catid = $bid = 0;
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
			if($join != '') {
				$join .= ' AND Types IN ('.$catid.')';
			} else {
				$join .= 'Types IN ('.$catid.')';
			}
		}
				
		
		$branddata = Brand::where('UniqueKey', '=', $brand)->first();
		if($branddata) {
			$bid = $branddata->BrandId;
			if($join != '') {
				$products = Product::where('Brand', '=', $bid)->whereRaw($join)->where('ProdStatus', '=', '1')->orderBy($sortby, 'ASC')->paginate($show);
				$producttags = Product::where('Brand', '=', $bid)->whereRaw($join)->where('ProdStatus', '=', '1')->select('ProdTags')->groupBy('ProdTags')->orderBy($sortby, 'ASC')->get();
			} else {
				$products = Product::where('Brand', '=', $bid)->where('ProdStatus', '=', '1')->orderBy($sortby, 'ASC')->paginate($show);
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
			
			$relatedproducts = Product::where('Types', '=', $typeid)->where('UniqueKey', '!=', $product)->orderByRaw('RAND()')->take(4)->get();
			return view('public/Product.productdetails', compact('productdetail', 'category', 'categoryurl', 'subcategory', 'subcategoryurl', 'childcategory', 'childcategoryurl', 'relatedproducts', 'options', 'galleries', 'reviews', 'rating'));
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
			
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'asc')->paginate($show);
		}  else {
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy($sortby, 'asc')->paginate($show);
			
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
	
	public function getproductslist(Request $request) {
		
	}
	
	public function setOptionPrice(Request $request) {
		$productid = $request->ProdId;
		$optionid = $request->optionid;	
		$displayprice = $request->displayprice;
		$optionprice = 0;	
				
		$option = ProductOptions::where('Id', '=', $optionid)->first();
		if($option) {
			$optionprice = $option->Price;
			$displayprice = $optionprice + $displayprice;
		}
		echo number_format($displayprice, 2);
	}
	
	public function search(Request $request) {
		$searchkey = $request->searchkey;
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
			

		$prod = new Product();
		$catids = $prod->getcategories($searchkey, 0);
		
		if($catids) {
			$categories = Category::where('TypeStatus', '=', '1')->whereIn('TypeId', $catids)->orderBy('DisplayOrder', 'asc')->get();
			$typebrands = DB::table('products')->select('Brand')->where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->whereIn('Types', $catids)->groupBy('Brand'); 
		} else {
			$typebrands = DB::table('products')->select('Brand')->where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->groupBy('Brand'); 
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
			$promoproducts = Product::where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->whereRaw($join)->orderBy($sortby, 'asc')->paginate($show);
			
			$producttags = Product::where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->whereRaw($join)->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
		}  else {
			$promoproducts = Product::where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->orderBy($sortby, 'asc')->paginate($show);
			
			$producttags = Product::where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->select('ProdTags')->orderBy($sortby, 'asc')->groupBy('ProdTags')->get();
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
			@mail('balamurugan.sk@gmail.com', $emailsubject, $emailcontent, $headers);
			@mail($ccemail, $emailsubject, $emailcontent, $headers);
		}
		echo 'Success';
	}
	
}
