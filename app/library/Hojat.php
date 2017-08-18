<?php namespace App\library;

use Jenssegers\Date\Date;
use Carbon\Carbon;
use App\library\Sity;
use App\library\Pant;

use App\Pcontable;
use App\Catalogo;
use App\Un;

use App\Ctdiario;
use App\Ctmayore;
use App\Ctdasm;

use App\Ctdiariohi;
use App\Ctmayorehi;
use App\Ctdasmhi;
//use App\Ht;

class Hojat {

  /** 
  *=============================================================================================
  * Esta function inicializa en el nuevo periodo todas las cuentas permanentes
  * con el saldo del periodo anterior activas presentes en el catalogo de cuentas
  * @param  string        $pcontable_id   "1"
  * @param  date/carbon   $fecha          +"date": "2016-02-01 00:00:00.000000"      
  * @return void
  *===========================================================================================*/
  public static function inicializaCuentasPerm($pcontable_id, $fecha) {
    //dd($pcontable_id, $fecha);
    
    // encuentras los datos, los mismos datos que se utilizaron para la Hoja de trabajo
    //$datos= self::getDataParaHojaDeTrabajo($pcontable_id);
    $datos= self::getDataParaHtProyectada($pcontable_id);
    
    //dd($datos);
    
    $pdo= Pcontable::find($pcontable_id)->periodo;

    $i=1;
    foreach($datos as $dato) {
      if ($dato['tipo'] == 1) {
        // registra en la tabla ctmayores
        $data = new Ctmayore;
        $data->pcontable_id     = $pcontable_id + 1;
        $data->tipo             = $dato['tipo'];
        $data->cuenta           = $dato['cuenta'];
        $data->codigo           = $dato['codigo'];
        $data->fecha            = $fecha;
        $data->detalle          = 'Inicializa, '.$dato['cta_nombre'].', por cierre '.$pdo;
        $data->debito           = $dato['saldoAjustado_debito'];
        $data->credito          = $dato['saldoAjustado_credito'];
        $data->save();
        
        if ($i == 1) {
          // registra en Ctdiario principal
          $data = new Ctdiario;
          $data->pcontable_id  = $pcontable_id + 1;
          $data->fecha         = $fecha;
          $data->detalle = $dato['cta_nombre'].' '.$pdo;
          $data->debito  = $dato['saldoAjustado_debito'] >= 0 ? $dato['saldoAjustado_debito'] : Null;
          $data->credito = $dato['saldoAjustado_credito'] >= 0 ? $dato['saldoAjustado_credito'] : Null;
          $data->save();

        } else {
          // registra en Ctdiario principal
          $data = new Ctdiario;
          $data->pcontable_id  = $pcontable_id + 1;
          $data->detalle = $dato['cta_nombre'].' '.$pdo;
          $data->debito  = $dato['saldoAjustado_debito'] >= 0 ? $dato['saldoAjustado_debito'] : Null;
          $data->credito = $dato['saldoAjustado_credito'] >= 0 ? $dato['saldoAjustado_credito'] : Null;
          $data->save();
        }
        $i++;

      } elseif(($dato['tipo']==2 && $dato['cuenta']!=5) || $dato['tipo']==3) {
        // se excluye la cuenta 5 2010.00 "Anticipos o avances recibidos de propietarios (Pasivo diferido)"
        // ya que es una cuenta que comparten diferentes unidades. La inicializacion tiene un trato especial debido
        // a que como es una cuenta compartida se debe inicializar un saldo por cada unidad que comparta esta cuenta.
        
        // registra en la tabla ctmayores
        $data = new Ctmayore;
        $data->pcontable_id     = $pcontable_id + 1;
        $data->tipo             = $dato['tipo'];
        $data->cuenta           = $dato['cuenta'];
        $data->codigo           = $dato['codigo'];
        $data->fecha            = $fecha;
        $data->detalle          = 'Inicializa, '.$dato['cta_nombre'].', por cierre '.$pdo;
        $data->debito           = $dato['saldoAjustado_debito'];
        $data->credito          = $dato['saldoAjustado_credito'];
        $data->un_id            = 0;
        $data->save();
        
        // registra en Ctdiario principal
        $data = new Ctdiario;
        $data->pcontable_id  = $pcontable_id + 1;
        $data->detalle = $dato['cta_nombre'].' '.$pdo;
        $data->debito  = $dato['saldoAjustado_debito'] >= 0 ? $dato['saldoAjustado_debito'] : Null;
        $data->credito = $dato['saldoAjustado_credito'] >= 0 ? $dato['saldoAjustado_credito'] : Null;
        $data->save();  
      }
    }
    
    // inicializacion especial de la cuenta 5  2010.00 "Anticipos o avances recibidos de propietarios (Pasivo diferido)"

    // Encuentra todas las cuentas cuenta 5  2010.00 activas en ctmayores para un determinado periodo
    $cuentas= Ctmayore::where('pcontable_id', $pcontable_id)->where('cuenta', 5)->get();
    //dd($cuentas->toArray());
    
    $uns= $cuentas->unique('un_id');
    //dd($uns->toArray());
      
    // procesa cada una de las unidades con saldo en la cuenta 5 2010.00 encontradas
    foreach ($uns as $un) {

      // Encuentra saldo a favor en cuenta 2010.00 No. 5 "Anticipos y avances recibidos de propietarios" 
      $saldocpa= Pant::getSaldoCtaPagosAnticipados($un->un_id, $pcontable_id);
      //dd($saldocpa);

      $detalle= 'Inicializa, '.Catalogo::find(5)->nombre.', '.Un::find($un->un_id)->codigo.', por cierre '.$pdo;
      
      // registra en la tabla ctmayores
      $data = new Ctmayore;
      $data->pcontable_id     = $pcontable_id + 1;
      $data->tipo             = $un->tipo;
      $data->cuenta           = $un->cuenta;
      $data->codigo           = $un->codigo;
      $data->fecha            = $fecha;
      $data->detalle          = $detalle;
      $data->debito           = 0;
      $data->credito          = $saldocpa;
      $data->un_id            = $un->un_id;
      $data->save();

      // registra en Ctdiario principal
      $data = new Ctdiario;
      $data->pcontable_id  = $pcontable_id + 1;
      $data->detalle = $detalle;
      $data->debito  =  Null;
      $data->credito = $saldocpa;
      $data->save();  
    }
  
    // registra la utilidad en el diario del periodo posterior
    $data = new Ctdiario;
    $data->pcontable_id     = $pcontable_id + 1;
    $data->detalle          = 'Para registrar aperturas de cuentas permanentes y utilidad neta del periodo anterior '.Pcontable::find($pcontable_id)->periodo;
    $data->save(); 
  } 


  /** 
  *=============================================================================================
  * Arma un arreglo de informacion para un periodo en especifico, en donde se procesan todas las
  * transacciones contenidas en el mayor. Esta informacion se utilizara para desplegar la Hoja 
  * de trabajo proyectada.
  * @param  integer $periodo  4   
  * @return void
  *===========================================================================================*/
  public static function getDataParaHtProyectada($periodo_id)
  {
    //dd($periodo_id);    

    // encuentra todas las cuentas en el mayor que fueron utilizadas
    $cuentas = Ctmayore::where('pcontable_id', $periodo_id)->select('cuenta')->get();
    //dd($cuentas->toArray());
    
    // segrega las cuentas unicas sin duplicados
    $cuentas = $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // encuentra todas las transacciones en el mayor para un determinado peridod contable   
    $datos = Ctmayore::where('pcontable_id', $periodo_id)->get();
    //dd($datos->toArray());
    
    // procesa los datos y genera arreglo
    $data = self::procesaDataParaHtPorCuenta($cuentas, $datos, $periodo_id);
    //dd($data);

    return $data;
  }


   /** 
  *=============================================================================================
  * Arma un arreglo de informacion para un periodo en especifico, en donde se procesan todas las
  * transacciones contenidas en el mayor. Esta informacion se utilizara para desplegar la Hoja 
  * de trabajo proyectada.
  * @param  integer $periodo  4   
  * @return void
  *===========================================================================================*/
  public static function getDataParaHtFinal($periodo_id)
  {
    //dd($periodo_id);    

    // encuentra todas las cuentas en el mayor que fueron utilizadas
    $cuentas = Ctmayorehi::where('pcontable_id', $periodo_id)->where('cierre', 0)->select('cuenta')->get();
    //dd($cuentas->toArray());
    
    // segrega las cuentas unicas sin duplicados
    $cuentas = $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // encuentra todas las transacciones en el mayor para un determinado peridod contable   
    $datos = Ctmayorehi::where('pcontable_id', $periodo_id)->where('cierre', 0)->get();
    //dd($datos->toArray());
    
    // procesa los datos y genera arreglo
    $data = self::procesaDataParaHtPorCuenta($cuentas, $datos, $periodo_id);
    //dd($data);

    return $data;
  } 

