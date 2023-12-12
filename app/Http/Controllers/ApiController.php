<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\ProductOptions;
use Illuminate\Http\Request;
use Str;

class ApiController extends Controller
{
    public function createCategory(Request $request)
    {
        if (!$request->has('category_name') || !$request->has('status')) {
            return response()->json(['status' => 'Failure', 'message' => 'Mandatory field(s) are required']);
        }

        $request->category_name = trim($request->category_name);
        $isExists = Category::where('EnName', $request->category_name)->count();

        if ($isExists > 0) {
            return response()->json(['status' => 'Failure', 'message' => 'Category name already exists']);
        }

        $category = new Category();
        $category->EnName = $request->category_name;
        $category->UniqueKey = Str::slug($request->category_name);
        $category->TypeStatus = $request->status;
        $category->save();

        return response()->json(['status' => 'Success', 'message' => 'Category added successfully']);

    }

    public function createSubCategory(Request $request)
    {
        if (!$request->has('category_name') || !$request->has('parent_category_id') || !$request->has('status')) {
            return response()->json(['status' => 'Failure', 'message' => 'Mandatory field(s) are required']);
        }

        $request->category_name = trim($request->category_name);
        $isExists = Category::where('EnName', $request->category_name)->count();

        if ($isExists > 0) {
            return response()->json(['status' => 'Failure', 'message' => 'Sub category name already exists']);
        }

        $isParentExists = Category::where('TypeId', $request->parent_category_id)->count();

        if ($isParentExists == 0) {
            return response()->json(['status' => 'Failure', 'message' => 'Parent category is not exists']);
        }

        $category = new Category();
        $category->EnName = $request->category_name;
        $category->ParentLevel = $request->parent_category_id;
        $category->UniqueKey = Str::slug($request->category_name);
        $category->TypeStatus = $request->status;
        $category->save();

        return response()->json(['status' => 'Success', 'message' => 'Sub category added successfully']);

    }

    public function createProduct(Request $request)
    {
        #Category Validation
        if (!$request->has('category_name') || empty($request->category_name)) return response()->json(['status' => 'Failure', 'message' => 'category is required']);

        $category = Category::where('EnName', trim($request->category_name))->first();

        if (empty($category)) return response()->json(['status' => 'Failure', 'message' => 'category is not exists']);

        $categoryId = $category->TypeId;

        #Sub Category validation
        if ($request->has('sub_category_name') && !empty($request->sub_category_name)) {
            $subCategory = Category::where('EnName', trim($request->sub_category_name))->first();
            if (empty($subCategory)) return response()->json(['status' => 'Failure', 'message' => 'sub category is not exists']);
            $categoryId = $subCategory->TypeId;
        }

        $request->product_name = trim($request->product_name);
        $productId = '';

        if ($request->has('product_id') && !empty($request->product_id)) {
            $product = Product::where('Id', trim($request->product_id))->first();
            if (empty($product)) return response()->json(['status' => 'Failure', 'message' => 'product id ' . $request->product_id . ' is not exists']);
            $productId = $product->Id;
        }

        $isExists = Product::where('EnName', $request->product_name)->count();

        $barCode = '';
        if ($request->has('barcode1')) $barCode .= trim($request->barcode1);
        if ($request->has('barcode2')) $barCode .= trim($request->barcode2);

        if (empty($productId)) {

            $product = new Product();
            $product->EnName = $request->product_name;
            $product->UniqueKey = Str::slug($request->product_name) . ($isExists > 0 ? '-' . $isExists : '');
            $product->Types = $categoryId;
            $product->SKU = $request->sku;
            $product->Price = $request->price;
            $product->Quantity = $request->quantity;
            $product->barcode = $barCode;
            $product->save();
            $productId = $product->id;

        } else {
            Product::where('Id', '=', $productId)->update([
                'EnName' => $request->product_name,
                'UniqueKey' => Str::slug($request->product_name) . ($isExists > 0 ? '-' . $isExists : ''),
                'Types' => $categoryId,
                'SKU' => $request->sku,
                'Price' => $request->price,
                'Quantity' => $request->quantity,
                'barcode' => $barCode,
            ]);
        }

        return response()->json(['status' => 'Success', 'message' => 'product added or modified successfully', 'product_id' => $productId]);

    }

    public function productStatusUpdate(Request $request)
    {
        if (!$request->has('sku') || empty($request->sku)) {
            return response()->json(['status' => 'Failure', 'message' => 'sku is required']);
        }

        $product = Product::where('SKU', trim($request->sku))->first();
        if (empty($product)) {
            return response()->json(['status' => 'Failure', 'message' => 'product ' . $request->sku . ' is not exists']);
        }

        Product::where('Id', '=', $product->Id)->update([
            'ProdStatus' => $request->status,
        ]);

        return response()->json(['status' => 'Success', 'message' => 'status updated']);

    }

