<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Concilia;
use App\Dte_concilia;

class ConciliasController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // encuentra todas las conciliaciones
    $datos = Concilia::all();
        
    return view('contabilidad.concilias.index')
                ->with('datos', $datos); 
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($periodo_id)
  {
    //dd($periodo_id);
    
    // encuentra todas las conciliaciones
    $concilia = Concilia::where('periodo_id', $periodo_id)->first();
    
    // encuentra todas la notas de credito
    $ncs = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'n/c')->get();
    
    // encuentra todas la notas de credito
    $aj_lmas = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'aj_lmas')->get();    

    // encuentra todas la notas de credito
    $nds = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'n/d')->get();

    // encuentra todas la notas de credito
    $aj_lmenos = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'aj_lmenos')->get();    
    
    // encuentra todas la notas de credito
    $d_transitos = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'd_transito')->get();

    // encuentra todas la notas de credito
    $chq_circulacions = Dte_concilia::where('concilia_id', $concilia->id)->where('tipo', 'chq_circulacion')->get();
    
    // calcula el total en libro mas
    $t_libromas = Dte_concilia::where('concilia_id', $concilia->id)->where('seccion', 'libro')->where('masmenos', 'mas')->sum('monto');    

    // calcula el total en libro menos
    $t_libromenos = Dte_concilia::where('concilia_id', $concilia->id)->where('seccion', 'libro')->where('masmenos', 'menos')->sum('monto');    
 
    

    //dd($concilia, $ncs, $nds, $aj_lmas, $aj_lmenos, );    
    
    return view('contabilidad.concilias.show')
                ->with('ncs', $ncs)
                ->with('aj_lmas', $aj_lmas)
                ->with('nds', $nds)
                ->with('aj_lmenos', $aj_lmenos)
                ->with('d_transitos', $d_transitos)
                ->with('chq_circulacions', $chq_circulacions)
                ->with('t_libromas', $t_libromas)
                ->with('t_libromenos', $t_libromenos)
                ->with('concilia', $concilia); 
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}
