<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BrandPrice;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Session;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $sortby = 'BrandId';
        $sortorder = 'desc';
        $Nametype = $request->Nametype;
        $EnName = $request->EnName;
        $PopularBrand = $request->PopularBrand;
        $dis_type = $request->dis_type;
        $exclude_global_dis = $request->exclude_global_dis;
        $BrandStatus = $request->BrandStatus;

        $join = 'BrandId > 0';
        if ($EnName != '') {
            if ($Nametype == 1) {
                $join .= ' AND EnName = "' . $EnName . '"';
            } else {
                $join .= ' AND EnName LIKE "%' . $EnName . '%"';
            }
        }
        if ($PopularBrand != '') {
            $join .= ' AND PopularBrand = ' . $PopularBrand;
        }
        if ($dis_type != '') {
            $join .= ' AND dis_type = ' . $dis_type;
        }
        if ($BrandStatus != '') {
            $join .= ' AND BrandStatus = ' . $BrandStatus;
        }
        if ($exclude_global_dis != '') {
            $join .= ' AND exclude_global_dis = ' . $exclude_global_dis;
        }
        $brands = Brand::whereRaw($join)->orderBy($sortby, $sortorder)->paginate(50);

        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }

        return view('admin.Brand.index')->with(compact('brands', 'PopularBrand', 'dis_type', 'exclude_global_dis', 'EnName', 'Nametype', 'moduleaccess', 'adminrole', 'BrandStatus'));
    }

    public function create()
    {
        return view('admin/Brand.create');
    }

    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->EnName = $request->EnName;
        /*$request->validate([
        'EnName' => 'required|unique:brands'
        ]);*/

        $brand->UniqueKey = $request->UniqueKey;
        $brand->PopularBrand = $request->PopularBrand;
        if (isset($request->exclude_global_dis)) {
            $brand->exclude_global_dis = $request->exclude_global_dis;
        } else {
            $brand->exclude_global_dis = 0;
        }
        $brand->Details = $request->Details;
        $brand->dis_type = $request->dis_type;
        $brand->discount = $request->discount;
        $brand->meta_title = $request->meta_title;
        $brand->meta_keywords = $request->meta_keywords;
        $brand->meta_description = $request->meta_description;
        $brand->seo_star_script = $request->seo_star_script;
        $brand->BrandStatus = $request->BrandStatus;
        $filename = $mobilefilename = '';
        if ($request->hasFile('Image')) {
            $image = $request->file('Image');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/brands');
            $image->move($destinationPath, $filename);
        }

        if ($request->hasFile('MobileImage')) {
            $image = $request->file('MobileImage');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $mobilefilename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/brands');
            $image->move($destinationPath, $mobilefilename);
        }

        $brand->Image = $filename;
        $brand->MobileImage = $mobilefilename;
        $brand->save();
        return redirect('/admin/brands')->with('success', 'Brand Successfully Created!');
    }
    public function edit($id)
    {

        $brand = Brand::where('BrandId', '=', $id)->first();
        return view('admin/Brand.edit', compact('brand'));
    }

    public function update(Request $request)
    {
        $id = $request->id;

        $filename = $request->ExistImage;
        $mobilefilename = $request->ExistMobileImage;

        if ($request->hasFile('Image')) {
            $image = $request->file('Image');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/brands');
            $image->move($destinationPath, $filename);
        }

        if ($request->hasFile('MobileImage')) {
            $image = $request->file('MobileImage');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $mobilefilename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/brands');
            $image->move($destinationPath, $mobilefilename);
        }

        $exclude_global_dis = 0;
        if (isset($request->exclude_global_dis)) {
            $exclude_global_dis = $request->exclude_global_dis;
        }

        Brand::where('BrandId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'PopularBrand' => $request->PopularBrand, 'exclude_global_dis' => $exclude_global_dis, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'Image' => $filename, 'MobileImage' => $mobilefilename, 'BrandStatus' => $request->BrandStatus));

        /*if($filename != '') {
        Brand::where('BrandId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'PopularBrand' => $request->PopularBrand, 'exclude_global_dis' => $exclude_global_dis, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'Image' => $filename, 'BrandStatus' => $request->BrandStatus));
        } else {
        Brand::where('BrandId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'PopularBrand' => $request->PopularBrand, 'exclude_global_dis' => $exclude_global_dis, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'BrandStatus' => $request->BrandStatus));
        }*/

        return redirect('/admin/brands')->with('success', 'Brand Successfully Updated!');
    }

    public function destroy($id)
    {
        Brand::where('BrandId', '=', $id)->delete();
        return redirect('/admin/brands')->with('success', 'Brand Successfully Deleted!');
    }

    public function updatestatus($id, $status)
    {
        $statusval = 1;
        if ($status == 1) {
            $statusval = 0;
        }
        Brand::where('BrandId', '=', $id)->update(array('BrandStatus' => $statusval));
        return redirect('/admin/brands')->with('success', 'Brand Status Successfully Updated!');
    }

    public function bulkupdate(Request $request)
    {
        //print_r($request->all()); exit;
        $bulkaction = $request->bulk_action;
        $BrandIds = $request->brandids;
        if ($bulkaction == 'delete') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->delete();
                }
            }
        } elseif ($bulkaction == 'assign_promo') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('dis_type' => 1));
                }
            }
        } elseif ($bulkaction == 'remove_promo') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('dis_type' => 0));
                }
            }
        } elseif ($bulkaction == 'assign_papular') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('PopularBrand' => 1));
                }
            }
        } elseif ($bulkaction == 'remove_papular') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('PopularBrand' => 0));
                }
            }
        } elseif ($bulkaction == 'exclude_discount') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('PopularBrand' => 0));
                }
            }
        } elseif ($bulkaction == 'include_discount') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('PopularBrand' => 1));
                }
            }
        } elseif ($bulkaction == 'status_active') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('BrandStatus' => 1));
                }
            }
        } elseif ($bulkaction == 'status_inactive') {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandId) {
                    Brand::where('BrandId', '=', $BrandId)->update(array('BrandStatus' => 0));
                }
            }
        } elseif ($bulkaction == 'update_group_price') {
            if (is_array($BrandIds)) {
                $groupid = 0;
                $group = CustomerGroup::where('Status', '=', 1)->first();
                if ($group) {
                    $groupid = $group->Id;
                }
                foreach ($BrandIds as $BrandId) {

                    $bprice = BrandPrice::where('Brand', '=', $BrandId)->where('GroupId', '=', $groupid)->first();
                    $typefield = 'type' . $BrandId;
                    $type = $request->{$typefield};
                    $pricefield = 'Price' . $BrandId;
                    $price = $request->{$pricefield};

                    if ($bprice) {
                        BrandPrice::where('Brand', '=', $BrandId)->update(array('GroupId' => $groupid, 'type' => $type, 'Price' => $price));
                    } else {
                        BrandPrice::insert(array('Brand' => $BrandId, 'GroupId' => $groupid, 'type' => $type, 'Price' => $price));
                    }
                }
            }
        } else {
            if (is_array($BrandIds)) {
                foreach ($BrandIds as $BrandIds) {
                    $field = 'DisplayOrder' . $BrandIds;
                    $displayorder = $request->{$field};
                    Brand::where('BrandId', '=', $BrandIds)->update(array('DisplayOrder' => $displayorder));
                }

            }
        }

        return redirect('/admin/brands')->with('success', 'Brand Successfully Updated!');
    }

    public function groupprice($brandid)
    {
        $brand = Brand::where('BrandId', '=', $brandid)->first();
        $groups = CustomerGroup::where('Status', '=', '1')->get();
        $groupprices = BrandPrice::where('Brand', '=', $brandid)->get();
        $editprice = [];
        return view('admin/Brand.groupprice', compact('brand', 'groups', 'groupprices', 'editprice'));
    }

    public function addgroupprice(Request $request)
    {
        $brandprice = new BrandPrice;
        $brandprice->GroupId = $request->GroupId;
        $brandprice->Brand = $request->brandid;
        $brandprice->Price = $request->Price;
        $brandprice->type = $request->type;
        $brandprice->Status = $request->Status;
        $brandprice->save();
        return redirect('/admin/brands/' . $request->brandid . '/groupprice')->with('success', 'Group Price Successfully Added!');
    }

    public function editgroupprice($id, $brandid)
    {
        $brand = Brand::where('BrandId', '=', $brandid)->first();
        $groups = CustomerGroup::where('Status', '=', '1')->get();
        $groupprices = BrandPrice::where('Brand', '=', $brandid)->get();
        $editprice = BrandPrice::where('Brand', '=', $brandid)->where('Id', '=', $id)->first();
        return view('admin/Brand.groupprice', compact('brand', 'groups', 'groupprices', 'editprice'));
    }

    public function updategroupprice(Request $request)
    {
        $editid = $request->editid;
        BrandPrice::where('Id', '=', $editid)->update(array('GroupId' => $request->GroupId, 'Brand' => $request->brandid, 'type' => $request->type, 'Price' => $request->Price, 'Status' => $request->Status));
        return redirect('admin/brands/' . $request->brandid . '/groupprice')->with('success', 'Group Price Successfully Updated');
    }

    public function destroygroupprice($id, $brandid)
    {
        BrandPrice::where('Id', '=', $id)->delete();
        return redirect('admin/brands/' . $brandid . '/groupprice')->with('success', 'Group Price Successfully Deleted');
    }
}
