@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Editar serviproducto')

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
						<h2>Editar serviproducto</h2>
	
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
							{{ Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('serviproductos.update', $dato->id))) }}
									<fieldset>
										{{ csrf_field() }}
										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', $dato->nombre, array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre del serviproducto')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
								
										<div class="form-group">
											<label class="col-md-2 control-label">Activo</label>
											<div class="col-md-10">
												{{ Form::checkbox('activo') }}
											</div>
										</div>	

									</fieldset>
									
									<div class="form-actions">
										{{Form::open(array(
											'route' => array('serviproductos.update', $dato->id),
											'method' => 'GET',
											'style' => 'display:inline'
										))}}
						        
						        {{Form::button('Salvar', array(
						            'class' => 'btn btn-success btn-xs',
						            'data-toggle' => 'modal',
						            'data-target' => '#confirmAction',
						            'data-title' => 'Salvar cambios al serviproducto',
						            'data-message' => 'Esta seguro(a) que desea salvar los cambios al serviproducto?',
						            'data-btntxt' => 'Si, salvar cambios',
						            'data-btncolor' => 'btn-success'
						        ))}}
						        {{Form::close()}} 
										
										<a href="{{ URL::route('serviproductos.index') }}" class="btn btn-large">Cancelar</a>
									</div>
							{{ Form::close() }}
							</div>
							<!-- end widget content -->
					</div>
					<!-- end widget div -->
				</div>
				<!-- end widget -->
		    <!-- Incluye la modal box -->
		    @include('templates.backend._partials.modal_confirm')			

			</article>
			<!-- WIDGET END -->
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
	<!-- PAGE RELATED PLUGIN(S) -->
	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script> 

@stop