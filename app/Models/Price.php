<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentSettings;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductPrices;
use App\Models\BrandPrice;
use App\Models\ProductOptions;
use App\Models\Country;
use Session;

class Price extends Model
{
    use HasFactory;
	
	
	public function getPrice($productid = 0) {
		$brand = $brandgroup = [];
		$displayprice = $standardprice = $customergroupid = $globaldiscount = $promodiscount = 0;
		$brand_general_dis_included = 1;
		$type_allow_global_dis = $brand_allow_group_price = 1;
		
		if($productid > 0) {
			
			$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
			if($globalsettings) {
				$globaldiscount = $globalsettings->discount_percentage;
				$promodiscount = $globalsettings->promo_discount_percentage;
			}
		
			$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Types', 'Brand', 'Price')->first();
			
			/*if($product->StandardPrice != '0' && $product->StandardPrice != '0.00') {
				$displayprice = $product->StandardPrice;
				$standardprice = $product->StandardPrice;
			} else {
				$displayprice = $product->Price;
				$standardprice = $product->Price;
			}*/
			
			$displayprice = $product->Price;
			$standardprice = $product->Price;			
			
			if($product->Types > 0) {
				$category = Category::where('TypeId', '=', $product->Types)->select('dis_type')->first();				
				if($category) {
					if($category->dis_type == 0) {
						$type_allow_global_dis = 0;
					}
				}						
			}
			
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
			
			if(Session::has('customer_id') && $brand_general_dis_included == 1 && $type_allow_global_dis == 1) {
				$customerid = Session::get('customer_id');
				$customer = Customer::where('cust_id', '=', $customerid)->select('cust_type')->first();
				
				if($customer->cust_type == 0) {
					$customergroupid = 1;
				} else {
					$customergroupid = $customer->cust_type;
				}
				
				/* Product Group Price */
				$productgroup = ProductPrices::where('Prod', '=', $productid)->where('GroupId', '=', $customergroupid)->where('Status', '=', '1')->first();
				$brandgroup = BrandPrice::where('Brand', '=', $product->Brand)->where('GroupId', '=', $customergroupid)->where('Status', '=', '1')->first();
				if($productgroup) {
					$displayprice = $productgroup->Price;
				} elseif($brandgroup) {
					$brandprice =  ($standardprice / 100) * $brandgroup->Price;
					if($brandgroup->type == 1) {
						$displayprice = $standardprice + $brandprice;
					} else {
						$displayprice = $standardprice - $brandprice;
					}
				} else {
					$customergroup = CustomerGroup::where('Id', '=', $customergroupid)->where('Status', '=', '1')->first();
					if($customergroup) {
						if($customergroup->type == 1) {
							if($customergroup->DiscountType == 1) { // $ 						
								$displayprice = $standardprice + $customergroup->DiscountValue;
							} elseif($customergroup->DiscountType == 2) { // %
								$displayprice = $standardprice + (($standardprice * $customergroup->DiscountValue) / 100);
							}						
						} elseif($customergroup->type == 2) {
							$discountapplied = 1;
							if($customergroup->DiscountType == 1) { // $ 						
								$displayprice = $standardprice - $customergroup->DiscountValue;
							} elseif($customergroup->DiscountType == 2) { // %
								$displayprice = $standardprice - (($standardprice * $customergroup->DiscountValue) / 100);
							}
						}
					} else {
						$displayprice = $product->Price;
						$standardprice = $product->Price;
					}
				}
			} else {				
				if(!empty($brandgroup) && $brand_allow_group_price == 1) {				
					if($brandgroup->type == 1 && $brand_general_dis_included == 1) {
						$percentage = (100 - $globaldiscount) / 100;
						$displayprice = ($standardprice * $percentage);
					} else {
						if($brandgroup->type == 1) {
							$displayprice = $standardprice + (($standardprice * $brandgroup->Price) / 100);
						} else {
							$displayprice = $standardprice - (($standardprice * $brandgroup->Price) / 100);
						}
					}
				} elseif($globaldiscount > 0 && $brand_general_dis_included == 1) {
					$percentage = (100 - $globaldiscount) / 100;
					$displayprice = ($standardprice * $percentage);
				} elseif($promodiscount > 0 && $brand_general_dis_included == 1 && $product->IsPromotion == 1) {
					$percentage = (100 - $promodiscount) / 100;
					$displayprice = ($standardprice * $percentage);
				}
			}
		} 
		return $displayprice;
	}
	
	
	public function getGroupPrice($productid = 0) {
		
		$displayprice = $standardprice = 0;
		
		
		$customergroupid = 1;
		
		if($productid > 0) {
			
			$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
			if($globalsettings) {
				$globaldiscount = $globalsettings->discount_percentage;
				$promodiscount = $globalsettings->promo_discount_percentage;
			}
			$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Price', 'Brand')->first();
			$displayprice = $product->Price;
			$standardprice = $product->Price;
			
			if(Session::has('customer_id')) {
				$customerid = Session::get('customer_id');
				$customer = Customer::where('cust_id', '=', $customerid)->select('cust_type')->first();
				
				if($customer->cust_type == 0) {
					$customergroupid = 1;
				} else {
					$customergroupid = $customer->cust_type;
				}
			}
				
			/* Product Group Price */
			$productgroup = ProductPrices::where('Prod', '=', $productid)->where('GroupId', '=', $customergroupid)->where('Status', '=', '1')->first();	

			$brandgroup = BrandPrice::where('Brand', '=', $product->Brand)->where('GroupId', '=', $customergroupid)->where('Status', '=', '1')->first();

			$customergroup = CustomerGroup::where('Id', '=', $customergroupid)->where('Status', '=', '1')->first();
			if($customergroup && Session::has('customer_id')) {				
				if($customergroup->type == 1) {
					if($customergroup->DiscountType == 1) { // $ 						
						$displayprice = $standardprice + $customergroup->DiscountValue;
					} elseif($customergroup->DiscountType == 2) { // %
						$displayprice = $standardprice + (($standardprice * $customergroup->DiscountValue) / 100);
					}						
				} elseif($customergroup->type == 2) {
					$discountapplied = 1;
					if($customergroup->DiscountType == 1) { // $ 						
						$displayprice = $standardprice - $customergroup->DiscountValue;
					} elseif($customergroup->DiscountType == 2) { // %
						$displayprice = $standardprice - (($standardprice * $customergroup->DiscountValue) / 100);
					}
				}
			} elseif($productgroup) {
				$displayprice = $productgroup->Price;				
			} elseif($brandgroup) {
				$brandprice =  ($standardprice / 100) * $brandgroup->Price;
				if($brandgroup->type == 1) {
					$displayprice = $standardprice + $brandprice;
				} else {
					$displayprice = $standardprice - $brandprice;
				}
			}
						
			
		} 
		return $displayprice;
	}
	
