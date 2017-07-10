@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear Junta Directiva')

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
						<h2>Crear un nueva Junta Directiva</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'jds.store')) }}		
									<fieldset>
										{{ csrf_field() }}
										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre de la Junta Directiva...', 'autocomplete' => 'off')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Descripción</label>
											<div class="col-md-10">
												{{ Form::text('descripcion', '', array('class' => 'form-control input-sm', 'title' => 'Escriba una descripción de la Junta Directiva...', 'autocomplete' => 'off')) }}
												{!! $errors->first('descripcion', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::route('jds.index') }}" class="btn btn-large">Cancelar</a>
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

/*
* SmartAlerts
*/
// With Callback
$("#smart-mod-eg1").click(function(e) {
	$.SmartMessageBox({
		title : "Smart Alert!",
		content : "This is a confirmation box. Can be programmed for button callback",
		buttons : '[No][Yes]'
	}, function(ButtonPressed) {
		if (ButtonPressed === "Yes") {

			$.smallBox({
				title : "Salvar nueva Junta Directiva",
				content : "<i class='fa fa-clock-o'></i> <i>Usted presionó SI...</i>",
				color : "#659265",
				iconSmall : "fa fa-check fa-2x fadeInRight animated",
				timeout : 4000
			});
		}
		if (ButtonPressed === "No") {
			$.smallBox({
				title : "Salvar nueva Junta Directiva",
				content : "<i class='fa fa-clock-o'></i> <i>Usted presionó No...</i>",
				color : "#C46A69",
				iconSmall : "fa fa-times fa-2x fadeInRight animated",
				timeout : 4000
			});
		}

	});
	e.preventDefault();
})


</script>

@stop