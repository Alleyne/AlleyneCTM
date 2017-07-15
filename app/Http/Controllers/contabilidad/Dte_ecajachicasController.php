<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB, Validator;
use App\library\Sity;
use Jenssegers\Date\Date;

use App\Ecajachica;
use App\Dte_ecajachica;
use App\Catalogo;
use App\Bitacora;
use App\Serviproducto;
use App\Org;

class Dte_ecajachicasController extends Controller {
  
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
            ->select('dte_ecajachicas.id','serviproductos.catalogo_id','serviproductos.nombre','serviproductos.tipo', 'dte_ecajachicas.cantidad','dte_ecajachicas.precio','dte_ecajachicas.itbms')
            ->get();
    //dd($datos->toArray());		

    // encuentra los datos generales del encabezado de egreso de caja chica
    $ecajachica= Ecajachica::find($ecajachica_id);
    $ecajachica['fecha'] = Date::parse($ecajachica->fecha)->toFormattedDateString();
		//dd($ecajachica->toArray());

    //Obtiene todos los productos registrados en la factura de egresos de caja chica
    $datos_1= $datos->where('tipo', 0);
    $datos_1= $datos_1->pluck('nombre', 'id')->all();       
    //dd($datos_2);
        
    //Obtiene todos los servicios registrados en la factura de egresos de caja chica
    $datos_2= $datos->where('tipo', 1);
    $datos_2= $datos_2->pluck('nombre', 'id')->all();       
   
    // encuentra todos los productos asignados a un determinado proveedor
		$datos_3 = Org::find($ecajachica->org_id)->serviproductos()->where('tipo', 0)->where('activo', 1);
    $datos_3= $datos_3->pluck('nombre', 'serviproductos.id')->all();   
    
    // encuentra todos los servicios asignados a un determinado proveedor
		$datos_4 = Org::find($ecajachica->org_id)->serviproductos()->where('tipo', 1)->where('activo', 1);
    $datos_4= $datos_4->pluck('nombre', 'serviproductos.id')->all(); 

    // Subtrae de la lista total de productos de la tabla serviproductos,
    // todos los productos ya registrados en la factura de egresos de caja chica.
    // para evitar asignar productos previamente asignadas
		$productos = array_diff($datos_3, $datos_1);		
		//dd($productos);  
    
    // Subtrae de la lista total de servicios de la tabla serviproductos,
    // todos los servicios ya registrados en la factura de egresos de caja chica.
    // para evitar asignar servicios previamente asignadas
    $servicios = array_diff($datos_4, $datos_2);    
    //dd($servicios); 		
 		
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

		return view('contabilidad.dte_ecajachicas.show')
				 ->with('productos', $productos)
				 ->with('servicios', $servicios)
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
      
			if (Input::get('tipo_radios') == 0) {
	      // es un producto
	      $rules = array(
	        'producto_id'	=> 'required',
	        'cantidad'  	=> 'Required|Numeric',
	        'precio'    	=> 'required|Numeric|min:0.01',
	        'itbms'    		=> 'required|Numeric|min:0'
	      );
			
			} else {
	      // es un servicio
	      $rules = array(
	        'servicio_id'	=> 'required',
	        'precio'    	=> 'required|Numeric|min:0.01',
	        'itbms'    		=> 'required|Numeric|min:0'
	      );
			}
  
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

				if (Input::get('tipo_radios') == 0) {
					$serviproducto_id = Input::get('producto_id');
					$cantidad = Input::get('cantidad');

				} else {
					$serviproducto_id = Input::get('servicio_id');
					$cantidad = 1;
				}				

				// encuentra los datos del serviproducto
      	$serviproducto= Serviproducto::find($serviproducto_id);				

				$dato = new Dte_ecajachica;
				$dato->serviproducto_id  	= $serviproducto_id;
				$dato->nombre					  	= $serviproducto->nombre;
				$dato->cantidad 	       	= $cantidad;
				$dato->precio 	       		= Input::get('precio');
				$dato->itbms 	       			= Input::get('itbms');
				$dato->ecajachica_id	  	= Input::get('ecajachica_id');
				$dato->catalogo_id				= $serviproducto->catalogo->id;
				$dato->codigo	  					= $serviproducto->catalogo->codigo;
				$dato->cuenta	  					= $serviproducto->catalogo->nombre;
				$dato->save();
				
				$totaldetalles= 0;

			  //calcula el total de detallefacturas para la presente factura
		 	  $detalles= Dte_ecajachica::where('ecajachica_id', Input::get('ecajachica_id'))->get();		    
				//dd($detalles->toArray());
				
				foreach ($detalles as $detalle) {
					$totaldetalles = $totaldetalles + (($detalle->cantidad * $detalle->precio) + $detalle->itbms);
				}
				//dd((float)$totalecajachica, (float)$totaldetalles);
		    
		    if ((float)$totalecajachica < (float)$totaldetalles) {
	        Session::flash('danger', '<< ERROR >> El valor total de los detalles no puede sobrepasar al valor total de la factura de egresos. Intente nuevamente!');
	        DB::rollback();
	        return back()->withInput()->withErrors($validation);
		    	
		    } elseif ((float)$totalecajachica > (float)$totaldetalles) {
					$ecajachica->totaldetalle= $totaldetalles;
					$ecajachica->save();
		    
		    } elseif ((float)$totalecajachica == (float)$totaldetalles) {
					$ecajachica->totaldetalle= $totaldetalles;
					$ecajachica->etapa= 2;
					$ecajachica->save();		
		    }
			    
				Sity::RegistrarEnBitacora($dato, Input::get(), 'Dte_ecajachica', 'Registra detalle de Caja chica');
				
				Session::flash('success', 'El detalle de ecajachica No. ' .$dato->id. ' ha sido creado con éxito.');
		    DB::commit();				
				return redirect()->route('dte_ecajachicas.show', Input::get('ecajachica_id'));
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
				$ecajachica->etapa = 2;
				$ecajachica->save();		
	    
	    } else {
				$ecajachica->totaldetalle = $totaldetalles;
				$ecajachica->etapa = 1;
				$ecajachica->save();
	    }
			
  		Sity::RegistrarEnBitacora($dato, Null, 'Dte_ecajachica', 'Elimina detalle de egreso de Caja chica');   

			Session::flash('success', 'El detalle "' .$dato->nombre .'" ha sido borrado de la factura.');
			DB::commit();
			return redirect()->route('dte_ecajachicas.show', $dato->ecajachica_id);
		
		} catch (\Exception $e) {
	    DB::rollback();
    	Session::flash('warning', ' Ocurrio un error en el modulo DtecajachicasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());

    	return back();
		}
	}
} 