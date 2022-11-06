<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductTag;


class ProductTagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {		
        $producttag = ProductTag::all();
		return view('admin/ProductTag.index', compact('producttag'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
        return view('admin/ProductTag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $producttag = new ProductTag;
		$producttag->tagname = $request->tagname;		
		$producttag->status = $request->status;
		$producttag->save();		
		return redirect('/admin/producttag')->with('success', 'Product tag Successfully Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {		
        $producttag = ProductTag::where('id', '=', $id)->first();			
		return view('admin/ProductTag.edit', compact('producttag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {		
        $id = $request->id;
		
		ProductTag::where('id', '=', $id)->update(array('tagname' => $request->tagname, 'status' => $request->status));
		
		return redirect('/admin/producttag')->with('success', 'Product Tag Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProductTag::where('id', '=', $id)->delete();
		return redirect('/admin/producttag')->with('success', 'Product Tag Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		ProductTag::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/producttag')->with('success', 'Product Tag Status Successfully Updated!');
	}
}
