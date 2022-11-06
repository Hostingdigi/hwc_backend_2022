<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use APP\Models\Masthead_Image;

class Masthead_ImageController extends Controller
{
    public function index()
    {
        return view('admin.masthead_image.create');
    }
}
