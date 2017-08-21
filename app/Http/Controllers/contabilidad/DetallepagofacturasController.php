<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB, Validator, Date;
use App\library\Sity;
use App\library\Npago;
use Carbon\Carbon;

use App\Org;
use App\Factura;
use App\Catalogo;
use App\Detallepagofactura;
use App\Bitacora;
use App\Pcontable;
use App\Ctdiario;
use App\Trantipo;
use App\Diariocaja;

class DetallepagofacturasController extends Controller {
  public function __construct()
  {
     	$this->middleware('hasAccess');    
  }
  
  /*************************************************************************************
   * Despliega un grupo de registros en formato de tabla
   ************************************************************************************/	
	public function show($factura_id)
	{
    $datos = Detallepagofactura::where('factura_id', $factura_id)->get();
    //dd($datos->toArray());		
	    
    // obtiene todos los diferentes tipos de pagos
    $trantipos= Trantipo::pluck('nombre', 'id')->all();
    $trantipos= Trantipo::orderBy('nombre')->get();		
		//dd($trantipos);	

		foreach ($datos as $dato) {
			if ($dato->fecha) {
			  $dato->fecha= Date::parse($dato->fecha)->toFormattedDateString();
			}        
		}
    //dd($datos->toArray());

    $factura= Factura::find($factura_id);
		
		return view('contabilidad.detallepagofacturas.show')
				->with('factura', $factura)
				->with('trantipos', $trantipos)
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
			    'factura_id'		=> 'required',
			    'fecha'    			=> 'required|Date',
			    'detalle'    		=> 'Required',
			    'monto'    			=> 'required|Numeric|min:0.01'
			);

			$messages = [
			  'required'	=> 'Informacion requerida!',
				'numeric'		=> 'Solo se admiten valores numericos!',
				'date'			=> 'Fecha invalida!',
				'min'				=> 'Se requiere un valor mayor que cero!'
			];        
			    
			$validation = \Validator::make($input, $rules, $messages);      	
			if ($validation->passes())
			{
				
			  // encuentra el periodo mas antiguo abierto
				$periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
			  //dd($periodo);
		    
		    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
		    if (Carbon::parse($periodo->fecha)->gt(Carbon::parse(Input::get('fecha')))) {
					Session::flash('danger', 'Solamente se permite programar pagos cuya fecha se mayor o igual al dia primero del periodo vigente de '.$periodo->periodo);
					return back()->withInput()->withErrors($validation);
		    }

		    // encuentra la fecha del ultimo pago programado de la presente factura que haya sido pagado y contabilizado
	 	    $ultimaFecha = Detallepagofactura::orderBy('fecha', 'desc')->where('factura_id', Input::get('factura_id'))->where('etapa', 2)->first();
				//dd(Carbon::parse($ultimaFecha->fecha), Carbon::parse(Input::get('fecha')));

				if ($ultimaFecha) {
			    // solamente se permite programar pagos cuya fecha se mayor o igual a la fecha del ultimo pago programado pagado y contabilizado
			    if (Carbon::parse(Input::get('fecha'))->lt(Carbon::parse($ultimaFecha->fecha))) {
						Session::flash('danger', 'Solamente se permite programar pagos con fecha igual o posterior a '. Date::parse($ultimaFecha->fecha)->toFormattedDateString());
						return back()->withInput()->withErrors($validation);
			    }
				}

				// encuentra el monto total de la factura			
				$factura = Factura::find(Input::get('factura_id'));
				$totalfactura = round(floatval($factura->total),2);
				//dd($totalfactura);			
			
		    // verifica que el monto del nuevo detalle no sobrepase al monto total de la factura
		    if (Input::get('monto')>$totalfactura) {
					Session::flash('danger', 'Monto del pago programado sobrepasa al monto total de la factura!');
					return redirect()->route('detallepagofacturas.show', $factura->id);
		    } 

		    // cuenta la cantidad de detalles en la factura
	 	    $cantDetalles = Detallepagofactura::where('factura_id', Input::get('factura_id'))->count('id');
	 	    
	 	    // si no existen detalles en la factura y el monto total de la factura es igual al monto del nuevo detalle
	 	    // entonces se trata de un pago completo
	 	    if ($cantDetalles == 0 && ($totalfactura == Input::get('monto'))) {
					// salva el nuevo detalle
					$dato = new Detallepagofactura;
					$dato->factura_id     = Input::get('factura_id');
					$dato->fecha 	   			= Input::get('fecha');
					$dato->detalle 	      = Input::get('detalle');
					$dato->monto 	       	= Input::get('monto');
					$dato->pagotipo				= 'completo';
					$dato->save();	
  				
  				Sity::RegistrarEnBitacora($dato, Input::get(), 'Detallepagofactura', 'Programa pago completo de factura de egreso de Caja general');
					
					// actualiza el monto de los detalles en la factura
					$factura->totalpagodetalle= Input::get('monto');
					$factura->save();
	 	    
	 	    } else {	// se trata de un pago parcial
			    // calcula el monto total de los detalles de la presente factura mas el monto del nuevo detalle
		 	    $totaldetalles = Detallepagofactura::where('factura_id', Input::get('factura_id'))->sum('monto');		    
					$totaldetalles = round(floatval($totaldetalles + Input::get('monto')),2);
					//dd($totaldetalles);

			    // verifica que el monto total de los detalle incluyendo al nuevo no sobrepase al monto total de la factura
			    if ($totaldetalles > $totalfactura) {
					Session::flash('danger', 'Monto del pago programado sobrepasa al monto total de la factura!');
					return redirect()->route('detallepagofacturas.show', $factura->id);
			   
			    } else {
						// salva el nuevo detalle
						$dato = new Detallepagofactura;
						$dato->factura_id    	= Input::get('factura_id');
						$dato->fecha 	   			= Input::get('fecha');
						$dato->detalle 	      = Input::get('detalle');
						$dato->monto 	       	= Input::get('monto');
						$dato->pagotipo				= 'parcial';
						$dato->save();	

						Sity::RegistrarEnBitacora($dato, Input::get(), 'Detallepagofactura', 'Programa pago parcial de factura de egreso de Caja general');
			    }
	 	    }
	 	    
				//Sity::RegistrarEnBitacora(1, 'detallepagofacturas', $dato->id, Input::all());
				Session::flash('success', 'Se ha programado programado el pago con con éxito.');
				DB::commit();				
				return redirect()->route('detallepagofacturas.show', $dato->factura_id);
			}

