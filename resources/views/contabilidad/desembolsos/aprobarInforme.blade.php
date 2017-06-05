@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear desembolso')

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
						<h2>Aprobar Informe de Caja Chica</h2>
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
							{{ Form::open(array('class' => 'form-horizontal', 'route' => 'storeAprobarInforme')) }}		
									<fieldset>
	 									{{ csrf_field() }}
                   	{{ Form::hidden('desembolso_id', $desembolso_id) }}
										
										<div class="col-sm-12 mensaje_1">
											<div class="alert alert-block alert-warning">
												<a class="close" data-dismiss="alert" href="#">×</a>
												<h4 class="alert-heading">Atencion!</h4>
												Antes de aprobar el desembolos asegurece que el arqueo de Caja chica banlance con el efectivo en caja. Si no balancea, entoces debera registrar el monto del sobrante o faltante para que el sistema pueda hacer el ajuste pertinente.
											</div>
										</div>

										<!-- Multiple Radios (inline) -->
										<div class="form-group">
										  <label class="col-md-4 control-label" for="radios">Resultado del arqueo de Caja chica:</label>
										  <div class="col-md-8"> 
										    <label class="radio-inline" for="radios-1">
										      <input type="radio" name="arqueocc_radios" id="arqueocc-1" value="1" checked="checked">
										      Arqueo balanceado
										    </label> 
										    <label class="radio-inline" for="radios-2">
										      <input type="radio" name="arqueocc_radios" id="arqueocc-2" value="2">
										      Arqueo con faltante
										    </label>
										    <label class="radio-inline" for="radios-3">
										      <input type="radio" name="arqueocc_radios" id="arqueocc-3" value="3">
										      Arqueo con sobrante
										    </label>
										  </div>
										</div>
										
										<div class="form-group montofaltante" style="display: none;">
											<label class="col-md-3 control-label">Monto faltante</label>
											<div class="col-md-9">
												{{ Form::text('montofaltante', old('montofaltante'),
													array(
													    'class' => 'form-control',
													    'id' => 'montofaltante',
													    'placeholder' => 'Escriba monto faltante...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('montofaltante', '<li style="color:red">:message</li>') !!}
											</div>
										</div>										
										
										<div class="form-group montosobrante" style="display: none;">
											<label class="col-md-3 control-label">Monto sobrante</label>
											<div class="col-md-9">
												{{ Form::text('montosobrante', old('montosobrante'),
													array(
													    'class' => 'form-control',
													    'id' => 'montosobrante',
													    'placeholder' => 'Escriba monto sobrante...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('montosobrante', '<li style="color:red">:message</li>') !!}
											</div>
										</div>

										<hr>

										<div class="form-group">
											<label class="col-md-3 control-label">Aprobado por:</label>
											<div class="col-md-9">
												{{ Form::select('user_id', ['' => 'Selecione un aprobador ...'] + $aprobadores, 0, ['class' => 'form-control']) }}
												{!! $errors->first('user_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group factura">
											<label class="col-md-3 control-label">Cheque No. </label>
											<div class="col-md-9">
												{{ Form::text('cheque', old('cheque'),
													array(
													    'class' => 'form-control',
													    'id' => 'cheque',
													    'placeholder' => 'Escriba el numero del cheque...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('cheque', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										
										<div class="form-group">
											<label class="col-md-3 control-label">Monto del cheque</label>
											<div class="col-md-9">
												{{ Form::text('monto', old('monto'),
													array(
													    'class' => 'form-control',
													    'id' => 'monto',
													    'placeholder' => 'Escriba monto del cheque...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
									</fieldset>
									
									<div class="form-actions">
						        {{Form::button('Salvar', array(
						            'class' => 'btn btn-success btn-large',
						            'data-toggle' => 'modal',
						            'data-target' => '#confirmAction',
						            'data-title' => 'Aprovar informe de Caja chica',
						            'data-message' => 'Esta seguro(a) que desea aprobar el informe de Caja chica?',
						            'data-btntxt' => 'SI, aprobar informe',
						            'data-btncolor' => 'btn-success'
						        ))}}
										<a href="{{ URL::route('verDesembolsos', $cchica_id) }}" class="btn btn-large">Cancelar</a>
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
    <!-- Incluye la modal box -->
    @include('templates.backend._partials.modal_confirm')
	
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
<!-- PAGE RELATED PLUGIN(S) -->
<!-- Incluye javascript -->
<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>

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

	   $("#arqueocc-1").click(function(){
	      $(".montofaltante").hide();
	      $(".montosobrante").hide();
	   });

	   $("#arqueocc-2").click(function(){
	      $(".montofaltante").show();
	      $(".montosobrante").hide();
	   });
	   
	   $("#arqueocc-3").click(function(){
	      $(".montofaltante").hide();
	      $(".montosobrante").show();
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