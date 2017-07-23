<?php namespace App\library;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use App\library\Sity;
use App\library\Pant;
//use App\Notifications\emailNuevaOcobro;
use App\Notifications\emailUsoDeCuentaAnticipados;
use Session, DB, Log;

use App\Un;
use App\Prop;
use App\User;
use App\Ctdasm;
use App\Catalogo;
use App\Ctdiario;
use App\Detallepago;
use App\Pcontable;
use App\Ctmayore;
use App\Pago;

class Ppago {

  // variable global a la clase
  public static $mensaje;
  
  /** 
  *=============================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible en la
  * al cuenta de pagos por adelantados al momento de crear un nuevo periodo contable. Tambien notifica
  * al propietario del uso de su cuenta de pagos anticipados para cubri la deuda o parte de la deuda total
  *
  * @param  string        $un_id          "1"
  * @param  string        $f_pago         "2016-01-30"
  * @param  integer       $periodo        3
  * @param  string        $pdo            "Mar-2016" 
  * @return void
  *===========================================================================================*/
  public static function iniciaPago($un_id, $f_pago, $periodo, $pdo) {
  //dd($un_id, $f_pago, $periodo, $pdo);

    // procesa el pago recibido
    self::$mensaje = '';
    self::procesaPago($un_id, $f_pago, $periodo, $pdo);
    //dd(self::$mensaje);

    // contruye el mensaje a enviar de acuerdo a si se utilizo la cuenta de pagos anticipados de la unidad
    if (self::$mensaje != "") {
      $nota = 'Para notificarle que se ha generado la orden de cobro para el mes de '. $pdo.' '.
              'y hacer de su conocimiento que se utilizo un saldo de B/.'.number_format((100.25),2). ' proveniente de su cuenta de pagos anticipados para cancelar lo siguiente: '.
              self::$mensaje.' '.
              'El nuevo saldo de su cuenta de pagos anticipados es de B/.'.number_format(0.00,2).' ';
    
    } else {
      $nota = 'Para notificarle que se ha generado la orden de cobro para el mes de '. $pdo.'. '.
              'Se le agradece hacer sus pagos a tiempo, para evitar ser penalizado con recargos.';
    } 
    //dd($nota);
    
    // procede a notificar a cada uno de los propietario encargados sobre la generacion de una nueva order de cobro  
    // encuentra todos los propietarios encargados de la unidad
    $props= Prop::where('un_id', $un_id)->where('encargado', 1)->get();

    // notifica a cada uno
    foreach ($props as $prop) {
      $user= User::find($prop->user_id);              
      
      //$user->notify(new emailUsoDeCuentaAnticipados($nota, $user->nombre_completo));
      //$user->notify(new emailNuevaOcobro($pdo, $prop->user->nombre_completo));   
    }  
  } // end function

  /** 
  *=============================================================================================
  * Este proceso se encarga de registrar el cobro por cuotas de mantenimiento o recargos,
  * también realiza los debidos asientos contables en cada una de las cuentas afectadas.
  * @param  string  $un_id         "1"
  * @param  string  $f_pago        "2016-01-30"  
  * @param  integer $periodo       3 
  * @param  string  $pdo           "Mar-2016" 
  * @return void
  *===========================================================================================*/
  
  public static function procesaPago($un_id, $f_pago, $periodo, $pdo) {
    //dd($un_id, $f_pago, $periodo, $pdo);

    //Prioridad no 1, verifica si hay cuotas regulares pendiente por pagar.
    self::cobraFacturas($periodo, $un_id, $f_pago);
     
    //Prioridad no 2, verifica si hay recargos pendiente por pagar.
    self::cobraRecargos($periodo, $un_id, $f_pago);
    //dd($sobrante);
    
    //Prioridad no 3, verifica si hay cuotas extraordinarias por pagar.
    self::cobraCuotaExtraordinaria($periodo, $un_id, $f_pago);
    //dd($sobrante);
  }
 
  /** 
  *=========================================================================================================
  * Este proceso se encarga de cobrar todas las facturaciones posibles dependiendo del monto disponible en la
  * al cuenta de pagos por adelantados al momento de crear un nuevo periodo contable.
  *
  * El sistema acaba de crear un nuevo periodo contable, ejecuto la facturacion y esta tratando de utilizar
  * la cuenta de pagos adelantados para cubrir por lo menos una cuota de mantenimiento o recargo. En ese caso
  * se emite una nota al propietario donde se le informa que se hizo uso de su cuenta de pagos por anticipados
  * para cubrir la deuda, no es necesario emitir un recibo.
  *
  * @param  integer     $periodo        3
  * @param  string      $un_id          "1"
  * @param  string      $f_pago         "2016-01-30"  
  * @return void
  *=========================================================================================================*/
  public static function cobraFacturas($periodo, $un_id, $f_pago) { 
    //dd($periodo, $un_id, $f_pago);

    // Encuentra todas las facturaciones por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {

      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);  
      //dd($saldocpa);
      