			Session::flash('warning', 'Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', 'Ocurrio un error en el modulo DetallepagofacturaController.store, la transaccion ha sido cancelada! '.$e->getMessage());
			return back()->withInput()->withErrors($validation);
		}
	}
    
  /*************************************************************************************
   * Almacena un nuevo registro en la base de datos
   ************************************************************************************/	
	public function pagarContabilizar()
	{
        
		DB::beginTransaction();
		try {

			//dd(Input::all());
			$input = Input::all();
			
			if (Input::get('trantipo_id') == 1) {
				$rules = array(
				  'trantipo_id'	=> 'Required',
			  	'chqno'	=> 'Required'
				);			

			} elseif (Input::get('trantipo_id') == 5) {
				$rules = array(
				  'trantipo_id'	=> 'Required'
				);	
			
			} else {
				$rules = array(
				  'trantipo_id'	=> 'Required',
			  	'transno'	=> 'Required'
				);	
			}

			$messages = [
			  'required'	=> 'Informacion requerida!',
				'numeric'		=> 'Solo se admiten valores numericos!',
				'date'			=> 'Fecha invalida!',
				'min'				=> 'Se requiere un valor mayor que cero!'
			];        
			    
			$validation = \Validator::make($input, $rules, $messages);      	
			if ($validation->passes())
			{

				// salva el nuevo detalle
				$detallepagofactura_id = Input::get('detallepagofactura_id');
				$dato = Detallepagofactura::find($detallepagofactura_id);

		    // verifica que exista un periodo de acuerdo a la fecha de pago
		    $year = Carbon::parse($dato->fecha)->year;
		    $month = Carbon::parse($dato->fecha)->month;
		    $pdo = Sity::getMonthName($month).'-'.$year;

		    // encuentra el periodo mas antiguo abierto
		    $periodo = Pcontable::where('cerrado',0)->orderBy('id')->first();
		    //dd($periodo);
		    
		    // solamente se permite registrar pagos de facturas que correspondan al periodo mas antiguo abierto
		    if ($pdo != $periodo->periodo) {
		      Session::flash('danger', 'Solamente se permite contabilizar pagos que correspondan al periodo vigente de '.$periodo->periodo);
		      return back();
		    }

		    // verifica si existe algun pago programado anterior al presente que no haya sido contabilizado
		    $exiteAnterior= Detallepagofactura::whereDate('fecha', '<', Carbon::parse($dato->fecha)->toDateString())->where('etapa', 1)->first();
		    if ($exiteAnterior) {
		      Session::flash('warning', 'Debe realizar sus pagos programados en orden cronologico!');
		      return back();
		    }

				$dato->trantipo_id = Input::get('trantipo_id');
				
				if (Input::get('trantipo_id') == 1) {
					$dato->doc_no = Input::get('chqno');
				
				} elseif (Input::get('trantipo_id') == 5) {
				
				} else {
					$dato->doc_no = Input::get('transno');		
				}
				$dato->save();	

				// paga y contabiliza el pago programado
				Npago::contabilizaDetallePagoFactura($detallepagofactura_id, $periodo);

				//Sity::RegistrarEnBitacora($dato, Input::get(), 'Detallepagofactura', 'Programa pago parcial de factura de egreso de Caja general');
				Session::flash('success', 'Pago realizado con exito!');
				DB::commit();				
				return redirect()->route('detallepagofacturas.show', $dato->factura_id);	 	  
	 	  }

			Session::flash('warning', 'Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallepagofacturaController.pagarContabilizar, la transaccion ha sido cancelada! '.$e->getMessage());
			return back()->withInput()->withErrors($validation);
		}
	}

  /*************************************************************************************
   * Borra registro de la base de datos
   ************************************************************************************/	
	public function destroy($detallepagofactura_id)
	{
		DB::beginTransaction();
		try {
	    // verifica si existe algun pago programado posterior al presente
			//$exitePosterior= Detallepagofactura::where('id', '>', $detallepagofactura_id)->first();
	    
	    //if ($exitePosterior) {
        //Session::flash('danger', 'No puede eliminar el presente pago programado, solo se puede eliminar el ultimo pago programado!');
    		//return back();
	   // }
			//dd($detallefactura_id);
			
			$dato = Detallepagofactura::find($detallepagofactura_id);
			$dato->delete();			
		
		  // actualiza el totalpagodetalle en la tabla facturas
		  $factura= Factura::find($dato->factura_id);
			$factura->totalpagodetalle = $factura->totalpagodetalle - $dato->monto;
			$factura->save();		
			
  		Sity::RegistrarEnBitacora($dato, Null, 'Factura', 'Elimina pago programado por Caja general'); 
			
			DB::commit();			
			Session::flash('info', 'Pago programado ha sido eliminado con exito!');
			return redirect()->route('detallepagofacturas.show', $dato->factura_id);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallepagofacturasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}

  /***********************************************************************************
  * Despliega el estado de resultado final
  ************************************************************************************/ 
  public function facturasporpagar() {
    
    $datos = Detallepagofactura::where('pagada', '=', 0)->orderBy('fecha', 'desc')->get();
    //dd($datos->toArray());

    // calcula el total por pagar
    $totalPorPagar = $datos->sum('monto');
    //dd($totalPorPagar);
    
    $i = 0;
    foreach ($datos as $dato) {
      // agrega los datos a la collection
      $datos[$i]["afavorde"] = $dato->factura->afavorde;
      $datos[$i]["f_pago"] = Date::parse($dato->fecha)->toFormattedDateString();
      $i++;
    }

    //dd($datos->toArray());

    return \View::make('contabilidad.detallepagofacturas.facturasporpagar')
            ->with('datos', $datos)
            ->with('totalPorPagar', $totalPorPagar);
  } 

  /***********************************************************************************
  * Despliega el estado de resultado final
  ************************************************************************************/ 
  public function facturasporpagarhoy() {
    
    $datos = Detallepagofactura::whereDate('fecha',  Carbon::today())
    													->where('pagada', '=', 0)
    													->orderBy('fecha', 'desc')
    													->get();
    //dd($datos->toArray());

    // obtiene todos los diferentes tipos de pagos
    $trantipos= Trantipo::pluck('nombre', 'id')->all();
    $trantipos= Trantipo::orderBy('nombre')->get();		
		//dd($trantipos);	

    // calcula el total por pagar
    $totalPorPagar = $datos->sum('monto');
    //dd($totalPorPagar);
    
    $i = 0;
    foreach ($datos as $dato) {
      // agrega los datos a la collection
      $datos[$i]["afavorde"] = $dato->factura->afavorde;
      $datos[$i]["f_pago"] = Date::parse($dato->fecha)->toFormattedDateString();
      $i++;
    }

    //dd($datos->toArray());

    return \View::make('contabilidad.detallepagofacturas.facturasporpagarhoy')
            ->with('trantipos', $trantipos)
            ->with('datos', $datos)
            ->with('totalPorPagar', $totalPorPagar);
  } 

} 