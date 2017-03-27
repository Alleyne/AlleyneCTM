@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Registrar Factura')

@section('content')
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-12">
	
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-orange" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
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
						<h2>Pago por Caja Chica</h2>
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
							{{ Form::open(array('class' => 'form-horizontal', 'route' => 'cajachicas.store')) }}		
									<fieldset>
	 									{{ csrf_field() }}
										
                    <div class="form-group">
                        <label class="col-md-3 control-label">Fecha</label>
                        <div class="col-md-9">
													<div class="input-group">
														<input type="text" name="fecha" placeholder="Seleccione la fecha de la factura ..." class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													</div>
                        	<p>{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                        </div>
                    </div>  
										
										<hr />
										
										<!-- Multiple Radios (inline) -->
										<div class="form-group">
										  <label class="col-md-3 control-label" for="radios">Pagar a favor de:</label>
										  <div class="col-md-9"> 
										    <label class="radio-inline" for="radios-0">
										      <input type="radio" name="afavorde_radios" id="afavorde-1" value="1" checked="checked">
										      Organizaciones
										    </label> 
										    <label class="radio-inline" for="radios-1">
										      <input type="radio" name="afavorde_radios" id="afavorde-2" value="2">
										      Persona natural
										    </label>
										  </div>
										</div>

										<div class="form-group organizaciones">
											<label class="col-md-3 control-label"></label>
											<div class="col-md-9">
												{{ Form::select('org_id', ['' => 'Selecione una organizacion ...'] + $organizaciones, 0, ['class' => 'form-control']) }}
												{!! $errors->first('org_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group usuarios" style=" display: none;">
											<label class="col-md-3 control-label"></label>
											<div class="col-md-9">
												{{ Form::select('user_id', ['' => 'Selecione un usuario ...'] + $users, 0, ['class' => 'form-control']) }}
												{!! $errors->first('user_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<hr />
										
										<!-- Multiple Radios (inline) -->
										<div class="form-group">
										  <label class="col-md-3 control-label" for="radios">Tipo de documento:</label>
										  <div class="col-md-9"> 
										    <label class="radio-inline" for="radios-0">
										      <input type="radio" name="tipodoc_radios" id="tipodoc-1" value="1" checked="checked">
										      Factura
										    </label> 
										    <label class="radio-inline" for="radios-1">
										      <input type="radio" name="tipodoc_radios" id="tipodoc-2" value="2">
										      Comprobante de caja
										    </label>
										  </div>
										</div>

										<div class="form-group factura">
											<label class="col-md-3 control-label"></label>
											<div class="col-md-9">
												{{ Form::text('no', old('no'),
													array(
													    'class' => 'form-control',
													    'id' => 'no',
													    'placeholder' => 'Escriba el numero de la factura...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('no', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										
										<hr />

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

									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>
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
	
		<!-- row -->
	
		<div class="row">
	
		</div>
	
		<!-- end row -->
	
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
<!-- PAGE RELATED PLUGIN(S) -->

<script type="text/javascript">
// DO NOT REMOVE : GLOBAL FUNCTIONS!
$(document).ready(function() {
	pageSetUp();
	
	$('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
	$('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function(e) {
		var children = $(this).parent('li.parent_li').find(' > ul > li');
		if (children.is(':visible')) {
			children.hide('fast');
			$(this).attr('title', 'Expand this branch').find(' > i').removeClass().addClass('fa fa-lg fa-plus-circle');
		} else {
			children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > i').removeClass().addClass('fa fa-lg fa-minus-circle');
		}
		e.stopPropagation();
	});


	   $("#afavorde-1").click(function(){
	       $(".organizaciones").show();
	       $(".usuarios").hide();	   
	   });

	   $("#afavorde-2").click(function(){
	       $(".organizaciones").hide();
	       $(".usuarios").show();	   
	   });

	
	   $("#tipodoc-1").click(function(){
	       $(".factura").show();
	   });

	   $("#tipodoc-2").click(function(){
	       $(".factura").hide();
	   });


		$('#fecha').datepicker({
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


    $("input[type='submit']").attr("disabled", false);
    $("form").submit(function(){
      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
      return true;
    });
})

</script>
@stop