      // incializa variables a utilizar
      $cuenta_1 =  Catalogo::find(1)->nombre;    // 1120.00 Cuota de mantenimiento regular por cobrar
      $cuenta_5 =  Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 =  Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;   // 1000.00 Caja general
      $i = 0;
      
      $cuotasText='';
               
      foreach ($datos as $dato) {
        $importe= round(floatval($dato->importe),2);        
        $ocobro= $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        
        if ($saldocpa >= $importe) {    
          // hay suficiente dinero para pagar por lo menos una cuota de  mantenimiento
          // por lo tanto, registra la cuota mensual como pagada
          $dato->pagada = 1;
          $dato->save(); 

          // disminuye el saldo de la cuenta Pagos anticipados
          $saldocpa = $saldocpa - $importe;
 
          // registra en el diario
          // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          if ($i == 0) { $diario->fecha = $f_pago; }
          $diario->detalle = $cuenta_5;
          $diario->debito = $importe;
          $diario->credito = Null;
          $diario->save();

          // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = $cuenta_1;
          $diario->debito = Null;
          $diario->credito = $importe;
          $diario->save();
          
          // agrega ultima linea al libro diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = 'Para registrar cobro de couta de mant regular, unidad '.$ocobro;
          $diario->save();  
          
          // almacena en la variable global los datos del cobro para luego notificar al propietario via email, del descuento
          // realizado de su cuenta de pagos anticipados
          $cuotasText = $cuotasText.'Se descuenta B/.'.$importe.' para cancelar la cuota de mantenimiento de '.$ocobro.', ';

          // registra en el mayor 
          // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
          Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta cuota de mant regular de '.$mesAnio, $importe, $un_id);

          // registra en el mayor
          // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
          Sity::registraEnCuentas($periodo, 'menos', 1, 1, $f_pago, 'Cobra cuota de mant regular de '.$mesAnio.' (Anticipos)', $importe, $un_id);
        
        } // endif 2
      } //endforeach 1

