<?php namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Ctdasm;
use App\Pcontable;

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
    protected $description = 'Registra recargo por pagos atrasados en cuota por servicio de mantenimiento.';

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
/*        // inicializa variable para almacenar el total de recargos
        $totalRecargos= 0; 

        // encuentra todas aquella unidades que no han sido pagadas y que tienen fecha de pago vencida
        $datos = Ctdasm::where('f_vencimiento', '<', Carbon::today())
                    ->where('pagada', 0)
                    ->get();
        
        // si encuentra alguna la penaliza con recargo.        
        $i=1;
        foreach ($datos as $dato) {
            $dato = Ctdasm::find($dato->id);
            $dato->recargo_siono = 1;
            $dato->save();  
            
            // acumula el total de recargos
            $totalRecargos = $totalRecargos + $dato->recargo;
            
            // encuentra el periodo contable actual
            $periodo= Pcontable::all()->last()->id;
     
            Sity::registraEnCuentas(
                                $periodo,
                                'mas',
                                1,
                                2, //'1130.00',
                                Carbon::today(),
                                'Recargo por cobrar en cuota de mantenimiento de la unidad '.$dato->ocobro,
                                $dato->recargo,
                                $dato->un_id
                               );

            // registra 'Ingreso por cuota de mantenimiento' 4130.00
            Sity::registraEnCuentas(
                                $periodo,
                                'mas',
                                4,
                                4, //'4130.00',
                                Carbon::today(),
                                '   Ingreso por recargo en cuota de mantenimiento de la unidad '.$dato->ocobro,
                                $dato->recargo,
                                $dato->un_id
                               );
            
            // registra resumen de la facturacion mensual en Ctdiario principal 
             if ($i==1) {
                // registra en Ctdiario principal
                $dato = new Ctdiario;
                $dato->pcontable_id  = $periodo_id;
                $dato->fecha   = Carbon::today();
                $dato->detalle = Catalogo::find(2)->nombre.'unidad '.$ocobro;
                $dato->debito  = $dato->recargo;
                $dato->save(); 
          
            } else {
                // registra en Ctdiario principal
                $dato = new Ctdiario;
                $dato->pcontable_id  = $periodo_id;
                $dato->detalle = Catalogo::find(2)->nombre.'unidad '.$ocobro;
                $dato->debito  = $dato->recargo;
                $dato->save(); 
            }
            $i++;
        }
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo_id;
        $dato->detalle = '   '.Catalogo::find(4)->nombre;
        $dato->credito  = $totalRecargos;
        $dato->save(); 
        
        // registra en Ctdiario principal
        $dato = new Ctdiario;
        $dato->pcontable_id  = $periodo_id;
        $dato->detalle = 'Para registrar resumen de recargos por cobrar en cuotas de mantenimiento';
        $dato->save(); 
    }*/
}