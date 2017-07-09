<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\library\Grupo;
use Session;
use Cache;
use App\Un;
use App\User;
use App\Post;

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

        Cache::flush();
        if (Grupo::esAdmin()) {
            Cache::forever('esAdminkey', Grupo::esAdmin());
       
        } elseif (Grupo::esJuntaDirectiva()) {
            Cache::forever('esJuntaDirectivakey', Grupo::esJuntaDirectiva());

        } elseif (Grupo::esAdministrador()) {
            Cache::forever('esAdministradorkey', Grupo::esAdministrador());        
      
        } elseif (Grupo::esPropietario()) {
            Cache::forever('esPropietariokey', Grupo::esPropietario());
        
        } elseif (Grupo::esContador()) {
            Cache::forever('esContadorkey', Grupo::esContador());
        
        } else {
            Session::flash('warning', '<< ATENCION >> Usuario no pertenece a ningun grupo!');
            return redirect()->route('frontend');
        }
        
        Cache::forever('userRoleskey', Auth::user()->roles);
 
        Cache::forever('userFullNamekey', Auth::user()->first_name .' '.Auth::user()->last_name);
        
        Cache::forever('unsAllkey', Un::all());

        Cache::forever('recentPostkey', Post::orderBy('created_at', 'desc')->limit(3)->get());

        return view('home');
    }
}