<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductGallery;
use App\Models\ProductOptions;
use App\Models\ProductPrices;
use App\Models\CustomerGroup;
use Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
		$sortby = 'Id';
		$sortorder = 'DESC';
		$join = 'Id > 0 ';
		$status = $request->status;
		$Types = $request->Types;
		$Brand = $request->Brand;
		$Nametype = $request->Nametype;
		$EnName = $request->EnName;
		$Qtytype = $request->Qtytype;
		$Qty = $request->Qty;
		$productcode = $request->productcode;
		$IsPromotion = $request->IsPromotion;
		$sortcolumn = $request->sortcolumn;
		
		if($status != '') {
			$join .= ' AND ProdStatus = '.$status;
		}
		if($Types != '' && $Types > 0) {
			$join .= ' AND Types = '.$Types;
		}
		if($Brand != '' && $Brand > 0) {
			$join .= ' AND Brand = '.$Brand;
		}
		if($EnName != '') {
			if($Nametype == 1) {
				$join .= " AND EnName = '".$EnName."'";
			} else {
				$join .= " AND EnName LIKE '%".$EnName."%'";
			}
		}
		if($Qty > 0 && $Qty != '') {
			if($Qtytype == 1) {
				$join .= ' AND Quantity = '.$Qty;
			} else {
				$join .= ' AND Quantity <= '.$Qty;
			}
		}
		if($productcode != '') {
			$join .= ' AND Code = "'.$productcode.'"';
		}
		if($IsPromotion != '') {
			$join .= ' AND IsPromotion = '.$IsPromotion;
		}
		if($sortcolumn > 0) {
			if($sortcolumn == 1){ 
				$sortby = 'Brand';
				$sortorder = 'asc';
			} elseif($sortcolumn == 2){ 
				$sortby = 'Types';
				$sortorder = 'asc';
			} elseif($sortcolumn == 3){ 
				$sortby = 'EnName';
				$sortorder = 'asc';
			} elseif($sortcolumn == 4){ 
				$sortby = 'EnName';
				$sortorder = 'desc';
			}
		}
		
		$products = Product::whereRaw($join)->orderBy($sortby, $sortorder)->paginate(100);
		$categories = Category::where('ParentLevel', '=', 0)->get();
		$brands = Brand::where('ParentLevel', '=', 0)->get();
		
		$adminrole = 0;
		$moduleaccess = [];
		if(Session::has('accessrights')) {
			$moduleaccess = Session::get('accessrights');
		}
		
		if(Session::has('priority')) {
			$adminrole = Session::get('priority');
		}
		
        return view('admin.Product.index', compact('products', 'categories', 'brands', 'status', 'Types', 'Brand', 'Nametype', 'EnName', 'Qty', 'Qtytype', 'productcode', 'IsPromotion', 'sortcolumn', 'moduleaccess', 'adminrole'));
    }
	
	public function exportproducts(Request $request)
    {
		$sortby = 'Id';
		$sortorder = 'DESC';
		$join = 'Id > 0 ';
		$status = $request->status;
		$Types = $request->Types;
		$Brand = $request->Brand;
		$Nametype = $request->Nametype;
		$EnName = $request->EnName;
		$Qtytype = $request->Qtytype;
		$Qty = $request->Qty;
		$productcode = $request->productcode;
		$IsPromotion = $request->IsPromotion;
		$sortcolumn = $request->sortcolumn;
		
		if($status != '') {
			$join .= ' AND ProdStatus = '.$status;
		}
		if($Types != '' && $Types > 0) {
			$join .= ' AND Types = '.$Types;
		}
		if($Brand != '' && $Brand > 0) {
			$join .= ' AND Brand = '.$Brand;
		}
		if($EnName != '') {
			if($Nametype == 1) {
				$join .= " AND EnName = '".$EnName."'";
			} else {
				$join .= " AND EnName LIKE '%".$EnName."%'";
			}
		}
		if($Qty > 0 && $Qty != '') {
			if($Qtytype == 1) {
				$join .= ' AND Quantity = '.$Qty;
			} else {
				$join .= ' AND Quantity <= '.$Qty;
			}
		}
		if($productcode != '') {
			$join .= ' AND Code = "'.$productcode.'"';
		}
		if($IsPromotion != '') {
			$join .= ' AND IsPromotion = '.$IsPromotion;
		}
		if($sortcolumn > 0) {
			if($sortcolumn == 1){ 
				$sortby = 'Brand';
				$sortorder = 'asc';
			} elseif($sortcolumn == 2){ 
				$sortby = 'Types';
				$sortorder = 'asc';
			} elseif($sortcolumn == 3){ 
				$sortby = 'EnName';
				$sortorder = 'asc';
			} elseif($sortcolumn == 4){ 
				$sortby = 'EnName';
				$sortorder = 'desc';
			}
		}
		
		$products = Product::whereRaw($join)->orderBy($sortby, $sortorder)->get();
		
		if($products) {
			$filename = 'products_'.date("YmdHis").'.csv';
			
			$fp = fopen('php://output', 'w');
			
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename='.$filename);
			
			$header = ['Product #', 'Name', 'Code', 'Category', 'Brand', 'Size', 'Color', 'Standard Price ($)', 'Price ($)', 'Vendor', 'Supplier', 'Quantity', 'Customer Qty Per Day', 'Shipping Box', 'Weight', 'Dimension', 'MOQ', 'Gebiz Item', 'IsFeatured', 'IsPromotion', 'Image', 'TDS', 'SDS', 'Options'];
			
			fputcsv($fp, $header);
			
			foreach($products as $product) {
				$data = [];							
				$categoryname = $brandname = $image = $tds = $sds = '';
				$options = '';
				$productoptions = [];
				$featured = $promotion = 'No';
				$types = Category::where('TypeId', '=', $product->Types)->select('EnName')->first();
				if($types) {
					$categoryname = $types->EnName;
				}
				
				$brands = Brand::where('BrandId', '=', $product->Brand)->select('EnName')->first();
				if($brands) {
					$brandname = $brands->EnName;
				}
				
				
				if($product->IsFeatured == 1) {
					$featured = 'Yes';
				}
				if($product->IsPromotion == 1) {
					$promotion = 'Yes';
				}
				if($product->Image != '') {
					$image = url('/').'/uploads/product/'.$product->Image;
				}
				if($product->Tds != '') {
					$tds = url('/').'/uploads/product/'.$product->Tds;
				}
				if($product->Sds != '') {
					$sds = url('/').'/uploads/product/'.$product->Sds;
				}
				
				
				$prodoptions = ProductOptions::where('Prod', '=', $product->Id)->get();
				if($prodoptions) {
					foreach($prodoptions as $prodoption) {
						$productoptions[] = $prodoption->Title;
					}
					if(!empty($productoptions)) {
						$options = @implode(',', $productoptions);
					}
				}
				
				
				$data = [$product->Id, $product->EnName, $product->Code, $categoryname, $brandname, $product->Size, $product->Color, $product->StandardPrice, $product->Price, $product->Vendor, $product->Supplier, $product->Quantity, $product->cust_qty_per_day, $product->ShippingBox, $product->Weight, $product->Dimension, $product->MOQ, $product->gebiz_item, $featured, $promotion, $image, $tds, $sds, $options];
				
				fputcsv($fp, $data);
			}
			
			fclose($fp);
			
			exit;
		}
    }
	
	
    public function create()
    {
		$brands = Brand::all();	
		$categories = Category::where('ParentLevel', '=', 0)->get();
		$displayorder = 1;
		$prods = Product::orderBy('Id', 'DESC')->skip(0)->take(1)->first();
		if($prods) {
			$displayorder = $prods->Id + 1;
		}
        return view('admin/Product.create', compact('brands', 'categories', 'displayorder'));
    }
    public function store(Request $request)
    {
		$product = new Product();            
		$product->EnName = $request->EnName;
		$product->UniqueKey = $request->UniqueKey;
		$product->ProdCode = $request->ProdCode;
		$product->Types = $request->Types;
		$product->Brand = $request->Brand;
		$product->Price = $request->Price;
		$product->StandardPrice = $request->StandardPrice;
		$product->Vendor = $request->Vendor;
		$product->Supplier = $request->Supplier;
		if(isset($request->gebiz_item)) {
			$product->gebiz_item = $request->gebiz_item;
		} else {
			$product->gebiz_item = 0;
		}
		$product->Color = $request->Color;
		$product->Size = $request->Size;
		$product->Specs = $request->Specs;
		$product->Dimension = $request->Dimension;
		$product->MOQ = $request->MOQ;
		$product->unspsc = $request->unspsc;
		$product->Quantity = $request->Quantity;
		$product->cust_qty_per_day = $request->cust_qty_per_day;
		$product->ShippingBox = $request->ShippingBox;
		$product->Weight = $request->Weight;
		$product->Video = $request->Video;
		$product->IsFeatured = $request->IsFeatured;
		$product->IsPromotion = $request->IsPromotion;
		$product->IsOverseasShippingTrue = $request->IsOverseasShippingTrue;
		$product->EnInfo = $request->EnInfo;
		$product->EnShortDesc = $request->EnShortDesc;
		$product->MetaTitle = $request->MetaTitle;
		$product->MetaKey = $request->MetaKey;
		$product->MetaDesc = $request->MetaDesc;
		$product->ProdStatus = $request->ProdStatus;
		$product->ProdTags = $request->ProdTags;
		$filename = $largefilename = $tdsfilename = $sdsfilename = '';
		$mobilefilename = $mobilelargefilename = '';
		if($request->hasFile('Image')) {
			$smallimage = $request->file('Image');
			$small_image = time().'.'.$smallimage->getClientOriginalExtension();
			$filename = time().'_'.$smallimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$smallimage->move($destinationPath, $filename); 			
		}
		if($request->hasFile('LargeImage')) {
			$largeimage = $request->file('LargeImage');
			$large_image = time().'.'.$largeimage->getClientOriginalExtension();
			$largefilename = time().'_'.$largeimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$largeimage->move($destinationPath, $largefilename);                                
		}
		
		if($request->hasFile('MobileImage')) {
			$smallimage = $request->file('MobileImage');
			$small_image = time().'.'.$smallimage->getClientOriginalExtension();
			$mobilefilename = time().'_'.$smallimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$smallimage->move($destinationPath, $mobilefilename); 			
		}
		if($request->hasFile('MobileLargeImage')) {
			$largeimage = $request->file('MobileLargeImage');
			$large_image = time().'.'.$largeimage->getClientOriginalExtension();
			$mobilelargefilename = time().'_'.$largeimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$largeimage->move($destinationPath, $mobilelargefilename);                                
		}
		
		if($request->hasFile('Tds')) {
			$filetds = $request->file('Tds');
			$file_tds = time().'.'.$filetds->getClientOriginalExtension();
			$tdsfilename = time().'_'.$filetds->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$filetds->move($destinationPath, $tdsfilename);                                
		}
		if($request->hasFile('Sds')) {
			$filesds = $request->file('Sds');
			$file_sds = time().'.'.$filesds->getClientOriginalExtension();
			$sdsfilename = time().'_'.$filesds->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$filesds->move($destinationPath, $sdsfilename);                                
		}
		
		$product->Image = $filename;
		$product->LargeImage = $largefilename;
		$product->MobileImage = $mobilefilename;
		$product->MobileLargeImage = $mobilelargefilename;
		$product->Tds = $tdsfilename;
		$product->Sds = $sdsfilename;
		
		$product->DisplayOrder = $request->DisplayOrder;
        
		$product->save();
		return redirect('/admin/products')->with('message','Product added successfully');
	
    }
    public function edit($id)
    {
		$brands = Brand::all();	
		$categories = Category::where('ParentLevel', '=', 0)->get();
        $product = Product::find($id);			
		return view('admin/Product.edit', compact('product', 'brands', 'categories'));
    }
    public function update(Request $request)
    {
		//print_r($request->all()); exit;
		
        $id = $request->id;
		$filename = $largefilename = $tdsfilename = $sdsfilename = '';
		$mobilefilename = $mobilelargefilename = '';
		if($request->hasFile('Image')) {
			$smallimage = $request->file('Image');
			$small_image = time().'.'.$smallimage->getClientOriginalExtension();
			$filename = time().'_'.$smallimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$smallimage->move($destinationPath, $filename); 			
		} else {
			$filename = $request->ExistImage;
		}
		if($request->hasFile('LargeImage')) {
			$largeimage = $request->file('LargeImage');
			$large_image = time().'.'.$largeimage->getClientOriginalExtension();
			$largefilename = time().'_'.$largeimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$largeimage->move($destinationPath, $largefilename);                                
		} else {
			$largefilename = $request->ExistLargeImage;
		}
		
		if($request->hasFile('MobileImage')) {
			$smallimage = $request->file('MobileImage');
			$small_image = time().'.'.$smallimage->getClientOriginalExtension();
			$mobilefilename = time().'_'.$smallimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$smallimage->move($destinationPath, $mobilefilename); 			
		} else {
			$mobilefilename = $request->ExistMobileImage;
		}
		if($request->hasFile('MobileLargeImage')) {
			$largeimage = $request->file('MobileLargeImage');
			$large_image = time().'.'.$largeimage->getClientOriginalExtension();
			$mobilelargefilename = time().'_'.$largeimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$largeimage->move($destinationPath, $mobilelargefilename);                                
		} else {
			$mobilelargefilename = $request->ExistMobileLargeImage;
		}
		
		if($request->hasFile('Tds')) {
			$filetds = $request->file('Tds');
			$file_tds = time().'.'.$filetds->getClientOriginalExtension();
			$tdsfilename = time().'_'.$filetds->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$filetds->move($destinationPath, $tdsfilename);                                
		} else {
			$tdsfilename = $request->ExistTds;
		}
		if($request->hasFile('Sds')) {
			$filesds = $request->file('Sds');
			$file_sds = time().'.'.$filesds->getClientOriginalExtension();
			$sdsfilename = time().'_'.$filesds->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$filesds->move($destinationPath, $sdsfilename);                                
		} else {
			$sdsfilename = $request->ExistSds;
		}
		
		if(isset($request->gebiz_item)) {
			$gebizitem = $request->gebiz_item;
		} else {
			$gebizitem = 0;
		}
				
		Product::where('Id', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'ProdCode' => $request->ProdCode, 'Types' => $request->Types, 'Brand' => $request->Brand, 'Price' => $request->Price, 'StandardPrice' => $request->StandardPrice, 'Vendor' => $request->Vendor, 'Supplier' => $request->Supplier, 'gebiz_item' => $gebizitem, 'Color' => $request->Color, 'Size' => $request->Size, 'Specs' => $request->Specs, 'Dimension' => $request->Dimension, 'MOQ' => $request->MOQ, 'unspsc' => $request->unspsc, 'Quantity' => $request->Quantity, 'cust_qty_per_day' => $request->cust_qty_per_day, 'ShippingBox' => $request->ShippingBox, 'Weight' => $request->Weight, 'Video' => $request->Video, 'IsFeatured' => $request->IsFeatured, 'IsPromotion' => $request->IsPromotion, 'IsOverseasShippingTrue' => $request->IsOverseasShippingTrue, 'EnInfo' => $request->EnInfo, 'MetaTitle' => $request->MetaTitle, 'MetaKey' => $request->MetaKey, 'MetaDesc' => $request->MetaDesc, 'ProdStatus' => $request->ProdStatus, 'Image' => $filename, 'LargeImage' => $largefilename, 'MobileImage' => $mobilefilename, 'MobileLargeImage' => $mobilelargefilename, 'Tds' => $tdsfilename, 'Sds' => $sdsfilename, 'EnShortDesc' => $request->EnShortDesc, 'ProdTags' => $request->ProdTags));
		return redirect('/admin/products')->with('success', 'Product Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::where('Id', '=', $id)->delete();
		return redirect('/admin/products')->with('success', 'Product Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Product::where('Id', '=', $id)->update(array('ProdStatus' => $statusval));		
		return redirect('/admin/products')->with('success', 'Product Status Successfully Updated!');
	}
	
	public function quantity($id) {
		$product = Product::find($id);			
		return view('admin/Product.quantity', compact('product'));
	}
	
	public function updatequantity(Request $request) {
		$id = $request->id;
		$remainingqty = $request->remaining_qty;
		$qty = $request->Quantity + $remainingqty;
		Product::where('Id', '=', $id)->update(array('Quantity' => $qty));
		return redirect('/admin/products')->with('success', 'Product Status Successfully Updated!');
	}
	
	public function gallery($id) {
		$product = Product::where('Id', '=', $id)->first();
		$galleries = ProductGallery::where('ProdId', '=', $id)->get();
		return view('admin/Product.gallery', compact('galleries', 'product'));
	}
	
	public function addgallery(Request $request) {
		$id = $request->id;
		
		$filename = $largefilename = '';
		if($request->hasFile('Image')) {
			$smallimage = $request->file('Image');
			$small_image = time().'.'.$smallimage->getClientOriginalExtension();
			$filename = time().'_'.$smallimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$smallimage->move($destinationPath, $filename); 			
		} 
		if($request->hasFile('LargeImage')) {
			$largeimage = $request->file('LargeImage');
			$large_image = time().'.'.$largeimage->getClientOriginalExtension();
			$largefilename = time().'_'.$largeimage->getClientOriginalName();
			$destinationPath = public_path('uploads/product');
			$largeimage->move($destinationPath, $largefilename);                                
		} 
		$productgallery = new ProductGallery();            
		$productgallery->ProdId = $id;
		$productgallery->Title = $request->Title;
		$productgallery->Image = $filename;
		$productgallery->LargeImage = $largefilename;
		$productgallery->Status = $request->Status;
		$productgallery->save();
		
		return redirect('/admin/products/'.$id.'/gallery')->with('success', 'Product Gallery Successfully Added!');
	}
	
	public function updategallerystatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		ProductGallery::where('Id', '=', $id)->update(array('Status' => $statusval));	
		$productid = 0;
		$productgallery = ProductGallery::where('Id', '=', $id)->select('ProdId')->first();
		if($productgallery) {
			$productid = $productgallery->ProdId;
		}
		
		return redirect('/admin/products/'.$productid.'/gallery')->with('success', 'Product Status Successfully Updated!');
	}
	
	public function productoptions($id) {
		$product = Product::where('Id', '=', $id)->first();
		$prodoptions = ProductOptions::where('Prod', '=', $id)->get();
		$editoption = [];
		return view('admin/Product.options', compact('prodoptions', 'product', 'editoption', 'id'));
	}
	
	public function addoptions(Request $request) {
		$id = $request->id;
				
		$productoptions = new ProductOptions();            
		$productoptions->Prod = $id;
		$productoptions->Title = $request->Title;
		$productoptions->Price = $request->Price;
		if(isset($request->Quantity)) {
			$productoptions->Quantity = $request->Quantity;
		} else {
			$productoptions->Quantity = 0;
		}
		if(isset($request->cust_qty_per_day)) {
			$productoptions->cust_qty_per_day = $request->cust_qty_per_day;
		} else {
			$productoptions->cust_qty_per_day = 0;
		}
		$productoptions->ShippingBox = $request->ShippingBox;
		$productoptions->Weight = $request->Weight;
		$productoptions->Status = $request->Status;
		$productoptions->save();		
		return redirect('/admin/products/'.$id.'/productoptions')->with('success', 'Product Options Successfully Added!');
	}
	
	public function editproductoptions($id, $prodid) {
		$product = Product::where('Id', '=', $prodid)->first();
		$prodoptions = ProductOptions::where('Prod', '=', $prodid)->get();
		$editoption = ProductOptions::where('Id', '=', $id)->first();
		return view('admin/Product.options', compact('prodoptions', 'product', 'editoption'));
	}
	
	public function updateoptions(Request $request) {
		$id = $request->id;
		$editid = $request->editid;		
		
		$qty = 0;
		if(isset($request->Quantity)) {
			$qty = $request->Quantity;
		} 
		$cust_qty_per_day = 0;
		if(isset($request->cust_qty_per_day)) {
			$cust_qty_per_day = $request->cust_qty_per_day;
		} 		

		ProductOptions::where('Id', '=', $editid)->update(array('Title' => $request->Title, 'Price' => $request->Price, 'Quantity' => $qty, 'cust_qty_per_day' => $cust_qty_per_day, 'ShippingBox' => $request->ShippingBox, 'Weight' => $request->Weight, 'Status' => $request->Status));
		
		return redirect('/admin/products/'.$id.'/productoptions')->with('success', 'Product Options Successfully Added!');
	}
	
	public function updateoptionstatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		$option = ProductOptions::where('Id', '=', $id)->select('Prod')->first();		
		ProductOptions::where('Id', '=', $id)->update(array('Status' => $statusval));		
		return redirect('/admin/products/'.$option->Prod.'/productoptions')->with('success', 'Product Option Status Successfully Updated!');
	}
	
	public function bulkoptionupdate(Request $request) {
		//print_r($request->all());
		$bulkaction = $request->bulk_action;
		$optionids = $request->optionids;
		$prodid = $request->prodid;
		if($bulkaction == 'delete') {
			if(is_array($optionids)) {
				foreach($optionids as $optionid) {					
					ProductOptions::where('Id', '=', $optionid)->delete();
				}
			}	
		} elseif($bulkaction == 'update_price') {
			if(is_array($optionids)) {
				foreach($optionids as $optionid) {
					$field = 'Price'.$optionid;
					$price = $request->{$field};
					ProductOptions::where('Id', '=', $optionid)->update(array('Price' => $price));
				}
			}
		} elseif($bulkaction == 'update_qty') {
			if(is_array($optionids)) {
				foreach($optionids as $optionid) {
					$field = 'Quantity'.$optionid;
					$qty = $request->{$field};
					ProductOptions::where('Id', '=', $optionid)->update(array('Quantity' => $qty));
				}
			}
		} elseif($bulkaction == 'update_boxsize') {
			if(is_array($optionids)) {
				foreach($optionids as $optionid) {
					$field = 'ShippingBox'.$optionid;
					$shipbox = $request->{$field};
					ProductOptions::where('Id', '=', $optionid)->update(array('ShippingBox' => $shipbox));
				}
			}
		} elseif($bulkaction == 'update_custqty') {
			if(is_array($optionids)) {
				foreach($optionids as $optionid) {
					$field = 'custqty'.$optionid;
					$custqty = $request->{$field};
					ProductOptions::where('Id', '=', $optionid)->update(array('cust_qty_per_day' => $custqty));
				}
			}
		}
		return redirect('/admin/products/'.$prodid.'/productoptions')->with('success', 'Product Option Successfully Updated!');
	}
		
	public function bulkupdate(Request $request) {
		//print_r($request->all());
		$bulkaction = $request->bulk_action;
		$productids = $request->productids;
		if($bulkaction == 'delete') {
			if(is_array($productids)) {
				foreach($productids as $productid) {					
					Product::where('Id', '=', $productid)->delete();
				}
			}	
		} elseif($bulkaction == 'assign_promo') {
			if(is_array($productids)) {
				foreach($productids as $productid) {					
					Product::where('Id', '=', $productid)->update(array('IsPromotion' => 1));
				}
			}
		} elseif($bulkaction == 'remove_promo') {
			if(is_array($productids)) {
				foreach($productids as $productid) {					
					Product::where('Id', '=', $productid)->update(array('IsPromotion' => 0));
				}
			}
		} elseif($bulkaction == 'update_price') {
			if(is_array($productids)) {
				foreach($productids as $productid) {
					$field = 'Price'.$productid;
					$price = $request->{$field};
					Product::where('Id', '=', $productid)->update(array('Price' => $price));
				}
			}
		} elseif($bulkaction == 'update_weight') {
			if(is_array($productids)) {
				foreach($productids as $productid) {	
					$field = 'Weight'.$productid;
					$weight = $request->{$field};
					Product::where('Id', '=', $productid)->update(array('Weight' => $weight));
				}
			}
		} elseif($bulkaction == 'update_qty') {
			if(is_array($productids)) {
				foreach($productids as $productid) {
					$field = 'Qty'.$productid;
					$qty = $request->{$field};
					Product::where('Id', '=', $productid)->update(array('Quantity' => $qty));
				}
			}
		} elseif($bulkaction == 'update_boxsize') {
			if(is_array($productids)) {
				foreach($productids as $productid) {
					$field = 'ShippingBox'.$productid;
					$shipbox = $request->{$field};
					Product::where('Id', '=', $productid)->update(array('ShippingBox' => $shipbox));
				}
			}
		} elseif($bulkaction == 'update_custqty') {
			if(is_array($productids)) {
				foreach($productids as $productid) {
					$field = 'cust_qty'.$productid;
					$custqty = $request->{$field};
					Product::where('Id', '=', $productid)->update(array('cust_qty_per_day' => $custqty));
				}
			}
		} elseif($bulkaction == 'update_disporder') {
			if(is_array($productids)) {
				foreach($productids as $productid) {	
					$field = 'dorder'.$productid;
					$displayorder = $request->{$field};					
					Product::where('Id', '=', $productid)->update(array('DisplayOrder' => $displayorder));
				}
				
			}
		}
		return redirect('/admin/products')->with('success', 'Product Successfully Updated!');
	}
	
	public function groupprice($id) {
		$product = Product::where('Id', '=', $id)->first();
		$groupprices = ProductPrices::where('Prod', '=', $id)->get();
		$groups = CustomerGroup::where('Status', '=', '1')->get();
		$editprice = [];
		return view('admin/Product.groupprice', compact('groupprices', 'product', 'editprice', 'groups'));
	}
	
	public function addgroupprice(Request $request) {
		$productprice = new ProductPrices;
		$productprice->GroupId = $request->GroupId;
		$productprice->Prod = $request->productid;
		$productprice->Price = $request->Price;
		$productprice->Status = $request->Status;
		$productprice->save();
		return redirect('/admin/products/'.$request->productid.'/groupprice')->with('success', 'Group Price Successfully Added!');
	}
	
	public function editgroupprice($id, $productid) {
		$product = Product::where('Id', '=', $productid)->first();
		$groups = CustomerGroup::where('Status', '=', '1')->get();
		$groupprices = ProductPrices::where('Prod', '=', $productid)->get();
		$editprice = ProductPrices::where('Prod', '=', $productid)->where('Id', '=', $id)->first();
		return view('admin/Product.groupprice', compact('product', 'groups', 'groupprices', 'editprice'));
	}
	
	public function updategroupprice(Request $request){
		$editid = $request->editid;
		ProductPrices::where('Id', '=', $editid)->update(array('GroupId' => $request->GroupId, 'Prod' => $request->productid, 'Price' => $request->Price, 'Status' => $request->Status));
		return redirect('admin/products/'.$request->productid.'/groupprice')->with('success', 'Group Price Successfully Updated');
	}
	
	public function destroygroupprice($id, $productid) {
		ProductPrices::where('Id', '=', $id)->delete();
		return redirect('admin/products/'.$productid.'/groupprice')->with('success', 'Group Price Successfully Deleted');
	}
}
