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
use Session;

class Price extends Model
{
    use HasFactory;
	
	public function getPrice($productid = 0) {
		$displayprice = 0;
		$standardprice = $globaldiscount = $ispromo = $promodiscount = $customergroup = 0;
		$discountapplied = 0;
		$productcategory = $productbrand = $allowdiscount = 0;
		$product = Product::where('Id', '=', $productid)->select('StandardPrice', 'IsPromotion', 'Types', 'Brand')->first();
		if($product) {
			$standardprice = $product->StandardPrice;
			$ispromo = $product->IsPromotion;
			$displayprice = $product->StandardPrice;
			$productcategory = $product->Types;
			$productbrand = $product->Brand;
		}
				
		$globalsettings = PaymentSettings::where('id', '=', '1')->select('discount_percentage', 'promo_discount_percentage')->first();
		if($globalsettings) {
			$globaldiscount = $globalsettings->discount_percentage;
			$promodiscount = $globalsettings->promo_discount_percentage;
		}
		
		if($productcategory > 0) {
			$category = Category::where('TypeId', '=', $productcategory)->select('dis_type')->first();
			
			if($category) {
				$allowdiscount = $category->dis_type;
			}
		}
		
		if($productbrand > 0) {
			$brand = Brand::where('BrandId', '=', $productbrand)->select('exclude_global_dis', 'dis_type')->first();
			if($brand) {
				if($brand->exclude_global_dis == 1) {
					$allowdiscount = 0;
				}
				if($brand->dis_type == 1) {
				}
			}
		}
		
		/* check customer group */
		/*if(Session::has('customer_id')) {
			$customerid = Session::get('customer_id');
			$customer = Customer::where('cust_id', '=', $customerid)->select('cust_type')->first();
			if($customer) {				
				$group = CustomerGroup::where('Id', '=', $customer->cust_type)->where('Status', '=', '1')->first();
				if($group) {
					if($group->type == 1) {
						if($group->DiscountType == 1) { // $ 						
							$standardprice = $standardprice + $group->DiscountValue;
						} elseif($group->DiscountType == 2) { // %
							$standardprice = $standardprice + (($standardprice * $group->DiscountValue) / 100);;
						}						
					} elseif($group->type == 2) {
						$discountapplied = 1;
						if($group->DiscountType == 1) { // $ 						
							$displayprice = $standardprice - $group->DiscountValue;
						} elseif($group->DiscountType == 2) { // %
							$displayprice = $standardprice - (($standardprice * $group->DiscountValue) / 100);;
						}
					}
				}
			}
		}*/
		
		if($discountapplied == 0) {
			if($allowdiscount == 1) {
				if($ispromo == 1 && $promodiscount > 0) {
					$displayprice = $standardprice - (($standardprice * $promodiscount) / 100);
				} elseif($globaldiscount > 0) {
					$displayprice = $standardprice - (($standardprice * $globaldiscount) / 100);
				}
			}
		}
		
		return $displayprice;
	}
}
