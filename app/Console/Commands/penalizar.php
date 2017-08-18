<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\library\Sity;
use Carbon\Carbon;
use App\Pcontable;
use DB;

class penalizar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctmaster:penalizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Penaliza a todas aquella unidades cuya fecha limite de pago sea anterior a la fecha actual';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        DB::beginTransaction();
        try {
            // verifica si estamos en el periodo real
            $periodoActualExiste= Pcontable::where('fecha', Carbon::today()->firstOfMonth())->first();
            //dd($periodoActualExiste);     

            // si estamos en el periodo real, procede a penalizar diariamente
            if ($periodoActualExiste) {
                $this->penalizarTipo3(Carbon::today());
                DB::commit();                
                $this->info('Penalizacion diaria culminada...!'); 
            } 

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', ' Ocurrio un error en el Command penalizar.php, la transaccion ha sido cancelada! '.$e->getMessage());
        }
    }

    /** 
    *=============================================================================================
    * Esta function penaliza en grupo cada dia desde el cron
    * @param  date/Carbon  $today   - fecha del dia de hoy
    * @return void
    *===========================================================================================*/
    public function penalizarTipo3($today)
    {
      //dd($today);

      // clona $fecha para mantener su valor original
      $f_limite = clone $today;
       
      // encuentra las fechas de vencimiento del periodo al final del mes
      $vfechas= Ctdasm::whereDate('f_vencimiento','<', $today)
                  ->where('pagada', 0)
                  ->where('recargo_siono', 0)
                  ->select('f_vencimiento')
                  ->orderBy('f_vencimiento')              
                  ->distinct()
                  ->get();
      //dd($vfechas->toArray());
      
      // si encuentra alguna fecha, quiere decir que hay unidades a penalizar        
      if ($vfechas->count()>0) {
        foreach ($vfechas as $vfecha) {
          
          // determina a que periodo corresponde la fecha de vencimiento 
          $f_vencimiento= Carbon::parse($vfecha->f_vencimiento);
          $month= $f_vencimiento->month;    
          $year= $f_vencimiento->year;    
        
          $pdo= Sity::getMonthName($month).'-'.$year;
          $periodo= Pcontable::where('periodo', $pdo)->first()->id;
          //dd($periodo);

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
                    $periodo,
                    'mas',
                    1,
                    2, //'1130.00',
                    $today,
                    'Recargo en cuota de mant por cobrar unidad '.$dato->ocobro,
                    $dato->recargo,
                    $dato->un_id
                   );

              // registra 'Ingreso por cuota de mantenimiento' 4130.00
              Sity::registraEnCuentas(
                    $periodo,
                    'mas',
                    4,
                    4, //'4130.00',
                    $today,
                    '   Ingreso por recargo en cuota de mant unidad '.$dato->ocobro,
                    $dato->recargo,
                    $dato->un_id
                   );

              // registra resumen de la facturacion mensual en Ctdiario principal 
              if ($i==1) {
                // registra en Ctdiario principal
                $dto = new Ctdiario;
                $dto->pcontable_id  = $periodo;
                $dto->fecha   = $today;
                $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
                $dto->debito  = $dato->recargo; 
                $dto->save(); 
              
              } else {
                  // registra en Ctdiario principal
                  $dto = new Ctdiario;
                  $dto->pcontable_id  = $periodo;
                  $dto->detalle = Catalogo::find(2)->nombre.' unidad '.$dato->ocobro;
                  $dto->debito  = $dato->recargo;
                  $dto->save(); 
              }
              $i++;
            } // end foreach $datos 
            
            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id = $periodo;
            $dto->detalle = '   '.Catalogo::find(4)->nombre;
            $dto->credito  = $totalRecargos;
            $dto->save(); 

            // registra en Ctdiario principal
            $dto = new Ctdiario;
            $dto->pcontable_id = $periodo;
            $dto->detalle = 'Para registrar resumen de recargos en cuotas de mant por cobrar vencidas a '.Date::parse($dato->f_vencimiento)->toFormattedDateString();
            $dto->save();     
           
            $totalRecargos= 0;
          } // end of if
        } // end foreach $vfechas
      } // end of if
    } // end of function

}