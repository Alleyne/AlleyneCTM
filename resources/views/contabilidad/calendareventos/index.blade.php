@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Reservaciones')

@section('content')
    <style type="text/css">
	    .progress {
	     margin-bottom: 0px;     	
    	}
    </style>

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
									<h2>Reservaciones y alquileres de amenidades </h2>
									<div class="widget-toolbar">
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
													<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>
													<th>No</th>
													<th>UNIDAD</th>
													<th>PROPIETARIOS</th>
													<th>INICIO</th>
													<th>FIN</th>
													<th>AME</th>
													<th>ESTATUS</th>
												</tr>
												</thead>
												<tbody>
													@foreach ($datos as $dato)
														<tr>
															<td col width="70px">
																<div class="btn-group">
																	@if ($dato->status != 5)
																		<button class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
																			Accion <span class="caret"></span>
																		</button>
																	@else
																		<button class="btn btn-primary btn-xs dropdown-toggle disabled" data-toggle="dropdown">
																			Accion <span class="caret"></span>
																		</button>
																	@endif
																		<ul class="dropdown-menu">
																			{{-- seccion de editar reservaciones --}}
																			<li>
																				<a href="{{ URL::route('calendareventos.edit', $dato->id) }}">Editar reservacion</a>
																			</li>
																			
																			{{-- seccion de acciones de proxima etapa --}}
																			<li class="divider"></li>
																				@if ($dato->status == 1)
																					<li>
																						<a href="{{ URL::route('eventoAlquiler', $dato->id) }}">Registrar pago por alquiler</a>
																					</li>
																				@endif
																				@if ($dato->status == 3)
																					<li>
																						<a href="{{ URL::route('eventoDevolucion', [$dato->id, 0]) }}">Devolver deposito por culminacion</a>
																					</li>
																				@endif


																			{{-- seccion de recibos --}}
																			<li class="divider"></li>
																				@if ($dato->status == 1)
																					<li>
																						<a href="{{ URL::route('showRecibo', $dato->res_pago_id) }}">Ver recibo de deposito</a>
																					</li>
																				@endif
																				@if ($dato->status == 2)
																					<li>
																						<a href="{{ URL::route('showRecibo', $dato->res_pago_id) }}">Ver recibo de deposito</a>
																					</li>
																					<li>
																						<a href="{{ URL::route('showRecibo', $dato->pc_pago_id) }}">Ver recibo de alquiler</a>
																					</li>
																				@endif
																				@if ($dato->status == 3 || $dato->status == 4)
																					<li>
																						<a href="{{ URL::route('showRecibo', $dato->res_pago_id) }}">Ver recibo de deposito</a>
																					</li>
																					<li>
																						<a href="{{ URL::route('showRecibo', $dato->pc_pago_id) }}">Ver recibo de alquiler</a>
																					</li>
																					<li>
																						<a href="#">Ver recibo de devolucion de deposito por culminacion</a>
																					</li>
																				@endif

																			

																			{{-- seccion de cancelacion de reservaciones --}}
																			<li class="divider"></li>
																				@if ($dato->status == 1)
																					<li>
																						<a href="{{ URL::route('eventoDevolucion', [$dato->id, 1]) }}" style="color:red">Cancelar reservacion y devolver deposito</a>
																					</li>										
																				@endif
																				@if ($dato->status == 2 || $dato->status == 3)
																					<li>
																						<a href="{{ URL::route('eventoDevolucion', [$dato->id, 1]) }}" style="color:red">Cancelar reservacion y devolver deposito mas alquiler</a>
																					</li>										
																				@endif

																		</ul>
																</div>
															</td>
															<td>{{ $dato->id }}</td>
															<td col width="80px"><strong>{{ $dato->un_id }}</strong></td>
															<td>{{ $dato->props }}</td>
															<td col width="140px">{{ $dato->start }}</td>
															<td col width="140px">{{ $dato->end }}</td>
															<td col width="25px" align="center"><strong>{{ $dato->am_id }}</strong></td>
														
                              @if ($dato->status == 1)
			                        <td>      
	                              {{-- <td>
																	<div class="progress">
																		<div class="progress-bar bg-color-teal" aria-valuetransitiongoal="25"></div>
																	</div>
																</td> --}}																
																<div class="progress progress-sm">
																	<div class="progress-bar bg-color-yellow" style="width: 20%"></div>
																</div>
															</td>
                              
                              @elseif ($dato->status == 2)
				                        <td>      
																	<div class="progress progress-sm">
																		<div class="progress-bar bg-color-purple" style="width: 50%"></div>
																	</div>
																</td>
                              
                              @elseif ($dato->status == 3)
				                        <td>      
																	<div class="progress progress-sm">
																		<div class="progress-bar bg-color-green" style="width: 80%"></div>
																	</div>
																</td>
                              
                              @elseif ($dato->status == 4)
				                        <td>      
																	<div class="progress progress-sm">
																		<div class="progress-bar bg-color-darken" style="width: 100%"></div>
																	</div>
																</td>
                              @else
																<td>
                              		Cancelado
                              	</td>
                              @endif
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
						<style type="text/css">
							    	.datetimepicker { position: relative; z-index: 10000 !important; }
						</style>
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'calendareventos.store')) }}
							<fieldset>
	    					{{-- {{ Form::hidden('calendarevento_id', $dato->id) }}  --}}            
								
								<div class="alert alert-info fade in">
									<button class="close" data-dismiss="alert">
										×
									</button>
									<i class="fa-fw fa fa-warning"></i>
									<strong>Atencion: </strong> Para poder registrar una nueva reservacion en el sistema, el propietario debera pagar por adelantado un deposito de garantia y entregar comprobante de pago.
								</div>

               {{--  <div class="form-group">
                    <label class="col-md-3 control-label">Fecha</label>
                    <div class="col-md-9">
											<div class="input-group">
												<input type="text" name="fecha" placeholder="Fecha en que se efectuo el deposito de garantia!" class="form-control" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
                    	<p>{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                    </div>
                </div> --}}
								
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
						                <input type='text' name="start" class="form-control" placeholder="Fecha de inicio del evento" />
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
						                <input type='text' name="end" class="form-control" placeholder="Fecha de finalizacion del evento" />
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
						        		'placeholder' => 'Motivo por el cual se reservan las amenidades',
						        		'rows' => '3',
						        		'required' => ''
						        	))
						        }}
									</div>
								</div>	

								<hr />
	              
	              <div class="form-group">
	                <label class="col-md-3 control-label">Fecha</label>
	                <div class="col-md-9">
									
						            <div class='input-group date' id='datetimepicker8'>
						                <input type='text' name="fecha" class="form-control" placeholder="Fecha en que se efectuo el deposito de garantia!" />
						                <span class="input-group-addon">
						                    <span class="glyphicon glyphicon-calendar"></span>
						                </span>
						            </div>
	                  {!! $errors->first('fecha', '<li style="color:red">:message</li>') !!} 
	                
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
								
								<div class="bancos form-group">
									<label class="col-md-3 control-label">Banco</label>
									<div class="col-md-9">
										{{ Form::select('banco_id', ['' => 'Selecione una Institucion Bancaria ...'] + $bancos, 0, ['class' => 'form-control']) }}
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
								
								{{-- <div class="form-group">
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
								</div> --}}
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
    <script src="{{ URL::asset('assets/fullcalendar340/lib/moment.min.js') }}"></script>
    



    <script src="{{ URL::asset('assets/backend/js/datetimepicker/bootstrap-datetimepicker-4.17.47.min.js') }}"></script>
  	
  	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>   
  	<script src="{{ URL::asset('assets/backend/js/plugin/bootstrap-progressbar/bootstrap-progressbar.js') }}"></script> 

    <script>
      $(document).ready(function() {
		    
		    $(function () {

	        // Fill all progress bars with animation
					$('.progress-bar').progressbar({
						display_text : 'fill'
					});

	        var dateToday = new Date();
	        $('#datetimepicker6').datetimepicker({
      			format: 'DD/MM/YYYY hh:mm A',
      			stepping: 30,
      			minDate: dateToday
	        });

	        $('#datetimepicker7').datetimepicker({
            format: 'DD/MM/YYYY hh:mm A',
            stepping: 30,
            useCurrent: false //Important! See issue #1075
	        });

	        $('#datetimepicker8').datetimepicker({
            format: 'DD/MM/YYYY',
            useCurrent: false //Important! See issue #1075
	        });	        

	        $("#datetimepicker6").on("dp.change", function (e) {
	            $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
	        });
	        
	        $("#datetimepicker7").on("dp.change", function (e) {
	            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	        });
		    
	        $("#datetimepicker8").on("dp.change", function (e) {
	            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	        });					

					var trantipo_id = jQuery('#trantipo_id');
					var select = this.value;
					
					trantipo_id.change(function () {
				    if ($(this).val() == 1) {
			        $('.bancos').show();
			        $('.chequeNo').show();
			    		$('.transaccionNo').hide();
				    
				    } else if ($(this).val() == 5) {
				    	$('.bancos').hide();
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').hide();
			    
				    }	else {
				    	$('.bancos').show();
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').show();
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
      $('#dt_basic').DataTable({
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
      })          
  </script>
@stop