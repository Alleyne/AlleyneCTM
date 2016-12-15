<?php namespace App\library;

use App\Ctdasm;
use App\Un;
use App\Pcontable;
use App\Ctmayore;
use App\Catalogo;

class Graph {

  /** 
  *==================================================================================================
  * Procesa la data necesaria para desplegar la grafica de propietarios mosoros
  * @return void
  **************************************************************************************************/
  public static function getDataGraphMorosos() {
   
    $ctdasm= Ctdasm::All();
    $uns= Un::where('activa', 1)->get();

    // agrega a la colleccion $uns un nuevo elemento llamado "deuda" el cual almacena el total de la deuda por unidad
    $i=0;
    foreach ($uns as $un) {
      $ctdasm= Ctdasm::where('un_id', $un->id)->get();
      $importe= $ctdasm->where('pagada', 0)->sum('importe');
      $recargo= $ctdasm->where('recargo_siono', 1)->where('recargo_pagado', 0)->sum('recargo');
      $extra= $ctdasm->where('extra_siono', 1)->where('extra_pagada', 0)->sum('extra');
      
      $uns[$i]["deuda"] = $importe + $recargo + $extra;
      $i++;
    }

    // calcula el total adeudado
    $totalAdeudado= $uns->where('deuda', '>', 0)->sum('deuda');
    
    // ordena de forma descenciente la colleccion
    $uns= $uns->where('deuda', '>', 0)->sortByDesc('deuda');
    //dd($uns->toArray()); 

    if ($uns->count()) {
      foreach ($uns as $un) {
        $ctdasm= Ctdasm::where('un_id', $un->id)->get();
        $_ctaRegular= $ctdasm->where('pagada', 0)->sum('importe');
        $_ctaRecargo= $ctdasm->where('recargo_siono', 1)->where('recargo_pagado', 0)->sum('recargo');
        $_ctaExtra= $ctdasm->where('extra_siono', 1)->where('extra_pagada', 0)->sum('extra');
        
        $ctaRegular[]= $_ctaRegular;
        $ctaRecargo[]= $_ctaRecargo;
        $ctaExtra[]= $_ctaExtra;
        
        $propietario= $un->props()->where('encargado', 1)->first();
        $propietario= $propietario->user->nombre_completo; 
        $categorias[]= $propietario.' '.$un->codigo;
      }
      //dd($uns->toArray()); 
      
      // formatea los arrays
      $ctaRegular = implode(", ", $ctaRegular);
      $recargo = implode(", ", $ctaRecargo);
      $ctaExtra = implode(", ", $ctaExtra);
      $categorias = '"'.implode('", "', $categorias).'"'; 
    
    } else {
      $ctaRegular = Null;
      $recargo = Null;
      $ctaExtra = Null;
      $categorias = Null; 
    }

    return ['ctaRegular' => $ctaRegular,
            'recargo' => $recargo,
            'ctaExtra' => $ctaExtra,
            'categorias' => $categorias,
            'totalAdeudado' => $totalAdeudado];

  } // end function

  /** 
  *==================================================================================================
  * Procesa la data necesaria para desplegar la grafica de Gastos por periodo
  * @return $series string
  **************************************************************************************************/
  public static function getDataGraphGastos() {
    // encuentra todas las cuentas de gastos existentes en el catalogo
    $cuentasGastos= Catalogo::where('tipo', 6)->get();      
    
    // obtiene los doce ultimos periodos contables registrados
    $periodos= Pcontable::orderBy('id', 'desc')->take(12)->get();
    $periodos= $periodos->sortBy('id');
    //dd($periodos->toArray());      
    
    $series= "";

    foreach ($cuentasGastos as $cuenta) {
      // almacena el nombre de la cuenta de gastos
      $name= Catalogo::find($cuenta->id)->nombre;
      //dd($name);
      
      $data="";
      
      foreach ($periodos as $periodo) {
        // calcula el total gastado de la cuenta en el periodo        
        $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('cuenta', $cuenta->id)->get();
        $total= $ctmayores->sum('debito') - $ctmayores->sum('credito');
        //dd($totales);     
        $data= $data.$total.',';
      }

      $data= rtrim($data, ',');       
      //dd($data);      
    
      $series= $series.'{name: "'.$name.'", data: ['.$data.']},';
    }
    $series= rtrim($series, ',');    
    //dd($series);        
    
    return $series;
  
  } // end function


  /** 
  *==================================================================================================
  * Procesa la data necesaria para desplegar la grafica de Gastos totales por periodo
  * @return $series string
  **************************************************************************************************/
  public static function getDataGraphGastosTotales() {
   
    // obtiene los doce ultimos periodos contables registrados
    $periodos= Pcontable::orderBy('id', 'desc')->take(12)->get();
    $periodos= $periodos->sortBy('id');
    //dd($periodos->toArray());      
    
    $data=""; 
    $series= "";
    foreach ($periodos as $periodo) {
      // calcula el total gastado de la cuenta en el periodo        
      $ctmayores= Ctmayore::where('pcontable_id', $periodo->id)->where('tipo', 6)->get();
      $total= $ctmayores->sum('debito') - $ctmayores->sum('credito');
      //dd($totales);     
      $data= $data.$total.',';
    }

    $data= rtrim($data, ',');       
    //dd($data);      
   
    $series= $series.'{name: "Totales por periodo", data: ['.$data.']},';
    $series= rtrim($series, ',');    
    return $series;
  
  } // end function


} //fin de Class Graph