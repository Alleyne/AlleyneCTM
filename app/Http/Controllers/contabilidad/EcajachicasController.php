<?php namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Ecajachica;
use App\Org;
use App\User;
use Session;

class EcajachicasController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('hasAccess');    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $datos = Ecajachica::all();
      return view('contabilidad.ecajachicas.index')->withDatos($datos);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Encuentra todos los proveedores registrados
        $organizaciones = Org::orderBy('nombre')->pluck('nombre', 'id')->All();
        //dd($proveedores);
        
        //Encuentra todos los usuarios del sistema
        $users = User::orderBy('nombre_completo')->pluck('nombre_completo', 'id')->All();
        //dd($proveedores);
    
        return view('contabilidad.ecajachicas.create')
            ->with('organizaciones', $organizaciones)
            ->with('users', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request->toArray());
        if ($request->afavorde_radios == 1) {
            $this->validate($request, array(
                'fecha' => 'required|date',          
                'org_id' => 'required|Numeric|min:1',
                'no' => 'required|Numeric|min:1',
                'descripcion' => 'required'
                ));
        
        } elseif ($request->afavorde_radios == 1) {
            $this->validate($request, array(
                'fecha' => 'required|date',          
                'user_id' => 'required|Numeric|min:1',
                'no' => 'required|Numeric|min:1',
                'descripcion' => 'required'
                ));
        }

        $ecajachica = new Ecajachica;
        $ecajachica->fecha = $request->fecha;
        
        if ($request->afavorde_radios == 1) {
            $ecajachica->afavorde = $request->org_id;
            $ecajachica->tipovenefis = 1;
        } elseif ($request->afavorde_radios == 2) {
            $ecajachica->afavorde = $request->user_id;
            $ecajachica->tipovenefis = 2;
        }
        
        if ($request->tipodoc_radios == 1) {
            $ecajachica->tipodoc = 1;
            $ecajachica->afavorde = $request->no;        
        } elseif ($request->tipodoc_radios == 2) {
            $ecajachica->tipodoc = 2;
        }
        
        $ecajachica->descripcion = $request->descripcion;
        $ecajachica->etapa = 1;
        $ecajachica->save();

        Session::flash('success', 'Se registrado un nuevo egreso de caja chica!');

        return redirect()->route('ecajachicas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
