<!DOCTYPE html>
<html lang="en">
<head>
  <title>Arqueo de Caja Chica</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">
</head>
<body style="font-size:15px;"">
@include('templates.backend._partials.flash_messages')

<div class="container" style="width:8.5in; background-color:white";>
  <h4 class="text-center">PH El Marquez</h4>
  <p class="text-center" style="margin:0px">Arqueo de Caja Chica</p>
  <p class="text-center" style="margin:0px">31 de Agosto de 2017</p>
  <p class="text-center" style="margin:0px">(en balboas)</p>

	<div class="row hidden-print" style="margin-top:0px; background-color:white;">
	  <div class="col-xs-6"><a href="{{ url(Cache::get('goto_pcontables_index')) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a></div>
	</div>	
	
	<div style="background-color:white";>
		<div class="row" style="margin-top:20px;">
		  <div class="col-xs-8">Fondo de Caja chica</div>
		  <div class="col-xs-2 text-right"></div>
		  <div class="col-xs-2 text-right">{{ $cchica->saldo + $t_desembolso }}</div>
		</div>
		
		<div class="row" style="margin-top:0px;">
	  	<div class="col-xs-8"><a href="#" class="hidden-print"><span class="glyphicon glyphicon-pencil text-warning" data-target="#Modal_editarEfectivoEnCaja" data-toggle="modal"></span></a> Efectivo en caja</div>
		  <div class="col-xs-2 text-right">{{ $cchica->saldo }}</div>
		  <div class="col-xs-2 text-right"></div>
		</div>

		<div class="row" style="margin-top:0px;">
		  <div class="col-xs-8">Desembolsos realizados</div>
		  <div class="col-xs-2 text-right">{{ $t_desembolso }}</div>
		  <div class="col-xs-2 text-right"></div>
		</div>

		<div class="row" style="margin-top:0px;">
		  <div class="col-xs-8">Sub total</div>
		  <div class="col-xs-2 text-right"></div>
		  <div class="col-xs-2 text-right">{{ $cchica->saldo + $t_desembolso }}</div>
		</div>

		<div class="row" style="margin-top:0px;">
		  <div class="col-xs-8"><strong>Sobrante en efectivo</strong></div>
		  <div class="col-xs-2 text-right"></div>
		  <div class="col-xs-2 text-right"><strong>15.01</strong></div>
		</div>
	</div>	
	
	@include('templates.backend._partials.modal_confirm')
	@include('contabilidad.desembolsos.Modal_editarEfectivoEnCaja')

</div> <!-- end container -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>

<script type="text/javascript">

    $("#libro-mas-1").click(function(){
      $(".DteLibroMas").show();
      $(".DteLibroMenos").hide();    
      $(".DteBancoMas").hide();
      $(".DteBancoMenos").hide();   
    });

    $("#libro-menos-2").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").show();    
      $(".DteBancoMas").hide();
      $(".DteBancoMenos").hide();   
    });

    $("#banco-mas-1").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").hide();
      $(".DteBancoMas").show();
      $(".DteBancoMenos").hide();    
    });

    $("#banco-menos-2").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").hide();
      $(".DteBancoMas").hide();
      $(".DteBancoMenos").show();    
    });

</script>	

</body>
</html>