<?php namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\library\Graph;
use App\Post;

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
	public function index()
	{
		
		/* if (Auth::check()) {
      foreach (Auth::user()->roles as $role) {
        if($role->name==='Propietarios') {
        	foreach (Auth::user()->props as $un)
        		dd($un->toArray(), $un->un->codigo);
        }
      }
    }*/

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
