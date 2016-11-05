<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\library\Grupo;
use Redirect, Session;
use Cache;
use App\Un;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index()
    {

        if (Grupo::esAdmin()) {
            Cache::forever('esAdminkey', Grupo::esAdmin());
        } elseif (Grupo::esAdminDeBloque()) {
            Cache::forever('esAdminDeBloquekey', Grupo::esAdminDeBloque());
        } elseif (Grupo::esJuntaDirectiva()) {
            Cache::forever('esJuntaDirectivakey', Grupo::esJuntaDirectiva());
        } elseif (Grupo::esPropietario()) {
            Cache::forever('esPropietariokey', Grupo::esPropietario());
        } else {
            Session::flash('warning', '<< ATENCION >> Usuario no pertenece a ningun grupo!');
            return Redirect::route('frontend');
        }
        
        Cache::forever('userRoleskey', Auth::user()->roles()->get());
 
        Cache::forever('userFullNamekey', Auth::user()->first_name .' '.Auth::user()->last_name);
        
        Cache::forever('unsAllkey', Un::all());

        return view('templates.backend.bienvenida_backend');
    }
}