  /** 
  *=============================================================================================
  * Procesa la informacion contenida en el mayor para ser usada en desplegar la Hoja de trabajo
  * o utilizar la misma para hacer la migracion de datos a la tabla hts despues de cerrar cada
  * perido contable. La informacion se procesa por cada cuenta contable.
  * @param  collection $cuentas   
  * @param  collection $datos  
  * @return array
  *===========================================================================================*/
  public static function procesaDataParaHtPorCuenta($cuentas, $datos, $periodo_id)
  {
    //dd($cuentas->toArray(), $datos->toArray()); 
    
    // inicializa variablea a utilizar
    $data = array();    
    $i = 0;    

    // procesa cada una de las cuentas recibidas
    foreach ($cuentas as $cuenta) {

      // encuentra las generales de la cuenta
      $cta = Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());
      
      // Calcula el saldo debito de la cuenta sin ajustes     
      $totalDebito =  $datos->where('ajuste_siono', 0)->where('cuenta', $cta->id)->sum('debito');
      $totalDebito = floatval($totalDebito);
      //dd($totalDebito);
      
      // Calcula el saldo credito de la cuenta sin ajustes
      $totalCredito = $datos->where('ajuste_siono', 0)->where('cuenta', $cta->id)->sum('credito');
      $totalCredito = floatval($totalCredito);
      //dd($totalCredito);
      
      // Arma un arreglo con la informacion de la cuenta en estudio
      $data[$i]["periodo"]= $periodo_id;
      $data[$i]["cuenta"]= $cta->id;      
      $data[$i]["htseccion"]= 1;   
      $data[$i]["tipo"]= $cta->tipo;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["clase"]= $cta->corriente_siono;
      $data[$i]["un_id"]= "";
      $data[$i]["seccione_id"]= "";
      $data[$i]["bloque_id"]= "";
      $data[$i]["cta_nombre"]= $cta->nombre;
      
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;
      
      // De acuerdo al tipo de cuenta, determina como aumenta o disminuye la misma
      // Si se trata de una cuenta de activo, aumenta por el debito y disminuye por el credito
      if ($cta->tipo == 1) {
        $saldo = $totalDebito - $totalCredito;
        
        if ($saldo >= 0) {
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de activo,
          // por lo tanto de registra por el lado debito          
          $data[$i]["saldo_debito"] = $saldo;
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = $saldo;
          $data[$i]["saldoAjustado_credito"] = 0;
         
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0; 
          
          $data[$i]["bg_debito"] = $saldo;
          $data[$i]["bg_credito"] = 0;
        
        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de activo,
          // por lo tanto de registra el valor absoluto por el lado credito
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = abs($saldo);
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = abs($saldo);
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;           
          
          $data[$i]["bg_debito"] = 0; 
          $data[$i]["bg_credito"] = abs($saldo);  
        }
      
      // Si se trata de una cuenta de gasto, aumenta por el debito y disminuye por el credito
      } elseif ($cta->tipo == 6) {
        $saldo = $totalDebito - $totalCredito;
        
        if ($saldo >= 0) {
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de gasto,
          // por lo tanto de registra por el lado debito   
          $data[$i]["saldo_debito"] = $saldo;
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = $saldo;
          $data[$i]["saldoAjustado_credito"] = 0;
        
          $data[$i]["er_debito"] = $saldo;
          $data[$i]["er_credito"] = 0;        
        
          $data[$i]["bg_debito"] = 0; 
          $data[$i]["bg_credito"] = 0;

        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de gasto,
          // por lo tanto de registra el valor absoluto por el lado credito
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = abs($saldo); 
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = abs($saldo); 
        
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = abs($saldo);
        
          $data[$i]["bg_debito"] = 0; 
          $data[$i]["bg_credito"] = 0;
        }
     
      // Si se trata de una cuenta de pasivo, aumenta por el credito y disminuye por el debito
      } elseif ($cta->tipo == 2) {
        $saldo = $totalCredito - $totalDebito;
        
        if ($saldo >= 0) {
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de pasivo,
          // por lo tanto de registra por el lado credito  
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = $saldo;
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = $saldo;
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = 0;
          $data[$i]["bg_credito"] = $saldo;
        
        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de pasivo,
          // por lo tanto de registra el valor absoluto por el lado debito
          $data[$i]["saldo_debito"] = abs($saldo);
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = abs($saldo);
          $data[$i]["saldoAjustado_credito"] = 0;
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = abs($saldo);
          $data[$i]["bg_credito"] = 0; 
        }
      
      // Si se trata de una cuenta de patrimonio, aumenta por el credito y disminuye por el debito
      } elseif ($cta->tipo == 3) {
        $saldo = $totalCredito - $totalDebito;        
        
        if ($saldo >= 0) {
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de patrimonio,
          // por lo tanto de registra por el lado credito  
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = $saldo;
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = $saldo;      
        
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = 0;
          $data[$i]["bg_credito"] = $saldo; 
        
        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de patrimonio,
          // por lo tanto de registra el valor absoluto por el lado debito
          $data[$i]["saldo_debito"] = abs($saldo);
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = abs($saldo);
          $data[$i]["saldoAjustado_credito"] = 0;     
        
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = abs($saldo);
          $data[$i]["bg_credito"] = 0;
        }
      
      // Si se trata de una cuenta de ingreso, aumenta por el credito y disminuye por el debito
      } elseif ($cta->tipo == 4) {
        $saldo = $totalCredito - $totalDebito;
        
        if ($saldo >= 0) {
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de ingreso,
          // por lo tanto de registra por el lado credito 
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = $saldo;
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = $saldo;
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = $saldo;        
        
          $data[$i]["bg_debito"] = 0;
          $data[$i]["bg_credito"] = 0;

        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de ingreso,
          // por lo tanto de registra el valor absoluto por el lado debito
          $data[$i]["saldo_debito"] = abs($saldo);
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = abs($saldo);
          $data[$i]["saldoAjustado_credito"] = 0;
          
          $data[$i]["er_debito"] = abs($saldo);
          $data[$i]["er_credito"] = 0;
        
          $data[$i]["bg_debito"] = 0;
          $data[$i]["bg_credito"] = 0;
        }
      }
      
      //=====================================================================================================
      //verifica si la cuenta en estudio tuvo ajustes
      //=====================================================================================================
      $ajustes = $datos->where('cuenta', $cta->id)
                       ->where('ajuste_siono', 1)
                       ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito = $datos->where('cuenta', $cta->id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        
        $totalAjusteDebito = floatval($totalAjusteDebito);

        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito = $datos->where('cuenta', $cta->id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        
        $totalAjusteCredito = floatval($totalAjusteCredito);
        //dd($totalAjusteDebito, $totalAjusteCredito); 
        
        // clasifica el total de ajuste hechos a la cuenta de acuerdo al tipo de cuenta
        if ($cta->tipo == 1) {
          $totalAjuste = $totalAjusteDebito - $totalAjusteCredito; 
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = $totalAjuste;
            $data[$i]["saldoAjuste_credito"] = 0;          
            
            $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"] = 0;           
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = $saldo + $totalAjuste;
            $data[$i]["bg_credito"] = 0;    
          
          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
            
            $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"] = 0;
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["bg_credito"] = 0;
          }
        
        } elseif ($cta->tipo == 6) {
          $totalAjuste = $totalAjusteDebito - $totalAjusteCredito; 
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = $totalAjuste;
            $data[$i]["saldoAjuste_credito"] = 0;          
            
            $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"] = 0;           
          
            $data[$i]["er_debito"] = $saldo + $totalAjuste;
            $data[$i]["er_credito"] = 0;        
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;

          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
            
            $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"] = 0;
          
            $data[$i]["er_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["er_credito"] = 0;
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;
          }        
        
        } elseif ($cta->tipo == 2) {
          $totalAjuste = $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = $saldo + $totalAjuste; 
          
          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = 0; 
            $data[$i]["saldoAjustado_credito"] = $saldo - abs($totalAjuste);
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0; 
            $data[$i]["bg_credito"] = $saldo - abs($totalAjuste);
          }
        
        } elseif ($cta->tipo == 3) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = $saldo + $totalAjuste;              
          
          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = abs($saldo - abs($totalAjuste));
            $data[$i]["saldoAjustado_credito"] = 0; 
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = abs($saldo - abs($totalAjuste));
            $data[$i]["bg_credito"] = 0;
          }
        
        } elseif ($cta->tipo == 4) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = $saldo + $totalAjuste;
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;

          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = 0; 
            $data[$i]["saldoAjustado_credito"] = $saldo - abs($totalAjuste);
            
            $data[$i]["er_debito"] = 0; 
            $data[$i]["er_credito"] = $saldo - abs($totalAjuste);
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;
          }
        }
      }

      $i++;    
    
    } // end foreach no 1
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    
    //dd($data);
    return $data;
  }  


  /** 
  *=============================================================================================
  * Arma un arreglo de informacion para un periodo en especifico, en donde se procesa todas las
  * transacciones contenidas en el mayor. Esta informacion se utilizara para migrar a la tabla jhs
  * como historicos.
  * @param  integer $periodo  4   
  * @return void
  *===========================================================================================*/
