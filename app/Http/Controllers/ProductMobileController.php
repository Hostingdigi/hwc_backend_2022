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
use App\Models\Customer;
use DB;

class ProductMobileController extends Controller
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
    	
	public function promotionalitems(Request $request) {
		$data = $promoitems = [];
		$productprice = 0;
		$page = 1;
		$skip = 0;
		$take = 20;
		$orderby = 'desc';
		$sortby = 'DisplayOrder';
		
		if(isset($request->page)) {
			$page = $request->page;
			if($page > 1) {
				$skip = (($page - 1) * $take) + 1;
				$take = $page * $take;
			}
		}
		
		$searchkey = $join = '';
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
		}
		
		if(isset($request->orderby)) {
			$orderby = $request->orderby;
			if($orderby == 'ascending') {
				$sortby = 'EnName';
				$orderby = 'asc';
			} elseif($orderby == 'descending') {
				$sortby = 'EnName';
				$orderby = 'desc';
			} elseif($orderby == 'lowtohigh') {
				$sortby = 'Price';
				$orderby = 'asc';
			} elseif($orderby == 'hightolow') {
				$sortby = 'Price';
				$orderby = 'desc';
			}
		}
				
		//$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		if($searchkey != '') {
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%'.$searchkey.'%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		} else {
			$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		}
		if($promoproducts) {
			$x = 0;
			foreach($promoproducts as $promoproduct) {
				$promoitems[$x]['id'] = $promoproduct->Id;
				$promoitems[$x]['urlkey'] = $promoproduct->UniqueKey;
				$promoitems[$x]['name'] = $promoproduct->EnName;
				$promoitems[$x]['size'] = $promoproduct->Size;
				$promoitems[$x]['shortdesc'] = $promoproduct->EnShortDesc;
				$promoitems[$x]['color'] = $promoproduct->Color;
				$promoitems[$x]['specification'] = $promoproduct->Specs;
				/*$promoitems[$x]['standardprice'] = $promoproduct->StandardPrice;
				
				$price = new \App\Models\Price();
				$productprice = $price->getPrice($promoproduct->Id);
				$promoitems[$x]['price'] = $productprice;*/
				
				$price = new \App\Models\Price();
						
				$productprice = $promoproduct->Price;
				$actualprice = $price->getGroupPrice($promoproduct->Id);
				$productprice = $price->getDiscountPrice($promoproduct->Id);
				$installmentPrice = $price->getInstallmentPrice($productprice);
										
				$promoitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
				$promoitems[$x]['price'] = number_format($productprice, 2, '.', '');
				$promoitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
				
				$promoitems[$x]['qty'] = $promoproduct->Quantity;
				$promoitems[$x]['cust_qty'] = $promoproduct->cust_qty_per_day;
				$promoitems[$x]['shippingbox'] = $promoproduct->ShippingBox;
				$promoitems[$x]['weight'] = $promoproduct->Weight;
				$promoitems[$x]['dimension'] = $promoproduct->Dimension;
				$promoitems[$x]['categoryid'] = $promoproduct->Types;
				$promoitems[$x]['brand'] = $promoproduct->Brand;
				if($promoproduct->MobileImage != '') {
					$promoitems[$x]['image'] = url('/uploads/product').'/'.$promoproduct->MobileImage;
				} else {
					$promoitems[$x]['image'] = url('/images/noimage.png');
				}
				if($promoproduct->MobileLargeImage != '') {
					$promoitems[$x]['largeimage'] = url('/uploads/product/large').'/'.$promoproduct->MobileLargeImage;
				} else {
					$promoitems[$x]['largeimage'] = url('/images/noimage.png');
				}
				$promoitems[$x]['video'] = $promoproduct->Video;
				$promoitems[$x]['description'] = $promoproduct->EnInfo;		
				
				$options = ProductOptions::where('Prod', '=', $promoproduct->Id)->where('Status', '=', '1')->get();
				
				$promoitems[$x]['optionscount'] = count($options);	
				
				if($options) {
					$o = 0;
					foreach($options as $option) {
						$promoitems[$x]['options'][$o]['optionid'] = $option->Id;
						$promoitems[$x]['options'][$o]['name'] = $option->Title;
						$oprice = new \App\Models\Price();
						$optionprice = $oprice->getOptionPrice($promoproduct->Id, $option->Id);
						$promoitems[$x]['options'][$o]['price'] = $optionprice;
						//$promoitems[$x]['options'][$o]['price'] = $option->Price;
						$promoitems[$x]['options'][$o]['qty'] = $option->Quantity;
						$promoitems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
						$promoitems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
						$promoitems[$x]['options'][$o]['weight'] = $option->Weight;
						++$o;
					}
				}				
				
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => $promoitems]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => '']);
		}
		
		
		return $data;
	}
	
	public function promotionalitemnames() {
		$data = [];
		$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		
		if($promoproducts) {
			$x = 0;
			foreach($promoproducts as $promoproduct) {				
				$promoitems[$x]['name'] = $promoproduct->EnName;
				++$x;
			}
			$data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => $promoitems]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => '']);
		}
		return $data;
	}
	
	public function branditems(Request $request) {
		$data = $branditems = [];
		$productprice = 0;
		$brand = $request->brand;
		$page = 1;
		$skip = 0;
		$take = 20;
		if(isset($request->page)) {
			$page = $request->page;
			if($page > 1) {
				$skip = (($page - 1) * $take) + 1;
				$take = $page * $take;
			}
		}
		
		$orderby = 'desc';
		$sortby = 'DisplayOrder';
		
		$searchkey = $join = '';
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
		}
		
		if(isset($request->orderby)) {
			$orderby = $request->orderby;
			if($orderby == 'ascending') {
				$sortby = 'EnName';
				$orderby = 'asc';
			} elseif($orderby == 'descending') {
				$sortby = 'EnName';
				$orderby = 'desc';
			} elseif($orderby == 'lowtohigh') {
				$sortby = 'Price';
				$orderby = 'asc';
			} elseif($orderby == 'hightolow') {
				$sortby = 'Price';
				$orderby = 'desc';
			}
		}
		
		//$brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		
		if($searchkey != '') {
			$brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%'.$searchkey.'%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		} else {		
			$brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		}
		if($brandproducts) {
			$x = 0;
			foreach($brandproducts as $brandproduct) {
				$branditems[$x]['id'] = $brandproduct->Id;
				$branditems[$x]['urlkey'] = $brandproduct->UniqueKey;
				$branditems[$x]['name'] = $brandproduct->EnName;
				$branditems[$x]['size'] = $brandproduct->Size;
				$branditems[$x]['shortdesc'] = $brandproduct->EnShortDesc;
				$branditems[$x]['color'] = $brandproduct->Color;
				$branditems[$x]['specification'] = $brandproduct->Specs;
				/*$branditems[$x]['standardprice'] = $brandproduct->StandardPrice;
				
				$price = new \App\Models\Price();
				$productprice = $price->getPrice($brandproduct->Id);
				$branditems[$x]['price'] = $productprice;*/
				
				$price = new \App\Models\Price();
						
				$productprice = $brandproduct->Price;
				$actualprice = $price->getGroupPrice($brandproduct->Id);
				$productprice = $price->getDiscountPrice($brandproduct->Id);
				$installmentPrice = $price->getInstallmentPrice($productprice);
										
				$branditems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
				$branditems[$x]['price'] = number_format($productprice, 2, '.', '');
				$branditems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
				
				$branditems[$x]['qty'] = $brandproduct->Quantity;
				$branditems[$x]['cust_qty'] = $brandproduct->cust_qty_per_day;
				$branditems[$x]['shippingbox'] = $brandproduct->ShippingBox;
				$branditems[$x]['weight'] = $brandproduct->Weight;
				$branditems[$x]['dimension'] = $brandproduct->Dimension;
				$branditems[$x]['categoryid'] = $brandproduct->Types;
				$branditems[$x]['brand'] = $brandproduct->Brand;
				if($brandproduct->MobileImage != '') {
					$branditems[$x]['image'] = url('/uploads/product').'/'.$brandproduct->MobileImage;
				} else {
					$branditems[$x]['image'] = url('/images/noimage.png');
				}
				if($brandproduct->MobileLargeImage != '') {
					$branditems[$x]['largeimage'] = url('/uploads/product/large').'/'.$brandproduct->MobileLargeImage;
				} else {
					$branditems[$x]['largeimage'] = url('/images/noimage.png');
				}
				$branditems[$x]['video'] = $brandproduct->Video;
				$branditems[$x]['description'] = $brandproduct->EnInfo;	

				$options = ProductOptions::where('Prod', '=', $brandproduct->Id)->where('Status', '=', '1')->get();
				
				$branditems[$x]['optionscount'] = count($options);	
				
				if($options) {
					$o = 0;
					foreach($options as $option) {
						$branditems[$x]['options'][$o]['optionid'] = $option->Id;
						$branditems[$x]['options'][$o]['name'] = $option->Title;
						$oprice = new \App\Models\Price();
						$optionprice = $oprice->getOptionPrice($brandproduct->Id, $option->Id);
						$branditems[$x]['options'][$o]['price'] = $optionprice;
						//$branditems[$x]['options'][$o]['price'] = $option->Price;
						$branditems[$x]['options'][$o]['qty'] = $option->Quantity;
						$branditems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
						$branditems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
						$branditems[$x]['options'][$o]['weight'] = $option->Weight;
						++$o;
					}
				}	
				
				++$x;	
			}
			$data = response()->json(['response' => 'success', 'message' => 'Brand Items', 'branditems' => $branditems]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Brand Items', 'branditems' => '']);
		}
		
		
		return $data;
	}
	
	public function categoryitems(Request $request) {
		$data = $categoryitems = [];
		$productprice = 0;
		$category = $request->category;
		$page = 1;
		$skip = 0;
		$take = 20;
		if(isset($request->page)) {
			$page = $request->page;
			if($page > 1) {
				$skip = (($page - 1) * $take) + 1;
				$take = $page * $take;
			}
		}
		
		$orderby = 'desc';
		$sortby = 'DisplayOrder';
		
		$searchkey = $join = '';
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
		}
		
		if(isset($request->orderby)) {
			$orderby = $request->orderby;
			if($orderby == 'ascending') {
				$sortby = 'EnName';
				$orderby = 'asc';
			} elseif($orderby == 'descending') {
				$sortby = 'EnName';
				$orderby = 'desc';
			} elseif($orderby == 'lowtohigh') {
				$sortby = 'Price';
				$orderby = 'asc';
			} elseif($orderby == 'hightolow') {
				$sortby = 'Price';
				$orderby = 'desc';
			}
		}
		
		//$categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		
		if($searchkey != '') {
			$categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%'.$searchkey.'%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		} else {
			$categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		}
		if($categoryproducts) {
			$x = 0;
			foreach($categoryproducts as $categoryproduct) {
				$categoryitems[$x]['id'] = $categoryproduct->Id;
				$categoryitems[$x]['urlkey'] = $categoryproduct->UniqueKey;
				$categoryitems[$x]['name'] = $categoryproduct->EnName;
				$categoryitems[$x]['size'] = $categoryproduct->Size;
				$categoryitems[$x]['shortdesc'] = $categoryproduct->EnShortDesc;
				$categoryitems[$x]['color'] = $categoryproduct->Color;
				$categoryitems[$x]['specification'] = $categoryproduct->Specs;
				/*$categoryitems[$x]['standardprice'] = $categoryproduct->StandardPrice;
				
				$price = new \App\Models\Price();
				$productprice = $price->getPrice($categoryproduct->Id);
				$categoryitems[$x]['price'] = $productprice;*/
				
				$price = new \App\Models\Price();
						
				$productprice = $categoryproduct->Price;
				$actualprice = $price->getGroupPrice($categoryproduct->Id);
				$productprice = $price->getDiscountPrice($categoryproduct->Id);
				$installmentPrice = $price->getInstallmentPrice($productprice);
										
				$categoryitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
				$categoryitems[$x]['price'] = number_format($productprice, 2, '.', '');
				$categoryitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
				
				$categoryitems[$x]['qty'] = $categoryproduct->Quantity;
				$categoryitems[$x]['cust_qty'] = $categoryproduct->cust_qty_per_day;
				$categoryitems[$x]['shippingbox'] = $categoryproduct->ShippingBox;
				$categoryitems[$x]['weight'] = $categoryproduct->Weight;
				$categoryitems[$x]['dimension'] = $categoryproduct->Dimension;
				$categoryitems[$x]['categoryid'] = $categoryproduct->Types;
				$categoryitems[$x]['brand'] = $categoryproduct->Brand;
				if($categoryproduct->MobileImage != '') {
					$categoryitems[$x]['image'] = url('/uploads/product').'/'.$categoryproduct->MobileImage;
				} else {
					$categoryitems[$x]['image'] = url('/images/noimage.png');
				}
				if($categoryproduct->MobileLargeImage != '') {
					$categoryitems[$x]['largeimage'] = url('/uploads/product/large').'/'.$categoryproduct->MobileLargeImage;
				} else {
					$categoryitems[$x]['largeimage'] = url('/images/noimage.png');
				}
				$categoryitems[$x]['video'] = $categoryproduct->Video;
				$categoryitems[$x]['description'] = $categoryproduct->EnInfo;

				$options = ProductOptions::where('Prod', '=', $categoryproduct->Id)->where('Status', '=', '1')->get();
				
				$categoryitems[$x]['optionscount'] = count($options);
				
				if($options) {
					$o = 0;
					foreach($options as $option) {
						$categoryitems[$x]['options'][$o]['optionid'] = $option->Id;
						$categoryitems[$x]['options'][$o]['name'] = $option->Title;
						$oprice = new \App\Models\Price();
						$optionprice = $oprice->getOptionPrice($categoryproduct->Id, $option->Id);
						$categoryitems[$x]['options'][$o]['price'] = $optionprice;
						//$categoryitems[$x]['options'][$o]['price'] = $option->Price;
						$categoryitems[$x]['options'][$o]['qty'] = $option->Quantity;
						$categoryitems[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
						$categoryitems[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
						$categoryitems[$x]['options'][$o]['weight'] = $option->Weight;
						++$o;
					}
				}	
				
				++$x;	
			}
			$data = response()->json(['response' => 'success', 'message' => 'Category Items', 'categoryitems' => $categoryitems]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Category Items', 'categoryitems' => '']);
		}		
		
		return $data;
	}
	
	public function itemdetails(Request $request) {
		$data = $itemdetails = [];
		$productprice = 0;
		$productid = $request->productid;
		$products = Product::where('Id', '=', $productid)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		if($products) {
			$x = 0;
			foreach($products as $product) {
				$itemdetails[$x]['id'] = $product->Id;
				$itemdetails[$x]['urlkey'] = $product->UniqueKey;
				$itemdetails[$x]['name'] = $product->EnName;
				$itemdetails[$x]['size'] = $product->Size;
				$itemdetails[$x]['shortdesc'] = $product->EnShortDesc;
				$itemdetails[$x]['color'] = $product->Color;
				$itemdetails[$x]['specification'] = $product->Specs;
				/*$itemdetails[$x]['standardprice'] = $product->StandardPrice;
				
				$price = new \App\Models\Price();
				$productprice = $price->getPrice($product->Id);
				$itemdetails[$x]['price'] = $productprice;*/
				
				$price = new \App\Models\Price();
						
				$productprice = $product->Price;
				$actualprice = $price->getGroupPrice($product->Id);
				$productprice = $price->getDiscountPrice($product->Id);
				$installmentPrice = $price->getInstallmentPrice($productprice);
										
				$itemdetails[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
				$itemdetails[$x]['price'] = number_format($productprice, 2, '.', '');
				$itemdetails[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
				
				$itemdetails[$x]['qty'] = $product->Quantity;
				$itemdetails[$x]['cust_qty'] = $product->cust_qty_per_day;
				$itemdetails[$x]['shippingbox'] = $product->ShippingBox;
				$itemdetails[$x]['weight'] = $product->Weight;
				$itemdetails[$x]['dimension'] = $product->Dimension;
				$itemdetails[$x]['categoryid'] = $product->Types;
				$itemdetails[$x]['brand'] = $product->Brand;
				if($product->MobileImage != '') {
					$itemdetails[$x]['image'] = url('/uploads/product').'/'.$product->MobileImage;
				} else {
					$itemdetails[$x]['image'] = url('/images/noimage.png');
				}
				if($product->MobileLargeImage != '') {
					$itemdetails[$x]['largeimage'] = url('/uploads/product/large').'/'.$product->MobileLargeImage;
				} else {
					$itemdetails[$x]['largeimage'] = url('/images/noimage.png');
				}
				
				if($product->Tds != '') {
					$itemdetails[$x]['tds'] = url('/uploads/product/tds').'/'.$product->Tds;
				} else {
					$itemdetails[$x]['tds'] = '';
				}
				
				if($product->Sds != '') {
					$itemdetails[$x]['sds'] = url('/uploads/product/sds').'/'.$product->Sds;
				} else {
					$itemdetails[$x]['sds'] = '';
				}
				
				$itemdetails[$x]['video'] = $product->Video;
				$itemdetails[$x]['description'] = $product->EnInfo;

				$options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();
				
				$itemdetails[$x]['optionscount'] = count($options);
				
				if($options) {
					$o = 0;
					foreach($options as $option) {
						$itemdetails[$x]['options'][$o]['optionid'] = $option->Id;
						$itemdetails[$x]['options'][$o]['name'] = $option->Title;
						$oprice = new \App\Models\Price();
						$optionprice = $oprice->getOptionPrice($product->Id, $option->Id);
						$itemdetails[$x]['options'][$o]['price'] = $optionprice;
						//$itemdetails[$x]['options'][$o]['price'] = $option->Price;
						$itemdetails[$x]['options'][$o]['qty'] = $option->Quantity;
						$itemdetails[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
						$itemdetails[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
						$itemdetails[$x]['options'][$o]['weight'] = $option->Weight;
						++$o;
					}
				}	

				$galleries = ProductGallery::where('ProdId', '=', $productid)->where('Status', '=', '1')->orderBy('DisplayOrder', 'ASC')->get();
				
				$itemdetails[$x]['gallerycount'] = count($galleries);
				
				if($galleries) {
					$g = 0;
					foreach($galleries as $gallery) {
						$itemdetails[$x]['galleries'][$g]['name'] = $gallery->Title;
						if($gallery->Image) {
							$itemdetails[$x]['galleries'][$g]['Image'] = url('/uploads/product').'/'.$gallery->Image;
						} else {
							$itemdetails[$x]['galleries'][$g]['Image'] = url('/images/noimage.png');
						}
						if($gallery->LargeImage) {
							$itemdetails[$x]['galleries'][$g]['LargeImage'] = url('/uploads/product/large').'/'.$gallery->LargeImage;
						} else {
							$itemdetails[$x]['galleries'][$g]['LargeImage'] = url('/images/noimage.png');
						}
						++$g;
					}
				}
				
				$reviews = ProductReviews::where('ProdId', '=', $productid)->where('status', '=', '1')->orderBy('created_at', 'asc')->get();
				
				$itemdetails[$x]['reviewcount'] = count($reviews);
				
				
				$rating = 0;
				if($reviews) {
					$r = 0;
					foreach($reviews as $review) {
						$customer = Customer::where('cust_id', '=', $review->CustomerId)->first();
						if($customer) {
							$itemdetails[$x]['reviews'][$r]['customer'] = $customer->cust_firstname.' '.$customer->cust_lastname;
						} else {
							$itemdetails[$x]['reviews'][$r]['customer'] = '';
						}
						$itemdetails[$x]['reviews'][$r]['rating'] = $review->rating;
						$itemdetails[$x]['reviews'][$r]['comments'] = $review->comments;
						$itemdetails[$x]['reviews'][$r]['review_date'] = date('d M Y', strtotime($review->created_at));
						
						foreach($reviews as $review) {
							$rating = (int)$rating + (int)$review->rating;					
						}					
						
						++$r;
					}
				}
				
				if($rating > 0) {
					$rating = round(($rating * 5) / 100);				
				}
				
				$itemdetails[$x]['startrating'] = $rating;
				
				++$x;	
			}
			$data = response()->json(['response' => 'success', 'message' => 'Product Details', 'itemdetails' => $itemdetails]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'Product Details', 'itemdetails' => '']);
		}		
		
		return $data;
	}
	
	public function childcategorywithproducts(Request $request) {
		$data = $childcategories = $grandchildcategories = $categoryproducts = $categoryitems = [];
		$category = $request->category;
		
		$page = 1;
		$skip = 0;
		$take = 20;
		if(isset($request->page)) {
			$page = $request->page;
			if($page > 1) {
				$skip = (($page - 1) * $take) + 1;
				$take = $page * $take;
			}
		}
		
		$orderby = 'desc';
		$sortby = 'DisplayOrder';
		
		$searchkey = $join = '';
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
		}
		
		if(isset($request->orderby)) {
			$orderby = $request->orderby;
			if($orderby == 'ascending') {
				$sortby = 'EnName';
				$orderby = 'asc';
			} elseif($orderby == 'descending') {
				$sortby = 'EnName';
				$orderby = 'desc';
			} elseif($orderby == 'lowtohigh') {
				$sortby = 'Price';
				$orderby = 'asc';
			} elseif($orderby == 'hightolow') {
				$sortby = 'Price';
				$orderby = 'desc';
			}
		}
		
		$catids = [];
		
		$categories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', $category)->orderBy('DisplayOrder', 'asc')->get();
		
		if(count($categories) <= 0) {
			$categories = Category::where('TypeStatus', '=', '1')->where('TypeId', '=', $category)->orderBy('DisplayOrder', 'asc')->get();
		}
		
		if(count($categories) > 0) {
			$x = 0;
			$p = 0;
			foreach($categories as $category) {
				$childcategories[$x]['category_name'] = $category->EnName;
				$childcategories[$x]['category_id'] = $category->TypeId;
				$childcategories[$x]['url_key'] = $category->UniqueKey;
				if($category->Image) {
					$childcategories[$x]['image'] = url('/').'/uploads/category/'.$category->Image;
				} else {
					$childcategories[$x]['image'] = '';
				}
				$childcategories[$x]['meta_title'] = $category->meta_title;
				$childcategories[$x]['meta_keywords'] = $category->meta_keywords;
				$childcategories[$x]['meta_description'] = $category->meta_description;	
				
				$catids[] = $category->TypeId;
				
				$categoryproducts = Product::where('Types', '=', $category->TypeId)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
								
				if(!$categoryproducts) {
					$grandchildcategories = Category::where('TypeStatus', '=', '1')->where('ParentLevel', '=', $category->TypeId)->orderBy('DisplayOrder', 'asc')->get();
					if($grandchildcategories) {
						foreach($grandchildcategories as $grandchildcategory) {
							$catids[] = $grandchildcategory->TypeId;							
						}
					}		
				}
				
				++$x;
			}
			
			if(!empty($catids)) {
				if($searchkey != '') {
					$categoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%'.$searchkey.'%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
				} else {
					$categoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
				}
				
				if($categoryproducts) {					
					foreach($categoryproducts as $categoryproduct) {
						$categoryitems[$p]['id'] = $categoryproduct->Id;
						$categoryitems[$p]['urlkey'] = $categoryproduct->UniqueKey;
						$categoryitems[$p]['name'] = $categoryproduct->EnName;
						$categoryitems[$p]['size'] = $categoryproduct->Size;
						$categoryitems[$p]['shortdesc'] = $categoryproduct->EnShortDesc;
						$categoryitems[$p]['color'] = $categoryproduct->Color;
						$categoryitems[$p]['specification'] = $categoryproduct->Specs;
						
						$price = new \App\Models\Price();
						
						$productprice = $categoryproduct->Price;
						$actualprice = $price->getGroupPrice($categoryproduct->Id);
						$productprice = $price->getDiscountPrice($categoryproduct->Id);
						$installmentPrice = $price->getInstallmentPrice($productprice);
												
						$categoryitems[$p]['standardprice'] = number_format($actualprice, 2, '.', '');
						$categoryitems[$p]['price'] = number_format($productprice, 2, '.', '');
						$categoryitems[$p]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
						
						$categoryitems[$p]['qty'] = $categoryproduct->Quantity;
						$categoryitems[$p]['cust_qty'] = $categoryproduct->cust_qty_per_day;
						$categoryitems[$p]['shippingbox'] = $categoryproduct->ShippingBox;
						$categoryitems[$p]['weight'] = $categoryproduct->Weight;
						$categoryitems[$p]['dimension'] = $categoryproduct->Dimension;
						$categoryitems[$p]['categoryid'] = $categoryproduct->Types;
						$categoryitems[$p]['brand'] = $categoryproduct->Brand;
						if($categoryproduct->MobileImage != '') {
							$categoryitems[$p]['image'] = url('/uploads/product').'/'.$categoryproduct->MobileImage;
						} else {
							$categoryitems[$p]['image'] = url('/images/noimage.png');
						}
						if($categoryproduct->MobileLargeImage != '') {
							$categoryitems[$p]['largeimage'] = url('/uploads/product/large').'/'.$categoryproduct->MobileLargeImage;
						} else {
							$categoryitems[$p]['largeimage'] = url('/images/noimage.png');
						}
						$categoryitems[$p]['video'] = $categoryproduct->Video;
						$categoryitems[$p]['description'] = $categoryproduct->EnInfo;

						$options = ProductOptions::where('Prod', '=', $categoryproduct->Id)->where('Status', '=', '1')->get();
						
						$categoryitems[$p]['optionscount'] = count($options);
												
						++$p;	
					}
				}
			}			
			$data = response()->json(['response' => 'success', 'message' => 'Child Category', 'childcategories' => $childcategories, 'categoryproducts' => $categoryitems]);
		} else {
			$data = response()->json(['response' => 'failed', 'message' => 'No Categories Found!', 'childcategories' => '', 'categoryproducts' => '']);
		}
		
		return $data;
	}
	
	public function allitems(Request $request) {
		$data = $itemdetails = [];		
		$page = 1;
		$skip = 0;
		$take = 20;
		if(isset($request->page)) {
			$page = $request->page;
			if($page > 1) {
				$skip = (($page - 1) * $take) + 1;
				$take = $page * $take;
			}
		}
		//$products = Product::where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		
		$orderby = 'desc';
		$sortby = 'DisplayOrder';
		
		
		if(isset($request->orderby)) {
			$orderby = $request->orderby;
			if($orderby == 'ascending') {
				$sortby = 'EnName';
				$orderby = 'asc';
			} elseif($orderby == 'descending') {
				$sortby = 'EnName';
				$orderby = 'desc';
			} elseif($orderby == 'lowtohigh') {
				$sortby = 'Price';
				$orderby = 'asc';
			} elseif($orderby == 'hightolow') {
				$sortby = 'Price';
				$orderby = 'desc';
			}
		}
		
		if(isset($request->searchkey)) {
			$searchkey = $request->searchkey;
			$products = Product::where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%'.$searchkey.'%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		} else {		
			$products = Product::where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
		}
		
		if($products) {
			$x = 0;
			foreach($products as $product) {
				$itemdetails[$x]['id'] = $product->Id;
				$itemdetails[$x]['urlkey'] = $product->UniqueKey;
				$itemdetails[$x]['name'] = $product->EnName;
				$itemdetails[$x]['size'] = $product->Size;
				$itemdetails[$x]['shortdesc'] = $product->EnShortDesc;
				$itemdetails[$x]['color'] = $product->Color;
				$itemdetails[$x]['specification'] = $product->Specs;
				/*$itemdetails[$x]['standardprice'] = $product->StandardPrice;
				
				$price = new \App\Models\Price();
				$productprice = $price->getPrice($product->Id);
				$itemdetails[$x]['price'] = $productprice;*/
				
				$price = new \App\Models\Price();
						
				$productprice = $product->Price;
				$actualprice = $price->getGroupPrice($product->Id);
				$productprice = $price->getDiscountPrice($product->Id);
				$installmentPrice = $price->getInstallmentPrice($productprice);
										
				$itemdetails[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
				$itemdetails[$x]['price'] = number_format($productprice, 2, '.', '');
				$itemdetails[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
				
				$itemdetails[$x]['qty'] = $product->Quantity;
				$itemdetails[$x]['cust_qty'] = $product->cust_qty_per_day;
				$itemdetails[$x]['shippingbox'] = $product->ShippingBox;
				$itemdetails[$x]['weight'] = $product->Weight;
				$itemdetails[$x]['dimension'] = $product->Dimension;
				$itemdetails[$x]['categoryid'] = $product->Types;
				$itemdetails[$x]['brand'] = $product->Brand;
				if($product->MobileImage != '') {
					$itemdetails[$x]['image'] = url('/uploads/product').'/'.$product->MobileImage;
				} else {
					$itemdetails[$x]['image'] = url('/images/noimage.png');
				}
				if($product->MobileLargeImage != '') {
					$itemdetails[$x]['largeimage'] = url('/uploads/product/large').'/'.$product->MobileLargeImage;
				} else {
					$itemdetails[$x]['largeimage'] = url('/images/noimage.png');
				}
				
				if($product->Tds != '') {
					$itemdetails[$x]['tds'] = url('/uploads/product/tds').'/'.$product->Tds;
				} else {
					$itemdetails[$x]['tds'] = '';
				}
				
				if($product->Sds != '') {
					$itemdetails[$x]['sds'] = url('/uploads/product/sds').'/'.$product->Sds;
				} else {
					$itemdetails[$x]['sds'] = '';
				}
				
				$itemdetails[$x]['video'] = $product->Video;
				$itemdetails[$x]['description'] = $product->EnInfo;

				$options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();
				
				$itemdetails[$x]['optionscount'] = count($options);
				
				if($options) {
					$o = 0;
					foreach($options as $option) {
						$itemdetails[$x]['options'][$o]['optionid'] = $option->Id;
						$itemdetails[$x]['options'][$o]['name'] = $option->Title;
						$oprice = new \App\Models\Price();
						$optionprice = $oprice->getOptionPrice($product->Id, $option->Id);
						$itemdetails[$x]['options'][$o]['price'] = $optionprice;
						//$itemdetails[$x]['options'][$o]['price'] = $option->Price;
						$itemdetails[$x]['options'][$o]['qty'] = $option->Quantity;
						$itemdetails[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
						$itemdetails[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
						$itemdetails[$x]['options'][$o]['weight'] = $option->Weight;
						++$o;
					}
				}
				++$x;				
			}
			$data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => $itemdetails]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'All Products', 'itemdetails' => '']);
		}		
		
		return $data;
	}
	
	public function allitemnames() {
		$data = [];
		$products = Product::where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
		if($products) {
			$x = 0;
			foreach($products as $product) {				
				$itemdetails[$x]['name'] = $product->EnName;				
				++$x;				
			}
			$data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => $itemdetails]);
		} else {
			$data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => '']);
		}		
		
		return $data;
		
	}
	
	public function storeproductrating(Request $request) {
		$data = [];
		$custid = $request->customerid;
		$prodid = $request->productid;
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
		$data = response()->json(['response' => 'success', 'message' => 'Product Rating Successfully Updated', 'productid' => $prodid]);
		return $data;
	}
	
}
