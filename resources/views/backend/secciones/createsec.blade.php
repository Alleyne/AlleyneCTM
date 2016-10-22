@extends('backend._layouts.default')

@section('main')<!-- MAIN PANEL -->
		
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-8">
	
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
					data-widget-sortable="false"
	
					-->
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Crear una nueva Sección administrativa</h2>
	
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
											<label class="col-md-4 control-label">Sección Nombre</label>
											<div class="col-md-8">
												{{ Form::text('nombre', '', array('class' => 'form-control','title' => 'Escriba el nombre de la Seccion...', 'autocomplete' => 'off')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										
										<div class="form-group">
											<label class="col-md-4 control-label">Codigo</label>
											<div class="col-md-8">
												{{ Form::text('codigo', '', array('class' => 'form-control','title' => 'Escriba el codigo de la Seccion...', 'autocomplete' => 'off')) }}
												{!! $errors->first('codigo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										
										<div class="form-group">
											<label class="col-md-4 control-label">Descripción</label>
											<div class="col-md-8">
												{{ Form::text('descripcion', '', array('class' => 'form-control','title' => 'Escriba la descripción del Bloque administrativo...', 'autocomplete' => 'off')) }}
											    {!! $errors->first('descripcion', '<li style="color:red">:message</li>') !!}
											</div>
										</div>			

										@if ($tipo==1) <!-- Apartamentos -->
											<legend>Sección tipo apartamentos</legend>
											<div class="form-group">
												<label class="col-md-4 control-label">No de Cuartos</label>
												<div class="col-md-8">
													{{ Form::text('cuartos', '', array('class' => 'form-control','title' => 'Escriba el número de cuartos que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuartos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>										

											<div class="form-group">
												<label class="col-md-4 control-label">No de baños</label>
												<div class="col-md-8">
													{{ Form::text('banos', '', array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-4 control-label">Agua caliente</label>
												<div class="col-md-8">
													{{ Form::text('agua_caliente', '', array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
													{!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
												</div>
											</div>					
											
											<div class="form-group">
												<label class="col-md-4 control-label">No de Estacionamientos</label>
												<div class="col-md-8">
													{{ Form::text('estacionamientos', '', array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-4 control-label">Area/m2</label>
												<div class="col-md-8">
													{{ Form::text('area', '', array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
													{!! $errors->first('area', '<li style="color:red">:message</li>') !!}
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-md-4 control-label">Cuota mantenimiento</label>
												<div class="col-md-8">
													{{ Form::text('cuota_mant', '', array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-4 control-label">Recargo (%)</label>
												<div class="col-md-8">
													{{ Form::text('recargo', '', array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-4 control-label">Descuento (%)</label>
												<div class="col-md-8">
													{{ Form::text('descuento', '', array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
																																	
											<div class="form-group">
												<label class="col-md-4 control-label">Genera Orden de cobro</label>
												<div class="col-md-8">
													<input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value="1" type="text">
													<p class="text-left">Día del mes en que se registrara la facturacion mensual.</p>
													{!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-4 control-label">Aplicar recargo en mes actual o posterior</label>
												<div class="col-md-8">
													<div class="col-md-6">				
														<input class="form-control spinner-left" id="spinner4" name="m_vence" value="0" type="text">
														<p class="text-left">0= actual    1= proximo</p>
														{!! $errors->first('m_vence', '<li style="color:red">:message</li>') !!}
													</div>
													
													<div class="col-md-6">				
														<input class="form-control spinner-left" id="spinner2" name="d_vence" value="0" type="text">
														<p class="text-left">Día limite del mes para aplicar recargo</p>
														{!! $errors->first('d_vence', '<li style="color:red">:message</li>') !!}
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-4 control-label">Meses para descuento</label>
												<div class="col-md-8">
													<input class="form-control spinner-left" id="spinner3" name="m_descuento" value="0" type="text">
													<p class="text-left">Cantidad de meses que debera pagar por adelantado para obtener descuento.</p>
													{!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
										@elseif ($tipo==2) <!-- Residencias -->
											<legend>Sección tipo residencias</legend>
											<div class="form-group">
												<label class="col-md-3 control-label">Avenida</label>
												<div class="col-md-9">
													{{ Form::text('avenida', '', array('class' => 'form-control','title' => 'Escriba la avenida en donde se encuentra localizada la residencia...', 'autocomplete' => 'off')) }}
													{!! $errors->first('avenida', '<li style="color:red">:message</li>') !!}
												</div>
											</div>					

											<div class="form-group">
												<label class="col-md-3 control-label">No de Cuartos</label>
												<div class="col-md-9">
													{{ Form::text('cuartos', '', array('class' => 'form-control','title' => 'Escriba el número de cuartos que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuartos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>										

											<div class="form-group">
												<label class="col-md-3 control-label">No de baños</label>
												<div class="col-md-9">
													{{ Form::text('banos', '', array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Agua caliente</label>
												<div class="col-md-9">
													{{ Form::text('agua_caliente', '', array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
													{!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
												</div>
											</div>					
											
											<div class="form-group">
												<label class="col-md-3 control-label">No de Estacionamientos</label>
												<div class="col-md-9">
													{{ Form::text('estacionamientos', '', array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Area/m2</label>
												<div class="col-md-9">
													{{ Form::text('area', '', array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
													{!! $errors->first('area', '<li style="color:red">:message</li>') !!}
												</div>
											</div>	
											
											<div class="form-group">
												<label class="col-md-3 control-label">Cuota mantenimiento</label>
												<div class="col-md-9">
													{{ Form::text('cuota_mant', '', array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Recargo (%)</label>
												<div class="col-md-9">
													{{ Form::text('recargo', '', array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Descuento (%)</label>
												<div class="col-md-9">
													{{ Form::text('descuento', '', array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
													
											<div class="form-group">
												<label class="col-md-3 control-label">Genera Orden de cobro</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value="1" type="text">
													<p class="text-left">Día del mes en que se registrara la facturacion mensual.</p>
													{!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Dias de gracias</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner2" name="d_gracias" value="0" type="text">
													<p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
													{!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
												</div>
											</div>	
											<div class="form-group">
												<label class="col-md-3 control-label">Meses para descuento</label>
												<div class="col-md-9">
													<input class="form-control spinner-left" id="spinner3" name="m_descuento" value="0" type="text">
													<p class="text-left">Cantidad de meses que debera pagar por adelantado para obtener descuento</p>
													{!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
										@elseif ($tipo==3) <!-- Local comercial en edificio -->
											<legend>Sección tipo Oficina o Local comercial en edificio</legend>
											<div class="form-group">
												<label class="col-md-3 control-label">No de baños</label>
												<div class="col-md-9">
													{{ Form::text('banos', '', array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Agua caliente</label>
												<div class="col-md-9">
													{{ Form::text('agua_caliente', '', array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
													{!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
												</div>
											</div>					
											
											<div class="form-group">
												<label class="col-md-3 control-label">No de Estacionamientos</label>
												<div class="col-md-9">
													{{ Form::text('estacionamientos', '', array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Area/m2</label>
												<div class="col-md-9">
													{{ Form::text('area', '', array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
													{!! $errors->first('area', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Cuota mantenimiento</label>
												<div class="col-md-9">
													{{ Form::text('cuota_mant', '', array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Recargo (%)</label>
												<div class="col-md-9">
													{{ Form::text('recargo', '', array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Descuento (%)</label>
												<div class="col-md-9">
													{{ Form::text('descuento', '', array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
													
											<div class="form-group">
												<label class="col-md-3 control-label">Genera Orden de cobro</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value="1" type="text">
													<p class="text-left">Día del mes en que se registrara la facturacion mensual.</p>
													{!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Dias de gracias</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner2" name="d_gracias" value="0" type="text">
													<p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
													{!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
								
											<div class="form-group">
												<label class="col-md-3 control-label">Meses para descuento</label>
												<div class="col-md-9">
													<input class="form-control spinner-left" id="spinner3" name="m_descuento" value="0" type="text">
													<p class="text-left">Cantidad de meses que debera pagar por adelantado para obtener descuento.</p>
													{!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

										@elseif ($tipo==4) <!-- Local comercial en residencial -->
											<legend>Sección tipo Oficina o Local comercial en residencial</legend>
											<div class="form-group">
												<label class="col-md-3 control-label">Avenida</label>
												<div class="col-md-9">
													{{ Form::text('avenida', '', array('class' => 'form-control','title' => 'Escriba la avenida en donde se encuentra localizada la residencia...', 'autocomplete' => 'off')) }}
													{!! $errors->first('avenida', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">No de baños</label>
												<div class="col-md-9">
													{{ Form::text('banos', '', array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">Agua caliente</label>
												<div class="col-md-9">
													{{ Form::text('agua_caliente', '', array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
													{!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
												</div>
											</div>					
											
											<div class="form-group">
												<label class="col-md-3 control-label">No de Estacionamientos</label>
												<div class="col-md-9">
													{{ Form::text('estacionamientos', '', array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Area/m2</label>
												<div class="col-md-9">
													{{ Form::text('area', '', array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
													{!! $errors->first('area', '<li style="color:red">:message</li>') !!}
												</div>
											</div>						
											
											<div class="form-group">
												<label class="col-md-3 control-label">Cuota mantenimiento</label>
												<div class="col-md-9">
													{{ Form::text('cuota_mant', '', array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Recargo (%)</label>
												<div class="col-md-9">
													{{ Form::text('recargo', '', array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Descuento (%)</label>
												<div class="col-md-9">
													{{ Form::text('descuento', '', array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
													
											<div class="form-group">
												<label class="col-md-3 control-label">Genera Orden de cobro</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value="1" type="text">
													<p class="text-left">Día del mes en que se registrara la facturacion mensual.</p>
													{!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											
											<div class="form-group">
												<label class="col-md-3 control-label">Dias de gracias</label>
												<div class="col-md-9">
													<input class="form-control spinner-left"  id="spinner2" name="d_gracias" value="0" type="text">
													<p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
													{!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label">Meses para descuento</label>
												<div class="col-md-9">
													<input class="form-control spinner-left" id="spinner3" name="m_descuento" value="0" type="text">
													<p class="text-left">Cantidad de meses que debera pagar por adelantado para obtener descuento.</p>
													{!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>
										@elseif ($tipo==5) <!-- Local comercial en residencial -->
											<legend>Sección tipo Amenidades propias</legend>
								
										@elseif ($tipo==6) <!-- Local comercial en residencial -->
											<legend>Sección tipo Amenidades comunes</legend>										
										
										@elseif ($tipo==7) <!-- Estacionamientos públicos alquilables -->
											<legend>Sección tipo Estacionamientos públicos alquilables</legend>										
										@endif
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
		
		// PAGE RELATED SCRIPTS

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
		    start: 1,
		    numberFormat: "C"
		});  
		// Spinners
		$("#spinner3").spinner({
		    min: 0,
		    max: 360,
		    step: 1,
		    start: 1,
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
	})
</script>
@stop