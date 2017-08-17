<!-- NEW WIDGET START -->
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget jarviswidget-color-orange" id="wid-id-1" data-widget-editbutton="false" data-widget-deletebutton="false">
	<!-- widget options:
	usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

	data-widget-colorbutton="false"
	data-widget-editbutton="false"
	data-widget-togglebutton="false"
	data-widget-deletebutton="false"
	data-widget-fullscreenbutton="false"
	data-widget-custombutton="false"
	data-widget-collapsed="true"
	data-widget-sortable="false"-->

	<header>
		<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
		<h2>Datos de la Sección Administrativa</h2>
	</header>
	
	<div><!-- widget div-->
		<div class="jarviswidget-editbox"><!-- widget edit box -->
			<!-- This area used as dropdown edit box -->
		</div><!-- end widget edit box -->

		<div class="row show-grid">
		    <div class="col-xs-12 col-sm-12 col-md-12">
				<div class="widget-body"><!-- widget content -->
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-md-3 control-label">Nombre Sección</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->nombre }}">
								</div>
							</div>						

							<div class="form-group">
								<label class="col-md-3 control-label">Descripción</label>
								<div class="col-md-9">
									{{ Form::textarea('descripcion', $seccion->descripcion, array('class' => 'form-control input-sm', 'rows' => '2', 'readonly' => 'readonly')) }}
								</div>
							</div>	

							@if ($seccion->tipo== 1) <!-- Apartamentos -->
								<legend>Sección tipo Apartamentos</legend>
							@elseif ($seccion->tipo== 2) <!-- Residencias -->
								<legend>Sección tipo Residencias</legend>
							@elseif ($seccion->tipo== 3) <!-- Oficinas o locales comerciales -->
								<legend>Sección tipo Oficinas o Locales Comerciales</legend>
							@endif

							@if ($seccion->tipo != 3) <!-- Oficinas o locales comerciales -->
								<div class="form-group">
									<label class="col-md-3 control-label">No. de cuartos</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->cuartos }}">
									</div>
								</div>						
							@endif
							
							<div class="form-group">
								<label class="col-md-3 control-label">No. de baños</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->banos }}">
								</div>
							</div>	

							<div class="form-group">
								<label class="col-md-3 control-label">Agua caliente</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->agua_caliente  == 0 ? 'No' : 'Si' }}">
								</div>
							</div>											

							<div class="form-group">
								<label class="col-md-3 control-label">Estacionamientos</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->estacionamientos }}">
								</div>
							</div>						
							
							<div class="form-group">
								<label class="col-md-3 control-label">Cuota mantenimiento</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->cuota_mant }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Genera Orden de Cobro el día</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->d_registra_cmpc }}">
								</div>
							</div>				
							
							<div class="form-group">
								<label class="col-md-3 control-label">Área/m2</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->area }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Aplica recargo</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->m_vence == 0 ? 'Mes corriente' : 'Proximo mes' }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-3 control-label">Después del día</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->d_vence }}">
								</div>
							</div>							

							<div class="form-group">
								<label class="col-md-3 control-label">Meses para descuento</label>
								<div class="col-md-9">
									<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seccion->secapto->m_descuento }}">
								</div>
							</div>	
						</fieldset>
					
						<div class="form-actions">
				   			<a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-primary btn-large">Regresar</a>
						</div>			
					</form>
				
				</div><!-- end widget content -->	
			</div>
		</div>
	</div><!-- end widget div -->
</div><!-- end widget -->
<!-- WIDGET END -->