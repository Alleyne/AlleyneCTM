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
						<h2>Pagar Facturas </h2>
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
											<th col width="25px">NUMERO</th>
											<th>PROVEEDOR</th>
											<th col width="70px">FECHA</th>
											<th col width="20px">TOTAL FAC</th>
											<th col width="20px">TOTAL PDO</th>
											<th col width="185px" class="text-center"><i class="fa fa-gear fa-lg"></i></th>							
									</tr>
								</thead>
								<tbody>
									@foreach ($datos as $dato)
											<tr>
												<td>{{ $dato->id }}</td>
												<td>{{ $dato->afavorde }}</td>
												<td>{{ $dato->fecha }}</td>
												<td>{{ $dato->total }}</td>
											
												@if ($dato->total == $dato->totalpagodetalle)
													<td>{{ $dato->totalpagodetalle }}</td>
												@else
													<td><mark>{{ $dato->totalpagodetalle }}</mark></td>
												@endif

												<td align="right">
													<ul class="demo-btns">
														@if ($dato->pagada == 0)
															<li>
																<span class="label label-warning">Pago pendiente<span>
															</li>
															<li>
																<a href="{{ URL::route('detallepagofacturas.show', $dato->id) }}" class="btn btn-info btn-xs"> Programar pagos</a>
															</li>										

														@elseif ($dato->pagada == 1)
															<li>
																<span class="label label-success">Factura pagada</span>
															</li>
															<li>
																<a href="{{ URL::route('detallepagofacturas.show', $dato->id) }}" class="btn btn-success btn-xs"> Ver Programación</a>
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

  <script type="text/javascript">
    $(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "393px",
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