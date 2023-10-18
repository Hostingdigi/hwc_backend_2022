<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Str;

class ApiController extends Controller
{
    public function createCategory(Request $request)
    {
        if (!$request->has('category_name') || !$request->has('status')) return response()->json(['status' => 'Failure', 'message' => 'Mandatory field(s) are required']);

        $request->category_name = trim($request->category_name);
        $isExists = Category::where('EnName', $request->category_name)->count();

        if ($isExists > 0) return response()->json(['status' => 'Failure', 'message' => 'Category name already exists']);

        $category = new Category();
        $category->EnName = $request->category_name;
        $category->UniqueKey = Str::slug($request->category_name);
        $category->TypeStatus = $request->status;
        $category->save();

        return response()->json(['status' => 'Success', 'message' => 'Category added successfully']);

    }

    public function createSubCategory(Request $request)
    {
        if (!$request->has('category_name') || !$request->has('parent_category_id') || !$request->has('status')) return response()->json(['status' => 'Failure', 'message' => 'Mandatory field(s) are required']);

        $request->category_name = trim($request->category_name);
        $isExists = Category::where('EnName', $request->category_name)->count();

        if ($isExists > 0) return response()->json(['status' => 'Failure', 'message' => 'Sub category name already exists']);

        $isParentExists = Category::where('TypeId', $request->parent_category_id)->count();

        if ($isParentExists > 0) return response()->json(['status' => 'Failure', 'message' => 'Parent category is not exists']);

        $category = new Category();
        $category->EnName = $request->category_name;
        $category->ParentLevel = $request->parent_category_id;
        $category->UniqueKey = Str::slug($request->category_name);
        $category->TypeStatus = $request->status;
        $category->save();

        return response()->json(['status' => 'Success', 'message' => 'Sub category added successfully']);

    }
}
