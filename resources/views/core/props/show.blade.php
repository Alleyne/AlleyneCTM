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
		<h2>Datos del Bloque administrativo </h2>
	</header>
	
	<div><!-- widget div-->
		
		<div class="jarviswidget-editbox"><!-- widget edit box -->
			<!-- This area used as dropdown edit box -->
		</div><!-- end widget edit box -->
		
		<div class="widget-body"><!-- widget content -->
			<form class="form-horizontal">
				<fieldset>
					<div class="form-group">
						<label class="col-md-2 control-label">Nombre</label>
						<div class="col-md-10">
							<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $dato->nombre }}">
						</div>
					</div>					

					<div class="form-group">
						<label class="col-md-2 control-label">Descripción</label>
						<div class="col-md-10">
							{{ Form::textarea('descripcion', $dato->descripcion, array('class' => 'form-control input-sm', 'rows' => '2', 'readonly' => 'readonly')) }}
						</div>
					</div>					
				</fieldset>
				
				<div class="form-actions">
				   <a href="{{ URL::route('indexPlusJdBloques', $dato->jd_id) }}" class="btn btn-primary btn-large">Regresar</a>
				</div>			
			</form>
		</div><!-- end widget content -->
	</div><!-- end widget div -->
</div><!-- end widget -->
<!-- WIDGET END -->