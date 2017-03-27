<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;

use App\Ecajachica;
use App\Dte_ecajachica;
use App\Catalogo;
use App\Bitacora;
use App\Serviproducto;
use App\Org;

class DtecajachicasController extends Controller {
  
  public function __construct()
  {
   	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function show($ecajachica_id)
	{
    $datos = Dte_ecajachica::where('ecajachica_id', $ecajachica_id)
    				->join('serviproductos', 'serviproductos.id', '=', 'dte_ecajachicas.serviproducto_id')
            ->select('dte_ecajachicas.id','serviproductos.catalogo_id','serviproductos.nombre','dte_ecajachicas.cantidad','dte_ecajachicas.precio','dte_ecajachicas.itbms')
            ->get();
    //dd($datos->toArray());		

		// encuentra todos los productos y servicios registrados en el egreso de caja chica
		$datos_2= $datos->pluck('nombre', 'id')->all();       
		//dd($datos_2);
   
    // encuentra los datos generales del encabezado de egreso de caja chica
    $ecajachica= Ecajachica::find($ecajachica_id);
		//dd($ecajachica->toArray());
    
    // encuentra los serviproductos asignados a un determinado proveedor
		$serviproductos = Org::find($ecajachica->org_id)->serviproductos;
		$datos_1= $serviproductos->pluck('nombre', 'id')->all();   
    //dd($datos_1, $datos_2);

		// calcula y agrega el total
		$i=0;		
		$subTotal = 0;
		$totalItbms = 0;

		foreach ($datos as $dato) {
		    $datos[$i]["total"] = number_format((($dato->cantidad * $dato->precio) + $dato->itbms),2);
		    $datos[$i]["codigo"] = Catalogo::find($dato->catalogo_id)->codigo;
		    $subTotal = $subTotal + ($dato->cantidad * $dato->precio);
		    $totalItbms = $totalItbms + $dato->itbms;		    
		    $i++;
		}        

    // Subtrae de la lista total de productos y servicios registrados toda aquellas
    // que ya están asignadas a un egreso de caja chica
    // para evitar asignar productos y servicios previamente asignadas
		$serviproductos = array_diff($datos_1, $datos_2);		
		//dd($serviproductos);  

		return view('contabilidad.dte_ecajachicas.show')
				 ->with('serviproductos', $serviproductos)
				 ->with('ecajachica', $ecajachica)
				 ->with('subTotal', $subTotal)
				 ->with('totalItbms', $totalItbms)
				 ->with('datos', $datos);     	
	}	

  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function store()
	{
        
		DB::beginTransaction();
		try {
      //dd(Input::all());
      $input = Input::all();
      $rules = array(
        'serviproducto_id'	=> 'required',
        'cantidad'  				=> 'Required|Numeric',
        'precio'    				=> 'required|Numeric|min:0.01',
        'itbms'    					=> 'required|Numeric|min:0'
      );
  
      $messages = [
        'required'	=> 'Informacion requerida!',
      	'numeric'		=> 'Solo se admiten valores numericos!',
      	'min'			  => 'El precio del servicio debe ser mayor que cero!'
      ];        
          
      $validation = \Validator::make($input, $rules, $messages);      	

			if ($validation->passes())
			{
				
				// encuentra el valor total en la factura			
      	$ecajachica= Ecajachica::find(Input::get('ecajachica_id'));
      	$totalecajachica= $ecajachica->total;

				// encuentra los datos del serviproducto
      	$serviproducto= Serviproducto::find(Input::get('serviproducto_id'));
				
				$dato = new Dte_ecajachica;
				$dato->serviproducto_id  	= Input::get('serviproducto_id');
				$dato->nombre					  	= $serviproducto->nombre;
				$dato->cantidad 	       	= Input::get('cantidad');
				$dato->precio 	       		= Input::get('precio');
				$dato->itbms 	       			= Input::get('itbms');
				$dato->ecajachica_id	  	= Input::get('ecajachica_id');
				$dato->catalogo_id				= $serviproducto->catalogo->id;
				$dato->codigo	  					= $serviproducto->catalogo->codigo;
				$dato->cuenta	  					= $serviproducto->catalogo->nombre;
				$dato->save();
				
				// Registra en bitacoras
				$det=	'serviproducto_id= '.	$dato->serviproducto_id. ', '.
							'nombre= '				  .	$dato->nombre. ', '.
							'cantidad= '				.	$dato->cantidad. ', '.
							'precio= '					.	$dato->precio. ', '.
							'itbms= '					  .	$dato->itbms. ', '.
							'factura_id= '			.	$dato->factura_id. ', '.
							'catalogo_ido= '		.	$dato->catalogo_id. ', '.
							'codigo= '				  .	$dato->codigo. ', '.
							'cuenta= '				  .	$dato->cuenta;

				$totaldetalles= 0;

			  //calcula el total de detallefacturas para la presente factura
		 	  $detalles= Dte_ecajachica::where('ecajachica_id', Input::get('ecajachica_id'))->get();		    
				//dd($detalles->toArray());
				
				foreach ($detalles as $detalle) {
					$totaldetalles = $totaldetalles + ($detalle->precio + $detalle->itbms);
				}
				//dd($totaldetalles);
			    
		    if (round(floatval($totalecajachica),2) < round(floatval($totaldetalles),2)) {
	        Session::flash('danger', '<< ERROR >> El valor total de los detalles no puede sobrepasar al valor total de la factura de egresos. Intente nuevamente!');
	        return back();
		    	
		    } elseif (round(floatval($totalecajachica),2) > round(floatval($totaldetalles),2)) {
					$ecajachica->totaldetalle= $totaldetalles;
					$ecajachica->save();
			    Session::flash('warning', '<< ATENCION >> El valor total de los detalles es inferior al valor total de la factura egresos. Continue ingresando detalles!');
		    
		    } elseif (round(floatval($totalecajachica),2) == round(floatval($totaldetalles),2)) {
					$ecajachica->totaldetalle= $totaldetalles;
					$ecajachica->etapa= 2;
					$ecajachica->save();		
		    }
			    
				Sity::RegistrarEnBitacora(1, 'dte_cajachicas', $dato->id, $det);
				Session::flash('success', 'El detalle de ecajachica No. ' .$dato->id. ' ha sido creado con éxito.');
		    DB::commit();				
				return redirect()->route('dte_cajachicas.show', $dato->factura_id);
			}
	        
      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo Dte_ecajachicasController.store, la transaccion ha sido cancelada! '.$e->getMessage());

			return back()->withInput()->withErrors($validation);
		}
	}
    
  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/	
	public function destroy($dte_ecajachica_id)
	{
		
		DB::beginTransaction();
		try {
			//dd($dte_ecajachica_id);
			$dato = Dte_ecajachica::find($dte_ecajachica_id);
			$dato->delete();			

			// Registra en bitacoras
			$det =	'Borra detalle de egreso de factura '.$dato->id. ', '.
					'cantidad= '.   		$dato->cantidad. ', '.
					'precio= '.   			$dato->precio. ', '.
					'itbms= '.   				$dato->itbms. ', '.
					'ecajachica_id= '. 	$dato->ecajachica_id;
			
			$totaldetalles = 0;

	    //calcula el total de detallefacturas para la presente factura
 	    $detalles= Dte_ecajachica::where('ecajachica_id', $dato->ecajachica_id)->get();		    
			//dd($detalles->toArray());
			
			foreach ($detalles as $detalle) {
				$totaldetalles=$totaldetalles +(($detalle->precio * $detalle->cantidad)+$detalle->itbms);
			}
			//dd($totaldetalles);
	    
	    $ecajachica= Ecajachica::find($dato->ecajachica_id);
	    if (round(floatval($ecajachica->total),2) == round(floatval($totaldetalles),2)) {
				$ecajachica->totaldetalle= $totaldetalles;
				$ecajachica->etapa = 1;
				$ecajachica->save();		
	    
	    } else {
				$ecajachica->totaldetalle = $totaldetalles;
				$ecajachica->etapa = 0;
				$ecajachica->save();
	    }
			
			Sity::RegistrarEnBitacora(3, 'dte_ecajachicas', $dato->id, $det);
			Session::flash('success', 'El detalle "' .$dato->detalle .'" ha sido borrado permanentemente de la base de datos.');
			DB::commit();
			return redirect()->route('dte_cajachicas.show', $dato->ecajachica_id);
		
		} catch (\Exception $e) {
	    DB::rollback();
    	Session::flash('warning', ' Ocurrio un error en el modulo DtecajachicasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());

    	return back();
		}
	}
} 