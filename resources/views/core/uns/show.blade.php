@extends('templates.backend._layouts.default')

@section('main')

<!-- NEW WIDGET START -->
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
	data-widget-sortable="false"-->

	<header>
		<span class="widget-icon"> <i class="fa fa-table"></i> </span>
		<h2>Detalles de la Unidad </h2>
		<div class="widget-toolbar">
			<a href="{{ Cache::get('indexunallkey') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
		</div>
	</header>

	<div><!-- widget div-->
		<div class="jarviswidget-editbox"><!-- widget edit box -->
			<!-- This area used as dropdown edit box -->
		</div><!-- end widget edit box -->
		
		<div class="widget-body padding"><!-- widget content -->
				<div class="row">
				  <div class="col-md-6">
					<form class="form-horizontal">
						<fieldset>
							<div class="form-group">
								<label class="col-md-4 control-label">Codigo</label>
								<div class="col-md-8">
									<input class="form-control input-lg" style="font-size:200% " name="numero" type="text" readonly value="{{ $dato->codigo }}">
								</div>
							</div>						
			
							<div class="form-group">
								<label class="col-md-4 control-label">Finca</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="finca" type="text" readonly value="{{ $dato->finca }}">
								</div>
							</div>
							
							<div class="form-group">
								<label class="col-md-4 control-label">Documento</label>
								<div class="col-md-8">
									<input class="form-control input-sm" name="documento" type="text" readonly value="{{ $dato->documento }}">
								</div>
							</div>					
							
							<div class="form-group">
								<label class="col-md-4 control-label">Características propia</label>
								<div class="col-md-8">
									{{ Form::textarea('caracteristicas', $dato->caracteristicas, array('class' => 'form-control input-sm', 'rows' => '2', 'readonly' => 'readonly')) }}
								</div>
							</div>	
							
							@if ($seccion->tipo==1) <!-- Apartamentos -->
								<div class="form-group">
									<label class="col-md-4 control-label">No de Cuartos</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuartos }}">
									</div>
								</div>						

								<div class="form-group">
									<label class="col-md-4 control-label">No de baños</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->banos }}">
									</div>
								</div>	

								<div class="form-group">
									<label class="col-md-4 control-label">Agua caliente</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->agua_caliente }}">
									</div>
								</div>											

								<div class="form-group">
									<label class="col-md-4 control-label">Estacionamientos</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->estacionamientos }}">
									</div>
								</div>						
								
								<div class="form-group">
									<label class="col-md-4 control-label">Cuota</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuota_mant }}">
									</div>
								</div>
												
								<div class="form-group">
									<label class="col-md-4 control-label">Area/m2</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->area }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 control-label">Dias de gracia</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->d_gracias }}">
									</div>
								</div>							

								<div class="form-group">
									<label class="col-md-4 control-label">Meses descuento</label>
									<div class="col-md-8">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->m_descuento }}">
									</div>
								</div>							

							@elseif ($seccion->tipo==2) <!-- Residencias -->
								<legend>Sección tipo residencias</legend>
								<div class="form-group">
									<label class="col-md-3 control-label">Avenida</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->avenida }}">
									</div>
								</div>						

								<div class="form-group">
									<label class="col-md-3 control-label">No de Cuartos</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->cuartos }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">No de baños</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->banos }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Agua caliente</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->agua_caliente }}">
									</div>
								</div>
														
								<div class="form-group">
									<label class="col-md-3 control-label">Estacionamientos</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->estacionamientos }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Cuota mantenimiento</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->cuota_mant }}">
									</div>
								</div>
							
								<div class="form-group">
									<label class="col-md-3 control-label">Area/m2</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secre->area }}">
									</div>
								</div>						

							@elseif ($seccion->tipo==3) <!-- Local comercial en edificio -->
								<legend>Sección tipo Oficina o Local comercial en edificio</legend>
								<div class="form-group">
									<label class="col-md-3 control-label">No de baños</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclced->banos }}">
									</div>
								</div>						

								<div class="form-group">
									<label class="col-md-3 control-label">Agua caliente</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclced->agua_caliente }}">
									</div>
								</div>	

								<div class="form-group">
									<label class="col-md-3 control-label">Estacionamientos</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclced->estacionamientos }}">
									</div>
								</div>	

								<div class="form-group">
									<label class="col-md-3 control-label">Cuota mantenimiento</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclced->cuota_mant }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Area/m2</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclced->area }}">
									</div>
								</div>
						
							@elseif ($seccion->tipo==4) <!-- Local comercial en residencial -->
								<legend>Sección tipo Oficina o Local comercial en residencial</legend>
								<div class="form-group">
									<label class="col-md-3 control-label">Avenida</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->avenida }}">
									</div>
								</div>						

								<div class="form-group">
									<label class="col-md-3 control-label">No de baños</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->banos }}">
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-3 control-label">Agua caliente</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->agua_caliente }}">
									</div>
								</div>
				
								<div class="form-group">
									<label class="col-md-3 control-label">Estacionamientos</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->estacionamientos }}">
									</div>
								</div>
				
								<div class="form-group">
									<label class="col-md-3 control-label">Cuota mantenimiento</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->cuota_mant }}">
									</div>
								</div>				
								
								<div class="form-group">
									<label class="col-md-3 control-label">Area/m2</label>
									<div class="col-md-9">
										<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $seclcre->area }}">
									</div>
								</div>
							@endif
						</fieldset>
					</form>
				  </div>

				  <div class="col-md-6">
						<div class="row">
						  <div class="col-md-6">
						  	<a href="{{ URL::route('indexPagos', $dato->id) }}"><img src="{{asset('assets/backend/img/rpago.png') }}" alt="" style="width:110px;height:110px;border:0;"></a>
						  </div>
						  <div class="col-md-6">
						  	<a href="{{ URL::route('ecuentas', array($dato->id, 'completo')) }}"><img src="{{asset('assets/backend/img/ecuentas.png') }}" alt="" style="width:82px;height:82px;border:0;"></a>
						  </div>
						
						</div>
						
						<h1></h1>
						
						<div class="row">
						  <div class="col-md-6">
						  	<a href="{{ URL::route('indexprops', array($dato->id, $seccion->id)) }}"><img src="{{asset('assets/backend/img/propietario.png') }}" alt="" style="width:92px;height:92px;border:0;"></a>
						  </div>
						  <div class="col-md-6">
						  	<a href="{{ URL::route('uns.edit', $dato->id) }}"><img src="{{asset('assets/backend/img/edit.png') }}" alt="" style="width:82px;height:82px;border:0;"></a>
						  </div>
						</div>
						
						<legend></legend>
						<div class="row">
						  <div class="col-md-12">
		                    <table id="dt_basic" class="table table-hover">
		                        <thead>
		                            <tr>
		                                <th>CEDULA</th>
		                                <th>NOMBRE</th>                          
		                                <th>RESPONSABLE</th>       
		                            </tr>
		                        </thead>
		                        <tbody>

		                            @foreach ($props as $prop)
		                                <tr>
		                                    <td col width="100px">{{ $prop->user->cedula }}</td>
		                                    <td>{{ $prop->user->fullname }}</td>
		                                    <td col width="10px">{{ $prop->encargado ? 'Si' : 'No' }}</td>
		                                </tr>
		                            @endforeach
		                        </tbody>
		                    </table>
						  </div>
						</div>
				  </div>
				</div>
		
		</div><!-- end widget content -->
	</div><!-- end widget div -->

</div>
<!-- end widget -->
<!-- WIDGET END -->
@stop

@section('relatedplugins') 
<!-- PAGE RELATED PLUGIN(S) -->
<script type="text/javascript">
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
})

</script>

@stop