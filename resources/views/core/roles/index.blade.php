@extends('templates.backend._layouts.smartAdmin')

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
							<h2>Roles </h2>
							<div class="widget-toolbar">

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
											<th>NOMBRE</th>
											<th>DESCRIPCIÓN</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
						
										@foreach ($datos as $dato)
											<tr>
												<td><strong>{{ $dato->id }}</strong></td>
												<td><strong>{{ $dato->name }}</strong></td>
												<td><strong>{{ $dato->description }}</strong></td>
												<td col width="250px" align="right">
													<ul class="demo-btns">
														@if (Cache::get('esAdminkey'))
															<a href="{{ URL::route('usuariosPorRole', $dato->id) }}" class="btn btn-success btn-xs"><i class="fa fa-search"></i> Asignar usuario</a>
															<a href="{{ URL::route('permisPorRole', $dato->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Asignar permisos</a>
														@elseif (Cache::get('esJuntaDirectivakey'))
															<a href="{{ URL::route('usuariosPorRole', $dato->id) }}" class="btn btn-success btn-xs"><i class="fa fa-search"></i> Asignar usuario</a>
														@endif
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
  <script type="text/javascript">
    $(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "382px",
        "scrollCollapse": true,
        "stateSave": true,

        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles para esta tabla",
            "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ registros",
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
              "last":       "Último",
              "next":       "Próximo",
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