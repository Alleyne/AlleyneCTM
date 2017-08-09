@extends('templates.backend._layouts.smartAdmin')

@section('title', '| All Posts')

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
						<h2>Artículos </h2>
						<div class="widget-toolbar">
							@if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
								<a href="{{ route('posts.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Crear articulo</a>
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
									<th>#</th>
									<th>Título</th>
									<th>Contenido</th>
									<th col width="70px"class="text-left">Creado el</th>
									<th col width="70px"class="text-center"><i class="fa fa-gear fa-lg"></i></th>
								</thead>

								<tbody>
									
									@foreach ($posts as $post)
										
										<tr>
											<th>{{ $post->id }}</th>
											<td>{{ $post->title }}</td>
											<td>{{ substr(strip_tags($post->body), 0, 50) }}{{ strlen(strip_tags($post->body)) > 50 ? "..." : "" }}</td>
											<td>{{ date('M j, Y', strtotime($post->created_at)) }}</td>
											<td><a href="{{ route('posts.show', $post->id) }}" class="btn btn-default btn-xs">Ver</a> <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-default btn-xs">Editar</a></td>
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
    })
  </script>
@stop