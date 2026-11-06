<?php

use App\Models as HWCModel;

if (! function_exists('getFavProductsIds')) {
    function getFavProductsIds(): array
    {
        if (! auth()->check()) return [];
        return HWCModel\FavouriteProducts::where('cust_id', auth()->id())
            ->pluck('prod_id')
            ->all();
    }
}

if( ! function_exists('getBanners')) {
    function getBanners($pageId): object
    {
        return HWCModel\Bannerads::select('Video','ban_link','EnBanimage', 'ban_name')->where(['PageId' => $pageId, 'ban_status' => 1])
            ->orderBy('display_order')->get();
    }
}