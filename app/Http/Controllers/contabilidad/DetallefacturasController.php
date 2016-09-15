<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use App\library\Sity;
use App\Http\Helpers\Grupo;
use Validator;

use App\Org;
use App\Factura;
use App\Catalogo;
use App\Detallefactura;
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
        		->join('catalogos', 'catalogos.id', '=', 'detallefacturas.catalogo_id')
                ->select('detallefacturas.id','detallefacturas.cantidad','detallefacturas.detalle','detallefacturas.precio','detallefacturas.itbms','detallefacturas.factura_id','catalogos.codigo')
                ->get();
        //dd($datos->toArray());		

		$datos_2= $datos->lists('detalle', 'id');       
		$datos_2= $datos_2->toArray();
  		//dd($datos_2);
	   
	    $factura= Factura::find($factura_id);

	    //Obtiene todas las cuenta de gastos asignadas a un determinado proveedor
 	    $datos_1= Org::find($factura->org_id)->catalogos;
	    $datos_1= $datos_1->lists('nombre_factura', 'id');       
		$datos_1= $datos_1->toArray();
	    //dd($datos_1, $datos_2);
		
		// calcula y agrega el total
		$i=0;		
		foreach ($datos as $dato) {
		    $datos[$i]["total"] = number_format((($dato->precio * $dato->cantidad)+$dato->itbms),2);
		    $i++;
		}        
        
        // Subtrae de la lista total de cuentas de gastos registrados toda aquellas
        // que ya están asignadas a una organizacion
        // para evitar asignar cuentas previamente asignadas
		$kresultadoctas = array_diff($datos_1, $datos_2);		
		//dd($kresultadoctas);  

		return view('contabilidad.detallefacturas.show')
				->with('kresultadoctas', $kresultadoctas)
				->with('factura', $factura)
				->with('datos', $datos);     	
	}	

    /*************************************************************************************
     * Almacena un nuevo registro en la base de datos
     ************************************************************************************/	
	public function store()
	{
        //dd(Input::all());
        $input = Input::all();
        $rules = array(
            'factura_id'		=> 'required',
            'catalogo_id'  		=> 'Required',
            'precio'    		=> 'required|Numeric|min:0.01',
            'itbms'    			=> 'required|Numeric|min:0',
        );
    
        $messages = [
            'required'		=> 'Informacion requerida!',
        	'numeric'		=> 'Solo se admiten valores numericos!',
        	'min'			=> 'El precio del servicio debe ser mayor que cero!'
        ];        
            
        $validation = \Validator::make($input, $rules, $messages);      	

		if ($validation->passes())
		{
			
			// encuentra el valor total en la factura			
        	$factura= Factura::find(Input::get('factura_id'));
        	$totalfactura= $factura->total;

        	// encuentra las generales de la cuenta
			$detallecta= Catalogo::find(Input::get('catalogo_id'));
			//dd($detallecta->toArray());

			$dato = new Detallefactura;
			$dato->factura_id       	= Input::get('factura_id');
			$dato->catalogo_id 	   		= Input::get('catalogo_id');
			$dato->detalle 	       		= $detallecta->nombre_factura;
			$dato->precio 	       		= Input::get('precio');
			$dato->itbms 	       		= Input::get('itbms');
			$dato->save();	
			
			// Registra en bitacoras
			$det =	'factura_id= '.			$dato->factura_id. ', '.
					'catalogo_id= '.	    $dato->catalogo_id. ', '.
					'detalle= '.   			$detallecta->nombre_factura. ', '.
					'precio= '.   			$dato->precio. ', '.
					'itbms= '.   			$dato->itbms;

			$totaldetalles=0;

		    //calcula el total de detallefacturas para la presente factura
	 	    $detalles= Detallefactura::where('factura_id', Input::get('factura_id'))->get();		    
			//dd($detalles->toArray());
			foreach ($detalles as $detalle) {
				$totaldetalles=$totaldetalles +($detalle->precio+$detalle->itbms);
			}
			//dd($total);
		    if (round(floatval($totalfactura),2) < round(floatval($totaldetalles),2)) {
		        Session::flash('danger', '<< ERROR >> El valor total de los detalles no puede sobrepasar al valor total de la factura. Borre el detalle e intente nuevamente!');
		        return Redirect::back();
		    	
		    } elseif (round(floatval($totalfactura),2) > round(floatval($totaldetalles),2)) {
		        Session::flash('warning', '<< ATENCION >> El valor total de los detalles es inferior al valor total de la factura. Continue ingresando detalles!');
		        return Redirect::back();

		    } elseif (round(floatval($totalfactura),2) == round(floatval($totaldetalles),2)) {
				$factura->totaldetalle= $totaldetalles;
				$factura->etapa= 1;
				$factura->save();		
		    	
		    } else {
				$factura->totaldetalle= $totaldetalles;
				$factura->etapa= 0;
				$factura->save();
		    }

			Sity::RegistrarEnBitacora(1, 'detallefacturas', $dato->id, $det);
			Session::flash('success', 'El detalle de factura No. ' .$dato->id. ' ha sido creado con éxito.');
			return Redirect::route('detallefacturas.show', $dato->factura_id);
		}
        Session::flash('danger', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
        return Redirect::back()->withInput()->withErrors($validation);
	}
    
    /*************************************************************************************
     * Borra registro de la base de datos
     ************************************************************************************/	
	public function destroy($detallefactura_id)
	{
		//dd($detallefactura_id);
		$dato = Detallefactura::find($detallefactura_id);
		$dato->delete();			

		// Registra en bitacoras
		$det =	'Borra detalle de Factura '.$dato->no. ', '.
				'cantidad= '.   		$dato->cantidad. ', '.
				'detalle= '.   			$dato->detalle. ', '.
				'precio= '.   			$dato->precio. ', '.
				'itbms= '.   			$dato->itbms. ', '.
				'factura_id= '. 		$dato->factura_id;
		
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
			$factura->etapa= 1;
			$factura->save();		
	    	
	    } else {
			$factura->totaldetalle= $totaldetalles;
			$factura->etapa= 0;
			$factura->save();
	    }
		
		Sity::RegistrarEnBitacora(3, 'detallefacturas', $dato->id, $det);
		Session::flash('success', 'El detalle "' .$dato->detalle .'" ha sido borrado permanentemente de la base de datos.');

		return Redirect::route('detallefacturas.show', $dato->factura_id);
	}
} 