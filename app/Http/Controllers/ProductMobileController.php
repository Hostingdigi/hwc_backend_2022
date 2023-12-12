<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\Price;
use App\Models\Product;
use App\Models\ProductGallery;
use App\Models\ProductOptions;
use App\Models\ProductReviews;
use App\Models\Settings;
use Illuminate\Http\Request;
use Mail;
use Storage;

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

    public function promotionalitems(Request $request)
    {
        $data = $promoitems = [];
        $productprice = 0;
        $page = 1;
        $skip = 0;
        $take = 20;
        $orderby = 'desc';
        $sortby = 'DisplayOrder';

        if (isset($request->page)) {
            $page = $request->page;
            if ($page > 1) {
                $skip = (($page - 1) * $take) + 1;
                //$take = $page * $take;
            }
        }

        $searchkey = $join = '';
        if (isset($request->searchkey)) {
            $searchkey = $request->searchkey;
        }

        if (isset($request->orderby)) {
            $orderby = $request->orderby;
            if ($orderby == 'ascending') {
                $sortby = 'EnName';
                $orderby = 'asc';
            } elseif ($orderby == 'descending') {
                $sortby = 'EnName';
                $orderby = 'desc';
            } elseif ($orderby == 'lowtohigh') {
                $sortby = 'Price';
                $orderby = 'asc';
            } elseif ($orderby == 'hightolow') {
                $sortby = 'Price';
                $orderby = 'desc';
            }
        }

        //$promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
        if ($searchkey != '') {
            $promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalpromoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->get();
        } else {
            $promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalpromoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->get();
        }

        $productsCount = count($totalpromoproducts);
        $pageCount = ceil($productsCount / 20);
        $islastpage = 0;
        if ($page == $pageCount) {
            $islastpage = 1;
        }
        if ($promoproducts) {
            $x = 0;
            foreach ($promoproducts as $promoproduct) {
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
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $productprice = $price->getDiscountPrice($promoproduct->Id);
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $promoitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $promoitems[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $promoitems[$x]['price'] = number_format($productprice, 2, '.', '');
                $promoitems[$x]['gstprice'] = number_format($gstprice, 2, '.', '');
                $promoitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');

                $promoitems[$x]['qty'] = $promoproduct->Quantity;
                $promoitems[$x]['cust_qty'] = $promoproduct->cust_qty_per_day;
                $promoitems[$x]['shippingbox'] = $promoproduct->ShippingBox;
                $promoitems[$x]['weight'] = $promoproduct->Weight;
                $promoitems[$x]['dimension'] = $promoproduct->Dimension;
                $promoitems[$x]['categoryid'] = $promoproduct->Types;
                $promoitems[$x]['brand'] = $promoproduct->Brand;
                if ($promoproduct->MobileImage != '') {
                    $promoitems[$x]['image'] = url('/uploads/product') . '/' . $promoproduct->MobileImage;
                } else {
                    if ($promoproduct->Image != '') {
                        $promoitems[$x]['image'] = url('/uploads/product') . '/' . $promoproduct->Image;
                    } else {
                        $promoitems[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($promoproduct->MobileLargeImage != '') {
                    $promoitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $promoproduct->MobileLargeImage;
                } else {
                    if ($promoproduct->LargeImage != '') {
                        $promoitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $promoproduct->LargeImage;
                    } else {
                        $promoitems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                }
                $promoitems[$x]['video'] = $promoproduct->Video;
                $promoitems[$x]['description'] = $promoproduct->EnInfo;

                $options = ProductOptions::where('Prod', '=', $promoproduct->Id)->where('Status', '=', '1')->get();

                $promoitems[$x]['optionscount'] = count($options);

                if ($options) {
                    $o = 0;
                    foreach ($options as $option) {
                        $promoitems[$x]['options'][$o]['optionid'] = $option->Id;
                        $promoitems[$x]['options'][$o]['name'] = $option->Title;
                        $oprice = new \App\Models\Price();
                        $optionprice = $oprice->getOptionPrice($promoproduct->Id, $option->Id);
                        $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');
                        $promoitems[$x]['options'][$o]['price'] = $optionprice;
                        $promoitems[$x]['options'][$o]['gstprice'] = $gstoptionprice;
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
            $data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => $promoitems, 'islastpage' => $islastpage]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => '', 'islastpage' => '0']);
        }

        return $data;
    }

    public function newArrivalsNames(Request $request)
    {

        $data = [];

        $whereCondition = [['ProdStatus', '=', '1'], ['isPromotion', '=', '1']];

        $items = Product::select('Id as id', 'EnName as name')->where($whereCondition)->latest()->get();

        return response()->json(['response' => 'success', 'message' => 'New Items Names', 'items' => $items]);

    }

    public function newArrivals(Request $request)
    {

        $data = [];
        $productprice = $islastpage = 0;
        $page = $request->has('page') ? $request->page : 1;
        $take = 20;
        $skip = $page > 1 ? (($page - 1) * $take) + 1 : 0;
        $orderby = $sortby = '';
        $searchkey = $request->has('searchkey') ? trim(strtolower($request->searchkey)) : '';

        $orderBySortBy = [
            'ascending' => ['EnName', 'asc'],
            'descending' => ['EnName', 'desc'],
            'lowtohigh' => ['Price', 'asc'],
            'hightolow' => ['Price', 'desc'],
        ];

        if ($request->has('orderby') && isset($orderBySortBy[trim($request->orderby)])) {
            $sortby = $orderBySortBy[trim($request->orderby)][0];
            $orderby = $orderBySortBy[trim($request->orderby)][1];
        } else {
            $orderby = 'desc';
            $sortby = 'Id';
        }

        //$whereCondition = [['ProdStatus','=','1'],['isPromotion','=','1']];
        $whereCondition = [['ProdStatus', '=', '1']];

        $productsCount = Product::where($whereCondition)->count();
        $items = Product::where($whereCondition)->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();

        if (!empty($searchkey)) {
            $items = Product::where($whereCondition)->whereRaw("LOWER(EnName) LIKE '%" . $searchkey . "%'")->orderBy($sortby, $orderby)->skip($skip)->take($take)->orderBy($sortby, $orderby)->get();
            $productsCount = Product::where($whereCondition)->whereRaw("LOWER(EnName) LIKE '%" . $searchkey . "%'")->count();
        }

        $islastpage = ($page == (ceil($productsCount / 20))) ? 1 : 0;

        if (count($items) > 0) {
            $x = 0;
            foreach ($items as $promoproduct) {
                $items[$x]['id'] = $promoproduct->Id;
                $items[$x]['urlkey'] = $promoproduct->UniqueKey;
                $items[$x]['name'] = $promoproduct->EnName;
                $items[$x]['size'] = $promoproduct->Size;
                $items[$x]['shortdesc'] = $promoproduct->EnShortDesc;
                $items[$x]['color'] = $promoproduct->Color;
                $items[$x]['specification'] = $promoproduct->Specs;

                $price = new \App\Models\Price();

                $productprice = $promoproduct->Price;
                $actualprice = $price->getGroupPrice($promoproduct->Id);
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $productprice = $price->getDiscountPrice($promoproduct->Id);
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $items[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $items[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $items[$x]['price'] = number_format($productprice, 2, '.', '');
                $items[$x]['gstprice'] = number_format($gstprice, 2, '.', '');
                $items[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');

                $items[$x]['qty'] = $promoproduct->Quantity;
                $items[$x]['cust_qty'] = $promoproduct->cust_qty_per_day;
                $items[$x]['shippingbox'] = $promoproduct->ShippingBox;
                $items[$x]['weight'] = $promoproduct->Weight;
                $items[$x]['dimension'] = $promoproduct->Dimension;
                $items[$x]['categoryid'] = $promoproduct->Types;
                $items[$x]['brand'] = $promoproduct->Brand;
                if ($promoproduct->MobileImage != '') {
                    $items[$x]['image'] = url('/uploads/product') . '/' . $promoproduct->MobileImage;
                } else {
                    if ($promoproduct->Image != '') {
                        $items[$x]['image'] = url('/uploads/product') . '/' . $promoproduct->Image;
                    } else {
                        $items[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($promoproduct->MobileLargeImage != '') {
                    $items[$x]['largeimage'] = url('/uploads/product/large') . '/' . $promoproduct->MobileLargeImage;
                } else {
                    if ($promoproduct->LargeImage != '') {
                        $items[$x]['largeimage'] = url('/uploads/product/large') . '/' . $promoproduct->LargeImage;
                    } else {
                        $items[$x]['largeimage'] = url('/images/noimage.png');
                    }
                }
                $items[$x]['video'] = $promoproduct->Video;
                $items[$x]['description'] = $promoproduct->EnInfo;

                $options = ProductOptions::where([['Prod', '=', $promoproduct->Id], ['Status', '=', '1']])->get();

                $items[$x]['optionscount'] = count($options);

                $optionsArray = [];
                foreach ($options as $ok => $option) {

                    $oprice = new \App\Models\Price();
                    $optionprice = $oprice->getOptionPrice($promoproduct->Id, $option->Id);
                    $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');

                    array_push($optionsArray, [
                        'optionid' => $option->Id,
                        'name' => $option->Title,
                        'price' => $optionprice,
                        'gstprice' => $gstoptionprice,
                        'qty' => $option->Quantity,
                        'cust_qty_per_day' => $option->cust_qty_per_day,
                        'shippingbox' => $option->ShippingBox,
                        'weight' => $option->Weight,
                    ]);
                }

                $items[$x]['options'] = $optionsArray;
                ++$x;
            }
        }

        return response()->json(['response' => 'success', 'message' => 'New Items', 'islastpage' => $islastpage, 'items' => $items]);

    }

    public function promotionalitemnames()
    {
        $data = [];
        $promoproducts = Product::where('isPromotion', '=', '1')->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();

        if ($promoproducts) {
            $x = 0;
            foreach ($promoproducts as $promoproduct) {
                $promoitems[$x]['name'] = $promoproduct->EnName;
                ++$x;
            }
            $data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => $promoitems]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Promotional Items', 'promoitems' => '']);
        }
        return $data;
    }

    public function branditems(Request $request)
    {
        $data = $branditems = [];
        $productprice = 0;
        $brand = $request->brand;
        $page = 1;
        $skip = 0;
        $take = 20;
        if (isset($request->page)) {
            $page = $request->page;
            if ($page > 1) {
                $skip = (($page - 1) * $take) + 1;
                //$take = $page * $take;
            }
        }

        $orderby = 'desc';
        $sortby = 'DisplayOrder';

        $searchkey = $join = '';
        if (isset($request->searchkey)) {
            $searchkey = $request->searchkey;
        }

        if (!is_numeric($brand)) {
            $brandDetails = Brand::whereRaw("LOWER(EnName) = '" . trim(strtolower($brand)) . "'")->first();
            $brand = $brandDetails->BrandId;
        }

        if (isset($request->orderby)) {
            $orderby = $request->orderby;
            if ($orderby == 'ascending') {
                $sortby = 'EnName';
                $orderby = 'asc';
            } elseif ($orderby == 'descending') {
                $sortby = 'EnName';
                $orderby = 'desc';
            } elseif ($orderby == 'lowtohigh') {
                $sortby = 'Price';
                $orderby = 'asc';
            } elseif ($orderby == 'hightolow') {
                $sortby = 'Price';
                $orderby = 'desc';
            }
        }

        //$brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();

        if ($searchkey != '') {
            $brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalbrandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->get();
        } else {
            $brandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalbrandproducts = Product::where('Brand', '=', $brand)->where('ProdStatus', '=', '1')->get();
        }

        $productsCount = count($totalbrandproducts);
        $pageCount = ceil($productsCount / 20);
        $islastpage = 0;
        if ($page == $pageCount) {
            $islastpage = 1;
        }

        if ($brandproducts) {
            $x = 0;
            foreach ($brandproducts as $brandproduct) {
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
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $branditems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $branditems[$x]['price'] = number_format($productprice, 2, '.', '');
                $branditems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
                $branditems[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $branditems[$x]['gstprice'] = number_format($gstprice, 2, '.', '');

                $branditems[$x]['qty'] = $brandproduct->Quantity;
                $branditems[$x]['cust_qty'] = $brandproduct->cust_qty_per_day;
                $branditems[$x]['shippingbox'] = $brandproduct->ShippingBox;
                $branditems[$x]['weight'] = $brandproduct->Weight;
                $branditems[$x]['dimension'] = $brandproduct->Dimension;
                $branditems[$x]['categoryid'] = $brandproduct->Types;
                $branditems[$x]['brand'] = $brandproduct->Brand;
                if ($brandproduct->MobileImage != '') {
                    $branditems[$x]['image'] = url('/uploads/product') . '/' . $brandproduct->MobileImage;
                } else {
                    if ($brandproduct->Image != '') {
                        $branditems[$x]['image'] = url('/uploads/product') . '/' . $brandproduct->Image;
                    } else {
                        $branditems[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($brandproduct->MobileLargeImage != '') {
                    $branditems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $brandproduct->MobileLargeImage;
                } else {
                    if ($brandproduct->LargeImage != '') {
                        $branditems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $brandproduct->LargeImage;
                    } else {
                        $branditems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                }
                $branditems[$x]['video'] = $brandproduct->Video;
                $branditems[$x]['description'] = $brandproduct->EnInfo;

                $options = ProductOptions::where('Prod', '=', $brandproduct->Id)->where('Status', '=', '1')->get();

                $branditems[$x]['optionscount'] = count($options);

                if ($options) {
                    $o = 0;
                    foreach ($options as $option) {
                        $branditems[$x]['options'][$o]['optionid'] = $option->Id;
                        $branditems[$x]['options'][$o]['name'] = $option->Title;
                        $oprice = new \App\Models\Price();
                        $optionprice = $oprice->getOptionPrice($brandproduct->Id, $option->Id);
                        $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');
                        $branditems[$x]['options'][$o]['price'] = $optionprice;
                        $branditems[$x]['options'][$o]['gstprice'] = $gstoptionprice;
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
            $data = response()->json(['response' => 'success', 'message' => 'Brand Items', 'branditems' => $branditems, 'islastpage' => $islastpage]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Brand Items', 'branditems' => '', 'islastpage' => '0']);
        }

        return $data;
    }

    public function categoryitems(Request $request)
    {
        $data = $categoryitems = [];
        $productprice = 0;
        $category = $request->category;
        $page = 1;
        $skip = 0;
        $take = 20;
        if (isset($request->page)) {
            $page = $request->page;
            if ($page > 1) {
                $skip = (($page - 1) * $take) + 1;
                //$take = $page * $take;
            }
        }

        $orderby = 'desc';
        $sortby = 'DisplayOrder';

        $searchkey = $join = '';
        if (isset($request->searchkey)) {
            $searchkey = $request->searchkey;
        }

        if (isset($request->orderby)) {
            $orderby = $request->orderby;
            if ($orderby == 'ascending') {
                $sortby = 'EnName';
                $orderby = 'asc';
            } elseif ($orderby == 'descending') {
                $sortby = 'EnName';
                $orderby = 'desc';
            } elseif ($orderby == 'lowtohigh') {
                $sortby = 'Price';
                $orderby = 'asc';
            } elseif ($orderby == 'hightolow') {
                $sortby = 'Price';
                $orderby = 'desc';
            }
        }

        //$categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();

        if ($searchkey != '') {
            $categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalctegoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->get();
        } else {
            $categoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalctegoryproducts = Product::where('Types', '=', $category)->where('ProdStatus', '=', '1')->get();
        }

        $productsCount = count($totalctegoryproducts);
        $pageCount = ceil($productsCount / 20);
        $islastpage = 0;
        if ($page == $pageCount) {
            $islastpage = 1;
        }

        if ($categoryproducts) {
            $x = 0;
            foreach ($categoryproducts as $categoryproduct) {
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
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $categoryitems[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $categoryitems[$x]['price'] = number_format($productprice, 2, '.', '');
                $categoryitems[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
                $categoryitems[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $categoryitems[$x]['gstprice'] = number_format($gstprice, 2, '.', '');

                $categoryitems[$x]['qty'] = $categoryproduct->Quantity;
                $categoryitems[$x]['cust_qty'] = $categoryproduct->cust_qty_per_day;
                $categoryitems[$x]['shippingbox'] = $categoryproduct->ShippingBox;
                $categoryitems[$x]['weight'] = $categoryproduct->Weight;
                $categoryitems[$x]['dimension'] = $categoryproduct->Dimension;
                $categoryitems[$x]['categoryid'] = $categoryproduct->Types;
                $categoryitems[$x]['brand'] = $categoryproduct->Brand;
                if ($categoryproduct->MobileImage != '') {
                    $categoryitems[$x]['image'] = url('/uploads/product') . '/' . $categoryproduct->MobileImage;
                } else {
                    if ($categoryproduct->Image != '') {
                        $categoryitems[$x]['image'] = url('/uploads/product') . '/' . $categoryproduct->Image;
                    } else {
                        $categoryitems[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($categoryproduct->MobileLargeImage != '') {
                    $categoryitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $categoryproduct->MobileLargeImage;
                } else {
                    if ($categoryproduct->LargeImage != '') {
                        $categoryitems[$x]['largeimage'] = url('/uploads/product/large') . '/' . $categoryproduct->LargeImage;
                    } else {
                        $categoryitems[$x]['largeimage'] = url('/images/noimage.png');
                    }
                }
                $categoryitems[$x]['video'] = $categoryproduct->Video;
                $categoryitems[$x]['description'] = $categoryproduct->EnInfo;

                $options = ProductOptions::where('Prod', '=', $categoryproduct->Id)->where('Status', '=', '1')->get();

                $categoryitems[$x]['optionscount'] = count($options);

                if ($options) {
                    $o = 0;
                    foreach ($options as $option) {
                        $categoryitems[$x]['options'][$o]['optionid'] = $option->Id;
                        $categoryitems[$x]['options'][$o]['name'] = $option->Title;
                        $oprice = new \App\Models\Price();
                        $optionprice = $oprice->getOptionPrice($categoryproduct->Id, $option->Id);
                        $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');
                        $categoryitems[$x]['options'][$o]['price'] = $optionprice;
                        $categoryitems[$x]['options'][$o]['gstprice'] = $gstoptionprice;
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
            $data = response()->json(['response' => 'success', 'message' => 'Category Items', 'categoryitems' => $categoryitems, 'islastpage' => $islastpage]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'Category Items', 'categoryitems' => '']);
        }

        return $data;
    }

    public function itemdetails(Request $request)
    {
        $data = $itemdetails = [];
        $productprice = 0;
        $productid = $request->productid;
        $products = Product::where('Id', '=', $productid)->where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
        if ($products) {
            $x = 0;
            foreach ($products as $product) {
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
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $itemdetails[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $itemdetails[$x]['price'] = number_format($productprice, 2, '.', '');
                $itemdetails[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
                $itemdetails[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $itemdetails[$x]['gstprice'] = number_format($gstprice, 2, '.', '');

                $itemdetails[$x]['qty'] = $product->Quantity;
                $itemdetails[$x]['cust_qty'] = $product->cust_qty_per_day;
                $itemdetails[$x]['shippingbox'] = $product->ShippingBox;
                $itemdetails[$x]['weight'] = $product->Weight;
                $itemdetails[$x]['dimension'] = $product->Dimension;
                $itemdetails[$x]['categoryid'] = $product->Types;
                $itemdetails[$x]['brand'] = $product->Brand;
                if ($product->MobileImage != '') {
                    $itemdetails[$x]['image'] = url('/uploads/product') . '/' . str_replace(' ', '%20', $product->MobileImage);
                } else {
                    if ($product->Image != '') {
                        $itemdetails[$x]['image'] = url('/uploads/product') . '/' . str_replace(' ', '%20', $product->Image);
                    } else {
                        $itemdetails[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($product->MobileLargeImage != '') {
                    $itemdetails[$x]['largeimage'] = Storage::exists('/uploads/product/large/' . $product->MobileLargeImage) ? url('/uploads/product/large') . '/' . str_replace(' ', '%20', $product->MobileLargeImage) : $itemdetails[$x]['image'];
                } else {
                    if ($product->LargeImage != '') {
                        $itemdetails[$x]['largeimage'] = Storage::exists('/uploads/product/large/' . $product->LargeImage) ? url('/uploads/product/large') . '/' . str_replace(' ', '%20', $product->LargeImage) : $itemdetails[$x]['image'];
                    } else {
                        $itemdetails[$x]['largeimage'] = $itemdetails[$x]['image'];
                    }
                }

                if ($product->Tds != '') {
                    $itemdetails[$x]['tds'] = url('/uploads/product') . '/' . $product->Tds;
                } else {
                    $itemdetails[$x]['tds'] = '';
                }

                if ($product->Sds != '') {
                    $itemdetails[$x]['sds'] = url('/uploads/product') . '/' . $product->Sds;
                } else {
                    $itemdetails[$x]['sds'] = '';
                }

                $itemdetails[$x]['video'] = $product->Video;
                $itemdetails[$x]['description'] = $product->EnInfo;

                $options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();

                $itemdetails[$x]['optionscount'] = count($options);

                if ($options) {
                    $o = 0;
                    foreach ($options as $option) {
                        $itemdetails[$x]['options'][$o]['optionid'] = $option->Id;
                        $itemdetails[$x]['options'][$o]['name'] = $option->Title;
                        $oprice = new \App\Models\Price();
                        $optionprice = $oprice->getOptionPrice($product->Id, $option->Id);
                        $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');
                        $oldprice = $option->Price;
                        $oldgstprice = $oprice->getGSTPrice($oldprice, 'SG');
                        $itemdetails[$x]['options'][$o]['price'] = $optionprice;
                        $itemdetails[$x]['options'][$o]['gstprice'] = $gstoptionprice;
                        //$itemdetails[$x]['options'][$o]['price'] = $option->Price;
                        $itemdetails[$x]['options'][$o]['qty'] = $option->Quantity;
                        $itemdetails[$x]['options'][$o]['cust_qty_per_day'] = $option->cust_qty_per_day;
                        $itemdetails[$x]['options'][$o]['shippingbox'] = $option->ShippingBox;
                        $itemdetails[$x]['options'][$o]['weight'] = $option->Weight;

                        $itemdetails[$x]['options'][$o]['standardprice'] = number_format($oldprice, 2, '.', '');
                        $itemdetails[$x]['options'][$o]['gststandardprice'] = number_format($oldgstprice, 2, '.', '');

                        ++$o;
                    }
                }

                $galleries = ProductGallery::where('ProdId', '=', $productid)->where('Status', '=', '1')->orderBy('DisplayOrder', 'ASC')->get();

                $itemdetails[$x]['gallerycount'] = count($galleries);

                if ($galleries) {
                    $g = 0;
                    foreach ($galleries as $gallery) {
                        $itemdetails[$x]['galleries'][$g]['name'] = $gallery->Title;
                        if ($gallery->Image) {
                            $itemdetails[$x]['galleries'][$g]['Image'] = url('/uploads/product/' . str_replace(' ', '%20', $gallery->Image));
                        } else {
                            $itemdetails[$x]['galleries'][$g]['Image'] = url('/images/noimage.png');
                        }
                        if ($gallery->LargeImage) {
                            $itemdetails[$x]['galleries'][$g]['LargeImage'] = Storage::exists('/uploads/product/large/' . $gallery->LargeImage) ? url('/uploads/product/large') . '/' . str_replace(' ', '%20', $gallery->LargeImage) : $itemdetails[$x]['galleries'][$g]['Image'];
                        } else {
                            $itemdetails[$x]['galleries'][$g]['LargeImage'] = url('/images/noimage.png');
                        }
                        ++$g;
                    }
                }

                $reviews = ProductReviews::where('ProdId', '=', $productid)->where('status', '=', '1')->orderBy('created_at', 'asc')->get();

                $itemdetails[$x]['reviewcount'] = count($reviews);

                $rating = 0;
                if ($reviews) {
                    $r = 0;
                    foreach ($reviews as $review) {
                        $customer = Customer::where('cust_id', '=', $review->CustomerId)->first();
                        if ($customer) {
                            $itemdetails[$x]['reviews'][$r]['customer'] = $customer->cust_firstname . ' ' . $customer->cust_lastname;
                        } else {
                            $itemdetails[$x]['reviews'][$r]['customer'] = '';
                        }
                        $itemdetails[$x]['reviews'][$r]['rating'] = $review->rating;
                        $itemdetails[$x]['reviews'][$r]['comments'] = $review->comments;
                        $itemdetails[$x]['reviews'][$r]['review_date'] = date('d M Y', strtotime($review->created_at));

                        foreach ($reviews as $review) {
                            $rating = (int) $rating + (int) $review->rating;
                        }

                        ++$r;
                    }
                }

                if ($rating > 0) {
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

    public function childcategorywithproducts(Request $request)
    {

        $data = $childcategories = $grandchildcategories = $categoryproducts = $categoryitems = [];
        $category = $request->category;
        $page = 1;
        $take = 20;
        $page = $request->has('page') ? (!empty($request->page) ? $request->page : 1) : 1;
        $skip = $page >= 2 ? ((($page - 1) * $take) + 1) : 0;
        $orderby = 'desc';
        $sortby = 'DisplayOrder';
        $join = '';
        $searchkey = $request->has('searchkey') ? $request->searchkey : '';

        $orderBySortBy = [
            'ascending' => ['EnName', 'asc'],
            'descending' => ['EnName', 'desc'],
            'lowtohigh' => ['Price', 'asc'],
            'hightolow' => ['Price', 'desc'],
        ];

        if ($request->has('orderby') && isset($orderBySortBy[trim($request->orderby)])) {
            $sortby = $orderBySortBy[trim($request->orderby)][0];
            $orderby = $orderBySortBy[trim($request->orderby)][1];
        }

        $whereCondition = [['TypeStatus', '=', '1'], ['TypeId', '=', $category]];

        $categories = Category::where([$whereCondition[0], ['ParentLevel', '=', $category]])->orderBy('DisplayOrder', 'asc')->get();

        if (count($categories) == 0) {
            $categories = Category::where($whereCondition)->orderBy('DisplayOrder', 'asc')->get();
        }

        if (!empty($categories)) {
            $x = $p = 0;

            foreach ($categories as $category) {
                $childcategories[$x]['category_name'] = $category->EnName;
                $childcategories[$x]['category_id'] = $category->TypeId;
                $childcategories[$x]['url_key'] = $category->UniqueKey;
                if ($category->Image) {
                    $childcategories[$x]['image'] = url('/') . '/uploads/category/' . $category->Image;
                } else {
                    $childcategories[$x]['image'] = '';
                }
                $childcategories[$x]['meta_title'] = $category->meta_title;
                $childcategories[$x]['meta_keywords'] = $category->meta_keywords;
                $childcategories[$x]['meta_description'] = $category->meta_description;

                ++$x;
            }

            $totalctegoryproducts = 0;
            $catids = $categories->pluck('TypeId');

            if (!empty($catids)) {

                if (!empty($searchkey)) {
                    $categoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
                    $totalctegoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->count();
                } else {
                    $totalctegoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '1')->count();
                    $categoryproducts = Product::whereIn('Types', $catids)->where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
                }

                if ($categoryproducts) {
                    foreach ($categoryproducts as $categoryproduct) {
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
                        $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                        $gstprice = $price->getGSTPrice($productprice, 'SG');
                        $installmentPrice = $price->getInstallmentPrice($gstprice);

                        $categoryitems[$p]['standardprice'] = number_format($actualprice, 2, '.', '');
                        $categoryitems[$p]['price'] = number_format($productprice, 2, '.', '');
                        $categoryitems[$p]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
                        $categoryitems[$p]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                        $categoryitems[$p]['gstprice'] = number_format($gstprice, 2, '.', '');

                        $categoryitems[$p]['qty'] = $categoryproduct->Quantity;
                        $categoryitems[$p]['cust_qty'] = $categoryproduct->cust_qty_per_day;
                        $categoryitems[$p]['shippingbox'] = $categoryproduct->ShippingBox;
                        $categoryitems[$p]['weight'] = $categoryproduct->Weight;
                        $categoryitems[$p]['dimension'] = $categoryproduct->Dimension;
                        $categoryitems[$p]['categoryid'] = $categoryproduct->Types;
                        $categoryitems[$p]['brand'] = $categoryproduct->Brand;
                        if ($categoryproduct->MobileImage != '') {
                            $categoryitems[$p]['image'] = url('/uploads/product') . '/' . $categoryproduct->MobileImage;
                        } else {
                            if ($categoryproduct->Image != '') {
                                $categoryitems[$p]['image'] = url('/uploads/product') . '/' . $categoryproduct->Image;
                            } else {
                                $categoryitems[$p]['image'] = url('/images/noimage.png');
                            }
                        }
                        if ($categoryproduct->MobileLargeImage != '') {
                            $categoryitems[$p]['largeimage'] = url('/uploads/product/large') . '/' . $categoryproduct->MobileLargeImage;
                        } else {
                            if ($categoryproduct->LargeImage != '') {
                                $categoryitems[$p]['largeimage'] = url('/uploads/product/large') . '/' . $categoryproduct->LargeImage;
                            } else {
                                $categoryitems[$p]['largeimage'] = url('/images/noimage.png');
                            }
                        }
                        $categoryitems[$p]['video'] = $categoryproduct->Video;
                        $categoryitems[$p]['description'] = $categoryproduct->EnInfo;

                        $options = ProductOptions::where('Prod', '=', $categoryproduct->Id)->where('Status', '=', '1')->get();

                        $categoryitems[$p]['optionscount'] = count($options);
                        ++$p;
                    }
                }
            }

            $pageCount = ceil($totalctegoryproducts / 20);
            $islastpage = ($page == $pageCount) ? 1 : 0;

            return response()->json(['response' => 'success', 'message' => 'Child Category', 'islastpage' => $islastpage, 'childcategories' => $childcategories, 'categoryproducts' => $categoryitems]);
        }

        return response()->json(['response' => 'failed', 'message' => 'No Categories Found!', 'islastpage' => '0', 'childcategories' => '', 'categoryproducts' => '']);
    }

    public function allitems(Request $request)
    {
        $data = $itemdetails = [];
        $page = 1;
        $skip = 0;
        $take = 20;

        if (isset($request->page)) {
            $page = $request->page;
            if ($page > 1) {
                $skip = (($page - 1) * $take) + 1;
                //$take = 20;
            }
        }
        //$products = Product::where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();

        $orderby = 'desc';
        $sortby = 'DisplayOrder';

        if (isset($request->orderby)) {
            $orderby = $request->orderby;
            if ($orderby == 'ascending') {
                $sortby = 'EnName';
                $orderby = 'asc';
            } elseif ($orderby == 'descending') {
                $sortby = 'EnName';
                $orderby = 'desc';
            } elseif ($orderby == 'lowtohigh') {
                $sortby = 'Price';
                $orderby = 'asc';
            } elseif ($orderby == 'hightolow') {
                $sortby = 'Price';
                $orderby = 'desc';
            }
        }

        if (isset($request->searchkey)) {
            $searchkey = $request->searchkey;
            $products = Product::where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalallproducts = Product::where('ProdStatus', '=', '1')->where('EnName', 'LIKE', '%' . $searchkey . '%')->get();
        } else {
            $products = Product::where('ProdStatus', '=', '1')->orderBy($sortby, $orderby)->skip($skip)->take($take)->get();
            $totalallproducts = Product::where('ProdStatus', '=', '1')->get();
        }

        $productsCount = count($totalallproducts);
        $pageCount = ceil($productsCount / 20);
        $islastpage = 0;
        if ($page == $pageCount) {
            $islastpage = 1;
        }

        if ($products) {
            $x = 0;
            foreach ($products as $product) {
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
                $gstactualprice = $price->getGSTPrice($actualprice, 'SG');
                $gstprice = $price->getGSTPrice($productprice, 'SG');
                $installmentPrice = $price->getInstallmentPrice($gstprice);

                $itemdetails[$x]['standardprice'] = number_format($actualprice, 2, '.', '');
                $itemdetails[$x]['price'] = number_format($productprice, 2, '.', '');
                $itemdetails[$x]['installmentPrice'] = number_format($installmentPrice, 2, '.', '');
                $itemdetails[$x]['gststandardprice'] = number_format($gstactualprice, 2, '.', '');
                $itemdetails[$x]['gstprice'] = number_format($gstprice, 2, '.', '');

                $itemdetails[$x]['qty'] = $product->Quantity;
                $itemdetails[$x]['cust_qty'] = $product->cust_qty_per_day;
                $itemdetails[$x]['shippingbox'] = $product->ShippingBox;
                $itemdetails[$x]['weight'] = $product->Weight;
                $itemdetails[$x]['dimension'] = $product->Dimension;
                $itemdetails[$x]['categoryid'] = $product->Types;
                $itemdetails[$x]['brand'] = $product->Brand;
                if ($product->MobileImage != '') {
                    $itemdetails[$x]['image'] = url('/uploads/product') . '/' . $product->MobileImage;
                } else {
                    if ($product->Image != '') {
                        $itemdetails[$x]['image'] = url('/uploads/product') . '/' . $product->Image;
                    } else {
                        $itemdetails[$x]['image'] = url('/images/noimage.png');
                    }
                }
                if ($product->MobileLargeImage != '') {
                    $itemdetails[$x]['largeimage'] = url('/uploads/product/large') . '/' . $product->MobileLargeImage;
                } else {
                    if ($product->LargeImage != '') {
                        $itemdetails[$x]['largeimage'] = url('/uploads/product/large') . '/' . $product->LargeImage;
                    } else {
                        $itemdetails[$x]['largeimage'] = url('/images/noimage.png');
                    }
                }

                if ($product->Tds != '') {
                    $itemdetails[$x]['tds'] = url('/uploads/product/tds') . '/' . $product->Tds;
                } else {
                    $itemdetails[$x]['tds'] = '';
                }

                if ($product->Sds != '') {
                    $itemdetails[$x]['sds'] = url('/uploads/product/sds') . '/' . $product->Sds;
                } else {
                    $itemdetails[$x]['sds'] = '';
                }

                $itemdetails[$x]['video'] = $product->Video;
                $itemdetails[$x]['description'] = $product->EnInfo;

                $options = ProductOptions::where('Prod', '=', $product->Id)->where('Status', '=', '1')->get();

                $itemdetails[$x]['optionscount'] = count($options);

                if ($options) {
                    $o = 0;
                    foreach ($options as $option) {
                        $itemdetails[$x]['options'][$o]['optionid'] = $option->Id;
                        $itemdetails[$x]['options'][$o]['name'] = $option->Title;
                        $oprice = new \App\Models\Price();
                        $optionprice = $oprice->getOptionPrice($product->Id, $option->Id);
                        $gstoptionprice = $oprice->getGSTPrice($optionprice, 'SG');
                        $itemdetails[$x]['options'][$o]['price'] = $optionprice;
                        $itemdetails[$x]['options'][$o]['gstprice'] = $gstoptionprice;
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
            $data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => $itemdetails, 'islastpage' => $islastpage]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'All Products', 'itemdetails' => '', 'islastpage' => '0']);
        }

        return $data;
    }

    public function allitemnames()
    {
        $data = [];
        $products = Product::where('ProdStatus', '=', '1')->orderBy('DisplayOrder', 'asc')->get();
        if ($products) {
            $x = 0;
            foreach ($products as $product) {
                $itemdetails[$x]['name'] = $product->EnName;
                ++$x;
            }
            $data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => $itemdetails]);
        } else {
            $data = response()->json(['response' => 'success', 'message' => 'All Products', 'allitems' => '']);
        }

        return $data;

    }

    public function storeproductrating(Request $request)
    {
        $data = [];
        $custid = $request->customerid;
        $prodid = $request->productid;
        $rating = $request->rating;
        $comments = $request->comments;
        $chkexist = ProductReviews::where('ProdId', '=', $prodid)->where('CustomerId', '=', $custid)->first();
        if ($chkexist) {
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

    public function sendqa(Request $request)
    {
        $data = [];
        $name = $request->name;
        $email = $request->email;
        $question = $request->question;
        $productcode = $request->productcode;
        $productname = $request->productname;

        $settings = Settings::where('id', '=', '1')->first();
        $adminemail = $settings->admin_email;
        $companyname = $settings->company_name;
        $ccemail = $settings->cc_email;

        $logo = url('/') . '/img/logo.png';
        $logo = '<img src="' . $logo . '">';

        $emailsubject = $emailcontent = '';
        $emailtemplate = EmailTemplate::where('template_type', '=', '15')->where('status', '=', '1')->first();
        if ($emailtemplate) {
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

            $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
            $headers .= 'Reply-To: ' . $adminemail . "\r\n";
            $headers .= 'X-Mailer: PHP/' . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
            //@mail('balamurugan.sk@gmail.com', $emailsubject, $emailcontent, $headers);
            //@mail($adminemail, $emailsubject, $emailcontent, $headers);
            //@mail($ccemail, $emailsubject, $emailcontent, $headers);
            Mail::send([], [], function ($message) use ($adminemail, $emailsubject, $emailcontent) {
                $message->to($adminemail)
                    ->subject($emailsubject)
                    ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                    ->setBody($emailcontent, 'text/html');
            });
        }
        $data = response()->json(['response' => 'success', 'message' => 'Your Question Successfully Sent']);
        return $data;
    }

    public function listProductsByCategoryWise(Request $request)
    {
        $category = $request->category;
        $orderby = 'desc';
        $sortby = 'DisplayOrder';
        $searchkey = $request->has('searchkey') ? $request->searchkey : '';

        $orderBySortBy = [
            'ascending' => ['EnName', 'asc'],
            'descending' => ['EnName', 'desc'],
            'lowtohigh' => ['Price', 'asc'],
            'hightolow' => ['Price', 'desc'],
        ];

        if ($request->has('orderby') && isset($orderBySortBy[trim($request->orderby)])) {
            $sortby = $orderBySortBy[trim($request->orderby)][0];
            $orderby = $orderBySortBy[trim($request->orderby)][1];
        }

        $allCategories = Category::where([['TypeStatus', '=', '1'], ['ParentLevel', '=', $category]])->pluck('TypeId');
        $allCategories->push((int) $category);

        $items = Product::select(['id', 'EnName'])->where('ProdStatus', '=', '1')
            ->whereIn('types', $allCategories);

        if (!empty($searchkey)) {
            $items = $items->where('EnName', 'LIKE', '%' . $searchkey . '%');
        }

        $items = $items->orderBy($sortby, $orderby)->get();

        return response()->json([
            'response' => 'success',
            'message' => 'Category Products',
            'items' => $items,
        ]);
    }

    public function listProductsByBrandWise(Request $request)
    {
        $brandId = $request->brand;
        $orderby = 'desc';
        $sortby = 'DisplayOrder';
        $searchkey = $request->has('searchkey') ? $request->searchkey : '';

        $orderBySortBy = [
            'ascending' => ['EnName', 'asc'],
            'descending' => ['EnName', 'desc'],
            'lowtohigh' => ['Price', 'asc'],
            'hightolow' => ['Price', 'desc'],
        ];

        if ($request->has('orderby') && isset($orderBySortBy[trim($request->orderby)])) {
            $sortby = $orderBySortBy[trim($request->orderby)][0];
            $orderby = $orderBySortBy[trim($request->orderby)][1];
        }

        $allBrands = Brand::where([['BrandStatus', '=', '1'], ['ParentLevel', '=', $brandId]])->pluck('BrandId');
        $allBrands->push((int) $brandId);

        $items = Product::select(['id', 'EnName'])->where('ProdStatus', '1')->whereIn('Brand', $allBrands);

        if (!empty($searchkey)) {
            $items = $items->where('EnName', 'LIKE', '%' . $searchkey . '%');
        }

        $items = $items->orderBy($sortby, $orderby)->get();

        return response()->json([
            'response' => 'success',
            'message' => 'Brand Products',
            'items' => $items,
        ]);
    }

}
