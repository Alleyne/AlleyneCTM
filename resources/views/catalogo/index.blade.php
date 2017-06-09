@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Catalogo')

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
							<h2>Cuentas </h2>
							<div class="widget-toolbar">
								@if (Cache::get('esAdminkey') || Cache::get('esContadorkey'))
									<div class="btn-group">
					                    <a class="btn btn-success btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Crear nueva cuenta de:</a>
					                    <a class="btn btn-success dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
					                    <ul class="dropdown-menu">
					                        <li><a href="{{ URL::route('createCuenta', 1) }}">Activos</a></li>
					                        <li><a href="{{ URL::route('createCuenta', 2) }}">Pasivos</a></li>
					                        <li><a href="{{ URL::route('createCuenta', 3) }}">Patrimonio</a></li>
					                        <li class="divider"></li>
					                        <li><a href="{{ URL::route('createCuenta', 6) }}">Gastos</a></li>
					                        <li><a href="{{ URL::route('createCuenta', 4) }}">Ingresos</a></li>
					                    </ul>
					                </div><!-- /btn-group --> 
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
											<th>CODIGO</th>
											<th>TIPO</th>
											<th>CONC</th>
											<th>CLASE</th>
											<th>ESTADO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="40px"><strong>{{ $dato->id }}</strong></td>
												<td><strong>{{ $dato->nombre }}</strong></td>
												<td col width="50px"><strong>{{ $dato->codigo }}</strong></td>
												<td col width="50px">{{ $dato->tipo }}</td>
												<td col width="50px">{{ $dato->conciliacion }}</td>
												<td col width="80px">
													@if ($dato->corriente_siono === 1)
														Corriente
													@elseif ($dato->corriente_siono === 0)
														No corriente
													@elseif ($dato->corriente_siono === null)
													@endif
												</td>
												<td col width="80px">
													{{ $dato->activa ? 'Activa': 'Inactiva' }}
												</td>
												<td col width="40px" align="right">
													<ul class="demo-btns">
														<li>
															<a href="{{ URL::route('catalogos.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
														</li>
													</ul>
												</td>
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
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
 
        $('#dt_basic').dataTable({
            "sPaginationType" : "bootstrap_full"
        });
    })
    </script>
@stop