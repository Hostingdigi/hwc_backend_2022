<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use APP\Models\Home_Banner;

class Home_BannerController extends Controller
{
    public function index()
    {
        return view('admin.home_banner.create');
    }
}
