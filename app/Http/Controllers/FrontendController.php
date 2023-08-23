<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function movies()
    {
        $movies = Movie::where('is_active', 1)->get();
        return view('frontend.movies', compact('movies'));
    }
}
