<?php namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\library\Graph;
use Cache, DB;

use App\Post;
use App\Role;
use App\Un;
use App\Jd;


class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index() {
		
    Cache::flush();

    if (Auth::check()) {
    // The user is logged in...
	    // encuentra todos los roles registrados 
	    $allRoles = Role::all();
	    //dd($allRoles, $user);
	    
	    // encuentra el o los roles que tiene el usuario autenticado
	    //dd(Auth::user()->roles);
	    $userRoles = Auth::user()->roles;
	    //dd($userRoles);

	    foreach ($allRoles as $allRole) {
	      $key = 'es'.$allRole->name.'key';
	    
	      if ($userRoles->contains('name', $allRole->name)) {
	          Cache::forever($key, true);
	      }
	    }  		
		}

    Cache::forever('unsAllkey', Un::all());
    //Cache::forever('recentPostkey', Post::orderBy('created_at', 'desc')->limit(3)->get());
    Cache::forever('jdkey', Jd::first());		


		// encuentra la data para la grafica de propietarios morosos a la fecha
		$data= Graph::getDataGraphMorosos();
		
		// encuentra los articulos mas recientes
		$posts = Post::where('mainpage', 1)->orderBy('id', 'desc')
               ->take(3)
               ->get();

		return view('welcome')->withPosts($posts)
													->withData($data);
	}

}
