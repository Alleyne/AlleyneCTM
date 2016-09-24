<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\library\Sity;

use App\Secapto;
use App\Un;
use App\Ctdasm;
use App\Pcontable;
use App\Ctdiario;
use App\Ctmayore;

class facturar extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'ctmaster:facturar {fecha : Fecha en que inicia la facturacion: Ejemplo 2016-07-1 o 2016-07-16}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Genera facturacion mensual de cada una de las unidades segun fecha de inicio de facturacion';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        
        // Inicializa variable para almacenar el total facturado en el mes
        $totalIngresosDia_1=0;        
        $totalIngresosDia_16=0; 
        
        // Construye la fecha de facturacion segun el argumento
        //$year= Carbon::today()->year;
        //$month= Carbon::today()->month;
        $day= $this->argument('fecha');
      
        $year= Carbon::parse($fecha)->year;
        $month= Carbon::parse($fecha)->month;
        $day= Carbon::parse($fecha)->day;
        //dd($year, $month, $day);
        
        // encuentra el ultimo periodo contable registrado
        $periodo= Pcontable::all()->last()->id; 
        //dd($periodo);

        // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
        // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
        $secaptos= Secapto::with('seccione')->where('d_registra_cmpc', $day)->get();
        //dd($secaptos->toArray());
        
        foreach ($secaptos as $secapto) {
            // Encuentra el administrador encargado del bloque al cual pertenece la seccion
            $blqadmin= Sity::findBlqadmin($secapto->seccione->bloque_id);
            //dd($blqadmin);

            // Encuentra todas las unidades que pertenecen a la seccion 
            $uns= Un::where('seccione_id', $secapto->seccione_id)->get();
            //dd($uns->toArray());

            // Por cada apartamento que exista registra su cuota de mantenimiento por cobrar en el ctdiario auxiliar
            foreach ($uns as $un) {
                // Prepara los parametros necesarios para la function checkDescuento
                $un_id= $un->id;
                $cuota_mant= floatval($secapto->cuota_mant);
                $descuento= (floatval($secapto->cuota_mant) * floatval($secapto->descuento))/100;
                $ocobro= $un->codigo.' '.Sity::getMonthName($month).$day.'-'.$year;
               
                // Registra facturacion mensual de la unidad en estudio en el Ctdiario auxiliar de servicios de mantenimiento
                $dato= new Ctdasm;
                $dato->pcontable_id     = $periodo;
                $dato->fecha            = Carbon::createFromDate($year, $month, $day);
                $dato->ocobro           = $ocobro;
                $dato->diafact          = $day;                
                $dato->mes_anio         = Sity::getMonthName($month). '-'.$year;
                $dato->detalle          = 'Cuota de mantenimiento Unidad No ' . $un->id;
                $dato->importe          = $cuota_mant;
                if ($day==1) {
                  $dato->f_vencimiento    = Carbon::createFromDate($year, $month, $day)->endOfMonth()->addDays($secapto->d_gracias);
                } elseif ($day==16) {
                  $dato->f_vencimiento    = Carbon::createFromDate($year, $month, $day)->endOfMonth()->addDays(15+$secapto->d_gracias);
                }
                $dato->recargo          = ($secapto->cuota_mant * $secapto->recargo)/100;
                $dato->descuento        = $descuento;             
                $dato->f_descuento      = Carbon::createFromDate($year, $month, $day)->subMonths($secapto->m_descuento);   
                $dato->bloque_id        = $secapto->seccione->bloque_id;
                $dato->seccione_id      = $secapto->seccione_id;
                $dato->blqadmin_id      = $blqadmin;
                $dato->un_id            = $un_id;
                $dato->pagada           = 0;
                $dato->descuento_siono  = 0;
                $dato->save(); 
                
                // Acumula el total facturado
                $totalIngresosDia_1 = $totalIngresosDia_1+$cuota_mant;
                
                // Verifica si la unidad clasifica para obtener descuento por pagos adelantados,
                // si la misma clasifica, otorga el debido descuento
            }
        }    
        
        //dd($totalIngresosDia_1);
        
        // si se trata de la facturacion del dia primero se debe incluir la de los dias diesiceis
        if ($day==1) {
          // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
          // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
          $secaptos= Secapto::where('d_registra_cmpc', 16)->get();
          //dd($secaptos->toArray());
          
          foreach ($secaptos as $secapto) {
              // Encuentra todas las unidades que pertenecen a la seccion 
              $uns= Un::where('seccione_id', $secapto->seccione_id)->get();
              //dd($uns->toArray());

              // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento
              foreach ($uns as $un) {
                   $totalIngresosDia_16= $totalIngresosDia_16+ floatval($secapto->cuota_mant);
              }
          }            
          
          //dd($totalIngresosDia_16);
          
          // Registra facturacion mensual de la unidad en cuenta 'Cuota de mantenimiento por cobrar' 1120.00
          Sity::registraEnCuentas(
                  $periodo, // periodo                      
                  'mas',  // aumenta
                  1,      // cuenta id
                  1,      // '1120.00',
                  Carbon::createFromDate($year, $month, 1),   // fecha
                  'Resumen de Cuota de mantenimiento por cobrar para el periodo contable '.Sity::getMonthName($month).'-'.$year, // detalle
                  ($totalIngresosDia_1+$totalIngresosDia_16) // monto
                 );

          // Registra facturacion mensual de la unidad en cuenta 'Ingreso por cuota de mantenimiento' 4120.00
          Sity::registraEnCuentas(
                  $periodo, // periodo
                  'mas',    // aumenta
                  4,        // cuenta id
                  3,        //'4120.00'
                  Carbon::createFromDate($year, $month, 1),   // fecha
                  'Resumen de Ingreso por cuota de mantenimiento para el periodo contable '.Sity::getMonthName($month).'-'.$year, // detalle
                  ($totalIngresosDia_1+$totalIngresosDia_16) // monto
                 );
        }  

        $this->info('Finaliza facturacion para el mes de '.Sity::getMonthName($month).'-'.$year );
    }
} //fin de clase facturar