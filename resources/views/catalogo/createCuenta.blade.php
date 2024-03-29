@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear cuenta')

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
					data-widget-sortable="false"-->
					
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Crear una nueva cuenta contable</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'catalogos.store')) }}		
									<fieldset>
										{{ csrf_field() }}
										{{ Form::hidden('id', $id) }}
										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre de la cuenta ...')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Código</label>
											<div class="col-md-10">
												{{ Form::text('codigo', old('codigo'),
													array(
													    'class' => 'form-control',
													    'id' => 'codigo',
													    'placeholder' => 'Escriba el código de la cuenta. Ejempo escriba 01.00 para crear la cuenta XX01.00',
															'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('codigo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										@if ($id == 1 || $id ==2)
											<div class="form-group">
												<label class="col-md-2 control-label">Clasificación</label>
												<div class="col-md-10">
												{{ Form::select('corriente_siono', ['Seleccione el Tipo de cuenta ...', 'Corriente', 'No corriente'], 0, ['class' => 'form-control']) }}
												{!! $errors->first('corriente_siono', '<li style="color:red">:message</li>') !!}
												</div>
											</div>	
										@endif

									</fieldset>
									
									<div class="form-actions">
							      {{Form::button('Salvar', array(
							          'class' => 'btn btn-success btn-large',
							          'data-toggle' => 'modal',
							          'data-target' => '#confirmAction',
							          'data-title' => 'Crear nueva cuenta contable',
							          'data-message' => 'Esta seguro(a) que desea crear una nueva cuenta contable?',
							          'data-btntxt' => 'SI, crear nueva cuenta',
							          'data-btncolor' => 'btn-success'
							      ))}}
										<a href="{{ URL::route('catalogos.index') }}" class="btn btn-large">Cancelar</a>
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