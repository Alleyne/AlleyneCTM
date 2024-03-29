@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Editar Unidad')

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
					data-widget-sortable="false"
	
					-->
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Editar datos de la Unidad Administrada</h2>
	
					</header>
	
					<!-- widget div-->
					<div>
	
						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
	
						</div>
						<!-- end widget edit box -->
						
						<div class="row show-grid">
						    <div class="col-xs-12 col-sm-6 col-md-6">		
								<div class="widget-body"><!-- widget content -->
								{{ Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('uns.update', $dato->id))) }}
										{{ csrf_field() }}
                    {{ Form::hidden('seccione_id', $seccion->id) }}
										<fieldset>
											<div class="form-group">
												<label class="col-md-3 control-label">Código</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="numero" type="text" readonly value="{{ $dato->codigo }}">
												</div>
											</div>											

											<div class="form-group">
												<label class="col-md-3 control-label">Finca</label>
												<div class="col-md-9">
													{{ Form::text('finca', $dato->finca, array('class' => 'form-control','title' => 'Escriba el número de finca...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('finca', '<li style="color:red">:message</li>') !!}
												</div>
											</div>			
											<div class="form-group">
												<label class="col-md-3 control-label">Documento</label>
												<div class="col-md-9">
													{{ Form::text('documento', $dato->documento, array('class' => 'form-control','title' => 'Escriba el número del documento...', 'autocomplete' => 'off')) }}
												    {!! $errors->first('documento', '<li style="color:red">:message</li>') !!}
												</div>
											</div>			
											
											<div class="form-group">
												<label class="col-md-3 control-label">Características propias</label>
												<div class="col-md-9">
													{{ Form::textarea('caracteristicas', $dato->caracteristicas, array('class' => 'form-control input-sm', 'rows' => '2', 'title' => 'Escriba las características propias de la unidad...')) }}
												    {!! $errors->first('caracteristicas', '<li style="color:red">:message</li>') !!}
												</div>
											</div>													
											<div class="form-group">
												<label class="col-md-3 control-label">Activa</label>
												<div class="col-md-9">
													{{ Form::checkbox('activa') }}
												</div>
											</div>	
											
											@if ($seccion->tipo == 'AP') <!-- Apartamentos -->
												<legend>Sección tipo Apartamentos</legend>
											@elseif ($seccion->tipo == 'RE') <!-- Residencias -->
												<legend>Sección tipo Residencias</legend>
											@elseif ($seccion->tipo == 'LC') <!-- Oficinas o locales comerciales -->
												<legend>Sección tipo Oficinas o Locales Comerciales</legend>
											@endif	

											<legend>Sección tipo Apartamentos</legend>
											<div class="form-group">
												<label class="col-md-3 control-label">No. de cuartos</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuartos }}">
												</div>
											</div>						

											<div class="form-group">
												<label class="col-md-3 control-label">No. de baños</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->banos }}">
												</div>
											</div>	

											<div class="form-group">
												<label class="col-md-3 control-label">Agua caliente</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->agua_caliente }}">
												</div>
											</div>											

											<div class="form-group">
												<label class="col-md-3 control-label">Estacionamientos</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->estacionamientos }}">
												</div>
											</div>						
											
											<div class="form-group">
												<label class="col-md-3 control-label">Cuota</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->cuota_mant }}">
												</div>
											</div>
															
											<div class="form-group">
												<label class="col-md-3 control-label">Área/m2</label>
												<div class="col-md-9">
													<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $secapto->area }}">
												</div>
											</div>
										</fieldset>
										
										<div class="form-actions">
								      {{Form::button('Salvar', array(
								          'class' => 'btn btn-success btn-large',
								          'data-toggle' => 'modal',
								          'data-target' => '#confirmAction',
								          'data-title' => 'Editar datos generales de la unidad',
								          'data-message' => 'Esta seguro(a) que desea editar los datos de la unidad?',
								          'data-btntxt' => 'SI, editar datos',
								          'data-btncolor' => 'btn-success'
								      ))}}
											<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>

										</div>
									{{ Form::close() }}
								</div><!-- end widget content -->
							</div>

							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="well">
									@include('core.secciones.imagen')
								</div>
							</div>	
							
						</div>						
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
<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>

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
})
</script>
@stop