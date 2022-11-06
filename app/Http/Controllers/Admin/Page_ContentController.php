<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use APP\Models\Page_Content;

class Page_ContentController extends Controller
{
    public function index()
    {
        return view('admin.page_content.create');
    }
}