      self::$mensaje = rtrim($cuotasText,',');    
    } // endif 1
  }  // end function

  /** 
  *=====================================================================================================
  * Este proceso se encarga de cobrar todos los recargos posibles dependiendo del monto disponible en la
  * al cuenta de pagos por adelantados al momento de crear un nuevo periodo contable.
  * @param  integer       $periodo        1
  * @param  string        $un_id          "7"
  * @param  string      $f_pago           "2016-01-30"  
  * @return void
  *=====================================================================================================*/
  public static function cobraRecargos($periodo, $un_id, $f_pago) { 
    //dd($periodo, $un_id, $f_pago);
    
    // Encuentra todos los recargos por pagar
    $datos = Ctdasm::where('pcontable_id','<=', $periodo)
                   ->where('un_id', $un_id)
                   ->whereDate('f_vencimiento','<', $f_pago)
                   ->where('recargo_siono', 1)
                   ->where('recargo_pagado', 0)
                   ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {
      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);       
      // dd($saldocpa);

      // incializa variables a utilizar
      $cuenta_2 = Catalogo::find(2)->nombre;    //2 1130.00 Recargo en cuota de mantenimiento por cobrar
      $cuenta_5 = Catalogo::find(5)->nombre;    //5 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 = Catalogo::find(8)->nombre;    //8 1020.00 Banco
      $cuenta_32 = Catalogo::find(32)->nombre;  //32 1000.00 Caja general
      $i = 0;
      $recargosText='';
      
      foreach ($datos as $dato) {
        $recargo= round(floatval($dato->recargo),2);
        $ocobro= $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        
        if ($saldocpa >= $recargo) {   
          // hay suficiente dinero para pagar por lo menos un recargo en cuota de  mantenimiento
          // por lo tanto, registra el recargo como pagado
          $dato->recargo_pagado = 1;
          $dato->save();  

          // disminuye el saldo de la cuenta Pagos anticipados
          $saldocpa = $saldocpa - $recargo;

          // registra en el diario
          // registra un disminucion en la cuenta 5 2010.00 Anticipos recibidos de propietarios  
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          if ($i == 0) { $diario->fecha = $f_pago; }
          $diario->detalle = $cuenta_5;
          $diario->debito = $recargo;
          $diario->credito = Null;
          $diario->save();
          $i = 1;

          // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = $cuenta_2;
          $diario->debito = Null;
          $diario->credito = $recargo;
          $diario->save();
          
          // agrega ultima linea al libro diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = 'Para registrar cobro de recargo en couta de mantenimiento regular, unidad '.$ocobro;
          $diario->save();
          
          // almacena en la variable global los datos del cobro para luego notificar al propietario via email, del descuento
          // realizado de su cuenta de pagos anticipados
          $recargosText= $recargosText.'Se descuenta B/.'.$recargo.' para cancelar recargo en cuota de mantenimiento de '.$ocobro.', ';
          
          // registra en el mayor
          // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
          Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta recargo en cuota de mant regular de '.$mesAnio, $recargo, $un_id);

          // registra en el mayor
          // registra un disminucion en la cuenta 2 1130.00 Recargo en cuota de mantenimiento por cobrar
          Sity::registraEnCuentas($periodo, 'menos', 1, 2, $f_pago, 'Cobra recargo en cuota de mant regular de '.$mesAnio.' (Anticipos)', $importe, $un_id);

        } // endif 2
      } // endforeach 1
      
      self::$mensaje = rtrim($recargosText,',');  
    } // endif 1
  } // end function 

  /** 
  *=====================================================================================================
  * Este proceso se encarga de cobrar todas la cuotas extraodinarias posibles dependiendo del monto disponible en la
  * al cuenta de pagos por adelantados al momento de crear un nuevo periodo contable.
  * @param  integer       $periodo        1
  * @param  string        $un_id          "7"
  * @param  string      $f_pago           "2016-01-30"  
  * @return void
  *=====================================================================================================*/
  public static function cobraCuotaExtraordinaria($periodo, $un_id, $f_pago) { 
    //dd($periodo, $un_id, $f_pago);

    // Encuentra todas las cuotas extraordinarias por pagar en un determinado periodo contable o en los anteriores al mismo
    $datos = Ctdasm::where('pcontable_id', '<=', $periodo)
                  ->where('un_id', $un_id)
                  ->where('extra_siono', 1)
                  ->where('extra_pagada', 0)
                  ->orderBy('fecha', 'asc')
                  ->get();
    //dd($datos->toArray());
    
    // si hay cuotas de mantenimiento regular por cobrar continua proceso, si no hay regresa
    if (!$datos->isEmpty()) {

      // encuentra el saldo de la cuenta de Pagos anticipados antes del ejercicio
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un_id, $periodo);    
      $saldocpa= round(floatval($saldocpa),2);
      //dd($saldocpa);
      
      // incializa variables a utilizar
      $cuenta_16 =  Catalogo::find(16)->nombre;  // 1110.00 Cuotas de mantenimiento extraordinarias por cobrar
      $cuenta_5 =  Catalogo::find(5)->nombre;    // 2010.00 Anticipos recibidos de propietarios
      $cuenta_8 =  Catalogo::find(8)->nombre;    // 1020.00 Banco Nacional
      $cuenta_32 = Catalogo::find(32)->nombre;   // 1000.00 Caja general
      $i = 0;
      $cuotasText = '';
      
      foreach ($datos as $dato) {
        $importe= round(floatval($dato->importe),2);               
        $ocobro= $dato->ocobro;
        $mesAnio = $dato->mes_anio;
        
        if ($saldocpa >= $importe) {    
          // hay suficiente dinero para pagar por lo menos una cuota de extraordinaria
          // por lo tanto, registra la cuota extraordinaria como pagada
          $dato->pagada = 1;
          $dato->save(); 

          // disminuye el saldo de la cuenta Pagos anticipados
          $saldocpa = $saldocpa - $importe;
 
          // registra en el diario
          // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"  
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          if ($i == 0) { $diario->fecha = $f_pago; }
          $diario->detalle = $cuenta_5;
          $diario->debito = $importe;
          $diario->credito = Null;
          $diario->save();

          // registra un disminucion en la cuenta 1110.00 "Cuotas de mantenimiento extraordinarias por cobrar"
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = $cuenta_16;
          $diario->debito = Null;
          $diario->credito = $importe;
          $diario->save();
          
          // agrega ultima linea al libro diario
          $diario = new Ctdiario;
          $diario->pcontable_id  = $periodo;
          $diario->detalle = 'Para registrar cobro de couta de mantenimiento extraordinaria, unidad '.$ocobro;
          $diario->save();  
          
          // almacena en la variable global los datos del cobro para luego notificar al propietario via email, del descuento
          // realizado de su cuenta de pagos anticipados
          $cuotasText = $cuotasText.'Se descuenta B/.'.$importe.' para cancelar la cuota de mantenimiento extraordinaria de '.$ocobro.', ';

          // registra en el mayor
          // registra un disminucion en la cuenta 2010.00 "Anticipos recibidos de propietarios"
          Sity::registraEnCuentas($periodo, 'menos', 2, 5, $f_pago, 'Descuenta cuota de mant extraordinaria de '.$mesAnio, $importe, $un_id);

          // registra en el mayor
          // registra un disminucion en la cuenta 1120.00 "Cuota de mantenimiento regular por cobrar" 
          Sity::registraEnCuentas($periodo, 'menos', 1, 16, $f_pago, 'Cobra cuota de mant extraordinaria de '.$mesAnio.' (Anticipos)', $importe, $un_id);
        
        } // endif 2
      } //endforeach 1

      self::$mensaje = rtrim($cuotasText,',');    
    } // endif 1
  } // end function 

} //fin de Class Ppago