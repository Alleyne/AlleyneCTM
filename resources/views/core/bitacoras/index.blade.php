s@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Bitácora')

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
						<h2>Bitacoras </h2>
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
										<th>#</th>
										<th>Fecha</th>
										<th>Acción</th>				
										<th>Tabla</th>
										<th>Registro</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($bitacoras as $bitacora)
										<tr>
											<td>{{ $bitacora->id }}</td>
											<td><a href="{{ URL::route('bitacoras.show', $bitacora->id) }}">{{ $bitacora->fecha }}</a></td>
											<td>{{ $bitacora->accion }}</td>
											<td>{{ $bitacora->tabla }}</td>
											<td>{{ $bitacora->registro }}</td>				
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
            "zeroRecords":    "No se encontró ninguna unidad con ese filtro",
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