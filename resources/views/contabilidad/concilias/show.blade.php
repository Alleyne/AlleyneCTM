<!DOCTYPE html>
<html lang="en">
<head>
  <title>Conciliacion bancaria</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">
</head>
<body style="font-size:15px;"">
@include('templates.backend._partials.flash_messages')

<div class="container" style="width:8.5in; background-color:white";>
  <h4 class="text-center">PH El Marquez</h4>
  <p class="text-center" style="margin:0px">Conciliacion Bancaria- Banco Nacional</p>
  <p class="text-center" style="margin:0px">31 de Agosto de 2017</p>
  <p class="text-center" style="margin:0px">(en balboas)</p>

	<div class="row hidden-print" style="margin-top:0px; background-color:white;">
	  <div class="col-xs-6"></div>
	  <div class="col-xs-6 text-right"><strong><a href="#" class="hidden-print"><span class="glyphicon glyphicon-plus text-success"  data-target="#Modal_AddDetalleConciliacion" data-toggle="modal"></span></a></strong></div>
	</div>	

	<!-- Seccion del libro de Banco -->
	<h4><b><i>Informacion en libro</i></b></h4>
	
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en libro al 31 de julio de 2017</div>
		  <div class="col-xs-6 text-right"><strong>{{ $concilia->saldo_libro }}</strong></div>
		</div>

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MAS</strong></div>
		</div>

		<div class="row">
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp; <strong>Depositos del mes</strong></div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right">8,320.00</div>	
		  <div class="col-xs-2"></div>	
		</div>
		
		<div style="background-color:lavender;">
			<div class="row">	
			  <!-- <div class="col-xs-6">&nbsp;&nbsp;<a href="#" class="hidden-print"><span class="glyphicon glyphicon-plus text-success" aria-hidden="true"></span></a> Cheques en circulacion</div> -->
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Notas de credito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($ncs as $nc)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $nc->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Nota de credito',
		          'data-message' => 'Esta seguro(a) que desea eliminar la presente Nota de credito?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $nc->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ $nc->monto }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach
			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total notas de credito</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $ncs->sum('monto') }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		</div>

		<div style="background-color:lavender;">
			<div class="row" style="margin-top:5px;">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Ajustes por error</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($aj_lmas as $aj_lma)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $aj_lma->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Ajuste por error',
		          'data-message' => 'Esta seguro(a) que desea eliminar el presene ajuste por error?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $aj_lma->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ $aj_lma->monto }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $aj_lmas->sum('monto') }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $t_libromas }}</div>	
			</div>
		</div>
		
		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MENOS</strong></div>
		</div>
		
		<div class="row">
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp; <strong>Cheques girados del mes</strong></div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right">8,320.00</div>	
		  <div class="col-xs-2"></div>	
		</div>	
		
		<div style="background-color:lavender;">	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Notas de debito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($nds as $nd)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $nd->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Nota de credito',
		          'data-message' => 'Esta seguro(a) que desea eliminar la presente Nota de credito?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $nd->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ $nd->monto }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	
			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total notas de debito</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $nds->sum('monto') }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		</div>	
		
		<div style="background-color:lavender;">
			<div class="row" style="margin-top:5px;">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Ajustes por error</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($aj_lmenos as $aj_lmeno)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $aj_lmeno->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Nota de credito',
		          'data-message' => 'Esta seguro(a) que desea eliminar la presente Nota de credito?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $aj_lmeno->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ $aj_lmeno->monto }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $aj_lmenos->sum('monto') }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $t_libromenos }}</div>	
			</div>
		</div>

		<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo conciliado en libro al 31 de agosto 2017</div>
		  <div class="col-xs-6 text-right"><strong>8,320.00</strong></div>
		</div>
	</div>

	<br>
	
	<!-- Seccion del Banco -->
	<h4><i><b>Informacion en banco</i></b></h4>
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px;background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en banco al 31 de agosto de 2017</div>
		  <div class="col-xs-6 text-right"><strong>{{ $concilia->saldo_banco }}</strong></div>
		</div>

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MAS</strong></div>
		</div>

		<div style="background-color:lavender;">
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Depositos en transito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($d_transitos as $d_transito)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $d_transito->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Nota de credito',
		          'data-message' => 'Esta seguro(a) que desea eliminar la presente Nota de credito?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $d_transito->detalle }}
 			    </div>

			  <div class="col-xs-2"></div>	
		  	<div class="col-xs-2 text-right">{{ $d_transito->monto }}</div>	
			  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total depositos en transito</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
		  	<div class="col-xs-2 text-right">{{ $d_transitos->sum('monto') }}</div>	
			</div>
		</div>

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MENOS</strong></div>
		</div>
		
		<div style="background-color:lavender;">	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Cheques en circulacion</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($chq_circulacions as $chq_circulacion)
				<div class="row">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $chq_circulacion->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar Nota de credito',
		          'data-message' => 'Esta seguro(a) que desea eliminar la presente Nota de credito?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $chq_circulacion->detalle }}
 			    </div>

			  <div class="col-xs-2"></div>	
		  	<div class="col-xs-2 text-right">{{ $chq_circulacion->monto }}</div>	
			  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total de cheques en circulacion</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ $chq_circulacions->sum('monto') }}</div>	
			</div>
		</div>	
		
		<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo conciliado en banco al 31 de agosto 2017</div>
		  <div class="col-xs-6 text-right"><strong>8,320.00</strong></div>
		</div>
	</div>

	<!-- <div class="row" style="margin-top:5px;">
	  <div class="col-xs-6" style="background-color:lavenderblush;">Saldo conciliado en libro al 31 de agosto 2017</div>
	  <div class="col-xs-6 text-right"><strong>8,320.00</strong></div>
	</div> -->
	<div class="row" style="margin-top:10px; margin-bottom:35px;">
	  <div class="col-xs-6"></div>
	  <div class="col-xs-6 text-right"><a href="#" class="btn btn-warning btn-sm hidden-print"><i class="fa fa-search"></i> Contabilizar conciliacion</a></div>
	</div>
	
	@include('templates.backend._partials.modal_confirm')
	@include('contabilidad.concilias.Modal_AddDetalleConciliacion')

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