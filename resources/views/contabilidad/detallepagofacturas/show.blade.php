@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de pagos')

@section('content')
<div class="well well-sm">
	<div class="card card-outline-danger text-center">
		    <h4 class="card-title">Egreso de Caja General</h4>
			  <div class="row">
			    <div class="col-md-3">
						Factura No: {{ $factura->doc_no }}							
			    </div>
			    <div class="col-md-3">
			      A Favor de: {{ $factura->afavorde }}
			    </div> 
			    <div class="col-md-3">
			    	Fecha: {{ $factura->fecha }}
			    </div>
			    <div class="col-md-3">
			      <strong>Total B/.: {{ $factura->total }}</strong>
			    </div> 
			  </div>	
	</div>
</div>
		
		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">
				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="true" data-widget-deletebutton="false">
						<!-- widget options:
						usage: <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">
		
						data-widget-colorbutton="false"
						data-widget-editbutton="false"
						data-widget-togglebutton="false"
						data-widget-deletebutton="false"
						data-widget-fullscreenbutton="false"
						data-widget-custombutton="false"
						data-widget-collapsed="true"
						data-widget-sortable="false"
						-->
						<header>
							<span class="widget-icon"> <i class="fa fa-table"></i> </span>
							<h2>Programación de pagos de la factura </h2>
							<div class="widget-toolbar">
								<a href="{{ URL::route('pagarfacturas') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
									@if ($factura->pagada == 0)
										<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
											 Agregar programacion de pago de factura
										</button>
									@endif
							</div>
						</header>

						<!-- widget div-->
						<div>
		
							<!-- widget edit box -->
							<div class="jarviswidget-editbox">
								<!-- This area used as dropdown edit box -->
		
							</div>
							<!-- end widget edit box -->
		
							<!-- widget content -->
							<div class="widget-body no-padding">
								<div class="widget-body-toolbar">
									<div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">

									</div>
								</div>

								<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>ID</th>
											<th>FECHA</th>
											<th>DETALLE</th>
											<th>MONTO</th>
											<th>PAGO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>	
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="40px">{{ $dato->id }}</td>
												<td col width="100px">{{ $dato->fecha }}</td>
												<td>{{ $dato->detalle }}</td>
												<td col width="100px">{{ $dato->monto }}</td>
												<td col width="100px">{{ $dato->pagotipo }}</td>
												@if ($dato->etapa == 1)
													<td col width="180px" align="right">
														<ul class="demo-btns">
															
															<li>
          											<button type="button" data-target="#myModalPagar" data-toggle="modal" class="btn btn-success btn-xs pagarContabilizarBtn" data-detallepagofactura_id="{{ $dato->id }}">Pagar contabilizar</button>
															</li>
															{{-- <li>
																{{Form::open(array(
																	'route' => array('contabilizaDetallePagoFactura', $dato->id),
																	'method' => 'GET',
																	'style' => 'display:inline'
																	))
																}}

																{{Form::button('Pagar y contabilizar', array(
												          'class' => 'btn btn-warning btn-xs',
												          'data-toggle' => 'modal',
												          'data-target' => '#confirmAction',
																	'data-title' => 'Pagar y contabilizar factura de egreso de Caja general',
																	'data-message' => 'Esta seguro(a) que desea pagar y contabilizar pago programado?',
																	'data-btntxt' => 'SI, pagar y contabilizar',
												          'data-btncolor' => 'btn-warning'
																	))
																}}
																{{Form::close()}}
 															</li> --}}
															
															<li>
																{{Form::open(array(
																	'route' => array('detallepagofacturas.destroy', $dato->id),
																	'method' => 'DELETE',
																	'style' => 'display:inline'
																	))
																}}

																{{Form::button('Eliminar', array(
																	'class' => 'btn btn-danger btn-xs',
																	'data-toggle' => 'modal',
												          'data-target' => '#confirmAction',
																	'data-title' => 'Eliminar pago programado de factura de egreso de Caja general',
																	'data-message' => 'Esta seguro(a) que desea eliminar pago programado?',
																	'data-btntxt' => 'SI, eliminar pago',
												          'data-btncolor' => 'btn-danger'
																	))
																}}

																{{Form::close()}}
															</li>
														</ul>
													</td>
												
												@elseif ($dato->etapa == 2)
													<td col width="140px" align="right">
														<ul class="demo-btns">
															<li>
																<span class="label label-success">Pagado y contabilizado</span>
															</li>
														</ul>
													</td>
												@endif
											</tr>
										@endforeach
									</tbody>
								</table>
								<!-- Incluye la modal box -->
								@include('templates.backend._partials.modal_confirm')
								@include('contabilidad.detallepagofacturas.modal_pagar')
							</div>
							<!-- end widget content -->
		
						</div>
						<!-- end widget div -->
		
					</div>
					<!-- end widget -->
				</article>
				<!-- WIDGET END -->
			</div>
			<!-- end row -->
		
		</section>
		<!-- end widget grid -->
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">Agregar programacion de pago de factura</h4>
					</div>
					<div class="modal-body">
		
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'detallepagofacturas.store')) }}
							<fieldset>
      				{{ Form::hidden('factura_id', $factura->id) }}                
                
                <div class="form-group">
                  <label class="col-md-3 control-label">Fecha</label>
                  <div class="col-md-9">
										<div class="input-group">
											<input type="text" class="Form-control datepicker" name="fecha" placeholder="Seleccione la fecha del pago de la factura ..." data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
	                  {!! $errors->first('fecha', '<li style="color:red">:message</li>') !!} 
                  </div>
                </div>  
	
								<div class="form-group">
									<label class="col-md-3 control-label">Monto</label>
									<div class="col-md-9">
										{{ Form::text('monto', old('monto'),
											array(
											    'class' => 'form-control',
											    'id' => 'monto',
											    'placeholder' => 'Escriba el monto ...',
												'autocomplete' => 'off',
											))
										}} 
										{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
									</div>
								</div>	
							
								<div class="form-group">
									<label class="col-md-3 control-label">Detalle</label>
									<div class="col-md-9">
										{{ Form::text('detalle', old('detalle'),
											array(
											    'class' => 'form-control',
											    'id' => 'detalle',
											    'placeholder' => 'Escriba el detalle del pago...',
												'autocomplete' => 'off',
											))
										}} 
										{!! $errors->first('detalle', '<li style="color:red">:message</li>') !!}
									</div>
								</div>	
							</fieldset>				
							
							<div class="form-actions">
								{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
								<button type="button" class="btn btn-default" data-dismiss="modal">
									Cancel
								</button>
							</div>
						{{ Form::close() }}
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>  
  
  <script type="text/javascript">
    $(document).ready(function() {
      


$('.datepicker').datepicker();


      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "393px",
        "scrollCollapse": true,
        "stateSave": true,

        "language": {
          "decimal":        "",
          "emptyTable":     "No hay datos disponibles para esta tabla",
          "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ unidades",
          "infoEmpty":      "",
          "infoFiltered":   "",
          "infoPostFix":    "",
          "thousands":      ",",
          "lengthMenu":     "Mostrar _MENU_ unidades",
          "loadingRecords": "Cargando...",
          "processing":     "Procesando...",
          "search":         "Buscar:",
          "zeroRecords":    "No se encontro ninguna unidad con ese filtro",
          "paginate": {
            "first":      "Primer",
            "last":       "Ultimo",
            "next":       "Proximo",
            "previous":   "Anterior"
          },
          "aria": {
            "sortAscending":  ": active para ordenar ascendentemente",
            "sortDescending": ": active para ordenar descendentemente"
          }
        }
      });

			$(function () {


				$.datepicker.regional['es'] = {
					closeText: 'Cerrar',
					prevText: '<Ant',
					nextText: 'Sig>',
					currentText: 'Hoy',
					monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
					monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
					dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
					dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
					dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
					weekHeader: 'Sm',
					dateFormat: 'yy/mm/dd',
					firstDay: 1,
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''
				};
						
				$.datepicker.setDefaults($.datepicker.regional['es']);

		    $("#dialog").dialog({
		        autoOpen: false,
		        show: {
		            effect: "blind",
		            duration: 1000
		        },
		        hide: {
		            effect: "explode",
		            duration: 1000
		        }
		    });
			    
				var trantipo_id = jQuery('#trantipo_id');
				var select = this.value;
				trantipo_id.change(function () {
				    
			    if ($(this).val() == 1) {
		        $('.chequeNo').show();
		    		$('.transaccionNo').hide();
			    
			    } else if ($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4 || $(this).val() == 6 || $(this).val() == 7) {
			    	$('.chequeNo').hide();
			    	$('.transaccionNo').show();
			    
			    } else if ($(this).val() == 5) {
			    	$('.chequeNo').hide();
			    	$('.transaccionNo').hide();
		    
			    }	else {
			    	$('.chequeNo').hide();
			    	$('.transaccionNo').hide();
			    }
				});

				// pasa el detallepagofactura_id al 
				$('#myModalPagar').on('show.bs.modal', function(e) {
				    var id = $(e.relatedTarget).data('detallepagofactura_id');
				    $(e.currentTarget).find('input[name="detallepagofactura_id"]').val(id);
				});

	      $("input[type='submit']").attr("disabled", false);
		    $("form").submit(function(){
		      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
		      return true;
		    });
			});
      


    })


	</script>
@stop

