<?php namespace App\Http\Controllers\contabilidad;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Evento;

class EventosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(); //declaramos un array principal que va contener los datos
        
        $id = Evento::all()->pluck('id'); //listamos todos los id de los eventos
        $titulo = Evento::all()->pluck('titulo'); //lo mismo para lugar y fecha
        $fechaIni = Evento::all()->pluck('fechaIni');
        $fechaFin = Evento::all()->pluck('fechaFin');
        $allDay = Evento::all()->pluck('todoeldia');
        $background = Evento::all()->pluck('color');
        $count = count($id); //contamos los ids obtenidos para saber el numero exacto de eventos
 
        //hacemos un ciclo para anidar los valores obtenidos a nuestro array principal $data
        for($i = 0; $i < $count; $i++){
            $data[$i] = array(
                "title" => $titulo[$i], //obligatoriamente "title", "start" y "url" son campos requeridos
                "start" => $fechaIni[$i], //por el plugin asi que asignamos a cada uno el valor correspondiente
                "end" => $fechaFin[$i],
                "allDay" => $allDay[$i],
                "backgroundColor" => $background[$i],
                "borderColor" => $background[$i],
                "id" => $id[$i]
                //"url"=>"cargaEventos".$id[$i]
                //en el campo "url" concatenamos el el URL con el id del evento para luego
                //en el evento onclick de JS hacer referencia a este y usar el método show
                //para mostrar los datos completos de un evento
            );
        }
 
        json_encode($data); //convertimos el array principal $data a un objeto Json 
        return $data; //para luego retornarlo y estar listo para consumirlo       
       
       //return view('contabilidad.eventos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Valores recibidos via ajax
        $title = $_POST['title'];
        $start = $_POST['start'];
        $back = $_POST['background'];

        //Insertando evento a base de datos
        $evento = new Evento;
        $evento->fechaIni = $start;
        //$evento->fechaFin = $end;
        $evento->todoeldia = true;
        $evento->color = $back;
        $evento->titulo = title;

        $evento->save();
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
        //Valores recibidos via ajax
        $id = $_POST['id'];
        $title = $_POST['title'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $allDay = $_POST['allday'];
        $back = $_POST['background'];

        $evento = Evento::find($id);
        if($end == 'NULL'){
            $evento->fechaFin = NULL;
        }else{
            $evento->fechaFin = $end;
        }
        $evento->fechaIni = $start;
        $evento->todoeldia = $allDay;
        $evento->color = $back;
        $evento->titulo = $title;
        //$evento->fechaFin = $end;

        $evento->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Valor id recibidos via ajax
        $id = $_POST['id'];

        Evento::destroy($id);
    }
}
