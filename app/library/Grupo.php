<?php namespace App\library;
use Illuminate\Support\Facades\Auth;

class Grupo {

	/*public function __construct() {
      $this->middleware('auth');
  }*/

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role Admin
	|-------------------------------------------------------------------------------------
	*/   
	public static function esAdmin()	{
		if (Auth::check()) {
			$roles = Auth::user()->roles;
			return $roles->contains('name', 'Admin');
		}
	}

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de JuntaDirectiva
	|-------------------------------------------------------------------------------------
	*/
	public static function esJuntaDirectiva()	{ 
		if (Auth::check()) {
			$roles = Auth::user()->roles;
			return $roles->contains('name', 'JuntaDirectiva');
		}
	}       

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de AdminDeBloque
	|-------------------------------------------------------------------------------------
	*/
	public static function esAdminDeBloque()	{ 
		if (Auth::check()) {
			$roles = Auth::user()->roles;
			return $roles->contains('name', 'AdminDeBloque');
		}
	}       

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de Propietario
	|-------------------------------------------------------------------------------------
	*/ 
	public static function esPropietario()	{ 
		if (Auth::check()) {
			$roles = Auth::user()->roles;
			return $roles->contains('name', 'Propietarios');
		}
	} 

	/*
	|------------------------------------------------------------------------------------
	|- Determina si el usuario logueado tiene role de Proveedor
	|-------------------------------------------------------------------------------------
	*/
	public static function esProveedor()	{  
		if (Auth::check()) {
			$roles = Auth::user()->roles;
			return $roles->contains('name', 'Proveedor');
		}
	} 
}