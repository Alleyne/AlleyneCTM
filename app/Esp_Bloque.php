<?php namespace App;
use Illuminate\Database\Eloquent\Model;

class Bloque extends Model 
{
	protected $table = 'bloques';
  public $timestamps = true;    
  
  public function secciones()
  {
	 return $this->hasMany('App\Seccione');    
  }
  
  public function blqadmins()
  {
	 return $this->hasMany('App\blqadmin');    
  }

  public function jd() {
      return $this->belongsTo('App\Jd');
  }


  /*
  * When building JSON APIs, you will often need to convert your models and relationships to arrays or JSON.
  * Eloquent includes convenient methods for making these conversions, as well as controlling which attributes
  * are included in your serializations.
  */
  public function getjdAttribute()
  {
      //$jd =Jd::find($this->attributes['jd_id']);
      $jd =Jd::where('id', $this->attributes['jd_id'])->select('nombre','descripcion','imagen_L')->first();
      //dd($jd->toArray());
      return $jd;
  }

  /*  cada vez que se llame al modelo Bloque automaticamente incorpora los datos del modelo Jd
      array:2 [▼
        0 => array:8 [▼
          "id" => 3
          "nombre" => "Bloque Torre 100"
          "descripcion" => "Descripción del Bloque Torre 100"
          "imagen_L" => "assets/img/bloques/bloq_L3.jpg"
          "imagen_M" => ""
          "imagen_S" => ""
          "jd_id" => 1
          "jd" => array:3 [▼
            "nombre" => "Junta Directiva Ph El Marquez"
            "descripcion" => "Descripcion de la Junta Directiva Ph El Marquezz"
            "imagen_L" => "assets/img/jds/jd_1.jpg"
          ]
        ]
        1 => array:8 [▼
          "id" => 4
          "nombre" => "Bloque Torre 200"
          "descripcion" => "Descripción del Bloque Torre 200"
          "imagen_L" => "assets/img/bloques/bloq_L4.jpg"
          "imagen_M" => ""
          "imagen_S" => ""
          "jd_id" => 1
          "jd" => array:3 [▼
            "nombre" => "Junta Directiva Ph El Marquez"
            "descripcion" => "Descripcion de la Junta Directiva Ph El Marquezz"
            "imagen_L" => "assets/img/jds/jd_1.jpg"
          ]
        ]
      ]*/


  /**
   * The accessors to append to the model's array form.
  */
  protected $appends = ['jd'];

}