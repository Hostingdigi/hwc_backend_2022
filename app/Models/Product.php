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
		$search = '';
		
		if($searchkey != '') {
			
			$like = '%' . $searchkey . '%';

			$typeproducts = DB::table('products')->select('Types')->whereRaw('(EnName LIKE ? OR ProdCode LIKE ?)', [$like, $like])->where('ProdStatus', '=', '1')->groupBy('Types');

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

	public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Category::class, 'Types', 'TypeId');
	}

	public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(Brand::class, 'Brand', 'BrandId');
	}

	public function options(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(ProductOptions::class, 'Prod', 'Id');
	}

	public function gallery(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(ProductGallery::class, 'ProdId', 'Id');
	}

	public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(ProductReviews::class, 'ProdId', 'Id');
	}
}
