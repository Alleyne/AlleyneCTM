@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Proveedores')

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
						<h2>Proveedores de Productos y Servicios</h2>
						<div class="widget-toolbar">
							@if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
								<a href="{{ URL::route('orgs.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Proveedor</a>
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
										<th>NOMBRE</th>
										<th>RUC</th>
										<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
									</tr>
								</thead>
								<tbody>
					
									@foreach ($datos as $dato)
										<tr>
											@if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
												<td><strong>{{ $dato->nombre }}</strong></td>
												<td col width="80px">{{ $dato->ruc }}</td>
												<td col width="250px" align="right">
													<ul class="demo-btns">
														<li>
															<a href="{{ URL::route('serviproductosPorOrg', $dato->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Vincular serviproducto</a>
														</li>
														<li>
															<a href="{{ URL::route('orgs.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
														</li>
														<li>
											        {{Form::open(array(
											            'route' => array('orgs.destroy', $dato->id),
											            'method' => 'DELETE', 
											            'style' => 'display:inline'
											        ))}}
											        
											        {{Form::button('<i class="fa fa-times"></i>', array(
											            'class' => 'btn btn-danger btn-xs',
											            'data-toggle' => 'modal',
											            'data-target' => '#confirmAction',
											            'data-title' => 'Eliminar organizacion',
											            'data-message' => 'Esta seguro(a) que desea eliminar la presente organizacion?',
											            'data-btntxt' => 'SI, eliminar',
											            'data-btncolor' => 'btn-danger'
											        ))}}
											        {{Form::close()}}  

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
@stop