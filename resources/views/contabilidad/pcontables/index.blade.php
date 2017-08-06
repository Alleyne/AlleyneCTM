@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Periodos contables')

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
							<h2>Periodos contables </h2>
							<div class="widget-toolbar">
								@if (!$datos->count())
									<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
										 Crear primer periodo contable
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
							<div class="widget-body">
								<div class="widget-body-toolbar">
									<div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">

									</div>
								</div>

								<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>NO</th>
											<th>PERIODO</th>
											<th>F_CIERRE</th>
											<th class="text-center">CDO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="20px" align="left"><strong>{{ $dato->id }}</strong></td>
												<td col width="70px" align="left"><strong>{{ $dato->periodo }}</strong></td>
												<td col width="70px" align="left">{{ $dato->f_cierre }}</td>
												<td col width="30px" align="center">{{ $dato->cerrado ? 'Si' : 'No' }}</td>
												<td col width="275px" align="right">
													<ul class="demo-btns">
														@if ( $dato->cerrado == 0 )
															<li>
																<div class="btn-group">
																	<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
																		<span class="glyphicon glyphicon-usd"></span> Maneja efectivo <span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		<li>
																			<a href="{{ URL::route('diariocajas.index') }}">Informes de Caja General</a>
																		</li>
																		<li class="divider"></li>	
																		<li>
																			<a href="{{ URL::route('concilias.show', $dato->id) }}">Conciliacion bancaria</a>
																		</li>
																	</ul>
																</div>
															</li>	
															<li>
																<div class="btn-group">
																	<button class="btn btn-xs btn-warning dropdown-toggle" data-toggle="dropdown">
																		<span class="fa fa-unlock-o"></span> BG <span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		<li>
																			<a href="{{ URL::route('balancegeneral', array($dato->id, $dato->periodo)) }}">Balance general Modelo 1</a>
																		</li>
																		<li>
																			<a href="{{ URL::route('bg_m2_proyectado', $dato->id) }}">Balance general Modelo 2</a>																		</li>
																		</li>
																	</ul>
																</div>
															</li>							
															<li>
																<a href="{{ URL::route('estadoderesultado', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-unlock-o"></i> ER</a>
															</li>																
															<li>
																<a href="{{ URL::route('htProyectada', $dato->id) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-unlock-o"></i> HT</a>
															</li>	
															<li>
																<a href="{{ URL::route('ctdiarios.show', $dato->id) }}" class="btn bg-color-green txt-color-white btn-xs"><i class="fa fa-unlock-o"></i> Diario</a>
															</li>
														@else
															<li>
																<div class="btn-group">
																	<button class="btn btn-xs btn-default txt-color-yellow dropdown-toggle" data-toggle="dropdown">
																		<span class="fa fa-lock"></span> BG <span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		<li>
																			<a href="{{ URL::route('bg', $dato->id) }}">Balance general Modelo 1</a>
																		</li>
																		<li>
																			<a href="{{ URL::route('bg_m2_final', $dato->id) }}">Balance general Modelo 2</a>																		</li>
																		</li>
																	</ul>
																</div>
															</li>			

															<li>
																<a href="{{ URL::route('er', $dato->id) }}" class="btn btn-default txt-color-blue btn-xs"><i class="fa fa-lock"></i> ER Final</a>
															</li>				
																							
															<li>
																<a href="{{ URL::route('htFinal', $dato->id) }}" class="btn btn-default txt-color-purple btn-xs"><i class="fa fa-lock"></i> HT</a>
															</li>	
															<li>
																<a href="{{ URL::route('diarioFinal', $dato->id) }}" class="btn btn-default txt-color-green btn-xs"><i class="fa fa-lock"></i> Diario</a>
															</li>
														@endif															
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
						<h4 class="modal-title" id="myModalLabel">Crear Periodo contable inicial</h4>
					</div>
					<div class="modal-body">
		
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'pcontables.store')) }}
							<fieldset>
								<div class="form-group">
									<label class="col-md-3 control-label">Fecha</label>
									<div class="col-md-9">
										<div class="input-group">
											<input type="text" name="fecha" id="fecha" placeholder="Entre la fecha del primer periodo a registrar (aaaa/mm/dd)" ..." class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
										<p>{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
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
	</div>
@stop

@section('relatedplugins')

  <script type="text/javascript">
  	$(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "393px",
        "scrollCollapse": true,
        "stateSave": true,
				"order": [[ 0, "desc" ]],
         
				"language": {
					"decimal":        "",
					"emptyTable":     "No hay datos disponibles para esta tabla",
					"info":           "Mostrando _END_ de un total de _MAX_ unidades",
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
    	
    	$("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    });

    })
  
  </script>
@stop