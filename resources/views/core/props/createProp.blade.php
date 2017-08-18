@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Vincular propietario')

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
						<h2>Vincular Propietario</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'props.store')) }}		
									<fieldset>
										{{ csrf_field() }}
										{{ Form::hidden('un_id', $un_id) }}
										{{ Form::hidden('seccione_id', $seccione_id) }}
										
										<div class="form-group">
											<label class="col-md-4 control-label">Usuarios Disponibles</label>
											<div class="col-md-8">
												{{ Form::select('user_id', array('' => 'Escoja el usuario que desea agregar...') + $datos, array('title' => 'Escoja el usuario que desea agregar')) }}
												{!! $errors->first('user_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-4 control-label">Encargado</label>
											<div class="col-md-8">
												{{ Form::checkbox('encargado','1', false) }}
											</div>
										</div>	
									</fieldset>
									
									<div class="form-actions">
							      {{Form::button('Salvar', array(
							          'class' => 'btn btn-success btn-large',
							          'data-toggle' => 'modal',
							          'data-target' => '#confirmAction',
							          'data-title' => 'Vincular propietario',
							          'data-message' => 'Esta seguro(a) que desea vincular este propietario?',
							          'data-btntxt' => 'Si, vincular propietario',
							          'data-btncolor' => 'btn-success'
							      ))}}
										<a href="{{ URL::route('indexprops', array($un_id, $seccione_id)) }}" class="btn btn-large">Cancelar</a>
										<!-- <a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a> -->
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
							Aquí va cualquier cosa
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
<!-- Incluye javascript -->
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