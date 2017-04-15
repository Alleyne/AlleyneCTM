<?php namespace App\library;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use App\library\Sity;
use App\library\Pant;

use App\Ctmayore;
use App\Ctdiario;
use App\Catalogo;
use App\Un;
use App\Ctdasm;
use App\Pcontable;
use App\Ht;
use App\Ctmayorehi;
use App\Ctdiariohi;

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
      $datos= self::getDataParaHojaDeTrabajo($pcontable_id);
      //dd($datos);
      
      $pdo= Pcontable::find($pcontable_id)->periodo;

      $i=1;
      foreach($datos as $dato) {
        if ($dato['tipo']==1) {
          // registra en la tabla ctmayores
          $data = new Ctmayore;
          $data->pcontable_id     = $pcontable_id + 1;
          $data->tipo             = $dato['tipo'];
          $data->cuenta           = $dato['cuenta'];
          $data->codigo           = $dato['codigo'];
          $data->fecha            = $fecha;
          $data->detalle          = $dato['cta_nombre'].' '.$pdo;
          $data->debito           = $dato['saldoAjustado_debito'];
          $data->credito          = $dato['saldoAjustado_credito'];
          $data->save();
          
          if ($i==1) {
            // registra en Ctdiario principal
            $data = new Ctdiario;
            $data->pcontable_id  = $pcontable_id + 1;
            $data->fecha         = $fecha;
            $data->detalle = $dato['cta_nombre'].' '.$pdo;
            $data->debito  = $dato['saldoAjustado_debito'];
            $data->credito = Null;
            $data->save();

          } else {
            // registra en Ctdiario principal
            $data = new Ctdiario;
            $data->pcontable_id  = $pcontable_id + 1;
            $data->detalle = $dato['cta_nombre'].' '.$pdo;
            $data->debito  = $dato['saldoAjustado_debito'];
            $data->credito = Null;
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
          $data->detalle          = $dato['cta_nombre'].' '.$pdo;
          $data->debito           = $dato['saldoAjustado_debito'];
          $data->credito          = $dato['saldoAjustado_credito'];
          $data->un_id            = 0;
          $data->save();
          
          // registra en Ctdiario principal
          $data = new Ctdiario;
          $data->pcontable_id  = $pcontable_id + 1;
          $data->detalle = $dato['cta_nombre'].' '.$pdo;
          $data->debito  =  Null;
          $data->credito = $dato['saldoAjustado_credito'];
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
      
      $detalle= Catalogo::find(5)->nombre.', '.Un::find($un->un_id)->codigo;
      
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
  * Arma un arreglo con la informacion necesaria para confeccionar
  * la hoja de trabajo de un determinado periodo contable
  * @param  integer $periodo  4   
  * @return void
  *===========================================================================================*/
  public static function getDataParaHojaDeTrabajo($periodo)
  {
    //dd($periodo);    

    $data=array();    
    $i=0;   

    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $cuentas= Ctmayore::where('pcontable_id', $periodo)->where('cuenta','!=', 5)->select('cuenta')->get();
    //dd($cuentas->toArray());
    
    $cuentas= $cuentas->unique('cuenta');
    //dd($cuentas->toArray());
      
    // procesa cada una de las cuentas encontradas excluyendo la cuenta no 5 de Pagos anticipados ya
    // que es una cuenta compartida por muchas unidades, por lo tanto hay que calcularle el saldo
    // a cada unidad por separado. Este proceso se hace al final del foreach
    foreach ($cuentas as $cuenta) {
      // encuentra las generales de la cuenta
      $cta= Catalogo::find($cuenta->cuenta);
      //dd($cta->toArray());

      // Calcula el saldo de la cuenta tomando en cuenta el periodo y eliminado los ajustes hechos a la misma      
      $totalDebito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
                    ->where('ajuste_siono', 0)
                    ->sum('debito');
      //dd($totalDebito);
      
      $totalCredito= Ctmayore::where('pcontable_id', $periodo)
                    ->where('cuenta', $cta->id)
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
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;

      // clasifica el saldo actual de la cuenta en estudio para determinar si el mismo es tipo debito o credito
      if ($cta->tipo==1) {
        $saldo= floatval($totalDebito)-floatval($totalCredito);
        $data[$i]["saldo_debito"]= $saldo;
        $data[$i]["saldo_credito"]= 0;
        
        $data[$i]["saldoAjustado_debito"]= $saldo;
        $data[$i]["saldoAjustado_credito"]= 0;

        $data[$i]["bg_debito"]= $saldo;
        $data[$i]["bg_credito"]= 0;      

      } elseif ($cta->tipo==6) {
        $saldo= floatval($totalDebito)-floatval($totalCredito);
        $data[$i]["saldo_debito"]= $saldo;
        $data[$i]["saldo_credito"]= 0;
        
        $data[$i]["saldoAjustado_debito"]= $saldo;
        $data[$i]["saldoAjustado_credito"]= 0;
      
        $data[$i]["er_debito"]= $saldo;
        $data[$i]["er_credito"]= 0;

      } elseif ($cta->tipo==2) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;

        $data[$i]["bg_debito"]= 0;
        $data[$i]["bg_credito"]= $saldo;

      } elseif ($cta->tipo==3) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;      
      
        $data[$i]["bg_debito"]= 0;
        $data[$i]["bg_credito"]= $saldo; 

      } elseif ($cta->tipo==4) {
        $saldo= floatval($totalCredito)-floatval($totalDebito);
        $data[$i]["saldo_debito"]= 0;
        $data[$i]["saldo_credito"]= $saldo;
        
        $data[$i]["saldoAjustado_debito"]= 0;
        $data[$i]["saldoAjustado_credito"]= $saldo;

        $data[$i]["er_debito"]= 0;
        $data[$i]["er_credito"]= $saldo;
      }

      //verifica si la cuenta en estudio tuvo ajustes
      $ajustes= Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 

        // clasifica el total de ajuste hechos a la cuenta de acuerdo a si es tipo debito o credito
        if ($cta->tipo==1) {
          $totalAjuste= floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= $totalAjuste;
            $data[$i]["saldoAjuste_credito"]= 0;          
            
            $data[$i]["saldoAjustado_debito"]= $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"]= 0;           
          
            $data[$i]["bg_debito"]= $saldo + $totalAjuste;
            $data[$i]["bg_credito"]= 0;    

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= abs($totalAjuste);

            $data[$i]["saldoAjustado_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"]= 0;
          
            $data[$i]["bg_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["bg_credito"]= 0;
          }
        
        } elseif ($cta->tipo==6) {
          $totalAjuste= floatval($totalAjusteDebito) - floatval($totalAjusteCredito); 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= $totalAjuste;
            $data[$i]["saldoAjuste_credito"]= 0;          
            
            $data[$i]["saldoAjustado_debito"]= $saldo + $totalAjuste;
            $data[$i]["saldoAjustado_credito"]= 0;           
          
            $data[$i]["er_debito"]= $saldo + $totalAjuste;
            $data[$i]["er_credito"]= 0;        

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= abs($totalAjuste);

            $data[$i]["saldoAjustado_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["saldoAjustado_credito"]= 0;
          
            $data[$i]["er_debito"]= $saldo - abs($totalAjuste); 
            $data[$i]["er_credito"]= 0;
          }        

        } elseif ($cta->tipo==2) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste; 

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);
          
            $data[$i]["bg_debito"]= 0; 
            $data[$i]["bg_credito"]= $saldo - abs($totalAjuste);
          }
        
        } elseif ($cta->tipo==3) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
            
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste;              
          
          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= abs($saldo - abs($totalAjuste));
            $data[$i]["saldoAjustado_credito"]= 0; 

            $data[$i]["bg_debito"]= abs($saldo - abs($totalAjuste));
            $data[$i]["bg_credito"]= 0;
          }

        } elseif ($cta->tipo==4) {
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["er_debito"]= 0;
            $data[$i]["er_credito"]= $saldo + $totalAjuste;

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);

            $data[$i]["er_debito"]= 0; 
            $data[$i]["er_credito"]= $saldo - abs($totalAjuste);
          }
        }
      }
      $i++;    
    }
    
    // procesa individualmente cada una de las cuentas que comparten la cuenta de  Pagos anticipados 
    $uns= Ctmayore::where('pcontable_id', $periodo)
                  ->where('cuenta', 5)
                  ->select('un_id')->get();
    //dd($uns->toArray());
    
    $uns= $uns->unique('un_id');
    //dd($uns->toArray());    
    
    // procesa cada una de las unidades que tubieron Pagos anticipados en el periodo
    foreach ($uns as $un) {

      // encuentra las generales de la cuenta
      $cta= Catalogo::find(5);
      //dd($cta->toArray());

      // encuentra el codigo de la unidad
      $cod= Un::find($un->un_id)->codigo;
      
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
      $data[$i]["cta_nombre"]= $cta->nombre.' '.$cod;
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= 0;
      $data[$i]["saldoAjuste_debito"]= 0;
      $data[$i]["saldoAjuste_credito"]= 0;
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= 0;

      // coloca el saldo de la cuenta sin ajustes
      $saldo= floatval($totalCredito)-floatval($totalDebito);
      $data[$i]["saldo_debito"]= 0;
      $data[$i]["saldo_credito"]= $saldo;
      
      $data[$i]["saldoAjustado_debito"]= 0;
      $data[$i]["saldoAjustado_credito"]= $saldo;

      $data[$i]["bg_debito"]= 0;
      $data[$i]["bg_credito"]= $saldo;

      //verifica si la cuenta en estudio tuvo ajustes
      $ajustes= Ctmayore::where('pcontable_id', $periodo)
                        ->where('cuenta', $cta->id)
                        ->where('un_id', $un->un_id)
                        ->where('ajuste_siono', 1)
                        ->first();
      //dd($ajustes->toArray());      
      
      if ($ajustes) {
        // si la cuenta tuvo ajustes entonces
        // calcula el total de ajustes debito que tuvo la cuentao
        $totalAjusteDebito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('un_id', $un->un_id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('debito');
        // dd($totalAjusteDebito);
     
        // calcula el total de ajustes credito que tuvo la cuenta
        $totalAjusteCredito= Ctmayore::where('pcontable_id', $periodo)
                                    ->where('cuenta', $cuenta->cuenta)
                                    ->where('un_id', $un->un_id)
                                    ->where('ajuste_siono', 1)
                                    ->sum('credito');
        //dd($totalAjusteDebito, $totalAjusteCredito); 
        
        // clasifica el total de ajuste hechos a la cuenta de acuerdo a si es tipo debito o credito
          $totalAjuste= $totalAjusteCredito - $totalAjusteDebito; 
          if ($totalAjuste>0) {
            // si es mayor que cero huvo aumento en la cuenta
            $data[$i]["saldoAjuste_debito"]= 0;
            $data[$i]["saldoAjuste_credito"]= $totalAjuste;          
            
            $data[$i]["saldoAjustado_debito"]= 0;
            $data[$i]["saldoAjustado_credito"]= $saldo + $totalAjuste;           
          
            $data[$i]["bg_debito"]= 0;
            $data[$i]["bg_credito"]= $saldo + $totalAjuste; 

          } elseif ($totalAjuste<0) {
            // si es menor que cero huvo una disminucion en la cuenta
            $data[$i]["saldoAjuste_debito"]= abs($totalAjuste);
            $data[$i]["saldoAjuste_credito"]= 0;

            $data[$i]["saldoAjustado_debito"]= 0; 
            $data[$i]["saldoAjustado_credito"]= $saldo - abs($totalAjuste);
          
            $data[$i]["bg_debito"]= 0; 
            $data[$i]["bg_credito"]= $saldo - abs($totalAjuste);
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
  * Calcula el total en la cuenta de Ingresos o Gastos segun sea el caso
  * @param  string    $periodo  "4"
  * @param  integer   $tipo     4
  * @return void
  *===========================================================================================*/
/*  public static function getTotalesParaEstadoResultado($periodo, $tipo) {
    //dd($periodo, $tipo);

    // Encuentra todas las cuentas activas en ctmayores para un determinado periodo
    $ctmayores= Ctmayore::where('pcontable_id', $periodo)
                    ->where('tipo', $tipo)
                    ->get();
    //dd($ctmayores->toArray());
    
    // calcula el saldo debito de todas las cuentas de ingresos o gastos
    $totalDebito= $ctmayores->sum('debito');
    $totalCredito= $ctmayores->sum('credito');
    //dd($totalDebito, $totalCredito);
      
    if ($tipo==6) {
      $total= floatval($totalDebito) - floatval($totalCredito);

    } elseif ($tipo==4) {
      $total= floatval($totalCredito) - floatval($totalDebito);
    }  

    return $total;
  }*/

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
      $data[$i]["periodo"]= $periodo;
      $data[$i]["cuenta"]= $cta->id;
      $data[$i]["codigo"]= $cta->codigo;
      $data[$i]["cta_nombre"]= $cta->nombre;
      
      if ($tipo==1) {
        $totalAjuste= floatval($totalDebito) - floatval($totalCredito);
        if ($totalAjuste>0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";         
        
        } elseif ($totalAjuste<0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= $totalAjuste;
          $data[$i]["saldo_credito"]= "";        
        
        } elseif ($totalAjuste==0) {
          // si es igual a cero
          $data[$i]["saldo_debito"]= 0;
          $data[$i]["saldo_credito"]= "";       
        }

      } elseif ($tipo==2 || $tipo==3) {
        $totalAjuste= floatval($totalCredito) - floatval($totalDebito);
        if ($totalAjuste>0) {
          // si es mayor que cero huvo aumento en la cuenta
          $data[$i]["saldo_debito"]= "";
          $data[$i]["saldo_credito"]= $totalAjuste;         
        
        } elseif ($totalAjuste<0) {
          // si es menor que cero huvo una disminucion en la cuenta
          $data[$i]["saldo_debito"]= "";
          $data[$i]["saldo_credito"]= $totalAjuste;       
        
        } elseif ($totalAjuste==0) {
          // si es igual a cero
          $data[$i]["saldo_debito"]=  "";
          $data[$i]["saldo_credito"]= 0;       
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
    $utilidad= $totalIngresos - $totalGastos;
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
      $dato->detalle          = Catalogo::find($cuenta->cuenta)->nombre.' '.$cuenta->codigo;
      $dato->debito           = $saldoIngresos;
      $dato->credito          = 0;
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
      $dato->detalle          = '   '.Catalogo::find($cuenta->cuenta)->nombre;
      $dato->debito           = 0;
      $dato->credito          = $saldoGastos;
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
    if ($utilidad>0 || $utilidad==0) {
      // En Ctmayores se registra un aumento en utilidad neta 
      Sity::registraEnCuentas($pcontable_id, 'mas', 3, 7, $fecha, 'Se registra aumento en la utilidad del periodo', $utilidad, Null, Null);
    
      // registra la utilidad en el diario del periodo posterior
      $data = new Ctdiario;
      $data->pcontable_id     = $pcontable_id;
      $data->detalle          = '   Utilidad neta';
      $data->credito          = $utilidad;
      $data->save();   

    } elseif ($utilidad<0) {
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
  * Almacena datos del periodo antes de cerrarlo y las almacena en la tabla Hts
  * hoja de trabajo historica
  * @param  $pcontable_id 
  * @return void
  *===========================================================================================*/
  public static function migraDatosHts($pcontable_id) {

    // encuentra la data necesaria para confeccionar la hoja de trabajo historica
    $datos= self::getDataParaHojaDeTrabajo($pcontable_id);
    //dd($datos); 
    
    // procesa cada una de las cuentas encontradas
    foreach ($datos as $dato) {        
        $dto = new Ht;
        $dto->cuenta       = $dato['cuenta'];
        $dto->tipo         = $dato['tipo'];
        $dto->codigo       = $dato['codigo'];
        $dto->nombre       = $dato['cta_nombre'];
        $dto->clase        = $dato['clase'];
        $dto->bp_debito    = $dato['saldo_debito'];
        $dto->bp_credito   = $dato['saldo_credito'];
        $dto->aj_debito    = $dato['saldoAjuste_debito'];
        $dto->aj_credito   = $dato['saldoAjuste_credito'];            
        $dto->ba_debito    = $dato['saldoAjustado_debito'];
        $dto->ba_credito   = $dato['saldoAjustado_credito'];
        
        if ($dato['tipo']==1 || $dato['tipo']==2 || $dato['tipo']==3) {
            $dto->bg_debito    = $dato['saldoAjustado_debito'];
            $dto->bg_credito   = $dato['saldoAjustado_credito'];

        } elseif ($dato['tipo']==4 || $dato['tipo']==6) {
            $dto->er_debito    = $dato['saldoAjustado_debito'];
            $dto->er_credito   = $dato['saldoAjustado_credito'];
        }
      
        $dto->pcontable_id = $dato['periodo'];
        $dto->save(); 
    }
     //dd($dato);
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
      //$data->saldocta         = $dato->saldocta;
      $data->un_id            = $dato->un_id;
      $data->org_id           = $dato->org_id; 
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
    if ($vfechas->count()>0) {
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
            $dto->recargo_siono= 1;
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
                  '   Ingreso por recargo en cuota de mantenimiento unidad '.$dato->ocobro,
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