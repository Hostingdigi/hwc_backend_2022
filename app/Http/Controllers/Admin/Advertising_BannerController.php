<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Advertising_Banner;

class Advertising_BannerController extends Controller
{
    public function index()
    {
        return view('admin.advertising_banner.create');
    }
}
