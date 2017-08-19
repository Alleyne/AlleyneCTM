@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear Pago')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-12">
	
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
					<!-- widget options:
					usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
	
					data-widget-colorbutton="false"
					data-widget-editbutton="false"
					data-widget-togglebutton="false"
					data-widget-deletebutton="false"
					data-widget-fullscreenbutton="false"
					data-widget-custombutton="false"
					data-widget-collapsed="true"
					data-widget-sortable="false" -->
	
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Registrar pagos tipo Banca en línea, Tarjetas de Débito y Crédito</h2>
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
							{{ Form::open(array('class' => 'form-horizontal', 'route' => 'pagos.store', 'data-parsley-validate' => '')) }}		
									<fieldset>

	 									{{ Form::hidden('un_id', $un_id) }}
	 									{{ Form::hidden('key', $key) }}
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label">Fecha de pago</label>
                        <div class="col-md-9">
												<div class="input-group">
													<input type="text" id="f_pago" name="f_pago" placeholder="Seleccione la fecha en que se hizo efectivo el pago ..." class="form-control datepicker" required="" value="{{ old('f_pago') }}">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
	                      </div>
	                  </div>  

										<div class="form-group">
											<label class="col-md-3 control-label">Banco</label>
											<div class="col-md-9">
												{{ Form::select('banco_id', ['' => 'Selecione una Institución Bancaria ...'] + $bancos, 0, ['class' => 'form-control', 'required' => '']) }}
											</div>
										</div>		

										<div class="transNo form-group">
											<label class="col-md-3 control-label">Transacción No.</label>
											<div class="col-md-9">
												{{ Form::text('transno', old('transno'),
													array(
												    'class' => 'form-control',
												    'id' => 'transno',
												    'placeholder' => 'Escriba el número de la transacción...',
														'autocomplete' => 'off',
														'data-parsley-type'=>'digits',
														'minlength '=>'1',
														'maxlength '=>'10',
														'data-parsley-error-message'=>'mi mensaje para el campo trans no'
													))
												}} 
											</div>
										</div>	
					
										<div class="form-group">
											<label class="col-md-3 control-label">Monto</label>
											<div class="col-md-9">
												{{ Form::text('monto', old('monto'),
													array(
												    'class' => 'form-control',
												    'id' => 'monto',
												    'placeholder' => 'Escriba el monto recibido ...',
														'autocomplete' => 'off',
														'required' => '',
														'data-parsley-pattern'=>'^[0-9]*\.[0-9]{2}$'
													))
												}} 
											</div>
										</div>					

										<div class="form-group">
											<label class="col-md-3 control-label">Descripción</label>
											<div class="col-md-9">
								        {{ Form::textarea('descripcion', old('descripcion'),
								        	array(
								        		'class' => 'form-control',
								        		'title' => 'Escriba la descripción',
								        		'rows' => '3',
								        		'required' => ''
								        	))
								        }}
											</div>
										</div>				
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::route('indexPagos', $un_id) }}" class="btn btn-large">Cancelar</a>
									</div>
								{{ Form::close() }}
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
	<script src="{{ URL::asset('assets/backend/js/libs/jquery-ui-1.10.3.min.js') }}"></script>
	
	{!! Html::script('js/parsley.min.js') !!}

	<script type="text/javascript">
		$(document).ready(function(){
		    $("input[type='submit']").attr("disabled", false);
		    $("form").submit(function(){
		      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envía la información . . .");
		      return true;
		    })
		})
	
		$('#f_pago').datepicker({
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
			onSelect : function(selectedDate) {
				$('#finishdate').datepicker('option', 'minDate', selectedDate);
			}
		});
		
		$.datepicker.regional['es'] = {
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
	
		var tipoDePago = jQuery('#trantipo_id');
		var select = this.value;
		tipoDePago.change(function () {
		    if ($(this).val() == '1') {
		      $('.bancos').show();		
		      $('.transNo').hide();	
		      $('.quecheNo').show();			    
		    
		    } else if ($(this).val() == '2' || $(this).val() == '3'|| $(this).val() == '4') {
		      $('.bancos').show();		    	
		      $('.transNo').show();	
		      $('.quecheNo').hide();	

		    } else if ($(this).val() == '5') {
		      $('.bancos').hide();		    	
		      $('.transNo').hide();	
		      $('.quecheNo').hide();	

		    } else if ($(this).val() == '6') {
		      $('.bancos').show();		    	
		      $('.transNo').hide();	
		      $('.quecheNo').hide();	
		    }		
		});

	</script>
@stop