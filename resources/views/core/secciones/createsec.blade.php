@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear Seccion')

@section('content')
		
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-8">
	
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
					data-widget-sortable="false"
	
					-->
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Crear una nueva Sección Administrativa</h2>
	
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
							{{ Form::open(array('class' => 'form-horizontal', 'route' => 'secciones.store')) }}		
									<fieldset>
				 						{{ csrf_field() }}
				 						{{ Form::hidden('bloque_id', $bloque_id) }}
				 						{{ Form::hidden('tipo', $tipo) }}
							
										<div class="form-group">
											<label class="col-md-4 control-label">Nombre Sección</label>
											<div class="col-md-8">
												{{ Form::text('nombre', '', array('class' => 'form-control','title' => 'Escriba el nombre de la Sección...', 'autocomplete' => 'off')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										
										{{-- <div class="form-group">
											<label class="col-md-4 control-label">Codigo</label>
											<div class="col-md-8">
												{{ Form::text('codigo', '', array('class' => 'form-control','title' => 'Escriba el codigo de la Seccion...', 'autocomplete' => 'off')) }}
												{!! $errors->first('codigo', '<li style="color:red">:message</li>') !!}
											</div>
										</div> --}}
										
										<div class="form-group">
											<label class="col-md-4 control-label">Descripción</label>
											<div class="col-md-8">
												{{ Form::text('descripcion', '', array('class' => 'form-control','title' => 'Escriba la descripción del Bloque Administrativo...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('descripcion', '<li style="color:red">:message</li>') !!}
											</div>
										</div>			

										@if ($tipo == 1) <!-- Apartamentos -->
											<legend>Sección tipo Apartamentos</legend>
											{{ Form::hidden('codigo', 'AP') }}
										@elseif ($tipo == 2) <!-- Residencias -->
											<legend>Sección tipo Residencias</legend>
											{{ Form::hidden('codigo', 'RE') }}
										@elseif ($tipo == 3) <!-- Oficinas o locales comerciales -->
											<legend>Sección tipo Oficinas o Locales Comerciales</legend>
											{{ Form::hidden('codigo', 'LC') }}
										@endif		

										<div class="form-group">
											<label class="col-md-4 control-label">Avenida</label>
											<div class="col-md-8">
												{{ Form::text('avenida', '', array('class' => 'form-control','title' => 'Escriba la avenida en donde se encuentra localizada la unidad...', 'autocomplete' => 'off')) }}
												{!! $errors->first('avenida', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										
										@if ($tipo != 3) <!-- Oficinas o locales comerciales -->
											<div class="form-group">
												<label class="col-md-4 control-label">No. de cuartos</label>
												<div class="col-md-8">
													{{ Form::text('cuartos', '', array('class' => 'form-control','title' => 'Escriba el número de cuartos que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuartos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>										
										@endif	

										<div class="form-group">
											<label class="col-md-4 control-label">No. de baños</label>
											<div class="col-md-8">
												{{ Form::text('banos', '', array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
											</div>
										</div>

										{{-- <div class="form-group">
											<label class="col-md-4 control-label">Agua caliente</label>
											<div class="col-md-8">
												{{ Form::text('agua_caliente', '', array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
												{!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	 --}}				

										<div class="form-group">
											<label class="col-md-4 control-label">Agua caliente</label>
											<div class="col-md-8">
												{{ Form::checkbox('agua_caliente') }}
											</div>
										</div>	
										
										<div class="form-group">
											<label class="col-md-4 control-label">No. de Estacionamientos</label>
											<div class="col-md-8">
												{{ Form::text('estacionamientos', '', array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Área/m2</label>
											<div class="col-md-8">
												{{ Form::text('area', '', array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
												{!! $errors->first('area', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										
										<div class="form-group">
											<label class="col-md-4 control-label">Cuota mantenimiento</label>
											<div class="col-md-8">
												{{ Form::text('cuota_mant', '', array('class' => 'form-control','title' => 'Escriba la cuota de mantenimiento mensual...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Recargo (%)</label>
											<div class="col-md-8">
												{{ Form::text('recargo', '', array('class' => 'form-control','title' => 'Escriba el recargo a cobrar por atraso en pago de la cuota de mantenimiento mensual...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Descuento (%)</label>
											<div class="col-md-8">
												{{ Form::text('descuento', '', array('class' => 'form-control','title' => 'Escriba el descuento por pagos adelantados en la cuota de mantenimiento mensual...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
																																
										<div class="form-group">
											<label class="col-md-4 control-label">Genera Orden de Cobro</label>
											<div class="col-md-8">
												<input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value="1" type="text">
												<p class="text-left">Día del mes en que se registrará la facturación mensual.</p>
												{!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Aplicar recargo en mes actual o posterior</label>
											<div class="col-md-8">
												<div class="col-md-6">				
													<input class="form-control spinner-left" id="spinner4" name="m_vence" value="0" type="text">
													<p class="text-left">0= actual    1= próximo</p>
													{!! $errors->first('m_vence', '<li style="color:red">:message</li>') !!}
												</div>
												
												<div class="col-md-6">				
													<input class="form-control spinner-left" id="spinner2" name="d_vence" value="10" type="text">
													<p class="text-left">Día límite del mes para aplicar recargo</p>
													{!! $errors->first('d_vence', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Meses para Descuento</label>
											<div class="col-md-8">
												<input class="form-control spinner-left" id="spinner3" name="m_descuento" value="12" type="text">
												<p class="text-left">Cantidad de meses que debe pagar por adelantado para obtener descuento.</p>
												{!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Cuota extraordinaria</label>
                        <div class="col-md-8">
                
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" id='f_iniciaextra' name="f_iniciaextra" placeholder="Fecha en que inicia el cobro de la cuota extraordinaria..." class="form-control datepicker" data-dateformat="yy/mm/dd">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                                {!! $errors->first('f_iniciaextra', '<li style="color:red">:message</li>') !!}</p> 
                            </div>
                        </div> 
                    </div>  

                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-8">
                            <div class="col-md-6">              
                                <input class="form-control spinner-left" id="spinner5" name="extra_meses"  value="1" type="text">
                                <p class="text-left">Meses en que se divide el pago</p>
                                {!! $errors->first('extra_meses', '<li style="color:red">:message</li>') !!}
                            </div>
                            <div class="col-md-6">              
                                <input class="form-control" name="extra" type="text">
                                <p class="text-left">Monto de la cuota extraordinaria</p>
                                {!! $errors->first('extra', '<li style="color:red">:message</li>') !!}
                            </div>
                        </div>
                    </div>  
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
                      <a href="{{ URL::route('indexsecplus', $bloque_id) }}" class="btn btn-large">Cancelar</a>
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
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-4">
	
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-editbutton="false" data-widget-deletebutton="false">
					<!-- widget options:
					usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
	
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
						<span class="widget-icon"> <i class="fa fa-sitemap"></i> </span>
						<h2>Simple View </h2>
	
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
							Aquí va cualquier cosa.
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
<!-- PAGE RELATED PLUGIN(S) -->

<script type="text/javascript">

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
		
	    // Spinners
	    $("#spinner1").spinner({
	        min: 1,
	        max: 16,
	        step: 15,
	        start: 1,
	        numberFormat: "C"
	    });

		// Spinners
		$("#spinner2").spinner({
		    min: 1,
		    max: 31,
		    step: 1,
		    start: 10,
		    numberFormat: "C"
		});  
		// Spinners
		$("#spinner3").spinner({
		    min: 0,
		    max: 360,
		    step: 1,
		    start: 12,
		    numberFormat: "C"
		}); 
		// Spinners
		$("#spinner4").spinner({
		    min: 0,
		    max: 1,
		    step: 1,
		    start: 0,
		    numberFormat: "C"
		}); 
		// Spinners
		$("#spinner5").spinner({
		    min: 1,
		    max: 24,
		    step: 1,
		    start: 1,
		    numberFormat: "C"
		}); 	

	})
</script>
@stop