    public function stockUpdate(Request $request)
    {
        if (!$request->has('json') || empty($request->json)) return response()->json(['status' => 'Failure', 'message' => 'json is required']);

        $decodedValues = json_decode($request->json);
        if (empty($decodedValues)) return response()->json(['status' => 'Failure', 'message' => 'json is required']);

        foreach ($decodedValues->ProductDatas as $key => $value) {
            $isExists = Product::where('Id', trim($value->product_id))->first();
            if (!$isExists) {
                return response()->json(['status' => 'Failure', 'message' => [
                    'sku' => '',
                    'quantity' => '',
                    'stock' => 'No product data found',
                ]]);
            }

        }

        foreach ($decodedValues->ProductDatas as $key => $value) {
            $product = Product::where('Id', trim($value->product_id))->first();

            Product::where('Id', '=', $product->Id)->update([
                'ProdStatus' => $value->operand == '+' ? ($product->Quantity + $value->quantity) : ($product->Quantity - $value->quantity),
            ]);

            $returnArray[] = [
                'product_id' => $product->Id,
                'quantity' => $value->quantity,
                'operand' => $value->operand,
                'sku' => $product->SKU,
                'stock' => 'Updated',
            ];
        }

        return response()->json([
            'status' => 'Product Stock Updated Successfully',
            'message' => $returnArray,
        ]);
    }

    public function listDailyOrders(Request $request)
    {
        if (!$request->has('from_date')) {
            return response()->json([
                'status' => false,
                'message' => 'from_date is missing',
            ]);
        }

        if (empty($request->from_date)) {
            return response()->json([
                'status' => false,
                'message' => 'from_date is mandatory',
            ]);
        }

        $allOrders = [];
        $orders = Order::where('date_entered', '>=', date('Y-m-d H:i:s', strtotime($request->from_date)))->oldest()->get();

        if (count($orders) > 0) {
            foreach ($orders as $key => $value) {

                $user = Customer::where('cust_id', $value->user_id)->first();
                $allOrderItems = [];
                $orderItems = OrderDetails::where('order_id', $value->order_id)->get();
                $product_model = !empty($value->prod_option) ? ProductOptions::where('Id', $value->prod_option)->first() : [];
                foreach ($orderItems as $item) {
                    $productDetail = Product::find($item->prod_id);
                    $orderItem = [
                        'order_product_id' => $item->detail_id,
                        'order_id' => $item->order_id,
                        'product_id' => $item->prod_id,
                        'sku' => $product_model->barcode ?? '',
                        'name' => $item->prod_name,
                        'model' => $product_model->Title ?? '',
                        'quantity' => $item->prod_quantity,
                        'price' => $item->prod_unit_price,
                        'total' => number_format(($item->prod_unit_price * $item->prod_quantity), 2),
                    ];
                    array_push($allOrderItems, $orderItem);
                }

                $order = [
                    'order_id' => $value->order_id,
                    'customer_id' => $value->user_id,
                    'firstname' => $user->cust_firstname ?? '',
                    'lastname' => $user->cust_lastname ?? '',
                    'email' => $user->cust_email ?? '',
                    'telephone' => $user->cust_phone ?? '',
                    'total_amount' => $value->paybale_amount,
                    'order_status_id' => $value->order_status,
                    'ordered_at' => \Carbon\Carbon::parse($value->date_entered)->format('d/m/Y h:i A'),
                    'shipping' => $value,
                    'products' => $allOrderItems,
                ];

                array_push($allOrders, $order);
            }
        }

        return response()->json([
            'status' => true,
            'orderInfo' => $allOrders,
        ]);
    }

    public function productAllUpdate(Request $request)
    {
        $statusUpdate = $stockUpdate = false;
        //product status update
        if ($request->has('sku') && !empty($request->sku)) {
            $product = Product::where('SKU', trim($request->sku))->first();
            if ($product) {
                if ($request->has('status') && $request->status != '') {
                    Product::where('Id', '=', $product->Id)->update([
                        'ProdStatus' => $request->status,
                    ]);
                    $statusUpdate = true;
                }
            }
        }

        //product stock update
        if ($request->has('json') && !empty($request->json)) {

            $decodedValues = json_decode($request->json);
            if (!empty($decodedValues)) {

                foreach ($decodedValues->ProductDatas as $key => $value) {
                    $product = Product::where('Id', trim($value->product_id))->first();
                    if ($product) {
                        Product::where('Id', '=', $product->Id)->update([
                            'ProdStatus' => $value->operand == '+' ? ($product->Quantity + $value->quantity) : ($product->Quantity - $value->quantity),
                        ]);

                        $returnArray[] = [
                            'product_id' => $product->Id,
                            'quantity' => $value->quantity,
                            'operand' => $value->operand,
                            'sku' => $product->SKU,
                            'stock' => 'Updated',
                        ];
                        $stockUpdate = true;
                    }
                }
            }
        }

        $message = '';

        if($statusUpdate) $message = 'Product status has been updated';
        if($stockUpdate) $message = 'Product stock has been updated';
        if($statusUpdate && $stockUpdate) $message = 'Product Status and Stock has been updated';

        return response()->json([
            'status' => empty($message) ? 'Failure' : 'Success',
            'message' => $message
        ]);
    }
}
