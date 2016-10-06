@extends('backend._layouts.default')

@section('main')

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
							<div class="widget-body no-padding">
								<div class="widget-body-toolbar">
									<div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">

									</div>
								</div>

								<table id="dt_basic" class="table table-hover">
									<thead>
										<tr>
											<th>NO</th>
											<th>PERIODO</th>
											<th>F_CIERRE</th>
											<th>CERRADO</th>
											<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="40px" align="right"><strong>{{ $dato->id }}</strong></td>
												<td col width="90px" align="right"><strong>{{ $dato->periodo }}</strong></td>
												<td col width="90px" align="right">{{ $dato->f_cierre }}</td>
												<td col width="90px" align="center">{{ $dato->cerrado ? 'Si' : 'No' }}</td>
												<td col width="700px" align="right">
													<ul class="demo-btns">
														@if ( $dato->cerrado == 0 )
															<li>
																<a href="{{ URL::route('hojadetrabajos.show', $dato->id) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-search"></i> Hoja de trabajo</a>
															</li>	
															<li>
																<a href="{{ URL::route('ctdiarios.show', $dato->id) }}" class="btn bg-color-green txt-color-white btn-xs"><i class="fa fa-search"></i> Diario</a>
															</li>
															<li>
																<a href="{{ URL::route('estadoderesultado', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i> Estado Resultado Proyectado</a>
															</li>				
															<li>
																<a href="{{ URL::route('balancegeneral', array($dato->id, $dato->periodo)) }}" class="btn btn-warning btn-xs"><i class="fa fa-search"></i> Balance General Proyectado</a>
															</li>
														@else
															<li>
																<a href="{{ URL::route('hojadetrabajo', $dato->id) }}" class="btn btn-default txt-color-purple btn-xs"><i class="glyphicon glyphicon-lock"></i> Hoja de trabajo</a>
															</li>	
															<li>
																<a href="{{ URL::route('diarioFinal', $dato->id) }}" class="btn btn-default txt-color-green btn-xs"><i class="glyphicon glyphicon-lock"></i> Diario</a>
															</li>
															<li>
																<a href="{{ URL::route('er', $dato->id) }}" class="btn btn-default txt-color-blue btn-xs"><i class="glyphicon glyphicon-lock"></i> Estado Resultado Final</a>
															</li>				
															<li>
																<a href="{{ URL::route('bg', $dato->id) }}" class="btn btn-default txt-color-yellow btn-xs"><i class="glyphicon glyphicon-lock"></i> Balance General Final</a>
															</li>
														@endif															
													</ul>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								<!-- Incluye la modal box -->
								@include('backend._partials.modal_confirm')
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
											<input type="text" class="datepicker" name="fecha" placeholder="Seleccione la fecha del pago de la factura ..." data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
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
	<!-- PAGE RELATED PLUGIN(S) -->
	<!-- <script src="js/plugin/datatables/jquery.dataTables-cust.min.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/jquery.dataTables-cust.min.js') }}"></script>
	
	<!-- <script src="js/plugin/datatables/ColReorder.min.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/ColReorder.min.js') }}"></script>

	<!-- <script src="js/plugin/datatables/FixedColumns.min.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/FixedColumns.min.js') }}"></script>

	<!-- <script src="js/plugin/datatables/ColVis.min.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/ColVis.min.js') }}"></script>

	<!-- <script src="js/plugin/datatables/ZeroClipboard.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/ZeroClipboard.js') }}"></script>
	
	<!-- <script src="js/plugin/datatables/media/js/TableTools.min.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/media/js/TableTools.min.js') }}"></script>
	
	<!-- <script src="js/plugin/datatables/DT_bootstrap.js"></script> -->
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
	
	<script type="text/javascript">
	// DO NOT REMOVE : GLOBAL FUNCTIONS!
	$(document).ready(function() {
		pageSetUp();
		
		/*
		 * BASIC
		 */
		$('#dt_basic').dataTable({
			"sPaginationType" : "bootstrap_full"
		});

		/* END BASIC */

		/* Add the events etc before DataTables hides a column */
		$("#datatable_fixed_column thead input").keyup(function() {
			oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $("thead input").index(this)));
		});

		$("#datatable_fixed_column thead input").each(function(i) {
			this.initVal = this.value;
		});
		$("#datatable_fixed_column thead input").focus(function() {
			if (this.className == "search_init") {
				this.className = "";
				this.value = "";
			}
		});
		$("#datatable_fixed_column thead input").blur(function(i) {
			if (this.value == "") {
				this.className = "search_init";
				this.value = this.initVal;
			}
		});		
		

		var oTable = $('#datatable_fixed_column').dataTable({
			"sDom" : "<'dt-top-row'><'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
			//"sDom" : "t<'row dt-wrapper'<'col-sm-6'i><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'>>",
			"oLanguage" : {
				"sSearch" : "Search all columns:"
			},
			"bSortCellsTop" : true
		});		

		/*
		 * COL ORDER
		 */
		$('#datatable_col_reorder').dataTable({
			"sPaginationType" : "bootstrap",
			"sDom" : "R<'dt-top-row'Clf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
			"fnInitComplete" : function(oSettings, json) {
				$('.ColVis_Button').addClass('btn btn-default btn-sm').html('Columns <i class="icon-arrow-down"></i>');
			}
		});
		
		/* END COL ORDER */

		/* TABLE TOOLS */
		$('#datatable_tabletools').dataTable({
			"sDom" : "<'dt-top-row'Tlf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
			"oTableTools" : {
				"aButtons" : ["copy", "print", {
					"sExtends" : "collection",
					"sButtonText" : 'Save <span class="caret" />',
					"aButtons" : ["csv", "xls", "pdf"]
				}],
				"sSwfPath" : "js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
			},
			"fnInitComplete" : function(oSettings, json) {
				$(this).closest('#dt_table_tools_wrapper').find('.DTTT.btn-group').addClass('table_tools_group').children('a.btn').each(function() {
					$(this).addClass('btn-sm btn-default');
				});
			}
		});

		$(function () {

		    $(".datepicker").datepicker({
		        dateFormat: 'yy-mm-dd'
		    });

		    $("#dialog").dialog({
		        autoOpen: false,
		        show: {
		            effect: "blind",
		            duration: 1000
		        },
		        hide: {
		            effect: "explode",
		            duration: 1000
		        }
		    });
		    $("#opener").click(function () {
		        $("#dialog").dialog("open");
		    });
		});

	})
	</script>
@stop