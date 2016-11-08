@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Permisos')

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
							<h2>Permisos </h2>
							<div class="widget-toolbar">
								@if (Cache::get('esAdminkey'))
									<a href="{{ URL::route('permissions.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Agregar permiso</a>
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
											<th>NOMBRE</th>
											<th>VALOR</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
						
										@foreach ($datos as $dato)
											<tr>
												@if (Cache::get('esAdminkey'))												
													<td><strong>{{ $dato->id }}</strong></td>
													<td><strong>{{ $dato->name }}</strong></td>
													<td><strong>{{ $dato->value }}</strong></td>
													<td col width="380px" align="right">
														<ul class="demo-btns">
															<li>
																<a href="{{ URL::route('permissions.show', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
															</li>				
															<li>
																<a href="{{ URL::route('permissions.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
															</li>
															<li>
																{{ Form::open(array('route' => array('permissions.destroy', $dato->id), 'method' => 'delete', 'data-confirm' => 'Deseas borrar el permiso '. $dato->name. ' permanentemente?')) }}
																	<button type="submit" href="{{ URL::route('permissions.destroy', $dato->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>
																{{ Form::close() }}										
															</li>
														</ul>
													</td>

												@elseif (Cache::get('esAdminDeBloquekey'))												
													<td><strong>{{ $dato->nombre }}</strong></td>
													<td col width="380px" align="right">
														<ul class="demo-btns">
															<li>
																<a href="#" class="btn btn-info btn-xs"><i class="fa fa-search"></i> Bloques</a>
															</li>
															<li>
																<a href="#" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
															</li>				
														</ul>
													</td>
												
												@elseif (Cache::get('esJuntaDirectivakey'))
													<td col width="40px"><strong>{{ $dato->jd->id }}</strong></td>
													<td><strong>{{ $dato->jd->nombre }}</strong></td>
													<td col width="160px" align="center">
														<ul class="demo-btns">
															<li>
																<a href="#" class="btn btn-info btn-xs"><i class="fa fa-search"></i> Bloques</a>
															</li>
															<li>
																<a href="#" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
															</li>				
														</ul>
													</td>
												@endif	
											</tr>
										@endforeach
							
									</tbody>
								</table>
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
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script> -->
    
    <script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
 
        $('#dt_basic').dataTable({
            "sPaginationType" : "bootstrap_full"
        });
    })
    </script>
@stop