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
                Sity::penalizarTipo3(Carbon::today());
                DB::commit();                
                $this->info('Penalizacion diaria culminada...!'); 
            } 

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('warning', ' Ocurrio un error en el Command penalizar.php, la transaccion ha sido cancelada!');
        }
    }
}