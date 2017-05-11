<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;

use App\Org;
use App\Factura;
use App\Catalogo;
use App\Detallefactura;
use App\Serviproducto;
use App\Bitacora;

class DetallefacturasController extends Controller {
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function show($factura_id)
	{
    
    $datos = Detallefactura::where('factura_id', $factura_id)
    				->join('serviproductos', 'serviproductos.id', '=', 'detallefacturas.serviproducto_id')
            ->select('detallefacturas.id','serviproductos.catalogo_id','serviproductos.nombre','serviproductos.tipo','detallefacturas.cantidad','detallefacturas.precio','detallefacturas.itbms')
            ->get();
    //dd($datos->toArray());		
    
    // encuentra los datos generales del encabezado de la factura de caja general
    $factura= Factura::find($factura_id);
		//dd($factura->toArray())
   
    //Obtiene todos los productos registrados en la factura de egresos de caja chica
    $datos_1= $datos->where('tipo', 0);
    $datos_1= $datos_1->pluck('nombre', 'id')->all();       
    //dd($datos_2);
        
    //Obtiene todos los servicios registrados en la factura de egresos de caja chica
    $datos_2= $datos->where('tipo', 1);
    $datos_2= $datos_2->pluck('nombre', 'id')->all();       
   
    // encuentra todos los productos asignados a un determinado proveedor
		$datos_3 = Org::find($factura->org_id)->serviproductos()->where('tipo', 0)->where('activo', 1);
    $datos_3= $datos_3->pluck('nombre', 'serviproductos.id')->all();   
    
    // encuentra todos los servicios asignados a un determinado proveedor
		$datos_4 = Org::find($factura->org_id)->serviproductos()->where('tipo', 1)->where('activo', 1);
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

		return view('contabilidad.detallefacturas.show')
				 ->with('productos', $productos)
				 ->with('servicios', $servicios)
				 ->with('factura', $factura)
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
      	$factura= Factura::find(Input::get('factura_id'));
      	$totalfactura= $factura->total;

				if (Input::get('tipo_radios') == 0) {
					$serviproducto_id = Input::get('producto_id');
					$cantidad = Input::get('cantidad');

				} else {
					$serviproducto_id = Input::get('servicio_id');
					$cantidad = 1;
				}		

				// encuentra los datos del serviproducto
      	$serviproducto= Serviproducto::find($serviproducto_id);
				
				$dato = new Detallefactura;
				$dato->serviproducto_id  	= $serviproducto_id;
				$dato->nombre					  	= $serviproducto->nombre;
				$dato->cantidad 	       	= $cantidad;
				$dato->precio 	       		= Input::get('precio');
				$dato->itbms 	       			= Input::get('itbms');
				$dato->factura_id	  			= Input::get('factura_id');
				$dato->catalogo_id				= $serviproducto->catalogo->id;
				$dato->codigo	  					= $serviproducto->catalogo->codigo;
				$dato->cuenta	  					= $serviproducto->catalogo->nombre;
				$dato->save();
				
  			Sity::RegistrarEnBitacora($dato, Input::get(), 'Detallefactura', 'Registra detalle de factura de egreso de Caja general');

				$totaldetalles= 0;

			  //calcula el total de detallefacturas para la presente factura
		 	  $detalles= Detallefactura::where('factura_id', Input::get('factura_id'))->get();		    
				//dd($detalles->toArray());
				
				foreach ($detalles as $detalle) {
					$totaldetalles = $totaldetalles + (($detalle->cantidad * $detalle->precio) + $detalle->itbms);
				}
				//dd($totaldetalles);
			    
		    if (round(floatval($totalfactura),2) < round(floatval($totaldetalles),2)) {
	        Session::flash('danger', '<< ERROR >> El valor total de los detalles no puede sobrepasar al valor total de la factura de egresos. Intente nuevamente!');
	        DB::rollback();
	        return back()->withInput()->withErrors($validation);
		    	
		    } elseif (round(floatval($totalfactura),2) > round(floatval($totaldetalles),2)) {
					$factura->totaldetalle= $totaldetalles;
					$factura->save();
			    Session::flash('warning', '<< ATENCION >> El valor total de los detalles es inferior al valor total de la factura egresos. Continue ingresando detalles!');
		    
		    } elseif (round(floatval($totalfactura),2) == round(floatval($totaldetalles),2)) {
					$factura->totaldetalle= $totaldetalles;
					$factura->etapa= 2;
					$factura->save();		
		    }
		    
		    DB::commit();				    
				
				Session::flash('success', 'El detalle de factura No. ' .$dato->id. ' ha sido creado con éxito.');
				return redirect()->route('detallefacturas.show', $dato->factura_id);
			}
	        
      Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
      return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallefacturasController.store, la transaccion ha sido cancelada! '.$e->getMessage());

			return back()->withInput()->withErrors($validation);
		}
	}

  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/	
	public function destroy($detallefactura_id)
	{
		
		DB::beginTransaction();
		try {
			//dd($detallefactura_id);
			$dato = Detallefactura::find($detallefactura_id);
			$dato->delete();			

			$totaldetalles=0;

	    //calcula el total de detallefacturas para la presente factura
 	    $detalles= Detallefactura::where('factura_id', $dato->factura_id)->get();		    
			//dd($detalles->toArray());
			foreach ($detalles as $detalle) {
				$totaldetalles=$totaldetalles +(($detalle->precio * $detalle->cantidad)+$detalle->itbms);
			}

	    $factura= Factura::find($dato->factura_id);
	    if (round(floatval($factura->total),2) == round(floatval($totaldetalles),2)) {
			$factura->totaldetalle= $totaldetalles;
			$factura->etapa= 2;
			$factura->save();		
	    	
	    } else {
			$factura->totaldetalle= $totaldetalles;
			$factura->etapa= 1;
			$factura->save();
	    }
			
			Sity::RegistrarEnBitacora($dato, Null, 'Detallefactura', 'Elimina detalle de egreso de factura de Caja general');   
			Session::flash('success', 'El detalle "' .$dato->detalle .'" ha sido borrado permanentemente de la base de datos.');
			DB::commit();
			return redirect()->route('detallefacturas.show', $dato->factura_id);
		
		} catch (\Exception $e) {
	    DB::rollback();
    	Session::flash('warning', ' Ocurrio un error en el modulo DetallefacturasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());
    	return back()->withInput()->withErrors($validation);
		}
	}
} 