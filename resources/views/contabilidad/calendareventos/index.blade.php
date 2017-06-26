@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Reservaciones')

@section('content')
    <div class="row show-grid">
        <div class="col-xs-12 col-sm-6 col-md-12">        
            <!-- NEW WIDGET START -->
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-colorbutton="true" data-widget-fullscreenbutton="true">
                <!-- widget options:
                usage: <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">

                data-widget-colorbutton="false"
                data-widget-editbutton="false"
                data-widget-togglebutton="false"
                data-widget-deletebutton="false"
                data-widget-fullscreenbutton="false"
                data-widget-custombutton="false"
                data-widget-collapsed="true"
                data-widget-sortable="false"-->
                
								<header>
									<span class="widget-icon"> <i class="fa fa-table"></i> </span>
									<h2>Reserva de amenidades </h2>
									<div class="widget-toolbar">
										<a href="#" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
											<button class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="fa fa-plus"></i>
												 Agregar reservacion
											</button>
									</div>
								</header>

                <div><!-- widget div-->
                    <div class="jarviswidget-editbox"><!-- widget edit box -->
                        <!-- This area used as dropdown edit box -->
                    </div><!-- end widget edit box -->
                    
                    <div class="widget-body"><!-- widget content -->
                      <div class="widget-body-toolbar">
                      </div>

											<table id="dt_basic" class="table table-hover">
												<thead>
												<tr>
													<th>No</th>
													<th>UNIDAD</th>
													<th>PROPIETARIOS</th>
													<th>INICIO</th>
													<th>FIN</th>
													<th>AME</th>
													<th>ESTATUS</th>
													<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>	
												</tr>
												</thead>
												<tbody>
													@foreach ($datos as $dato)
														<tr>
															<td>{{ $dato->id }}</td>
															<td col width="80px"><strong>{{ $dato->un_id }}</strong></td>
															<td>{{ $dato->props }}</td>
															<td col width="170px">{{ $dato->start }}</td>
															<td col width="170px">{{ $dato->end }}</td>
															<td col width="25px" align="center"><strong>{{ $dato->am_id }}</strong></td>
														
                              @if ($dato->etapa == 0)
                                <td col width="60px"><span class="label label-info">Deposito</span></td>
                              @else
                                <td col width="60px"><span class="label label-success">Pagada</span></td>
                              @endif

															<td col width="80px" align="right">
																<ul class="demo-btns">
																	<li>
																		<a href="{{ URL::route('calendareventos.edit', $dato->id) }}" class="btn btn-primary btn-xs"><i class="fa fa-user"></i></a>
																	</li>
																	<li>
																		<a href="#" class="btn btn-warning btn-xs"><i class="fa fa-reply"></i></a>
																	</li>																	
																	<li>
																		<a href="#" class="btn btn-success btn-xs"><i class="fa fa-lock"></i></a>
																	</li>
																</ul>
															</td>
														</tr>
													@endforeach
												</tbody>
											</table>
                     
											@include('templates.backend._partials.modal_confirm')
                     </div><!-- end widget content -->
                </div><!-- end widget div -->
            </div>
            <!-- end widget -->
            <!-- WIDGET END -->
        </div>        
    </div>
	
		<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">Agregar reservacion</h4>
					</div>
					<div class="modal-body">

						
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'calendareventos.store')) }}
							<fieldset>
	    					{{-- {{ Form::hidden('calendarevento_id', $dato->id) }}  --}}            
								<style>

								</style>

								<div class="form-group">
									<label class="col-md-3 control-label">Unidades</label>
									<div class="col-md-9">
										{{ Form::select('un_id', ['' => 'Selecione una unidad ...'] + $uns, 0, ['class' => 'form-control']) }}
	                	<p>{!! $errors->first('un_id', '<li style="color:red">:message</li>') !!}</p> 											
									</div>
								</div>	

								<div class="form-group">
									<label class="col-md-3 control-label">Amenidades</label>
									<div class="col-md-9">
										{{ Form::select('am_id', ['' => 'Selecione una amenidad ...'] + $ams, 0, ['class' => 'form-control']) }}
										{!! $errors->first('am_id', '<li style="color:red">:message</li>') !!}
									</div>
								</div>

	              <div class="form-group">
	                <label class="col-md-3 control-label">Inicia</label>
	                <div class="col-md-9">
									
						            <div class='input-group date' id='datetimepicker6'>
						                <input type='text' name="start" class="form-control" />
						                <span class="input-group-addon">
						                    <span class="glyphicon glyphicon-calendar"></span>
						                </span>
						            </div>
	                  {!! $errors->first('start', '<li style="color:red">:message</li>') !!} 
	                
	                </div>
	              </div>  

	              <div class="form-group">
	                <label class="col-md-3 control-label">Termina</label>
	                <div class="col-md-9">
									
						            <div class='input-group date' id='datetimepicker7'>
						                <input type='text' name="end" class="form-control" />
						                <span class="input-group-addon">
						                    <span class="glyphicon glyphicon-calendar"></span>
						                </span>
						            </div>
	                  {!! $errors->first('end', '<li style="color:red">:message</li>') !!} 
	                </div>
	              </div> 
								
								<div class="form-group">
									<label class="col-md-3 control-label">Descripción</label>
									<div class="col-md-9">
						        {{ Form::textarea('descripcion', old('descripcion'),
						        	array(
						        		'class' => 'form-control',
						        		'title' => 'Escriba la descripcion',
						        		'rows' => '3',
						        		'required' => ''
						        	))
						        }}
									</div>
								</div>	

								<hr />
		            
								<!-- Multiple Radios (inline)
								<div class="form-group">
								  <label class="col-md-3 control-label" for="radios">Tipo de reservacion</label>
								  <div class="col-md-9"> 
								    <label class="radio-inline" for="radios-0">
								      <input type="radio" name="tipores_radios" id="tipodoc-1" value="1" checked="checked">
								      Solo reservacion de evento
								    </label> 
								    <label class="radio-inline" for="radios-1">
								      <input type="radio" name="tipores_radios" id="tipodoc-2" value="2">
								      Reservacion y pago completo
								    </label>
								  </div>
								</div> 
								
								<hr />-->
		            
                    <div class="form-group">
                        <label class="col-md-3 control-label">Fecha</label>
                        <div class="col-md-9">
													<div class="input-group">
														<input type="text" name="fecha" placeholder="Seleccione la fecha de la factura de egreso de caja chica!" class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													</div>
                        	<p>{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                        </div>
                    </div>  

		            <div class="form-group">
		              <label class="col-md-3 control-label">Tipo de pago</label>
		              <div class="col-md-9">
		                <select name="trantipo_id" id="trantipo_id" class="form-control" onclick="createUserJsObject.ShowtipoDePago;">
		                  @foreach ($trantipos as $trantipo)
		                    <option id="{{ $trantipo->id }}" value="{{ $trantipo->id }}">{{ $trantipo->nombre }}</option>                 
		                  @endforeach
		                </select>
		              </div>    
		            </div>
		            
		            <div class="form-group chequeNo" style=" display: none;">
		              <label class="col-md-3 control-label">Cheque No.</label>
		              <div class="col-md-9">
		                {{ Form::text('chqno', old('chqno'),
		                  array(
		                      'class' => 'form-control',
		                      'id' => 'chqno',
		                      'placeholder' => 'Escriba el numero del cheque...',
		                      'autocomplete' => 'off',
		                  ))
		                }} 
		                {!! $errors->first('chqno', '<li style="color:red">:message</li>') !!}
		              </div>
		            </div>  
		            
		            <div class="form-group transaccionNo">
		              <label class="col-md-3 control-label">Transaccion No.</label>
		              <div class="col-md-9">
		                {{ Form::text('transno', old('transno'),
		                  array(
		                      'class' => 'form-control',
		                      'id' => 'transno',
		                      'placeholder' => 'Escriba el numero de la transaccion...',
		                      'autocomplete' => 'off',
		                  ))
		                }} 
		                {!! $errors->first('transno', '<li style="color:red">:message</li>') !!}
		              </div>
		            </div>  
								
								<div class="form-group">
									<label class="col-md-3 control-label">Monto</label>
									<div class="col-md-9">
										{{ Form::text('monto', old('monto'),
											array(
											    'class' => 'form-control',
											    'id' => 'monto',
											    'placeholder' => 'Escriba el monto del deposito o alquiler...',
												'autocomplete' => 'off',
											))
										}} 
										{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
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
  	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>   

		<script type="text/javascript">
		// DO NOT REMOVE : GLOBAL FUNCTIONS!
			$(document).ready(function() {
				pageSetUp();
				
					$('#fecha').datepicker({
						prevText : '<i class="fa fa-chevron-left"></i>',
						nextText : '<i class="fa fa-chevron-right"></i>',
						onSelect : function(selectedDate) {
							$('#finishdate').datepicker('option', 'minDate', selectedDate);
						}
					});
					
					$.datepicker.regional['es'] = {
						alert('aqui');
						closeText: 'Cerrar',
						prevText: '<Ant',
						nextText: 'Sig>',
						currentText: 'Hoy',
						monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
						monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
						dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
						dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
						dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
						weekHeader: 'Sm',
						dateFormat: 'yy/mm/dd',
						firstDay: 1,
						isRTL: false,
						showMonthAfterYear: false,
						yearSuffix: ''
					};

					$.datepicker.setDefaults($.datepicker.regional['es']);
					
					$(function () {
						$("#fecha").datepicker();
					});
			})
		</script>

    <script>
      $(document).ready(function() {
		    
		    $(function () {
	        $('#datetimepicker6').datetimepicker({
      			format: 'DD/MM/YYYY hh:mm A'
	        });
	        $('#datetimepicker7').datetimepicker({
            format: 'DD/MM/YYYY hh:mm A',
            useCurrent: false //Important! See issue #1075
	        });
	        $("#datetimepicker6").on("dp.change", function (e) {
	            $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
	        });
	        $("#datetimepicker7").on("dp.change", function (e) {
	            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	        });
		    
					var trantipo_id = jQuery('#trantipo_id');
					var select = this.value;
					
					trantipo_id.change(function () {
				    if ($(this).val() == 1) {
			        $('.chequeNo').show();
			    		$('.transaccionNo').hide();
				    
				    } else if ($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4 || $(this).val() == 6 || $(this).val() == 7) {
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').show();
				    
				    } else if ($(this).val() == 5) {
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').hide();
			    
				    }	else {
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').hide();
				    }
					});

		    })

        // Setup - add a text input to each footer cell
        $('#dt_basic tfoot th').each( function () {
            var title = $('#dt_basic thead th').eq( $(this).index() ).text();
            $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
        });
      });

      $("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    });

      // DataTable
      var table = $('#dt_basic').DataTable({
          stateSave: true,
    
         "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles para esta tabla",
            "info":           "Mostrando _END_ de un total de _MAX_ reservaciones",
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

      // Restore state
      if ( state ) {
        table.columns().eq( 0 ).each( function ( colIdx ) {
          var colSearch = state.columns[colIdx].search;
          
          if ( colSearch.search ) {
            $( 'input', table.column( colIdx ).footer() ).val( colSearch.search );
          }
        });
        
        table.draw();
      }
   
      // Apply the search
      table.columns().eq( 0 ).each( function ( colIdx ) {
          $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
              table
                  .column( colIdx )
                  .search( this.value )
                  .draw();
          });
      });
  </script>
@stop