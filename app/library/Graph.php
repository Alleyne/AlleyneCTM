<?php namespace App\library;

use App\Ctdasm;
use App\Un;

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

    // ordena de forma descenciente la colleccion
    $uns= $uns->where('deuda', '>', 0)->sortByDesc('deuda');
    //dd($uns->toArray()); 

    if ($uns->count()) {
      foreach ($uns as $un) {
          $ctdasm= Ctdasm::where('un_id', $un->id)->get();
          $importe= $ctdasm->where('pagada', 0)->sum('importe');
          $recargo= $ctdasm->where('recargo_siono', 1)->where('recargo_pagado', 0)->sum('recargo');
          $extra= $ctdasm->where('extra_siono', 1)->where('extra_pagada', 0)->sum('extra');
          
          $data_1[]= $importe;
          $data_2[]= $recargo;
          $data_3[]= $extra;
          
          $propietario= $un->props()->where('encargado', 1)->first();
          $propietario= $propietario->user->nombre_completo; 
          $categorias[]= $propietario.' '.$un->codigo;
      }
      //dd($uns->toArray()); 
      
      // formatea los arrays
      $data_1 = implode(", ", $data_1);
      $data_2 = implode(", ", $data_2);
      $data_3 = implode(", ", $data_3);
      $categorias = '"'.implode('", "', $categorias).'"'; 
    
    } else {
      $data_1 = Null;
      $data_2 = Null;
      $data_3 = Null;
      $categorias = Null; 
    }

    return ['data_1' => $data_1,
            'data_2' => $data_2,
            'data_3' => $data_3,
            'categorias' => $categorias];

  } // end function
} //fin de Class Graph