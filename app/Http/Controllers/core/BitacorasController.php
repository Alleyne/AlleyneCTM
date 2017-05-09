<?php
namespace App\Http\Controllers\core;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Input;

use App\Bitacora;
use App\User;


class BitacorasController extends Controller {
		
	public function __construct()
	{
		$this->middleware('hasAccess');    
	}
	
	/*************************************************************************************
	 * Despliega un grupo de registros en formato de tabla
	 ************************************************************************************/	
	public function index()
	{
		$bitacoras = Bitacora::orderBy('id', 'desc')->get();
		//dd($bitacoras->toArray());	

		return view('core.bitacoras.index')->with('bitacoras', $bitacoras);	
	}

	/*************************************************************************************
	 * Despliega el registro especificado en formato formulario solo lectura
	 ************************************************************************************/	
	public function show($id)
	{

		//Encuentra la bitacora con el id = $id
		$bitacora= Bitacora::find($id);
		//dd($bitacora);

		$usuario = User::find($bitacora->user_id);
		//dd($usuario);			

		return view('core.bitacoras.show')->with('bitacora', $bitacora)
																			->with('usuario', $usuario);
	}
}