/*  public static function getDataParaMigrarHtHistoricos($periodo_id)
  {
    //dd($periodo);    
   
    //------------------------------------------------------------------------------------
    // Arma el arreglo que formara la seccion no 1 de la tabla hts del periodo en estudio
    //------------------------------------------------------------------------------------

    // encuentra todas las cuentas en el mayor que fueron utilizadas
    $cuentas = Ctmayore::where('pcontable_id', $periodo_id)->get();
    //dd($cuentas->toArray());
    
    // agrega el nuevo elemento a la collection        
    //$cuentas->map(function ($cuentas) {
      //$cuentas->compartida = Catalogo::find($cuentas->cuenta)->compartida;       
    //});    

    // segrega las cuentas unicas sin duplicados
    $cuentas = $cuentas->unique('cuenta');
    //dd($cuentas->toArray());

    // encuentra todas las transacciones en el mayor para un determinado peridod contable   
    $datos = Ctmayore::where('pcontable_id', $periodo_id)->get();
    //dd($datos->toArray());    

    // procesa los datos y genera array
    $htSeccionNo1 = self::procesaDataParaHtPorCuenta($cuentas, $datos, $periodo_id);
    //dd($noPertenecen[0]);

    // ordena el arreglo por codigo y cta_nombre
    $sort = array();
    foreach($htSeccionNo1 as $k=>$v) {
        $sort['codigo'][$k] = $v['codigo'];
        $sort['cta_nombre'][$k] = $v['cta_nombre'];
    }

    array_multisort($sort['codigo'], SORT_ASC, $sort['cta_nombre'], SORT_ASC,$htSeccionNo1);    

    
    //------------------------------------------------------------------------------------
    // Arma el arreglo que formara la seccion no 1 de la tabla hts del periodo en estudio
    //------------------------------------------------------------------------------------

    // encuentra todas las transaccionesen el mayor que pertenecen a una unidad   
    $htSeccionNo2 = self::procesaDataParaHtPorUnidadCuenta($datos, $periodo_id);
    //dd($htSeccionNo2);    
    
    // ordena el arreglo por codigo y cta_nombre
    $sort = array();
    foreach($htSeccionNo2 as $k=>$v) {
        $sort['codigo'][$k] = $v['codigo'];
        $sort['cta_nombre'][$k] = $v['cta_nombre'];
    }

    array_multisort($sort['codigo'], SORT_ASC, $sort['cta_nombre'], SORT_ASC,$htSeccionNo2);

    // une los dos arreglos
    $data = array_merge($htSeccionNo1, $htSeccionNo2);     

    //dd($data);
    return $data;

  }*/


  /** 
  *=============================================================================================
  * Procesa la informacion contenida en el mayor para ser usada en desplegar la Hoja de trabajo
  * o utilizar la misma para hacer la migracion de datos a la tabla hts despues de cerrar cada
  * perido contable.
  * @param  collection $cuentas   
  * @param  collection $datos  
  * @return array
  *===========================================================================================*/
