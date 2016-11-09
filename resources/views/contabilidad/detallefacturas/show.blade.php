@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Detalle de Factura')

@section('content')

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
							<h2>Detalles de la factura </h2>
							<div class="widget-toolbar">
								<a href="{{ URL::route('facturas.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
									@if ($factura->etapa < 2)
										<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
											 Agregar detalle
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

								<table id="dt_basic" class="table table-hover">
									<thead>
										<tr>
											<th>ID</th>
											<th>CUENTA</th>
											<th>DETALLE</th>
											<th>PRECIO</th>
											<th>ITBMS</th>
											<th>TOTAL</th>
											@if ($factura->etapa < 2)
												<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>	
											@endif
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="40px">{{ $dato->id }}</td>
												<td col width="40px"><strong>{{ $dato->codigo }}</strong></td>
												<td>{{ $dato->detalle }}</td>
												<td col width="60px">{{ $dato->precio }}</td>
												<td col width="60px">{{ $dato->itbms }}</td>
												<td col width="60px"><strong>{{ $dato->total }}</strong></td>
												@if ($factura->etapa < 2)
													<td col width="40px" align="right">
														<ul class="demo-btns">
															<li>
																{{ Form::open(array(
																	'route' => array('detallefacturas.destroy', $dato->id),
																	'method' => 'DELETE',
																	'style' => 'display:inline'
																	))
																}}

																{{ Form::button('Borrar', array(
																	'class' => 'btn btn-danger btn-xs',
																	'data-toggle' => 'modal',
																	'data-target' => '#confirmDelete',
																	'data-title' => 'Borrar detalle de factura',
																	'data-message' => 'Esta seguro(a) que desea borrar el presente detalle de factura?',
																	'data-btncancel' => 'btn-default',
																	'data-btnaction' => 'btn-danger',
																	'data-btntxt' => 'Borrar detalle'
																	))
																}}

																{{ Form::close() }}
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
						<h4 class="modal-title" id="myModalLabel">Agregar detalle por servicios de mantenimiento</h4>
					</div>
					<div class="modal-body">
		
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'detallefacturas.store')) }}
							<fieldset>
								{{ Form::hidden('factura_id', $factura->id) }}
								
								<div class="form-group">
									<label class="col-md-3 control-label">Cuentas de gastos</label>
									<div class="col-md-9">
										{{ Form::select('catalogo_id', ['' => 'Selecione una cuenta de  gastos ...'] + $kresultadoctas, 0, ['class' => 'form-control']) }}
										{!! $errors->first('catalogo_id', '<li style="color:red">:message</li>') !!}
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 control-label">Precio</label>
									<div class="col-md-9">
										{{ Form::text('precio', old('precio'),
											array(
											    'class' => 'form-control',
											    'id' => 'precio',
											    'placeholder' => 'Escriba el precio ...',
												'autocomplete' => 'off',
											))
										}} 
										{!! $errors->first('precio', '<li style="color:red">:message</li>') !!}
									</div>
								</div>	
								<div class="form-group">
									<label class="col-md-3 control-label">Itbms</label>
									<div class="col-md-9">
										{{ Form::text('itbms', old('itbms'),
											array(
											    'class' => 'form-control',
											    'id' => 'itbms',
											    'placeholder' => 'Escriba el Itbms ...',
												'autocomplete' => 'off',
											))
										}} 
										{!! $errors->first('itbms', '<li style="color:red">:message</li>') !!}
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
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/jquery.dataTables-cust.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/ColReorder.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script> -->
    
    <script type="text/javascript">
	    $(document).ready(function() {
	        pageSetUp();
	 
	        $('#dt_basic').dataTable({
	            "sPaginationType" : "bootstrap_full"
	        });
	    })

	    $("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    });
	</script>
@stop