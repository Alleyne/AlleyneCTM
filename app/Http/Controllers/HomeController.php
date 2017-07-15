<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Session, Cache;

use App\Un;
use App\User;
use App\Post;
use App\Jd;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','hasAccess']);
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {

        return view('home');
    }
}