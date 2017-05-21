<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Session, DB, Validator, Date;
use App\library\Sity;
use App\library\Npago;

use App\Http\Helpers\Grupo;
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
					Session::flash('danger', '<< ERROR >> Solamente se permite registrar pago de facturas de gastos cuya fecha se mayor o igual al dia primero del periodo vigente de '.$periodo->periodo);
					return back()->withInput()->withErrors($validation);
		    }

		    // encuentra la fecha del ultimo pago programado de la presente factura
	 	    $ultimaFecha = Detallepagofactura::orderBy('id', 'desc')->where('factura_id', Input::get('factura_id'))->first();
				//dd($ultimaFecha);

				if ($ultimaFecha) {
			    // solamente se permite registrar facturas de gastos que correspondan al periodo mas antiguo abierto
			    if (Carbon::parse($ultimaFecha->fecha)->gte(Carbon::parse(Input::get('fecha')))) {
						Session::flash('danger', '<< ERROR >> La fecha del pago programado debera ser mayor o igual a la ultima fecha de pago programado');
						return back()->withInput()->withErrors($validation);
			    }
				}

				// encuentra el monto total de la factura			
				$factura = Factura::find(Input::get('factura_id'));
				$totalfactura = round(floatval($factura->total),2);
				//dd($totalfactura);			
			
		    // verifica que el monto del nuevo detalle no sobrepase al monto total de la factura
		    if (Input::get('monto')>$totalfactura) {
					Session::flash('danger', '<< Error >>: No se pudo agregar el nuevo detalle ya que su monto sobrepasa al monto total de la factura!');
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
					Session::flash('danger', '<< Error >>: No se pudo agregar el nuevo detalle ya con su monto sobrepasaria al monto total de la factura!');
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
				Session::flash('success', 'El detalle de factura No. ' .$dato->id. ' ha sido creado con éxito.');
				DB::commit();				
				return redirect()->route('detallepagofacturas.show', $dato->factura_id);
			}

			Session::flash('warning', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallepagofacturaController.store, la transaccion ha sido cancelada! '.$e->getMessage());
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
		      Session::flash('danger', '<< ERROR >> Solamente se permite contabilizar pagos que correspondan al periodo vigente de '.$periodo->periodo);
		      return back();
		    }

		    // verifica si existe algun detalle de pago anterior al presente que no haya sido contabilizado
		    $exiteAnterior= Detallepagofactura::where('id', '<', $detallepagofactura_id)->where('etapa', 1)->first();
		    if ($exiteAnterior) {
		      Session::flash('danger', '<< ERROR >> Debe pagar y contabilizar los detalles de pago en orden cronologico!');
		      return back();
		    }

				$dato->trantipo_id = Input::get('trantipo_id');
				
				if (Input::get('trantipo_id') == 1) {
					$dato->doc_no = Input::get('chqno');

				} elseif (Input::get('trantipo_id') != 5) {
					$dato->doc_no = Input::get('transno');		
				}
				$dato->save();	

				// paga y contabiliza el pago programado
				Npago::contabilizaDetallePagoFactura($detallepagofactura_id, $periodo);

				//Sity::RegistrarEnBitacora($dato, Input::get(), 'Detallepagofactura', 'Programa pago parcial de factura de egreso de Caja general');
				Session::flash('success', 'El detalle de factura No. ' .$dato->id. ' ha sido creado con éxito.');
				DB::commit();				
				return redirect()->route('detallepagofacturas.show', $dato->factura_id);	 	  
	 	  }

			Session::flash('warning', '<< ATENCION >> Se encontraron errores en su formulario, recuerde llenar todos los campos!');
			return back()->withInput()->withErrors($validation);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallepagofacturaController.store, la transaccion ha sido cancelada! '.$e->getMessage());
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
			$exitePosterior= Detallepagofactura::where('id', '>', $detallepagofactura_id)->first();
	    
	    if ($exitePosterior) {
        Session::flash('danger', '<< ERROR >> No puede eliminar el presente pago programado, solo se puede eliminar el ultimo pago programado!');
    		return back();
	    }
			//dd($detallefactura_id);
			
			$dato = Detallepagofactura::find($detallepagofactura_id);
			$dato->delete();			
		
		  // actualiza el totalpagodetalle en la tabla facturas
		  $factura= Factura::find($dato->factura_id);
			$factura->totalpagodetalle = $factura->totalpagodetalle - $dato->monto;
			$factura->save();		
			
  		Sity::RegistrarEnBitacora($dato, Null, 'Factura', 'Elimina pago programado por Caja general'); 
			
			DB::commit();			
			Session::flash('success', 'El detalle de factura "' .$dato->detalle .'" con monto de B/.'. $dato->monto.' ha sido borrado permanentemente de la base de datos.');
			return redirect()->route('detallepagofacturas.show', $dato->factura_id);
		
		} catch (\Exception $e) {
			DB::rollback();
			Session::flash('warning', ' Ocurrio un error en el modulo DetallepagofacturasController.destroy, la transaccion ha sido cancelada! '.$e->getMessage());
			return back();
		}
	}

} 