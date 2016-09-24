<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\library\Sity;
use Carbon\Carbon;
use App\Ctmayore;
use App\Pcontable;
use App\Catalogo;
use App\Ctdiario;
use App\Secapto;
use App\Un;

class crearNuevoPeriodo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctmaster:crearNuevoPeriodo {fecha : Fecha del periodo deseado: Ejemplo 2016-07-1 crea Periodo Julio-2016}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo periodo contable e inicializa cuenta temporales.';

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
        // Inicializa variable para almacenar el total facturado en el mes
        $totalIngresos=0;

        $day= $this->argument('fecha');
      
        $year= Carbon::parse($fecha)->year;
        $month= Carbon::parse($fecha)->month;
        $day= Carbon::parse($fecha)->day;
        //dd($year, $month, $day);

        // crea nuevo periodo contable
        $periodo= new Pcontable;
        $periodo->periodo = Sity::getMonthName($month).' '.$day.'-'.$year;
        $periodo->cerrado = 0;
        $periodo->save();

        $this->info('Crea nuevo periodo contable...');        
        
        // Encuentra todas las secciones de apartamentos en las cuales la fecha de registro
        // de cuota de mantenimiento por cobrar es el dia primero o el dia dieciséis de cada mes.
        $secaptos= Secapto::All();
        //dd($secaptos->toArray());
        
        foreach ($secaptos as $secapto) {
            // Encuentra todas las unidades que pertenecen a la seccion 
            $uns= Un::where('seccione_id', $secapto->seccione_id)->get();
            //dd($uns->toArray());

            // calcula el total que debera ingresar mensualmente en concepto de cuotas de mantenimiento
            foreach ($uns as $un) {
                 $totalIngresos= $totalIngresos+ floatval($secapto->cuota_mant);
            }
        }
        //dd($totalIngresos);
        
        // Esta function inicializa en el nuevo periodo todas las cuentas temporales
        // activas presentes en el catalogo de cuentas
        $this->inicializaCuentasTemp($periodo->id, $month, $year);     
    
        // Registra resumen de la facturacion mensual en Ctdiario principal 
        $this->registraEnCtdiario($totalIngresos, $periodo->id, $month, $year);
    }

  /****************************************************************************************
   * Esta function inicializa en el nuevo periodo todas las cuentas temporales
   * activas presentes en el catalogo de cuentas
   *****************************************************************************************/
  public function inicializaCuentasTemp($pcontable_id, $month, $year)
  {
      // Encuentra todas las cuentas de ingresos activas en el catalogo de cuentas
      $cuentas= Catalogo::where('tipo', 4)
                        ->where('activa', 1)
                        ->get();
      //dd($cuentas->toArray());
        
      // procesa cada una de las cuentas encontradas
      foreach ($cuentas as $cuenta) {
        // agrega un nuevo registro de apertura de periodo en donde se inicializan 
        // todas las cuentas de ingresos en cero
        $dato = new Ctmayore;
        $dato->pcontable_id     = $pcontable_id;
        $dato->tipo             = 4;
        $dato->cuenta           = $cuenta->id;
        $dato->codigo           = $cuenta->codigo;
        $dato->fecha            = Carbon::today();
        $dato->detalle          = 'Inicializa cuenta en cero por inicio de periodo contable';
        $dato->debito           = 0;
        $dato->credito          = 0;
        $dato->saldocta         = 0;
        $dato->saldoun          = 0;
        $dato->saldoorg         = 0;
        $dato->save();
      }
        
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id  = $pcontable_id;
      $dato->fecha         = Carbon::today();
      $dato->detalle = 'Inicializa todas las cuentas de ingresos en cero por inicio de periodo contable de '.Sity::getMonthName($month).'-'.$year;
      $dato->debito  = 0;
      $dato->credito = 0;
      $dato->save(); 

      // Encuentra todas las cuentas de gastos activas en el catalogo de cuentas
      $cuentas= Catalogo::where('tipo', 6)
                        ->where('activa', 1)
                        ->get();
      // dd($cuentas->toArray());
        
      // procesa cada una de las cuentas encontradas
      foreach ($cuentas as $cuenta) {
        // agrega un nuevo registro de apertura de periodo en donde se inicializan 
        // todas las cuentas de gastos en cero
        $dato = new Ctmayore;
        $dato->pcontable_id     = $pcontable_id;
        $dato->tipo             = 6;
        $dato->cuenta           = $cuenta->id;
        $dato->codigo           = $cuenta->codigo;
        $dato->fecha            = Carbon::today();
        $dato->detalle          = 'Inicializa cuenta en cero por inicio de periodo contable';
        $dato->debito           = 0;
        $dato->credito          = 0;
        $dato->saldocta         = 0;
        $dato->saldoun          = 0;
        $dato->saldoorg         = 0;
        $dato->save();
      }
      
      // registra en Ctdiario principal
      $dato = new Ctdiario;
      $dato->pcontable_id  = $pcontable_id;
      $dato->detalle = 'Inicializa todas las cuentas de gastos en cero por inicio de periodo contable de '.Sity::getMonthName($month).'-'.$year;
      $dato->debito  = 0;
      $dato->credito = 0;
      $dato->save(); 

      $this->info('Inicializa cuenta temporales para inicio de nuevo periodo contable...');
  }

    /****************************************************************************************
     * Esta function registra en Ctdiario principal el resumen de la facturacion del mes
     *****************************************************************************************/
    public function registraEnCtdiario($totalIngresos, $periodo, $month, $year)
    {
           
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo;
        $dato->fecha         = Carbon::today();
        $dato->detalle       = 'Cuota de mantenimiento por cobrar';
        $dato->debito        = $totalIngresos;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo;
        $dato->detalle = '   Ingresos por cuota de mantenimiento';
        $dato->credito = $totalIngresos;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo;
        $dato->detalle = 'Para registrar resumen de ingresos por cuotas de mantenimiento del mes de '.Sity::getMonthName($month).'-'.$year;
        $dato->save(); 
    
        $this->info('Finaliza registro de resumen en el diario');
    }

} // fin de clase