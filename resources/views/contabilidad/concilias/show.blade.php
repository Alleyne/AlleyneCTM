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
  <p class="text-center" style="margin:0px">{{ $concilia->f_endpresentdo }}</p>
  <p class="text-center" style="margin:0px">(en balboas)</p>

	<div class="row hidden-print" style="margin-top:0px; background-color:white;">
	  <div class="col-xs-6"><a href="{{ url(Cache::get('goto_pcontables_index')) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a></div>
	  <div class="col-xs-6 text-right"><a href="#" class="hidden-print"><span class="glyphicon glyphicon-plus text-success" data-target="#Modal_AddDetalleConciliacion" data-toggle="modal"><strong> Agregar</strong></span></a></div>
	</div>	

	<!-- Seccion del libro de Banco -->
	<h4><b><i>Informacion en libro</i></b></h4>
	
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en libro a dia {{ $concilia->f_endlastpdo }}</div>
		  <div class="col-xs-6 text-right"><strong>{{ number_format(floatval($concilia->slib_endlastpdo),2) }}</strong></div>
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

			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_depositado + $ncs->sum('monto')),2) }}</div>	
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

			<div class="row">
			  <div class="col-xs-6" style="margin-top:5px;">&nbsp;&nbsp;&nbsp;Subtotal</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($t_chq_girados +  $nds->sum('monto')),2) }}</div>	
			</div>
		</div>

		<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
		  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Saldo conciliado en libro al dia {{ $concilia->f_endpresentdo }}</div>
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2"></div>	
		  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($concilia->slib_endlastpdo + $t_depositado + $ncs->sum('monto') + $t_chq_girados - $nds->sum('monto')),2) }}</strong></div>
		</div>

	<br>
	
	<!-- Seccion del Banco -->
	<h4><i><b>Informacion en banco</i></b></h4>
	<div style="background-color:white";>
		<div class="row" style="margin-top:0px;background-color:rgb(200,200,200);">
		  <div class="col-xs-6">Saldo en banco al dia {{ $concilia->f_endpresentdo }}</div>
		  <div class="col-xs-6 text-right"><strong>{{ number_format(floatval($concilia->sban_endpresentpdo),2) }}</strong></div>
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

			  	<div class="col-xs-2"></div>	
				  <div class="col-xs-2 text-right">{{ number_format(floatval($d_transito->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	

			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total depositos en tránsito</div>
			  <div class="col-xs-2"></div>	
		  	<div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($d_transitos->sum('monto')),2) }}</div>	
			</div>
		</div>		

		<div class="row" style="margin-top:5px;">
		  <div class="col-xs-12"><strong>MENOS</strong></div>
		</div>
		
		<div style="background-color:lavenderblush;">	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp; <strong>Cheques girados en tránsito</strong></div>
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
		  	
			  	<div class="col-xs-2"></div>	
				  <div class="col-xs-2 text-right">{{ number_format(floatval($chq_circulacion->monto),2) }}</div>	
				  <div class="col-xs-2"></div>	
				</div>	
			@endforeach	
	
			<div class="row">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total de cheques en tránsito</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2" style="border-style: hidden hidden solid hidden;"></div>	
			  <div class="col-xs-2 text-right">{{ number_format(floatval($chq_circulacions->sum('monto')),2) }}</div>	
			</div>
		
		</div>			
			<div class="row" style="margin-top:5px; background-color:rgb(200,200,200);">
			  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;Saldo conciliado en banco al dia {{ $concilia->f_endpresentdo }}</div>
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2"></div>	
			  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($concilia->sban_endpresentpdo + ($d_transitos->sum('monto') - $chq_circulacions->sum('monto'))),2) }}</strong></div>
			</div>
	</div>

	<!-- <div class="row" style="margin-top:5px;">
	  <div class="col-xs-6" style="background-color:lavenderblush;">Saldo conciliado en libro al 31 de agosto 2017</div>
	  <div class="col-xs-6 text-right"><strong>8,320.00</strong></div>
	</div> -->
  <?php
  	$t_libro = ($concilia->slib_endlastpdo + $t_depositado + $ncs->sum('monto') + $t_chq_girados - $nds->sum('monto'));
  	$t_banco = ($concilia->sban_endpresentpdo + $d_transitos->sum('monto') - $chq_circulacions->sum('monto'));
  ?>

	@if ( number_format($t_libro,2)  == number_format($t_banco,2) )
		<div class="row" style="margin-top:10px; margin-bottom:35px;">
		  <div class="col-xs-6"></div>
		  <div class="col-xs-6 text-right"><a href="{{ URL::route('contabilizaConcilia', [$concilia->id, $concilia->pcontable_id]) }}" class="btn btn-warning btn-sm hidden-print"><i class="fa fa-search"></i> Contabilizar conciliacion</a></div>
		</div>
	@endif
	
	<hr style="margin-top:60px">

	<div class="panel-group row hidden-print" id="accordion" role="tablist" aria-multiselectable="true">
	  <div class="panel panel-default">
	    <div class="panel-heading" role="tab" id="headingOne">
	      <h4 class="panel-title">
	        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
	          Diario de conciliacion proyectado
	        </a>
	      </h4>
	    </div>
	    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
	      <div class="panel-body">
					<div class="row" style="background-color:rgb(200,200,200);">
					  <div class="col-xs-2"><strong>Fecha</strong></div>
					  <div class="col-xs-6"><strong>Descripcion</strong></div>	
					  <div class="col-xs-2 text-right"><strong>Debito</strong></div>	
					  <div class="col-xs-2 text-right"><strong>Credito</strong></div>	
					</div>

					@if (!$ncs->isEmpty())
						<div class="row" style="margin-top:10px;">	
						  <div class="col-xs-2"><strong>{{ \Carbon\Carbon::today()->format('M j\\, Y') }}</strong></div>
						  <div class="col-xs-6">{{ $banco }}</div>	
						  <div class="col-xs-2 text-right">{{ number_format(floatval($ncs->sum('monto')),2) }}</div>	
						  <div class="col-xs-2"></div>
						</div>

						@foreach ($ncs as $nc)		
							<div class="row">	
							  <div class="col-xs-2"></div>
							  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;{{ $nc->cuenta.' - '.$nc->detalle }}</div>	
							  <div class="col-xs-2"></div>	
							  <div class="col-xs-2 text-right">{{ number_format(floatval($nc->monto),2) }}</div>
							</div>
						@endforeach		
						
						<div class="row">	
						  <div class="col-xs-2"></div>
						  <div class="col-xs-6"><em><strong>Para registrar Notas de credito del mes</strong></em></div>	
						  <div class="col-xs-2"></div>	
						  <div class="col-xs-2"></div>
						</div>
					@endif

					<?php $i = 0; ?>
					@if (!$nds->isEmpty())
						@foreach ($nds as $nd)
							@if ($i == 0)
								<div class="row" style="margin-top:10px;">	
						  		<div class="col-xs-2"><strong>{{ \Carbon\Carbon::today()->format('M j\\, Y') }}</strong></div>
							  <div class="col-xs-6">{{ $nd->cuenta.' - '.$nd->detalle }}</div>	
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
						  <div class="col-xs-6">&nbsp;&nbsp;&nbsp;{{ $banco }}</div>	
						  <div class="col-xs-2"></div>	
						  <div class="col-xs-2 text-right">{{ number_format(floatval($nds->sum('monto')),2) }}</div>
						</div>		
						
						<div class="row">	
						  <div class="col-xs-2"></div>
						  <div class="col-xs-6"><em><strong>Para registrar Nota de debito</strong></em></div>
						  <div class="col-xs-2"></div>	
						  <div class="col-xs-2"></div>
						</div>	
					@endif
					
					<div class="row">	
					  <div class="col-xs-2"></div>
					  <div class="col-xs-6"></div>	
					  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($ncs->sum('monto') + $nds->sum('monto')),2) }}</strong></div>
					  <div class="col-xs-2 text-right" style="border-style: solid hidden double hidden;"><strong>{{ number_format(floatval($ncs->sum('monto') + $nds->sum('monto')),2) }}</strong></div>
					</div>

	      </div>
	    </div>
	  </div>
	  <div class="panel panel-default">
	    <div class="panel-heading" role="tab" id="headingTwo">
	      <h4 class="panel-title">
	        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
	          Glosario de teminos contables
	        </a>
	      </h4>
	    </div>
	    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
	      <div class="panel-body">
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
	  </div>
	</div>

	@include('templates.backend._partials.modal_confirm')
	@include('contabilidad.concilias.Modal_AddDetalleConciliacion')

</div> <!-- end container -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>

<script type="text/javascript">

    $("#nc").click(function(){
      $(".catalogo6s").hide();   
      $(".catalogo4s").show();   
      $(".detalle").show();  
    });

    $("#nd").click(function(){
      $(".catalogo6s").show();   
      $(".catalogo4s").hide();   
      $(".detalle").show();  
    });

    $("#dt").click(function(){
      $(".catalogo6s").hide();   
      $(".catalogo4s").hide();
      $(".detalle").show();   
    });

    $("#cc").click(function(){
      $(".catalogo6s").hide();   
      $(".catalogo4s").hide();
      $(".detalle").show();  
    });

    $("#sb").click(function(){
      $(".catalogo6s").hide();   
      $(".catalogo4s").hide();
      $(".detalle").hide();   
    });

</script>	

</body>
</html>