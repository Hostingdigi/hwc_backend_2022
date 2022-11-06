<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;


class Product extends Model
{
    use HasFactory;	
	
	public function getcategories($searchkey = '', $ispromo = 0) {
		$catids = [0];	
		if($searchkey != '') {
			$typeproducts = DB::table('products')->select('Types')->where('EnName', 'LIKE', '%'.$searchkey.'%')->where('ProdStatus', '=', '1')->groupBy('Types');

			$typecategories = DB::table('types')			
				->joinSub($typeproducts, 'products', function ($join) {
					$join->on('types.TypeId', '=', 'products.Types');
				})->select('types.TypeId', 'types.ParentLevel')->get();
				
			
			if($typecategories) {
				foreach($typecategories as $typecategory) {					
					$catids[] = $typecategory->TypeId;
				}
			}
		}
		return $catids;
	}
	
}