/*  public static function procesaDataParaHtPorUnidadCuenta($datos, $periodo_id)
  {
    //dd($datos); 

    // inicializa variablea a utilizar
    $data = array();    
    $i = 0;    
    
    $uns = Un::where('activa', 1)->get();
    //dd($uns->toArray());
    
    // procesa cada una de las unidades
    foreach ($uns as $un) {

      //------------------------------------------------------------------------------
      // encuentra todas las cuentas en el mayor que pertenecen a una unidad o sea
      // que la cuenta es compartida por una o varias unidades, ejemplo Cuotas regulares
      // por cobrar, Recargos por cobrar, Pagos anticipados, etc.
      //------------------------------------------------------------------------------
      //$cuentas = $datos->where('un_id', $un->id);
      //dd($cuentas->toArray());
      
      // encuentra los valores unicos
      //$cuentas = $cuentas->unique('cuenta');
      //dd($cuentas->toArray());      
      
        // encuentra las generales de la cuenta
        $cta = Catalogo::where('compartida', 1)->get();
        dd('ddd',$cta->toArray());      

      foreach ($cuentas as $cuenta) {
      
        // encuentra las generales de la cuenta
        $cta = Catalogo::find($cuenta->cuenta);
        //dd($cta->toArray());
        
        // Calcula el saldo debito de la cuenta sin ajustes     
        $totalDebito =  $datos->where('ajuste_siono', 0)->where('un_id', $un->id)->where('cuenta', $cta->id)->sum('debito');
        $totalDebito = floatval($totalDebito);
        //dd($totalDebito);
        
        // Calcula el saldo credito de la cuenta sin ajustes
        $totalCredito = $datos->where('ajuste_siono', 0)->where('un_id', $un->id)->where('cuenta', $cta->id)->sum('credito');
        $totalCredito = floatval($totalCredito);
        //dd($totalCredito);
        
        // Arma un arreglo con la informacion de la cuenta en estudio
        $data[$i]["periodo"]= $periodo_id;
        $data[$i]["cuenta"]= $cta->id;      
        $data[$i]["htseccion"]= 2;     
        $data[$i]["tipo"]= $cta->tipo;
        $data[$i]["codigo"]= $cta->codigo;
        $data[$i]["clase"]= $cta->corriente_siono;
        $data[$i]["un_id"]= $un->codigo;
        $data[$i]["seccione_id"]= $un->seccione->id;
        $data[$i]["bloque_id"]= $un->seccione->bloque_id;
        $data[$i]["cta_nombre"]= $cta->nombre.' '.$un->codigo;
        
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= 0;
        
        $data[$i]["saldoAjuste_debito"]= 0;
        $data[$i]["saldoAjuste_credito"]= 0;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= 0;
        
        // De acuerdo al tipo de cuenta, determina como aumenta o disminuye la misma
        // Si se trata de una cuenta de activo, aumenta por el debito y disminuye por el credito
        if ($cta->tipo == 1) {
          $saldo = $totalDebito - $totalCredito;
          
          if ($saldo >= 0) {                            
            // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de activo,
            // por lo tanto de registra por el lado debito          
            $data[$i]["saldo_debito"] = $saldo;
            $data[$i]["saldo_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = $saldo;
            $data[$i]["saldoAjustado_credito"] = 0;
           
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0; 
            
            $data[$i]["bg_debito"] = $saldo;
            $data[$i]["bg_credito"] = 0;
          
          } else {
            // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de activo,
            // por lo tanto de registra el valor absoluto por el lado credito
            $data[$i]["saldo_debito"] = 0;
            $data[$i]["saldo_credito"] = abs($saldo);
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = abs($saldo);
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;           
            
            $data[$i]["bg_debito"] = 0; 
            $data[$i]["bg_credito"] = abs($saldo);  
          }
        
        // Si se trata de una cuenta de gasto, aumenta por el debito y disminuye por el credito
        } elseif ($cta->tipo == 6) {
          $saldo = $totalDebito - $totalCredito;
          
          if ($saldo >= 0) {  
            // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de gasto,
            // por lo tanto de registra por el lado debito   
            $data[$i]["saldo_debito"] = $saldo;
            $data[$i]["saldo_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = $saldo;
            $data[$i]["saldoAjustado_credito"] = 0;
          
            $data[$i]["er_debito"] = $saldo;
            $data[$i]["er_credito"] = 0;        
          
            $data[$i]["bg_debito"] = 0; 
            $data[$i]["bg_credito"] = 0;

          } else {
            // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de gasto,
            // por lo tanto de registra el valor absoluto por el lado credito
            $data[$i]["saldo_debito"] = 0;
            $data[$i]["saldo_credito"] = abs($saldo); 
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = abs($saldo); 
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = abs($saldo);
          
            $data[$i]["bg_debito"] = 0; 
            $data[$i]["bg_credito"] = 0;
          }
       
        // Si se trata de una cuenta de pasivo, aumenta por el credito y disminuye por el debito
        } elseif ($cta->tipo == 2) {
          $saldo = $totalCredito - $totalDebito;
          
          if ($saldo >= 0) {  
            // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de pasivo,
            // por lo tanto de registra por el lado credito  
            $data[$i]["saldo_debito"] = 0;
            $data[$i]["saldo_credito"] = $saldo;
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo;
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = $saldo;
          
          } else {
            // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de pasivo,
            // por lo tanto de registra el valor absoluto por el lado debito
            $data[$i]["saldo_debito"] = abs($saldo);
            $data[$i]["saldo_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = abs($saldo);
            $data[$i]["saldoAjustado_credito"] = 0;
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = abs($saldo);
            $data[$i]["bg_credito"] = 0; 
          }
        
        // Si se trata de una cuenta de patrimonio, aumenta por el credito y disminuye por el debito
        } elseif ($cta->tipo == 3) {
          $saldo = $totalCredito - $totalDebito;        
          
          if ($saldo >= 0) {  
            // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de patrimonio,
            // por lo tanto de registra por el lado credito  
            $data[$i]["saldo_debito"] = 0;
            $data[$i]["saldo_credito"] = $saldo;
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo;      
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = $saldo; 
          
          } else {
            // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de patrimonio,
            // por lo tanto de registra el valor absoluto por el lado debito
            $data[$i]["saldo_debito"] = abs($saldo);
            $data[$i]["saldo_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = abs($saldo);
            $data[$i]["saldoAjustado_credito"] = 0;     
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = abs($saldo);
            $data[$i]["bg_credito"] = 0;
          }
        
        // Si se trata de una cuenta de ingreso, aumenta por el credito y disminuye por el debito
        } elseif ($cta->tipo == 4) {
          $saldo = $totalCredito - $totalDebito;
          
          if ($saldo >= 0) {  
            // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de ingreso,
            // por lo tanto de registra por el lado credito 
            $data[$i]["saldo_debito"] = 0;
            $data[$i]["saldo_credito"] = $saldo;
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo;
            
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = $saldo;        
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;

          } else {
            // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de ingreso,
            // por lo tanto de registra el valor absoluto por el lado debito
            $data[$i]["saldo_debito"] = abs($saldo);
            $data[$i]["saldo_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = abs($saldo);
            $data[$i]["saldoAjustado_credito"] = 0;
            
            $data[$i]["er_debito"] = abs($saldo);
            $data[$i]["er_credito"] = 0;
          
            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = 0;
          }
        }
        
        //=====================================================================================================
        //verifica si la cuenta en estudio tuvo ajustes
        //=====================================================================================================
        $ajustes = $datos->where('cuenta', $cta->id)
                         ->where('ajuste_siono', 1)
                         ->first();
        //dd($ajustes->toArray());      
        
        if ($ajustes) {
          // si la cuenta tuvo ajustes entonces
          
          // calcula el total de ajustes debito que tuvo la cuentao
          $totalAjusteDebito = $datos->where('cuenta', $cta->id)
                                      ->where('ajuste_siono', 1)
                                      ->sum('debito');
          
          $totalAjusteDebito = floatval($totalAjusteDebito);

          // calcula el total de ajustes credito que tuvo la cuenta
          $totalAjusteCredito = $datos->where('cuenta', $cta->id)
                                      ->where('ajuste_siono', 1)
                                      ->sum('credito');
          
          $totalAjusteCredito = floatval($totalAjusteCredito);
          //dd($totalAjusteDebito, $totalAjusteCredito); 
          
          // clasifica el total de ajuste hechos a la cuenta de acuerdo al tipo de cuenta
          if ($cta->tipo == 1) {
            $totalAjuste = $totalAjusteDebito - $totalAjusteCredito; 
            if ($totalAjuste >= 0) {
              // si es mayor que cero huvo aumento en la cuenta
              $data[$i]["saldoAjuste_debito"] = $totalAjuste;
              $data[$i]["saldoAjuste_credito"] = 0;          
              
              $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
              $data[$i]["saldoAjustado_credito"] = 0;           
            
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = $saldo + $totalAjuste;
              $data[$i]["bg_credito"] = 0;    
            
            } elseif ($totalAjuste < 0) {
              // si es menor que cero huvo una disminucion en la cuenta
              $data[$i]["saldoAjuste_debito"] = 0;
              $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
              
              $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
              $data[$i]["saldoAjustado_credito"] = 0;
            
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = $saldo - abs($totalAjuste); 
              $data[$i]["bg_credito"] = 0;
            }
          
          } elseif ($cta->tipo == 6) {
            $totalAjuste = $totalAjusteDebito - $totalAjusteCredito; 
            if ($totalAjuste >= 0) {
              // si es mayor que cero huvo aumento en la cuenta
              $data[$i]["saldoAjuste_debito"] = $totalAjuste;
              $data[$i]["saldoAjuste_credito"] = 0;          
              
              $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
              $data[$i]["saldoAjustado_credito"] = 0;           
            
              $data[$i]["er_debito"] = $saldo + $totalAjuste;
              $data[$i]["er_credito"] = 0;        
            
              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = 0;

            } elseif ($totalAjuste < 0) {
              // si es menor que cero huvo una disminucion en la cuenta
              $data[$i]["saldoAjuste_debito"] = 0;
              $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
              
              $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
              $data[$i]["saldoAjustado_credito"] = 0;
            
              $data[$i]["er_debito"] = $saldo - abs($totalAjuste); 
              $data[$i]["er_credito"] = 0;
            
              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = 0;

            }        
          
          } elseif ($cta->tipo == 2) {
            $totalAjuste = $totalAjusteCredito - $totalAjusteDebito; 
            if ($totalAjuste >= 0) {
              // si es mayor que cero huvo aumento en la cuenta
              $data[$i]["saldoAjuste_debito"] = 0;
              $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
              
              $data[$i]["saldoAjustado_debito"] = 0;
              $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
            
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = $saldo + $totalAjuste; 
            
            } elseif ($totalAjuste < 0) {
              // si es menor que cero huvo una disminucion en la cuenta
              $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
              $data[$i]["saldoAjuste_credito"] = 0;
              
              $data[$i]["saldoAjustado_debito"] = 0; 
              $data[$i]["saldoAjustado_credito"] = $saldo - abs($totalAjuste);
            
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = 0; 
              $data[$i]["bg_credito"] = $saldo - abs($totalAjuste);
            }
          
          } elseif ($cta->tipo == 3) {
            $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
            if ($totalAjuste >= 0) {
              // si es mayor que cero huvo aumento en la cuenta
              $data[$i]["saldoAjuste_debito"] = 0;
              $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
              
              $data[$i]["saldoAjustado_debito"] = 0;
              $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
              
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = $saldo + $totalAjuste;              
            
            } elseif ($totalAjuste < 0) {
              // si es menor que cero huvo una disminucion en la cuenta
              $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
              $data[$i]["saldoAjuste_credito"] = 0;
              
              $data[$i]["saldoAjustado_debito"] = abs($saldo - abs($totalAjuste));
              $data[$i]["saldoAjustado_credito"] = 0; 
              
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = 0;

              $data[$i]["bg_debito"] = abs($saldo - abs($totalAjuste));
              $data[$i]["bg_credito"] = 0;
            }
          
          } elseif ($cta->tipo == 4) {
            $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
            if ($totalAjuste >= 0) {
              // si es mayor que cero huvo aumento en la cuenta
              $data[$i]["saldoAjuste_debito"] = 0;
              $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
              
              $data[$i]["saldoAjustado_debito"] = 0;
              $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
            
              $data[$i]["er_debito"] = 0;
              $data[$i]["er_credito"] = $saldo + $totalAjuste;
            
              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = 0;

            } elseif ($totalAjuste < 0) {
              // si es menor que cero huvo una disminucion en la cuenta
              $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
              $data[$i]["saldoAjuste_credito"] = 0;
              
              $data[$i]["saldoAjustado_debito"] = 0; 
              $data[$i]["saldoAjustado_credito"] = $saldo - abs($totalAjuste);
              
              $data[$i]["er_debito"] = 0; 
              $data[$i]["er_credito"] = $saldo - abs($totalAjuste);
            
              $data[$i]["bg_debito"] = 0;
              $data[$i]["bg_credito"] = 0;
            }
          }
        }
      } // end foreach no 2
      $i++;    
    
    } // end foreach no 1
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    
    //dd($data);
    return $data;
  }*/  

  /** 
  *=============================================================================================
  * Arma un arreglo con la informacion de las cuentas compartidas
  * @param  integer   $cuenta  5
  * @param  integer   $un_id   4
  * @param  integer   $i       1
  * @return void
  *===========================================================================================*/
