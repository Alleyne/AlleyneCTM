<?php namespace App\library;

use Illuminate\Support\Facades\Auth;
use App\User;

class Grupo {

    /**
     * Returns an excerpt from a given string (between 0 and passed limit variable).
     *
     * @param $string
     * @param int $limit
     * @param string $suffix
     * @return string
     */
    /*public static function shorten($string, $limit = 100, $suffix = '…')
    {
        if (strlen($string) < $limit) {
            return $string;
        }

        return substr($string, 0, $limit) . $suffix;
    }*/

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role Admin
	|-------------------------------------------------------------------------------------
	*/   
	public static function esAdmin()
	{
		$es = false;
		if (Auth::check()) 
  		{

		    // encuentra todos los roles del usuario logueado
			$roles = Auth::user()->roles()->get();
			//dd($roles->toArray());
			
			foreach ($roles as $role) {
		    	if($role->name==='Admin') {
		    		$es = true;
		    	}
			}
		}
		return $es;	
	}

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de JuntaDirectiva
	|-------------------------------------------------------------------------------------
	*/
	public static function esJuntaDirectiva()
	{        
		$es = false;
		if (Auth::check()) 
  		{
		    // encuentra todos los roles del usuario logueado
			$roles = Auth::user()->roles()->get();
		    // dd($roles->toArray());

			foreach ($roles as $role) {
		    	if($role->name==='JuntaDirectiva') {
		    		$es = true;
		    	}
			}
		}
		return $es;
	}     

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de AdminDeBloque
	|-------------------------------------------------------------------------------------
	*/
	public static function esAdminDeBloque()
	{ 
		$es = false;
		if (Auth::check()) 
  		{
		    // encuentra todos los roles del usuario logueado
			$roles = Auth::user()->roles;
		    // dd($roles->toArray());

			foreach ($roles as $role) {
		    	if($role->name==='AdminDeBloque') {
		    		$es = true;
		    	}
			}
		}
		return $es;	
	} 

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de Propietario
	|-------------------------------------------------------------------------------------
	*/ 
	public static function esPropietario()
	{ 
		$es = false;
		if (Auth::check()) 
  		{
		    // encuentra todos los roles del usuario logueado
			$roles = Auth::user()->roles;
		  //dd($roles->toArray());

			foreach ($roles as $role) {
		    	if($role->name==='Propietarios') {
		    		$es = true;
		    	}
			}
		}
		return $es;	
	}

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de Proveedor
	|-------------------------------------------------------------------------------------
	*/
	public static function esProveedor()
	{         
		$es = false;
		if (Auth::check()) 
  		{
		    // encuentra todos los roles del usuario logueado
			$roles = Auth::user()->roles;
		    // dd($roles->toArray());

			foreach ($roles as $role) {
		    	if($role->name==='Proveedor') {
		    		$es = true;
		    	}
			}
		}
		return $es;
	}
}