	public function getDiscountPrice($productid = 0) {
		$brand = $brandgroup = [];
		$displayprice = $standardprice = $customergroupid = $globaldiscount = $promodiscount = 0;
		$brand_general_dis_included = 1;
		$type_allow_global_dis = $brand_allow_group_price = 1;
		$price = new \App\Models\Price();
		if($productid > 0) {
			
			$actualprice = $price->getGroupPrice($productid);
			
			$displayprice = $actualprice;
			$standardprice = $actualprice;	

			$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Types', 'Brand', 'Price')->first();	
			
			if($product->Types > 0) {
				$category = Category::where('TypeId', '=', $product->Types)->select('dis_type')->first();				
				if($category) {
					if($category->dis_type == 0) {
						$type_allow_global_dis = 0;
					}
				}						
			}
			
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
			
			$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
			if($globalsettings) {
				$globaldiscount = $globalsettings->discount_percentage;
				$promodiscount = $globalsettings->promo_discount_percentage;
			}
		
			if($globaldiscount > 0 && $brand_general_dis_included == 1) {
				$percentage = (100 - $globaldiscount) / 100;
				$displayprice = ($actualprice * $percentage);
			} elseif($promodiscount > 0 && $brand_general_dis_included == 1 && $product->IsPromotion == 1) {
				$percentage = (100 - $promodiscount) / 100;
				$displayprice = ($actualprice * $percentage);
			}
			
		} 
		return $displayprice;
	}
	
	
	public function getOptionPrice($productid = 0, $optionid = 0) {
		$brand = $brandgroup = [];
		$displayprice = $standardprice = $globaldiscount = $promodiscount = 0;
		$brand_general_dis_included = 1;
		$type_allow_global_dis = $brand_allow_group_price = 1;
		
		if($productid > 0) {
			
			$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
			if($globalsettings) {
				$globaldiscount = $globalsettings->discount_percentage;
				$promodiscount = $globalsettings->promo_discount_percentage;
			}
		
			$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Types', 'Brand', 'Price')->first();
			
			$optionprices = ProductOptions::where('Id', '=', $optionid)->where('Prod', '=', $productid)->select('Price')->first();
			
			if($optionprices) {
				

				$displayprice = $optionprices->Price;
				$standardprice = $optionprices->Price;			
			
				
				if($product->Types > 0) {
					$category = Category::where('TypeId', '=', $product->Types)->select('dis_type')->first();				
					if($category) {
						if($category->dis_type == 0) {
							$type_allow_global_dis = 0;
						}
					}						
				}
				
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
				
								
				if(!empty($brandgroup) && $brand_allow_group_price == 1) {				
					if($brandgroup->type == 1 && $brand_general_dis_included == 1) {
						$percentage = (100 - $globaldiscount) / 100;
						$displayprice = ($standardprice * $percentage);
					} else {
						if($brandgroup->type == 1) {
							$displayprice = $standardprice + (($standardprice * $brandgroup->Price) / 100);
						} else {
							$displayprice = $standardprice - (($standardprice * $brandgroup->Price) / 100);
						}
					}
				} elseif($globaldiscount > 0 && $brand_general_dis_included == 1) {
					$percentage = (100 - $globaldiscount) / 100;
					$displayprice = ($standardprice * $percentage);
				} elseif($promodiscount > 0 && $brand_general_dis_included == 1 && $product->IsPromotion == 1) {
					$percentage = (100 - $promodiscount) / 100;
					$displayprice = ($standardprice * $percentage);
				}
			}
			
		} 
		return $displayprice;
	}
	
	public function getInstallmentPrice($price = 0) {
		$installment = 0;
		if($price > 0) {
			$installment = (float)$price / 3;			
		}
		return $installment;
	}
	
	public function getGSTPrice($price = 0, $countrycode = '') {
	    
	    if ($countrycode == 'SG') {return $price;}

		$taxtitle = '';		
		$percentage = 1;
		$gstprice = $price;
		$countrydata = Country::where('countrycode', '=', $countrycode)->select('taxtitle', 'taxpercentage')->first();
		if($countrydata) {
			$taxtitle = $countrydata->taxtitle;
			$percentage = $countrydata->taxpercentage;						
		}
		return $gstprice = ($price + (($price * $percentage) / 100));		
	}
}
