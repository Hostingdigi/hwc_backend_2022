<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductReviews extends Model
{
    use HasFactory;
	protected $table = 'product_reviews';
	
	public function productRating($productid = 0) {
		$ratinghtml = '<ul class="list-unstyled list-inline fav">';
		$rating = $nonstar = 0;
		if($productid > 0) {
			$reviews = DB::table('product_reviews')->where('ProdId', '=', $productid)->where('status', '=', '1')->select('rating')->get();
			if($reviews) {
				foreach($reviews as $review) {
					$rating = (int)$rating + (int)$review->rating;					
				}
			}
			if($rating == 0) {				
				$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
				$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
				$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
				$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
				$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';				
			} else {
				$rating = round(($rating * 5) / 100);
				if($rating >= 5) {					
					$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
					$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
					$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
					$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
					$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';					
				} else {
					$nonstar = 5 - (int)$rating;
					for($r = 0; $r < $rating; $r++) {
						$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star"></i></li>';
					}
					if($nonstar > 0) {
						for($r = 0; $r < $nonstar; $r++) {
							$ratinghtml .= '<li class="list-inline-item"><i class="fa fa-star-o"></i></li>';
						}
					}
				}
			}
		}
		$ratinghtml .= '</ul>';
		return $ratinghtml;
	}
	
}
