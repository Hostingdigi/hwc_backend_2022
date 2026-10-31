<?php

namespace App\Support;

/** Absolute URLs for files under public/uploads (used by API v2 resources). */
class Media
{
    public static function upload(string $folder, ?string $file): ?string
    {
        return $file ? url('/uploads/' . $folder . '/' . rawurlencode($file)) : null;
    }

    /** Mobile image when there is one, otherwise the desktop image, otherwise a placeholder. */
    public static function productImage($product): string
    {
        $file = $product->MobileImage ?: $product->Image;

        return $file ? url('/uploads/product/' . rawurlencode($file)) : url('/images/noimage.png');
    }
}