/*  public static function procesaCuentasCompartidas($cuenta, $periodo, $i) {
    //dd($cuenta, $periodo, $i);
    
    $data = array();
    
    // encuentra todas las unidades que utilizaron la cuenta $cuenta en el periodo $periodo
    $uns = Ctmayore::where('pcontable_id', $periodo)
                  ->where('cuenta', $cuenta)
                  ->whereNotNull('un_id')
                  ->select('un_id')->get();
    //dd($uns->toArray());
    
    // encuentras el id unico de cada unidad que utilizo la cuenta
    $uns = $uns->unique('un_id');
    //dd($uns->toArray());    

    // encuentra las generales de la cuenta
    $cta = Catalogo::find($cuenta);    
    //dd($cta->toArray());   
    
    // procesa cada una de las unidades una a la vez
    foreach ($uns as $un) {

      // encuentra las generales de la unidad
      $unidad = Un::find($un->un_id);
      
      // Calcula el saldo de la cuenta tomando en cuenta el periodo y eliminado los ajustes hechos a la misma      
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('un_id', $un->un_id)
                    ->where('ajuste_siono', 0)
                    ->sum('debito');
      //dd($totalDebito);
      
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('un_id', $un->un_id)
                    ->where('ajuste_siono', 0)
                    ->sum('credito');
      //dd($totalCredito);
      
      // Arma un arreglo con la informacion de las cuenta en estudio
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;      
      $data[$i]["tipo"]= $cta->tipo;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["clase"]= $cta->corriente_siono;
      $data[$i]["cta_nombre"]= $cta->nombre.' '.$unidad->codigo;
      $data[$i]["un_id"]= $un->un_id;      
      
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;
      
      //======================================================================
      // CUENTAS TIPO ACTIVOS
      //======================================================================
      if ($cta->tipo == 1) {
        // calcula el saldo de la cuenta sin ajustes
        $saldo = floatval($totalDebito) - floatval($totalCredito);

        if ($saldo >= 0) {                            
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de activo,
          // por lo tanto de registra por el lado debito          
          $data[$i]["saldo_debito"] = $saldo;
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = $saldo;
          $data[$i]["saldoAjustado_credito"] = 0;
         
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0; 
          
          $data[$i]["bg_debito"] = $saldo;
          $data[$i]["bg_credito"] = 0;
        
        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de activo,
          // por lo tanto de registra el valor absoluto por el lado credito
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = abs($saldo);
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = abs($saldo);
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;           
          
          $data[$i]["bg_debito"] = 0; 
          $data[$i]["bg_credito"] = abs($saldo);  
        }

      //======================================================================
      // CUENTAS TIPO PASIVOS
      //======================================================================
      } elseif ($cta->tipo == 2) {
        // calcula el saldo de la cuenta sin ajustes
        $saldo = floatval($totalCredito) - floatval($totalDebito);
        
        if ($saldo >= 0) {  
          // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de pasivo,
          // por lo tanto de registra por el lado credito  
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = $saldo;
          
          $data[$i]["saldoAjustado_debito"] = 0;
          $data[$i]["saldoAjustado_credito"] = $saldo;
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = 0;
          $data[$i]["bg_credito"] = $saldo;
        
        } else {
          // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de pasivo,
          // por lo tanto de registra el valor absoluto por el lado debito
          $data[$i]["saldo_debito"] = abs($saldo);
          $data[$i]["saldo_credito"] = 0;
          
          $data[$i]["saldoAjustado_debito"] = abs($saldo);
          $data[$i]["saldoAjustado_credito"] = 0;
          
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = abs($saldo);
          $data[$i]["bg_credito"] = 0; 
        }
      }

     
      //=====================================================================================================
      //verifica si la cuenta en estudio tuvo ajustes
      //=====================================================================================================
      $ajustes = Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito = Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cta->id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito = Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cta->id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 
        
        //======================================================================
        // CUENTAS TIPO ACTIVOS
        //======================================================================
        if ($cta->tipo == 1) {
          // calcula el monto del ajuste
          $totalAjuste = floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
          
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = $totalAjuste;
            $data[$i]["saldoAjuste_credito"] = 0;          
            
            $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"] = 0;           
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = $saldo + $totalAjuste;
            $data[$i]["bg_credito"] = 0;    
          
          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
            
            $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"] = 0;
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = $saldo - abs($totalAjuste); 
            $data[$i]["bg_credito"] = 0;
          }   

        //======================================================================
        // CUENTAS TIPO PASIVOS
        //======================================================================
        } elseif ($cta->tipo == 2) {
          // calcula el monto del ajuste
          $totalAjuste = $totalAjusteCredito - $totalAjusteDebito; 
          
          if ($totalAjuste >= 0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"] = 0;
            $data[$i]["saldoAjuste_credito"] = $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"] = 0;
            $data[$i]["saldoAjustado_credito"] = $saldo + $totalAjuste;           
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0;
            $data[$i]["bg_credito"] = $saldo + $totalAjuste; 
          
          } elseif ($totalAjuste < 0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"] = abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"] = 0;
            
            $data[$i]["saldoAjustado_debito"] = 0; 
            $data[$i]["saldoAjustado_credito"] = $saldo - abs($totalAjuste);
          
            $data[$i]["er_debito"] = 0;
            $data[$i]["er_credito"] = 0;

            $data[$i]["bg_debito"] = 0; 
            $data[$i]["bg_credito"] = $saldo - abs($totalAjuste);
          }
        
        }
      } // end if ajustes

      $data[$i]["seccione_id"] = $unidad->seccione->id; 
      $data[$i]["bloque_id"] = $unidad->seccione->bloque_id;
      $i++;  
    }    
    //dd($data);
    return [$data, $i];

  }*/

  
  /** 
  *=============================================================================================
  * Arma un arreglo con la informacion de las cuentas compartidas
  * @param  integer   $cuenta  5
  * @param  integer   $un_id   4
  * @param  integer   $i       1
  * @return void
  *===========================================================================================*/
