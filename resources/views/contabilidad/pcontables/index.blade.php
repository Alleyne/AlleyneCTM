@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Periodos contables')

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
							<h2>Periodos contables </h2>
							<div class="widget-toolbar">
								@if (!$datos->count())
									<button class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
										 Crear primer periodo contable
									</button>
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
							<div class="widget-body">
								<div class="widget-body-toolbar">
									<div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">

									</div>
								</div>

								<table id="dt_basic2" class="table table-hover">
									<thead>
										<tr>
											<th>NO</th>
											<th>PERIODO</th>
											<th>F_CIERRE</th>
											<th>CDO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="20px" align="right"><strong>{{ $dato->id }}</strong></td>
												<td col width="70px" align="right"><strong>{{ $dato->periodo }}</strong></td>
												<td col width="70px" align="right">{{ $dato->f_cierre }}</td>
												<td col width="50px" align="center">{{ $dato->cerrado ? 'Si' : 'No' }}</td>
												<td col width="510px" align="right">
													<ul class="demo-btns">
														@if ( $dato->cerrado == 0 )
															<li>
																<div class="btn-group">
																	<button class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">
																		<span class="glyphicon glyphicon-usd"></span> Manejo del efectivo <span class="caret"></span>
																	</button>
																	<ul class="dropdown-menu">
																		<li>
																			<a href="{{ URL::route('diariocajas.index') }}">Diario de Caja General</a>
																		</li>
																		<li class="divider"></li>																					
																		<li>
																			<a href="javascript:void(0);">Diario de Caja Chica</a>
																		</li>
																		<li class="divider"></li>	
																		<li>
																			<a href="javascript:void(0);">Conciliación Bancaria</a>
																		</li>
																	</ul>
																</div>
															</li>	
															<li>
																<a href="{{ URL::route('hojadetrabajos.show', $dato->id) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-search"></i> HT</a>
															</li>	
															<li>
																<a href="{{ URL::route('ctdiarios.show', $dato->id) }}" class="btn bg-color-green txt-color-white btn-xs"><i class="fa fa-search"></i> Diario</a>
															</li>
															<li>
																<a href="{{ URL::route('estadoderesultado', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i> ER Proyectado</a>
															</li>				
															<li>
																<a href="{{ URL::route('balancegeneral', array($dato->id, $dato->periodo)) }}" class="btn btn-warning btn-xs"><i class="fa fa-search"></i> BG Proyectado</a>
															</li>
														@else
															<li>
																<a href="{{ URL::route('hojadetrabajo', $dato->id) }}" class="btn btn-default txt-color-purple btn-xs"><i class="glyphicon glyphicon-lock"></i> HT</a>
															</li>	
															<li>
																<a href="{{ URL::route('diarioFinal', $dato->id) }}" class="btn btn-default txt-color-green btn-xs"><i class="glyphicon glyphicon-lock"></i> Diario</a>
															</li>
															<li>
																<a href="{{ URL::route('er', $dato->id) }}" class="btn btn-default txt-color-blue btn-xs"><i class="glyphicon glyphicon-lock"></i> ER Final</a>
															</li>				
															<li>
																<a href="{{ URL::route('bg', $dato->id) }}" class="btn btn-default txt-color-yellow btn-xs"><i class="glyphicon glyphicon-lock"></i> BG Final</a>
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
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">Crear Periodo contable inicial</h4>
					</div>
					<div class="modal-body">
		
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'pcontables.store')) }}
							<fieldset>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Fecha</label>
                                    <div class="col-md-9">
										<div class="input-group">
											<input type="text" name="fecha" placeholder="Entre la fecha del primer periodo a registrar (aaaa/mm/dd)" ..." class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										</div>
                                    	{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                                    </div>
                                </div>  
							</fieldset>				
							
							<div class="form-actions">
								{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
								<button type="button" class="btn btn-default" data-dismiss="modal">
									Cancel
								</button>
							</div>
						{{ Form::close() }}
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
@stop

@section('relatedplugins')
    <script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#dt_basic2 tfoot th').each( function () {
                var title = $('#dt_basic2 thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            } );
         
            // DataTable
            var table = $('#dt_basic2').DataTable( {
                stateSave: true,
          
                 "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay datos disponibles para esta tabla",
                    "info":           "Mostrando _END_ de un total de _MAX_ periodos",
                    "infoEmpty":      "",
                    "infoFiltered":   "",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ periodos",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encontro ningun periodo con ese filtro",
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
            } );           

            // Restore state
            if ( state ) {
              table.columns().eq( 0 ).each( function ( colIdx ) {
                var colSearch = state.columns[colIdx].search;
                
                if ( colSearch.search ) {
                  $( 'input', table.column( colIdx ).footer() ).val( colSearch.search );
                }
              } );
              
              table.draw();
            }
         
            // Apply the search
            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
                    table
                        .column( colIdx )
                        .search( this.value )
                        .draw();
                } );
            } );
        } );
     
    	$("#fecha").datepicker({
	        dateFormat: 'yy-mm-dd'
	    });

	    $("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    });
     </script>
@stop