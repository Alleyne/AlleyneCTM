<!DOCTYPE html>
<html lang="en">
<head>
  <title>Conciliacion bancaria</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="{{ URL::asset('assets/backend/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" media="screen">
</head>
<body style="font-size:14px;"">
@include('templates.backend._partials.flash_messages')

<div class="container" style="width:8.5in; background-color:white";>
  <h4 class="text-center">PH El Marquez</h4>
  <p class="text-center" style="margin:0px">Conciliacion Bancaria- Banco Nacional</p>
  <p class="text-center" style="margin:0px">31 de Agosto de 2017</p>
  <p class="text-center" style="margin:0px">(en balboas)</p>

	<div class="row hidden-print" style="margin-top:0px; background-color:white;">
	  <div class="col-xs-6"><a href="{{ url(Cache::get('goto_pcontables_index')) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a></div>
	  <div class="col-xs-6 text-right"><a href="#" class="hidden-print"><span class="glyphicon glyphicon-plus text-success" data-target="#Modal_AddDetalleConciliacion" data-toggle="modal"><strong> Agregar</strong></span></a></div>
	</div>	

	<!-- Seccion del libro de Banco -->
	<h4><b><i>Informacion en libro</i></b></h4>
	
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en libro al 31 de julio de 2017</div>
		  <div class="col-xs-6 text-right"><strong>{{ number_format(floatval($concilia->saldo_libro),2) }}</strong></div>
		</div>

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MAS</strong></div>
		</div>
		
		<div style="background-color:lavender;">
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;<strong>Depositos del mes</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_depositado),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>

			<div class="row">	
			  <!-- <div class="col-xs-6">&nbsp;&nbsp;<a href="#" class="hidden-print"><span class="glyphicon glyphicon-plus text-success" aria-hidden="true"></span></a> Cheques en circulacion</div> -->
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Notas de credito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($ncs as $nc)
				<div class="row" style="margin-top:2px;">
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

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($nc->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach
			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total notas de credito</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($ncs->sum('monto')),2) }}</div>	
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
				<div class="row" style="margin-top:2px;">
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

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($aj_lma->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lmas->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_depositado + $ncs->sum('monto') + $aj_lmas->sum('monto')),2) }}</div>	
			</div>
		</div>
		
		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MENOS</strong></div>
		</div>
		
		<div style="background-color:lavender;">			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;<strong>Cheques girados del mes</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_chq_girados),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>	
		
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Notas de debito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($nds as $nd)
				<div class="row" style="margin-top:2px;">
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

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($nd->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	
			
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total notas de debito</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($nds->sum('monto')),2) }}</div>	
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
				<div class="row" style="margin-top:2px;">
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

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($aj_lmeno->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right" style="border-style: hidden hidden solid hidden;">{{ number_format(floatval($aj_lmenos->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_chq_girados +  $nds->sum('monto') +  $aj_lmenos->sum('monto')),2) }}</div>	
			</div>
		</div>

		<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Saldo conciliado en libro al 31 de agosto 2017</div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($concilia->saldo_libro + ($t_depositado + $ncs->sum('monto') + $aj_lmas->sum('monto'))+ ($t_chq_girados +  $nds->sum('monto') +  $aj_lmenos->sum('monto'))),2) }}</strong></div>
		</div>

	<br>
	
	<!-- Seccion del Banco -->
	<h4><i><b>Informacion en banco</i></b></h4>
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px;background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en banco al 31 de agosto de 2017</div>
		  <div class="col-xs-6 text-right"><strong>{{ number_format(floatval($concilia->saldo_banco),2) }}</strong></div>
		</div>

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MAS</strong></div>
		</div>

		<div style="background-color:lavenderblush;">
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Depositos en tránsito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($d_transitos as $d_transito)
				<div class="row" style="margin-top:2px;">
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

		  	<div class="col-xs-2 text-right">{{ number_format(floatval($d_transito->monto),2) }}</div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total depositos en tránsito</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
		  	<div class="col-xs-2 text-right">{{ number_format(floatval($d_transitos->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		</div>

		<div style="background-color:lavenderblush;">
			<div class="row" style="margin-top:5px;">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Ajustes por error</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($aj_bmas as $aj_bma)
				<div class="row" style="margin-top:2px;">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $aj_bma->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar ajuste por aumento',
		          'data-message' => 'Esta seguro(a) que desea eliminar el presente ajuste?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $aj_bma->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($aj_bma->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_bmas->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($d_transitos->sum('monto') + $aj_bmas->sum('monto')),2) }}</div>	
			</div>
		</div>		

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MENOS</strong></div>
		</div>
		
		<div style="background-color:lavenderblush;">	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Cheques en tránsito</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>
			
			@foreach ($chq_circulacions as $chq_circulacion)
				<div class="row" style="margin-top:2px;">
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
		  	
		  	<div class="col-xs-2 text-right">{{ number_format(floatval($chq_circulacion->monto),2) }}</div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	
	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total de cheques en tránsito</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($chq_circulacions->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		</div>	
		
		<div style="background-color:lavenderblush;">
			<div class="row" style="margin-top:5px;">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Ajustes por error</strong></div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			</div>

			@foreach ($aj_bmenos as $aj_bmeno)
				<div class="row" style="margin-top:2px;">
			    <div class="col-xs-6 form-actions">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        {{Form::open(array(
		          'route' => array('dte_concilias.destroy', $aj_bmeno->id),
		          'method' => 'DELETE',
		          'style' => 'display:inline'
		        ))}}
		        
		        {{Form::button('<i class="fa fa-times"></i>', array(
		          'class' => 'btn btn-danger btn-xs hidden-print',
		          'data-toggle' => 'modal',
		          'data-target' => '#confirmAction',
		          'data-title' => 'Eliminar ajuste por aumento',
		          'data-message' => 'Esta seguro(a) que desea eliminar el presente ajuste?',
		          'data-btntxt' => 'SI, eliminar',
		          'data-btncolor' => 'btn-danger'
		        ))}}
		        {{Form::close()}}  
 			    	{{ $aj_bmeno->detalle }}
 			    </div>

				  <div class="col-xs-2 text-right"> {{ number_format(floatval($aj_bmeno->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total ajustes</div>
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right" style="border-style: hidden hidden solid hidden;">{{ number_format(floatval($aj_bmenos->sum('monto')),2) }}</div>	
			  <div class="col-xs-2"></div>	
			</div>
		
			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($chq_circulacions->sum('monto') + $aj_bmenos->sum('monto')),2) }}</div>	
			</div>
		</div>			

		
		<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Saldo conciliado en banco al 31 de agosto 2017</div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($concilia->saldo_banco + ($d_transitos->sum('monto') + $aj_bmas->sum('monto')) + ($chq_circulacions->sum('monto') + $aj_bmenos->sum('monto'))),2) }}</strong></div>
		</div>
	</div>

	<!-- <div class="row" style="margin-top:5px;">
	  <div class="col-xs-6" style="background-color:lavenderblush;">Saldo conciliado en libro al 31 de agosto 2017</div>
	  <div class="col-xs-6 text-right"><strong>8,320.00</strong></div>
	</div> -->
	@if (($concilia->saldo_libro + ($t_depositado + $ncs->sum('monto') + $aj_lmas->sum('monto'))+ ($t_chq_girados +  $nds->sum('monto') +  $aj_lmenos->sum('monto'))) == ($concilia->saldo_banco + ($d_transitos->sum('monto') + $aj_bmas->sum('monto')) + ($chq_circulacions->sum('monto') + $aj_bmenos->sum('monto'))))
		<div class="row" style="margin-top:10px; margin-bottom:35px;">
		  <div class="col-xs-6"></div>
		  <div class="col-xs-6 text-right"><a href="{{ URL::route('contabilizaConcilia', [$concilia->id, $concilia->periodo_id]) }}" class="btn btn-warning btn-sm hidden-print"><i class="fa fa-search"></i> Contabilizar conciliacion</a></div>
		</div>
	@endif
	
	<hr style="margin-top:60px">
	<h4><strong>Diario de conciliacion proyectado</strong></h4>
	<div class="row" style="background-color:rgb(200,200,200);">
	  <div class="col-xs-2"><strong>Fecha</strong></div>
	  <div class="col-xs-6"><strong>Descripcion</strong></div>	
	  <div class="col-xs-2 text-right"><strong>Debito</strong></div>	
	  <div class="col-xs-2 text-right"><strong>Credito</strong></div>	
	</div>

	@if (!$ncs->isEmpty())
		<div class="row" style="margin-top:10px;">	
		  <div class="col-xs-2"><strong>31/01/2017</strong></div>
		  <div class="col-xs-6">Banco</div>	
		  <div class="col-xs-2 text-right">{{ number_format(floatval($ncs->sum('monto')),2) }}</div>	
		  <div class="col-xs-2"></div>
		</div>

		@foreach ($ncs as $nc)		
			<div class="row">	
			  <div class="col-xs-2"></div>
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;{{ $nc->detalle }}</div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($nc->monto),2) }}</div>
			</div>
		@endforeach		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6"><em>Para registrar Notas de credito del mes</em></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>
		</div>
	@endif

	@if (!$aj_lmas->isEmpty())
		<div class="row" style="margin-top:10px;">	
		  <div class="col-xs-2"><strong>31/01/2017</strong></div>
		  <div class="col-xs-6">Banco</div>	
		  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lmas->sum('monto')),2) }}</div>	
		  <div class="col-xs-2"></div>
		</div>

		@foreach ($aj_lmas as $aj_lma)
			<div class="row">	
			  <div class="col-xs-2"></div>
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;{{ $aj_lma->detalle }}</div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lma->monto),2) }}</div>
			</div>
		@endforeach		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6"><em>Para corregir errores</em></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>
		</div>	
	@endif
	
	<?php $i = 0; ?>
	@if (!$nds->isEmpty())
		@foreach ($nds as $nd)
			@if ($i == 0)
				<div class="row" style="margin-top:10px;">	
				  <div class="col-xs-2"><strong>31/01/2017</strong></div>
				  <div class="col-xs-6">{{ $nd->detalle }}</div>	
				  <div class="col-xs-2 text-right">{{ number_format(floatval($nd->monto),2) }}</div>	
				  <div class="col-xs-2"></div>
				</div>
			<?php $i = 1; ?>	
			@else
				<div class="row">	
				  <div class="col-xs-2"><strong></strong></div>
				  <div class="col-xs-6">{{ $nd->detalle }}</div>	
				  <div class="col-xs-2 text-right">{{ number_format(floatval($nd->monto),2) }}</div>	
				  <div class="col-xs-2"></div>
				</div>
			@endif
		@endforeach		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Banco</div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right">{{ number_format(floatval($nds->sum('monto')),2) }}</div>
		</div>		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6"><em>Para registrar Nota de debito</em></div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>
		</div>	
	@endif

	<?php $i = 0; ?>
	@if (!$aj_lmenos->isEmpty())
		@foreach ($aj_lmenos as $aj_lmeno)
			@if ($i == 0)
			<div class="row" style="margin-top:10px;">	
			  <div class="col-xs-2"><strong>31/01/2017</strong></div>
			  <div class="col-xs-6">{{ $aj_lmeno->detalle }}</div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lmeno->monto),2) }}</div>	
			  <div class="col-xs-2"></div>
			</div>
			<?php $i = 1; ?>
			@else
				<div class="row">	
				  <div class="col-xs-2"><</div>
				  <div class="col-xs-6">{{ $aj_lmeno->detalle }}</div>	
				  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lmeno->monto),2) }}</div>	
				  <div class="col-xs-2"></div>
				</div>
			@endif
		@endforeach		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Banco</div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right">{{ number_format(floatval($aj_lmas->sum('monto')),2) }}</div>
		</div>		
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6"><em>Para corregir errores</em></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>
		</div>	
		
		<div class="row">	
		  <div class="col-xs-2"></div>
		  <div class="col-xs-6"></div>	
		  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($ncs->sum('monto') + $aj_lmas->sum('monto') + $nds->sum('monto') + $aj_lmenos->sum('monto')),2) }}</strong></div>
		  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($ncs->sum('monto') + $aj_lmas->sum('monto') + $nds->sum('monto') + $aj_lmenos->sum('monto')),2) }}</strong></div>
		</div>		
	@endif

	<hr>

	<div class="row hidden-print" style="margin-top:0px; background-color:white;">
	  <div class="col-xs-12">
	  	<h3>Glosario de teminos contables:</h3>
	  	<dl>
  			<dt style="margin-top:9px;">Depositos del mes</dt>
  			<dd>Los conforman el efectivo que recibe la organizacion diariamente por concepto de cobro por servicio de mantenimiento, recargos, etc, menos aquellos gastos o pagos que no fueron girados en cheques.</dd>
  			<dt style="margin-top:9px;">Cheques girados en el mes</dt>
  			<dd>Se consideran los cheques emitidos por la organizacion en concepto de compras al contado, pagos a terceros o abonos.</dd>
  			<dt style="margin-top:9px;">Nota de crédito</dt>
  			<dd>Se llama así al documento que envía el banco a la organización para aumentar el efectivo bajo su custodia, por transacciones como: intereses ganados, documentos por cobrar, cuentas por cobrar, cobro de facturas, remesas de tarjetas debito/credito, diferencias en depósitos, préstamos solicitados, etc.</dd>
  			<dt style="margin-top:9px;">Nota de debito</dt>
  			<dd>Documento que envía el banco a la organización en concepto de disminución del efectivo bajo su custodia, por operaciones tales como: intereses pagoas, pago de documentos, cheques devueltos, cargos bancarios, diferencia en depósitos, cargos por tarjeta debito/credito, documentos por pagar, pago de obligaciones, etc.</dd>
  			<dt style="margin-top:9px;">Depósitos en tránsito</dt>
  			<dd>Son las cantidades que ya han sido registradas en los libros de la organizacion, pero aún no están incluidos en el estado de cuentas del banco. Por lo tanto, es necesario incluirlos en la conciliación bancaria como un incremento al saldo del banco, de tal forma que se reporte la cantidad correcta de efectivo.</dd>
  			<dt style="margin-top:9px;">Cheques en tránsito</dt>
  			<dd>Un cheque en tránsito está en los registros de la organizacion, pero no en el estado de cuenta bancario. Por lo tanto, es necesario incluirlos en la conciliación bancaria como una disminucion al saldo del bancos, pues una vez girados y entregados a sus beneficiarios, la organizacion ya no contará con ese dinero.</dd>
  			<dt style="margin-top:9px;">Ajustes</dt>
  			<dd>Registros para corregir errores u omisiones de los tenedores de libros, que pueden provocar una diferencia en los saldos de las cuentas.</dd>
			</dl>
		</div>
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
      $(".catalogo6s").hide();   
      $(".catalogo4s").show();   
    });

    $("#libro-menos-2").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").show();    
      $(".DteBancoMas").hide();
      $(".DteBancoMenos").hide();   
      $(".catalogo6s").show();   
      $(".catalogo4s").hide();   
    });

    $("#banco-mas-1").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").hide();
      $(".DteBancoMas").show();
      $(".DteBancoMenos").hide();    
      $(".catalogo6s").hide();   
      $(".catalogo4s").hide();
    });

    $("#banco-menos-2").click(function(){
      $(".DteLibroMas").hide();
      $(".DteLibroMenos").hide();
      $(".DteBancoMas").hide();
      $(".DteBancoMenos").show();    
      $(".catalogo6s").hide();   
      $(".catalogo4s").hide();
    });

</script>	

</body>
</html>