/*  public static function procesaCuentasPorCobrar($cuenta, $periodo, $i) {
    //dd($cuenta, $periodo, $i);
    
    $data = array();
    
    // encuentra todas las unidades que utilizaron la cuenta $cuenta en el periodo $periodo
    $procesaCuentas = Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta)
                    ->whereNull('un_id')
                    ->get();
    //dd($procesaCuentas->toArray());
    
    if (!$procesaCuentas->isEmpty()) {

      // encuentra las generales de la cuenta
      $cta = Catalogo::find($cuenta);    
      //dd($cta->toArray());   
      
      // Calcula el saldo de la cuenta tomando en cuenta el periodo y eliminado los ajustes hechos a la misma      
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->whereNull('un_id')
                    ->where('ajuste_siono', 0)
                    ->sum('debito');
      //dd($totalDebito);
      
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->whereNull('un_id')
                    ->where('ajuste_siono', 0)
                    ->sum('credito');
      //dd($totalCredito);
      
      // Arma un arreglo con la informacion de las cuenta en estudio
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;      
      $data[$i]["tipo"]= $cta->tipo;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["clase"]= $cta->corriente_siono;
      $data[$i]["cta_nombre"]= $cta->nombre;
      $data[$i]["un_id"]= '';      
      
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;
        
      $saldo = floatval($totalDebito) - floatval($totalCredito);

      if ($saldo >= 0) {                            
        // si el saldo de la cuenta es mayor o igual a cero, quiere decir que hubo un aumento en la cuenta de activo,
        // por lo tanto de registra por el lado debito          
        $data[$i]["saldo_debito"] = $saldo;
        $data[$i]["saldo_credito"] = 0;
        
        $data[$i]["saldoAjustado_debito"] = $saldo;
        $data[$i]["saldoAjustado_credito"] = 0;
       
        $data[$i]["er_debito"] = 0;
        $data[$i]["er_credito"] = 0; 
        
        $data[$i]["bg_debito"] = $saldo;
        $data[$i]["bg_credito"] = 0;
      
      } else {
        // si el saldo de la cuenta es menor que cero, quiere decir que hubo una disminucion en la cuenta de activo,
        // por lo tanto de registra el valor absoluto por el lado credito
        $data[$i]["saldo_debito"] = 0;
        $data[$i]["saldo_credito"] = abs($saldo);
        
        $data[$i]["saldoAjustado_debito"] = 0;
        $data[$i]["saldoAjustado_credito"] = abs($saldo);
        
        $data[$i]["er_debito"] = 0;
        $data[$i]["er_credito"] = 0;           
        
        $data[$i]["bg_debito"] = 0; 
        $data[$i]["bg_credito"] = abs($saldo);  
      }
      
      //=====================================================================================================
      //verifica si la cuenta en estudio tuvo ajustes
      //=====================================================================================================
      $ajustes = Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->whereNull('un_id')
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito = Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cta->id)
                                    ->whereNull('un_id')
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito = Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cta->id)
                                    ->whereNull('un_id')
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 
        

        // calcula el monto del ajuste
        $totalAjuste = floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
        
        if ($totalAjuste >= 0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldoAjuste_debito"] = $totalAjuste;
          $data[$i]["saldoAjuste_credito"] = 0;          
          
          $data[$i]["saldoAjustado_debito"] = $saldo + $totalAjuste;
          $data[$i]["saldoAjustado_credito"] = 0;           
        
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = $saldo + $totalAjuste;
          $data[$i]["bg_credito"] = 0;    
        
        } elseif ($totalAjuste < 0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldoAjuste_debito"] = 0;
          $data[$i]["saldoAjuste_credito"] = abs($totalAjuste);
          
          $data[$i]["saldoAjustado_debito"] = $saldo - abs($totalAjuste); 
          $data[$i]["saldoAjustado_credito"] = 0;
        
          $data[$i]["er_debito"] = 0;
          $data[$i]["er_credito"] = 0;

          $data[$i]["bg_debito"] = $saldo - abs($totalAjuste); 
          $data[$i]["bg_credito"] = 0;
        }   

      } // end if ajustes

      $data[$i]["seccione_id"] = ''; 
      $data[$i]["bloque_id"] = '';
      $i++;  
   
      //dd($data);
      return [$data, $i];
    }
  }*/


  /** 
  *=============================================================================================
  * Arma un arreglo con la informacion necesaria para confeccionar
  * el Estado de resultado de un determinado periodo contable
  * @param  string    $periodo  "4"
  * @param  integer   $tipo     4
  * @return void
  *===========================================================================================*/
  public static function getDataParaEstadoResultado($periodo, $tipo) {
    //dd($periodo, $tipo);
    
    $data= array();    
    $i= 0;   
    
    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $cuentas= Ctmayore::where('pcontable_id', $periodo)
                    ->where('tipo', $tipo)
                    ->select('cuenta')->get();
    
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());
      
      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);
      
      // si la cuenta no tuvo actividad la ignora
      $saldo= floatval($totalDebito) - floatval($totalCredito);
      
      if ($saldo!=0) {
        // Arma un arreglo con la informacion de las cuenta en estudio
        //$data[$i]["id"]= $datos->id;
        $data[$i]["periodo"]= $periodo;
        $data[$i]["cuenta"]= $cta->id;
        $data[$i]["codigo"]= $cta->codigo;
        $data[$i]["cta_nombre"]= $cta->nombre;
        
        if ($tipo==6) {
          $totalAjuste= floatval($totalDebito) - floatval($totalCredito);
          //dd($totalAjuste);
          
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= $totalAjuste;
            $data[$i]["saldo_credito"]= "";         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;        
          }
        
        } elseif ($tipo==4) {
          $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
          //dd($totalAjuste);
          
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]=  $totalAjuste;
            $data[$i]["saldo_credito"]= "";       
          }
        } else {
          return 'Error: tipo de cuenta invalido en function Hojat::getDataParaEstadoResultado()';
        }  
      } 
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    //dd($data);
    
    return $data;
  }

  
  /** 
  *=============================================================================================
  * Arma un arreglo con la informacion necesaria para confeccionar
  * el Estado de resultado de un determinado periodo contable
  * @param  string    $periodo  "4"
  * @param  integer   $tipo     4
  * @return void
  *===========================================================================================*/
  public static function getDataParaEstadoResultadoHis($periodo, $tipo) {
    //dd($periodo, $tipo);
    
    $data= array();    
    $i= 0;   
    
    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $cuentas= Ctmayorehi::where('pcontable_id', $periodo)
                    ->where('cierre', 0)
                    ->where('tipo', $tipo)
                    ->select('cuenta')->get();
    
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());
      
      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayorehi::where('pcontable_id', $periodo)
                    ->where('cierre', 0)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayorehi::where('pcontable_id', $periodo)
                    ->where('cierre', 0)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);
      
      // si la cuenta no tuvo actividad la ignora
      $saldo= floatval($totalDebito) - floatval($totalCredito);
      if ($saldo!=0) {
        // Arma un arreglo con la informacion de las cuenta en estudio
        //$data[$i]["id"]= $datos->id;
        $data[$i]["periodo"]= $periodo;
        $data[$i]["cuenta"]= $cta->id;
        $data[$i]["codigo"]= $cta->codigo;
        $data[$i]["cta_nombre"]= $cta->nombre;
        
        if ($tipo==6) {
          $totalAjuste= floatval($totalDebito) - floatval($totalCredito);
          //dd($totalAjuste);
          
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= $totalAjuste;
            $data[$i]["saldo_credito"]= "";         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;        
          }
        
        } elseif ($tipo==4) {
          $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
          //dd($totalAjuste);
          
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldo_debito"]= "";
            $data[$i]["saldo_credito"]= $totalAjuste;         
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldo_debito"]=  $totalAjuste;
            $data[$i]["saldo_credito"]= "";       
          }
        } else {
          return 'Error: tipo de cuenta invalido en function Hojat::getDataParaEstadoResultado()';
        }  
      } 
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
    //dd($data);
    
    return $data;
  }

  /** 
  *=============================================================================================
   * Arma un arreglo con la informacion de las cuentas tipo activos o pasivos, corriente o 
   * no corrientes necesaria para confeccionar el Balance general de un determinado periodo contable
  * @param  string   $periodo           "4"
  * @param  integer  $tipo              1 
  * @param  integer  $corriente_siono   1
  * @return void
  *===========================================================================================*/
  public static function getDataParaBalanceGeneral($periodo, $tipo, $corriente_siono=Null) {
    //dd($periodo, $tipo, $corriente_siono);
    
    $data=array();    
    $i=0;   

    // Encuentra todas las cuentas activas en ctmayores de un determinado periodo y tipo de cuenta
    if ($tipo==1 || $tipo==2) {
      $cuentas=Ctmayore::where('pcontable_id', $periodo)
                ->where('ctmayores.tipo', $tipo)
                ->join('catalogos','catalogos.id','=','ctmayores.cuenta')
                ->where('catalogos.corriente_siono', $corriente_siono)
                ->select('cuenta')
                ->get();

    } elseif ($tipo==3) {
      $cuentas=Ctmayore::where('pcontable_id', $periodo)
              ->where('ctmayores.tipo', $tipo)
              ->select('cuenta')
              ->get();
    } else {
      return 'Error: tipo de cuenta invalido en function Hojat::getDataParaBalanceGeneral()';
    }   
    
    $cuentas=$cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());

      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);

      // Arma un arreglo con la informacion de las cuenta en estudio
      //$data[$i]["id"]= $datos->id;
      $data[$i]["periodo"] = $periodo;
      $data[$i]["cuenta"] = $cta->id;
      $data[$i]["codigo"] = $cta->codigo;
      $data[$i]["cta_nombre"]= $cta->nombre;
      
      if ($tipo ==1 ) {
        $totalAjuste = floatval($totalDebito) - floatval($totalCredito);
        if ($totalAjuste > 0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";         
        
        } elseif ($totalAjuste < 0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";        
        
        } elseif ($totalAjuste == 0) {
          // si es igual a cero
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = "";       
        }

      } elseif ($tipo==2 || $tipo==3) {
        $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
        if ($totalAjuste > 0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = $totalAjuste;         
        
        } elseif ($totalAjuste < 0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = $totalAjuste;       
        
        } elseif ($totalAjuste == 0) {
          // si es igual a cero
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = 0;       
        }
      
      } else {
        return 'Error: tipo de cuenta invalido en function Sity::getDataParaBalanceGeneral()';
      }
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
 
    return $data;
  }

 
  /** 
  *=============================================================================================
   * Arma un arreglo con la informacion de las cuentas tipo activos o pasivos, corriente o 
   * no corrientes necesaria para confeccionar el Balance general de un determinado periodo contable
  * @param  string   $periodo           "4"
  * @param  integer  $tipo              1 
  * @param  integer  $corriente_siono   1
  * @return void
  *===========================================================================================*/
  public static function getDataParaBalanceGeneralHis($periodo, $tipo, $corriente_siono=Null) {
    //dd($periodo, $tipo, $corriente_siono);
    
    $data=array();    
    $i=0;   

    // Encuentra todas las cuentas activas en ctmayores de un determinado periodo y tipo de cuenta
    if ($tipo==1 || $tipo==2) {
      $cuentas=Ctmayorehi::where('pcontable_id', $periodo)
                ->where('ctmayorehis.tipo', $tipo)
                ->join('catalogos','catalogos.id','=','ctmayorehis.cuenta')
                ->where('catalogos.corriente_siono', $corriente_siono)
                ->select('cuenta')
                ->get();

    } elseif ($tipo==3) {
      $cuentas=Ctmayorehi::where('pcontable_id', $periodo)
              ->where('tipo', $tipo)
              ->select('cuenta')
              ->get();
    } else {
      return 'Error: tipo de cuenta invalido en function Hojat::getDataParaBalanceGeneral()';
    }   
    
    $cuentas=$cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());

      // calcula el saldo debito de la cuenta
      $totalDebito= Ctmayorehi::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('debito');
      //dd($totalDebito);
      
      // calcula el saldo credito de la cuenta
      $totalCredito= Ctmayorehi::where('pcontable_id', $periodo)
                    ->where('cuenta', $cuenta->cuenta)
                    ->sum('credito');
      //dd($totalCredito);

      // Arma un arreglo con la informacion de las cuenta en estudio
      //$data[$i]["id"]= $datos->id;
      $data[$i]["periodo"] = $periodo;
      $data[$i]["cuenta"] = $cta->id;
      $data[$i]["codigo"] = $cta->codigo;
      $data[$i]["cta_nombre"]= $cta->nombre;
      
      if ($tipo ==1 ) {
        $totalAjuste = floatval($totalDebito) - floatval($totalCredito);
        
        if ($totalAjuste > 0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";         
        
        } elseif ($totalAjuste < 0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";        
        
        } elseif ($totalAjuste == 0) {
          // si es igual a cero
          $data[$i]["saldo_debito"] = 0;
          $data[$i]["saldo_credito"] = "";       
        }

      } elseif ($tipo==2 || $tipo==3) {
        $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
        if ($totalAjuste > 0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = $totalAjuste;         
        
        } elseif ($totalAjuste < 0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = $totalAjuste;       
        
        } elseif ($totalAjuste == 0) {
          // si es igual a cero
          $data[$i]["saldo_debito"] = "";
          $data[$i]["saldo_credito"] = 0;       
        }
      
      } else {
        return 'Error: tipo de cuenta invalido en function Sity::getDataParaBalanceGeneral()';
      }
      $i++;    
    }
    
    // ordena el arreglo por codigo de cuenta ascendente
    $data = array_values(array_sort($data, function ($value) {
        return $value['codigo'];
    }));
 
    return $data;
  }


  /** 
  *=============================================================================================
  * Calcula la Utilidad neta de un periodo en especial
  * @param  stringt $pcontable_id  "1"
  * @return void
  *===========================================================================================*/
  public static function getUtilidadNeta($pcontable_id) {
    //dd($pcontable_id);

    // encuentra todas las cuentas de Ingresos de un determinado periodo contable
    $ingresos= self::getDataParaEstadoResultado($pcontable_id, 4);

    // encuentra todas las cuentas de Gastos de un determinado periodo contable
    $gastos= self::getDataParaEstadoResultado($pcontable_id, 6);
            
    //calcula el total de la columna debito y el de la columna credito
    $totalIngresos = 0;
    $totalGastos = 0;
    
    // calcula en total de ingresos recibidos         
    foreach($ingresos as $ingreso) {
      $totalIngresos += $ingreso['saldo_credito'];
    }        
    
    foreach($gastos as $gasto) {
      // totales balance ajustado        
      $totalGastos += $gasto['saldo_debito'];
    }
   
    // calcula la utilidad neta
    $utilidad = $totalIngresos - $totalGastos;
    return $utilidad;
  }

  /** 
  *=============================================================================================
  * Esta function cierra todas las cuentas temporales que tuvieron activas en el periodo
  * contable a cerrar
  * @param  string       $pcontable_id "1"
  * @param  date/carbon  $fecha        +"date": "2016-01-01 00:00:00.000000"  
  * @return void
  *===========================================================================================*/
  public static function cierraCuentasTemp($pcontable_id, $fecha) {
    //dd($pcontable_id, $fecha);
    
    // Antes de comenzar a cerrar la cuenta temporales, se calcula la Utilidad neta del periodo a cerrarse
    $utilidad= self::getUtilidadNeta($pcontable_id); 

    // Encuentra todas las cuentas de ingresos activas en periodo contable a cerrar
    $cuentas= Ctmayore::where('tipo', 4)
                    ->where('pcontable_id', $pcontable_id)
                    ->get();
    
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());

    // procesa cada una de las cuentas encontradas
    $i=1;
    foreach ($cuentas as $cuenta) {
      // almacena el saldo de la cuenta antes de cerrarla
      $saldoIngresos= Sity::getSaldoCuenta($cuenta->cuenta, $pcontable_id);

      // agrega un nuevo registro de cierre de cuenta
      $dato = new Ctmayore;
      $dato->pcontable_id     = $pcontable_id;
      $dato->tipo             = 4; 
      $dato->cuenta           = $cuenta->cuenta;
      $dato->codigo           = $cuenta->codigo;
      $dato->fecha            = $fecha->endOfMonth();
      $dato->detalle          = 'Cierra '.Catalogo::find($cuenta->cuenta)->nombre;
      $dato->debito           = $saldoIngresos;
      $dato->credito          = 0;
      $dato->cierre           = 1;
      $dato->save();
      
      if ($saldoIngresos>0) {      
        if ($i==1) {
          // registra en Ctdiario principal
          $dato = new Ctdiario;
          $dato->pcontable_id  = $pcontable_id;
          $dato->fecha         = $fecha;
          $dato->detalle = Catalogo::find($cuenta->cuenta)->nombre;
          $dato->debito  = $saldoIngresos;
          $dato->save();  
        
        } else {
          // registra en Ctdiario principal
          $dato = new Ctdiario;
          $dato->pcontable_id  = $pcontable_id;
          $dato->detalle = Catalogo::find($cuenta->cuenta)->nombre;
          $dato->debito  = $saldoIngresos;
          $dato->save();  
        }
        $i++;
      }    
    }

    // Encuentra todas las cuentas de ingresos activas en periodo contable a cerrar
    $cuentas= Ctmayore::where('tipo', 6)
                    ->where('pcontable_id', $pcontable_id)
                    ->get();
    $cuentas = $cuentas->unique('cuenta');
    //dd($cuentas->toArray());

    // procesa cada una de las cuentas encontradas
    foreach ($cuentas as $cuenta) {
      // almacena el saldo de la cuenta antes de cerrarla
      $saldoGastos= Sity::getSaldoCuenta($cuenta->cuenta, $pcontable_id);

      // agrega un nuevo registro de cierre de cuenta
      $dato = new Ctmayore;
      $dato->pcontable_id     = $pcontable_id;
      $dato->tipo             = 6;
      $dato->cuenta           = $cuenta->cuenta;
      $dato->codigo           = $cuenta->codigo;
      $dato->fecha            = $fecha;
      $dato->detalle          = 'Cierra '.Catalogo::find($cuenta->cuenta)->nombre;
      $dato->debito           = 0;
      $dato->credito          = $saldoGastos;
      $dato->cierre           = 1;
      $dato->save();
      
      if ($saldoGastos>0) {      
          // registra en Ctdiario principal
          $dato = new Ctdiario;
          $dato->pcontable_id  = $pcontable_id;
          $dato->detalle = '   '.Catalogo::find($cuenta->cuenta)->nombre;
          $dato->credito = $saldoGastos;
          $dato->save();  
        }
    }

    // registra la utilidad del periodo
    if ($utilidad > 0 || $utilidad == 0) {
      // En Ctmayores se registra un aumento en utilidad neta 
      Sity::registraEnCuentas($pcontable_id, 'mas', 3, 7, $fecha, 'Se registra aumento en la utilidad del periodo', $utilidad, Null, Null);
    
      // registra la utilidad en el diario del periodo posterior
      $data = new Ctdiario;
      $data->pcontable_id     = $pcontable_id;
      $data->detalle          = '   Utilidad neta';
      $data->credito          = $utilidad;
      $data->save();   

    } elseif ($utilidad < 0) {
      // En Ctmayores se registra una disminucion en utilidad neta 
      Sity::registraEnCuentas($pcontable_id, 'menos', 3, 7, $fecha, 'Se registra una perdida en el periodo', $utilidad, Null, Null);
    
      // registra la utilidad en el diario del periodo posterior
      $data = new Ctdiario;
      $data->pcontable_id     = $pcontable_id;
      $data->detalle          = '   Perdida neta';
      $data->debito           = $utilidad;
      $data->save(); 
    }

    // registra la utilidad en el diario del periodo posterior
    $data = new Ctdiario;
    $data->pcontable_id     = $pcontable_id;
    $data->detalle          = 'Para cerrar cuentas temporales y registrar utilidad neta de '.Pcontable::find($pcontable_id)->periodo;
    $data->save(); 
  }


  /** 
  *=============================================================================================
  * Al momento de cerrar un determinado periodo contable, el sistema migra los datos
  * de ctmayores a la tabla de datos historicos ctmayorehis y posteriormente borra
  * datos migrados de la tabla ctmayores
  * @param  $pcontable_id 
  * @return void
  *===========================================================================================*/
  public static function migraDatosCtmayorehis($pcontable_id) {
    
    $datos= Ctmayore::where('pcontable_id', $pcontable_id)->get();
    
    foreach ($datos as $dato) {
      $data= new Ctmayorehi;
      
      $data->id               = $dato->id;
      $data->pcontable_id     = $dato->pcontable_id;
      $data->tipo             = $dato->tipo;
      $data->cuenta           = $dato->cuenta;
      $data->codigo           = $dato->codigo;
      $data->fecha            = $dato->fecha;
      $data->detalle          = $dato->detalle;
      $data->debito           = $dato->debito;
      $data->credito          = $dato->credito;
      $data->un_id            = $dato->un_id;
      $data->org_id           = $dato->org_id; 
      $data->cierre           = $dato->cierre; 
      
      $data->save();

      // borra todos los datos del periodo de la tabla ctmayores
      Ctmayore::destroy($dato->id);
    }
  }

  /** 
  *=============================================================================================
  * Al momento de cerrar un determinado periodo contable, el sistema migra los datos
  * de ctdiarios a la tabla de datos historicos ctdiariohis y posteriormente los borra
  * los datos migrados de la tabla ctdiarios
  * @param  $pcontable_id 
  * @return void
  *===========================================================================================*/
  public static function migraDatosCtdiariohis($pcontable_id) {
    
    $datos= Ctdiario::where('pcontable_id', $pcontable_id)->get();
    
    foreach ($datos as $dato) {

      $data = new Ctdiariohi;
      
      $data->id               = $dato->id;     
      $data->pcontable_id     = $dato->pcontable_id;
      $data->fecha            = $dato->fecha;
      $data->detalle          = $dato->detalle;
      $data->debito           = $dato->debito ? $dato->debito : Null;
      $data->credito          = $dato->credito ? $dato->credito : Null;
      
      $data->save(); 

      // borra todos los datos del periodo de la tabla ctdiarios
      Ctdiario::destroy($dato->id);
    }
  }

  /** 
  *=============================================================================================
  * Al momento de cerrar un determinado periodo contable, el sistema migra los datos
  * de ctdasms a la tabla de datos historicos ctdasmhis
  * @param  $pcontable_id 
  * @return void
  *===========================================================================================*/
  public static function migraDatosCtdasmhis($pcontable_id) {
    
    $datos= Ctdasm::where('pcontable_id', $pcontable_id)->get();
    
    foreach ($datos as $dato) {

      $data = new Ctdasmhi;
      
      $data->id              = $dato->id;
      $data->pcontable_id    = $dato->pcontable_id;
      $data->fecha           = $dato->fecha;
      $data->ocobro          = $dato->ocobro;
      $data->diafact         = $dato->diafact;
      $data->mes_anio        = $dato->mes_anio;
      $data->detalle         = $dato->detalle;
      $data->importe         = $dato->importe;
      $data->recargo_siono   = $dato->recargo_siono;
      $data->f_vencimiento   = $dato->f_vencimiento;
      $data->recargo         = $dato->recargo;
      $data->recargo_pagado  = $dato->recargo_pagado;
      $data->descuento_siono = $dato->descuento_siono;
      $data->f_descuento     = $dato->f_descuento;
      $data->descuento       = $dato->descuento;
      $data->extra_siono     = $dato->extra_siono;
      $data->extra           = $dato->extra;
      $data->extra_pagada    = $dato->extra_pagada;
      $data->pagada          = $dato->pagada;
      $data->bloque_id       = $dato->bloque_id;
      $data->seccione_id     = $dato->seccione_id;

      $data->save(); 
    
    }
  }

  /** 
  *=============================================================================================
  * Esta function penaliza en grupo al cierre de periodo
  * @param  carbon/date $fecha          "2016-03-01 00:00:00.000000"    - fecha de inicio de periodo
  * @param  string      $pcontable_id   "3"                      - periodo a cerrar
  * @return void
  *===========================================================================================*/
  public static function penalizarTipo1($fecha, $pcontable_id) {
    //dd($fecha, $pcontable_id);
    
    // clona $fecha para mantener su valor original
    $f_limite = clone $fecha;
     
    // encuentra todas las fechas de vencimiento
    $vfechas= Ctdasm::whereDate('f_vencimiento','<', $f_limite->endOfMonth()->toDateString())
                ->where('pagada', 0)
                ->where('recargo_siono', 0)
                ->select('f_vencimiento')
                ->orderBy('f_vencimiento')              
                ->distinct()
                ->get();
    //dd($fecha, $vfechas->toArray(), $f_limite->toDateString(), $pcontable_id);
    
    // si encuentra alguna fecha, quiere decir que hay unidades a penalizar        
    if ($vfechas->count() > 0) {
      foreach ($vfechas as $vfecha) {
        // encuentra todas aquella unidades que no han sido pagadas y que tienen fecha de pago vencida
        $datos= Ctdasm::where('f_vencimiento', $vfecha->f_vencimiento)
                    ->where('pagada', 0)
                    ->where('recargo_siono', 0)
                    ->get();
        //dd($datos->toArray());

        $i= 1;   
        
        // inicializa variable para almacenar el total de recargos
        $totalRecargos= 0;       

        if ($datos->count()) {
          foreach ($datos as $dato) {
            $dto = Ctdasm::find($dato->id);
            $dto->recargo_siono = 1;
            $dto->save();  

            // acumula el total de recargos
            $totalRecargos = $totalRecargos + $dato->recargo;
            
            // registra 'Recargo por cobrar en cuota de mantenimiento' 1130.00
            Sity::registraEnCuentas(
                  $pcontable_id,
                  'mas',
                  1,
                  2, //'1130.00',
                  $f_limite->endOfMonth()->toDateString(),
                  'Recargo en cuota de mantenimiento por cobrar unidad '.$dato->ocobro,
                  $dato->recargo,
                  $dato->un_id
                 );

            // registra 'Ingreso por cuota de mantenimiento' 4130.00
            Sity::registraEnCuentas(
                  $pcontable_id,
                  'mas',
                  4,
                  4, //'4130.00',
                  $f_limite->endOfMonth()->toDateString(),
                  '   Ingreso por recargo en cuota de mant unidad '.$dato->ocobro,
                  $dato->recargo,
                  $dato->un_id
                 );

            // registra resumen de la facturacion mensual en Ctdiario principal 
            if ($i==1) {
              // registra en Ctdiario principal
              $dto = new Ctdiario;
              $dto->pcontable_id  = $pcontable_id;
              $dto->fecha   = $f_limite->endOfMonth()->toDateString();
              $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
              $dto->debito  = $dato->recargo; 
              $dto->save(); 
            
            } else {
                // registra en Ctdiario principal
                $dto = new Ctdiario;
                $dto->pcontable_id  = $pcontable_id;
                $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
                $dto->debito  = $dato->recargo;
                $dto->save(); 
            }
            $i++;
          } // end foreach $datos 
          
          // registra en Ctdiario principal
          $dto = new Ctdiario;
          $dto->pcontable_id = $pcontable_id;
          $dto->detalle = '   '.Catalogo::find(4)->nombre;
          $dto->credito  = $totalRecargos;
          $dto->save(); 

          // registra en Ctdiario principal
          $dto = new Ctdiario;
          $dto->pcontable_id = $pcontable_id;
          $dto->detalle = 'Para registrar resumen de recargos en cuotas de mantenimiento por cobrar vencidas a '.Date::parse($dato->f_vencimiento)->toFormattedDateString();
          $dto->save();     
         
          $totalRecargos= 0;
        } // end of if
      } // end foreach $vfechas
    } // end of if
} // end of function

} //fin de Class Hojat