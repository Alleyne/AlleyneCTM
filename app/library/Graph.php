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
} //fin de Class Graph