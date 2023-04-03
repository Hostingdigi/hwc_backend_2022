<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Session;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $sortby = 'TypeId';
        $sortorder = 'ASC';
        $Types = $request->Types;
        $dis_type = $request->dis_type;
        $Nametype = $request->Nametype;
        $EnName = $request->EnName;
        $offerCategory = $request->offerCategory;
        $TypeStatus = $request->TypeStatus;

        $join = 'TypeId > 0';

        if ($EnName != '') {
            if ($Nametype == 1) {
                $join .= ' AND EnName = "' . $EnName . '"';
            } else {
                $join .= ' AND EnName LIKE "%' . $EnName . '%"';
            }
        }

        if ($TypeStatus != '') {
            $join .= ' AND TypeStatus = ' . $TypeStatus;
        }

        if ($Types != '') {
            $join .= ' AND (TypeId = ' . $Types . ' OR ParentLevel = ' . $Types . ')';
        }
        if ($dis_type != '') {
            $join .= ' AND dis_type = ' . $dis_type;
        }
        if ($offerCategory != '') {
            $join .= ' AND offerCategory = ' . $offerCategory;
        }

        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }

        $categories = Category::whereRaw($join)->orderBy($sortby, $sortorder)->paginate(50);
        return view('admin.Category.index')->with(compact('categories', 'Types', 'dis_type', 'moduleaccess', 'adminrole', 'Nametype', 'EnName', 'offerCategory', 'TypeStatus'));
    }

    public function create()
    {
        $categories = Category::where('ParentLevel', '=', 0)->get();
        return view('admin/Category.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $category = new Category;
        $category->EnName = $request->EnName;
        $category->UniqueKey = $request->UniqueKey;
        $category->ParentLevel = $request->ParentLevel;
        $category->Details = $request->Details;
        $category->dis_type = $request->dis_type;
        $category->discount = $request->discount;

        if (isset($request->offerCategory)) {
            $category->offerCategory = $request->offerCategory;
        } else {
            $category->offerCategory = 0;
        }
        $category->meta_title = $request->meta_title;
        $category->meta_keywords = $request->meta_keywords;
        $category->meta_description = $request->meta_description;
        $category->seo_star_script = $request->seo_star_script;
        $category->TypeStatus = $request->TypeStatus;
        $filename = $mobilefilename = '';
        if ($request->hasFile('Image')) {
            $image = $request->file('Image');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/category');
            $image->move($destinationPath, $filename);
        }

        if ($request->hasFile('MobileImage')) {
            $image = $request->file('MobileImage');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $mobilefilename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/category');
            $image->move($destinationPath, $mobilefilename);
        }

        $category->Image = $filename;
        $category->MobileImage = $mobilefilename;
        $category->save();
        return redirect('/admin/category')->with('success', 'Category Successfully Created!');
    }
    public function edit($id)
    {
        $categories = Category::where('ParentLevel', '=', 0)->orderBy('EnName', 'ASC')->get();
        $category = Category::where('TypeId', '=', $id)->first();
        return view('admin/Category.edit', compact('category', 'categories'));
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
            $destinationPath = public_path('uploads/category');
            $image->move($destinationPath, $filename);
        }
        if ($request->hasFile('MobileImage')) {
            $image = $request->file('MobileImage');
            //$image_name = time().'.'.$image->getClientOriginalExtension();
            $mobilefilename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('uploads/category');
            $image->move($destinationPath, $mobilefilename);
        }

        $offerCategory = 0;
        if (isset($request->offerCategory)) {
            $offerCategory = $request->offerCategory;
        }

        Category::where('TypeId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'Image' => $filename, 'MobileImage' => $mobilefilename, 'TypeStatus' => $request->TypeStatus, 'offerCategory' => $offerCategory));

        /*if($filename != '') {
        Category::where('TypeId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'Image' => $filename, 'TypeStatus' => $request->TypeStatus, 'offerCategory' => $offerCategory));
        } else {
        Category::where('TypeId', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'Details' => $request->Details, 'dis_type' => $request->dis_type, 'discount' => $request->discount, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'seo_star_script' => $request->seo_star_script, 'TypeStatus' => $request->TypeStatus, 'offerCategory' => $offerCategory));
        }*/

        return redirect('/admin/category')->with('success', 'Category Successfully Updated!');
    }

    public function destroy($id)
    {
        Category::where('TypeId', '=', $id)->delete();
        return redirect('/admin/category')->with('success', 'Category Successfully Deleted!');
    }

    public function updatestatus($id, $status)
    {
        $statusval = 1;
        if ($status == 1) {
            $statusval = 0;
        }
        Category::where('TypeId', '=', $id)->update(array('TypeStatus' => $statusval));
        return redirect('/admin/category')->with('success', 'Category Status Successfully Updated!');
    }

    public function bulkupdate(Request $request)
    {
        //print_r($request->all());
        $bulkaction = $request->bulk_action;
        $typeids = $request->typeids;
        if ($bulkaction == 'delete') {
            if (is_array($typeids)) {
                foreach ($typeids as $typeid) {
                    Category::where('TypeId', '=', $typeid)->delete();
                }
            }
        } elseif ($bulkaction == 'assign_promo') {
            if (is_array($typeids)) {
                foreach ($typeids as $typeid) {
                    Category::where('TypeId', '=', $typeid)->update(array('dis_type' => 1));
                }
            }
        } elseif ($bulkaction == 'remove_promo') {
            if (is_array($typeids)) {
                foreach ($typeids as $typeid) {
                    Category::where('TypeId', '=', $typeid)->update(array('dis_type' => 0));
                }
            }
        } elseif ($bulkaction == 'status_active') {
            if (is_array($typeids)) {
                foreach ($typeids as $typeid) {
                    Category::where('TypeId', '=', $typeid)->update(array('TypeStatus' => 1));
                }
            }
        } elseif ($bulkaction == 'status_inactive') {
            if (is_array($typeids)) {
                foreach ($typeids as $typeid) {
                    Category::where('TypeId', '=', $typeid)->update(array('TypeStatus' => 0));
                }
            }
        } else {
            if (is_array($typeids)) {
                foreach ($typeids as $typeids) {
                    $field = 'DisplayOrder' . $typeids;
                    $displayorder = $request->{$field};

                    Category::where('TypeId', '=', $typeids)->update(array('DisplayOrder' => $displayorder));
                }

            }
        }

        return redirect('/admin/category')->with('success', 'Category Successfully Updated!');
    }
}
