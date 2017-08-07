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
								
								<table id="dt_basic" class="display compact" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>ID</th>
											<th>NOMBRE</th>
											<th>DESCRIPCION</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
						
										@foreach ($datos as $dato)
											<tr>
												<td>{{ $dato->id }}</td>
												<td><strong>{{ $dato->name }}</strong></td>
												<td>{{ $dato->description }}</td>
												<td col width="80px" align="right">
													<ul class="demo-btns">
														<li>
															<a href="{{ URL::route('permissions.show', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
														</li>				
														<li>
															<a href="{{ URL::route('permissions.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
														</li>
														<li>
									
											        {{Form::open(array(
											            'route' => array('permissions.destroy', $dato->id),
											            'method' => 'DELETE',  // or DELETE
											            'style' => 'display:inline'
											        ))}}

											        {{Form::button('<i class="fa fa-times"></i>', array(
											            'class' => 'btn btn-danger btn-xs',
											            'data-toggle' => 'modal',
											            'data-target' => '#confirmAction',
											            'data-title' => 'Eliminar permiso permanentemente',
											            'data-message' => 'Esta seguro(a) que desea eliminar el presente permiso?',
											            'data-btntxt' => 'SI, eliminar',
											            'data-btncolor' => 'btn-danger'
											        ))}}
											        {{Form::close()}}  

														</li>
													</ul>
												</td>
											</tr>
										@endforeach
							
									</tbody>
								</table>
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
        "scrollY": "394px",
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