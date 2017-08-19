@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Serviproductos por Organización')

@section('content')

		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">
				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<!-- Widget ID (each widget will need unique ID)-->
					<div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-fullscreenbutton="false" data-widget-togglebutton="false" data-widget-editbutton="true" data-widget-deletebutton="false">
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
							<h2>Serviproductos vinculados al Proveedor </h2>
							<div class="widget-toolbar">
								<a href="{{ URL::route('orgs.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>

								<button class="btn btn-success" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
									 Vincular Serviproducto
								</button>
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
											<th col width="25px">ID</th>
											<th>NOMBRE</th>
											<th col width="130px" class="text-center"><i class="fa fa-gear fa-lg"></i></th>								
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td><strong>{{ $dato->id }}</strong></td>
												<td><strong>{{ $dato->nombre }}</strong></td>
												<td align="right">
													<ul class="demo-btns">
												    <li>
												        {{Form::open(array(
												            'route' => array('desvincularServiproducto', $org_id, $dato->id),
												            'method' => 'GET',
												            'style' => 'display:inline'
												        ))}}

												        {{Form::button('Desvincular', array(
												            'class' => 'btn btn-warning btn-xs',
												            'data-toggle' => 'modal',
												            'data-target' => '#confirmAction',
												            'data-title' => 'Desvincular serviproducto de la organizacion',
												            'data-message' => 'Esta seguro(a) que desea desvincular el presente serviproducto de la organización?',
												            'data-title' => 'Desvincular serviproducto de la organización',
												            'data-message' => 'Esta seguro(a) que desea desvincular el serviproducto de esta organización?',
												            'data-btntxt' => 'SI, desvincular',
												            'data-btncolor' => 'btn-warning'
												        ))}}
												        {{Form::close()}}                                                    
												    </li>
													</ul>
												</td>
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
						<h4 class="modal-title" id="myModalLabel">Vincular Serviproducto</h4>
					</div>
					<div class="modal-body">

						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'vinculaServiproductoStore')) }}
							<fieldset>
								{{ Form::hidden('org_id', $org_id) }}
								<!-- Multiple Radios (inline) -->
								<div class="form-group">
								  <label class="col-md-3 control-label" for="radios">Tipo</label>
								  <div class="col-md-9"> 
								    <label class="radio-inline" for="radios-0">
								      <input type="radio" name="tipo_radios" id="tipo-1" value="0" checked="checked">
								      Producto
								    </label> 
								    <label class="radio-inline" for="radios-1">
								      <input type="radio" name="tipo_radios" id="tipo-2" value="1">
								      Servicio
								    </label>
								  </div>
								</div>

							<div class="form-group productos">
								<label class="col-md-3 control-label">Productos</label>
								<div class="col-md-9">
									{{ Form::select('producto_id', ['' => 'Escoja el Producto que desea vincular!'] + $productos, 0, ['class' => 'form-control']) }}
									{!! $errors->first('producto_id', '<li style="color:red">:message</li>') !!}
								</div>
							</div>

							<div class="form-group servicios" style="display: none;">
								<label class="col-md-3 control-label">Servicios</label>
								<div class="col-md-9">
									{{ Form::select('servicio_id', ['' => 'Escoja el Servicio que desea vincular!'] + $servicios, 0, ['class' => 'form-control']) }}
									{!! $errors->first('servicio_id', '<li style="color:red">:message</li>') !!}
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
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
 
        $('#dt_basic').dataTable({
            "sPaginationType" : "bootstrap_full"
        });
    })
    </script>
    
    <script>	
	   $("#tipo-1").click(function(){
	       $(".productos").show();
	       $(".servicios").hide();
	   });

	   $("#tipo-2").click(function(){
	       $(".productos").hide();
	       $(".servicios").show();
	   });

	</script>

@stop