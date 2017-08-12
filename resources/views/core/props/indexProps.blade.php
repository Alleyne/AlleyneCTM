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
							<h2>Propietarios </h2>
							<div class="widget-toolbar">
								<a href="{{ URL::route('uns.show', $un_id) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
								<a href="{{ URL::route('createprop', array($un_id, $seccione_id)) }}" class="btn btn-success"><i class="fa fa-plus"></i> Vincular Propietario</a>
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
											<th>NOMBRE</th>
											<th>ENCARGADO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td>{{ $dato->user->nombre_completo }}</td>
												<td col width="30px">{{ $dato->encargado ? 'Si' : 'No' }}</td>
												<td col width="150px" align="center">
													<ul class="demo-btns">
														<li>
											        {{Form::open(array(
											            'route' => array('desvincularprop', $dato->user_id, $dato->un_id),
											            'method' => 'GET',  // or DELETE
											            'style' => 'display:inline'
											        ))}}

											        {{Form::button('Desvincular', array(
											            'class' => 'btn btn-warning btn-xs',
											            'data-toggle' => 'modal',
											            'data-target' => '#confirmAction',
											            'data-title' => 'Desvincular propietario',
											            'data-message' => 'Esta seguro(a) que desea desvincular este propietario?',
											            'data-btntxt' => 'SI, desvincular',
											            'data-btncolor' => 'btn-success'
											        ))}}
											        {{Form::close()}}  
														</li>															
														<li>
															<a href="{{ URL::route('users.show', $dato->user_id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
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
@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>
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