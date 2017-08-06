@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Registrar Facturas')

@section('content')

	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-togglebutton="false" data-widget-fullscreenbutton="false" data-widget-editbutton="false" data-widget-deletebutton="false">
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
						<h2>Facturas </h2>
						<div class="widget-toolbar">
							@if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
								<a href="{{ URL::route('facturas.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Crear factura</a>
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
										<th>PROVEEDOR</th>
										<th>NUMERO</th>
										<th>FECHA</th>
										<th>TOTALFAC</th>
										<th>TOTALDET</th>
										<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
									</tr>
								</thead>
								<tbody>
									@foreach ($datos as $dato)
										<tr>
											<td col width="40px">{{ $dato->id }}</td>
											<td>{{ $dato->org->nombre }}</td>
											<td col width="40px">{{ $dato->doc_no }}</td>
											<td col width="90px">{{ $dato->fecha }}</td>
											<td col width="60px">{{ $dato->total }}</td>
							
											@if (round(floatval($dato->total),2) == round(floatval($dato->totaldetalle),2))
												<td col width="60px">{{ $dato->totaldetalle }}</td>
											@else
												<td col width="60px"><mark>{{ $dato->totaldetalle }}</mark></td>
											@endif
											<td col width="210px" align="right">
												<ul class="demo-btns">
													@if ($dato->etapa==1)
														<li>
															<span class="label label-warning">Registrando</span>
														</li>
														<li>
															<a href="{{ URL::route('detallefacturas.show', array($dato->id)) }}" class="btn btn-primary btn-xs"> Detalles</a>
														</li>					
														<li>
                              {{Form::open(array(
																	'route' => array('facturas.destroy', $dato->id),
																	'method' => 'DELETE',
                                  'style' => 'display:inline'
                              ))}}

                              {{Form::button('Eliminar', array(
                                  'class' => 'btn btn-danger btn-xs',
                                  'data-toggle' => 'modal',
                                  'data-target' => '#confirmAction',
																	'data-title' => 'Eliminar factura de egreso de Caja general',
																	'data-message' => 'Esta seguro(a) que desea eliminar la presente factura?',
																	'data-btntxt' => 'SI, eliminar factura',
                                  'data-btncolor' => 'btn-danger'
                              ))}}
                              {{Form::close()}}
														</li>
													
													@elseif ($dato->etapa==2)
														<li>
                              {{Form::open(array(
                                  'route' => array('contabilizaDetallesFactura', $dato->id),
                                  'method' => 'GET',
                                  'style' => 'display:inline'
                              ))}}

                              {{Form::button('Contabilizar', array(
                                  'class' => 'btn btn-warning btn-xs',
                                  'data-toggle' => 'modal',
                                  'data-target' => '#confirmAction',
                                  'data-title' => 'Contabilizar factura de egreso de Caja general',
                                  'data-message' => 'Esta seguro(a) que desea contabilizar la factura?',
                                  'data-btntxt' => 'SI, contabilizar factura',
                                  'data-btncolor' => 'btn-warning'
                              ))}}
                              {{Form::close()}}  
														</li>
														<li>
															<a href="{{ URL::route('detallefacturas.show', $dato->id) }}" class="btn btn-info btn-xs"> Detalles</a>
														</li>					
														<li>
                              {{Form::open(array(
																	'route' => array('facturas.destroy', $dato->id),
																	'method' => 'DELETE',
                                  'style' => 'display:inline'
                              ))}}

                              {{Form::button('Eliminar', array(
                                  'class' => 'btn btn-danger btn-xs',
                                  'data-toggle' => 'modal',
                                  'data-target' => '#confirmAction',
																	'data-title' => 'Eliminar factura de egreso de Caja general',
																	'data-message' => 'Esta seguro(a) que desea eliminar la presente de factura?',
																	'data-btntxt' => 'SI, eliminar factura',
                                  'data-btncolor' => 'btn-danger'
                              ))}}
                              {{Form::close()}}
														</li>
													@elseif ($dato->etapa==3)
														<li>
															<span class="label label-success">Contabilizada</span>
														</li>															
														<li>
															<a href="{{ URL::route('detallefacturas.show', $dato->id) }}" class="btn btn-primary btn-xs"> Detalles</a>
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

@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>  

  <script type="text/javascript">
  	$(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "385px",
        "scrollCollapse": true,
        "stateSave": true,

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
    })
    </script>
@stop