<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\library\Sity;
use Carbon\Carbon;
use App\Ctdasm;
use App\Secapto;

class autofacturar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ctmaster:autofacturar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera auto facturacion mensual de cada una de las unidades segun fecha de inicio de facturacion';

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
        // Construye la fecha de facturacion segun el argumento
        $year=Carbon::today()->year;
        $month=Carbon::today()->month;

        // verifica si hay periodo creado para el presente mes
        $periodo= Pcontable::where('periodo', $month.'-'.$year);

        // si no hay lo crea
        if (!$periodo) {
            $this->call('ctmaster:crearNuevoPerido', ['fecha' => Carbon::create($year, $month, 1)]);   
        } 

        /*-----------------------------------------------------------------------------------*/
        /*-- PERIODICAMENTE REVISA SI HAY FACTURACION PAR EL DIA PRIMERO DEL MES CORRIENTE --*/
        /*-----------------------------------------------------------------------------------*/
        // Pregunta si la fecha actual esta entre el dia primero y el dia quince,
        // incluyento el dia primero y el quince.
        if (Carbon::now()->between(Carbon::create($year, $month, 1), Carbon::create($year, $month, 15))) {
            //dd('estoy aqui');
            // Encuentra todas las secciones de apartamentos en las cuales la fecha de facturacion
            // es el dia primero de cada mes.
            $secaptos = Secapto::where('d_registra_cmpc', 1)->get();
            //dd($secaptos->toArray());
            
            if (!is_null($secaptos)) {
                // Encuentra las facturaciones para el dia primero del presente mes
                $dato = Ctdasm::where('fecha', Carbon::createFromDate($year, $month, 1)->toDateString())
                             ->where('diafact', 1)
                             ->first();
                //dd($dato->toArray());
                
                // Si no existen entonces ejecuta la facturacion
                if (is_null($dato)) {
                    // genera facturacion mensual los dias primero de cada mes            
                    $this->call('ctmaster:facturar', ['fecha' => Carbon::create($year, $month, 1)]);                         
                }
                else {
                    $this->line('Facturacion para el dia primero del presente mes ya exite, no es necesario reconstruirla...');
                }
            }        
        }        
        
        /*-------------------------------------------------------------------------------------*/
        /*-- PERIODICAMENTE REVISA SI HAY FACTURACION PAR EL DIA DIECISEIS DEL MES CORRIENTE --*/
        /*-------------------------------------------------------------------------------------*/        
        // Pregunta si la fecha actual esta entre el dia dieciséis y el ultimo dia del mes,
        // incluyento el dia dieciséis y el dia ultimo del mes.
        elseif (Carbon::now()->between(Carbon::create($year, $month, 16), Carbon::today()->endOfMonth())) {
            // Encuentra todas las secciones de apartamentos en las cuales la fecha de facturacion
            // es el dia dieciséis de cada mes.
            $secaptos = Secapto::where('d_registra_cmpc', 16)->get();
            //dd($secaptos->toArray());
            
            if (!is_null($secaptos)) {
                // Encuentra las facturaciones para el dia dieciséis del presente mes
                $dato = Ctdasm::where('fecha', Carbon::createFromDate($year, $month, 16)->toDateString())
                             ->where('diafact', 16)
                             ->first();
               //dd($dato->toArray());
                
                // Si no existen entonces ejecuta la facturacion
                if (is_null($dato)) {
                    // genera facturacion mensual los dias dieciséis de cada mes            
                    $this->call('ctmaster:facturar', ['fecha' => Carbon::create($year, $month, 16)]);                         
                }
                else {
                    $this->line('Facturacion para el dia dieciséis del presente mes ya exite, no es necesario reconstruirla...');
                }
            }        
        } 
        else {
            $this->error('Algo salio mal con la facturacion automatica mensual!');
        }
        $this->info('Finaliza la autofacturacion mensual con exito...!');
    }
}