@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear grupo de unidades')

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
					data-widget-sortable="false" -->
					
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Crear unidades en grupo</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'storeungrupo')) }}		
									<fieldset>
				 						{{ csrf_field() }}
                                        {{ Form::hidden('seccione_id', $dato->seccione_id) }}
                                        {{ Form::hidden('tipo', $dato->tipo) }}
                                        {{ Form::hidden('codigoseccion', $dato->codigoseccion) }}
                                        {{ Form::hidden('bloque_id', $dato->bloque_id) }}
                                        {{ Form::hidden('codigobloque', $dato->codigobloque) }}									
										{{ Form::hidden('codigoph', $dato->codigoph) }}	
										
										@if ($dato->tipo==1) 
											<legend>Crear grupo de Unidades tipo apartamento</legend>
										@elseif ($dato->tipo==3)
											<legend>Crear grupo de Unidades tipo local u oficina en un edificio</legend>
										@endif
	
										<div class="form-group">
											<label class="col-md-3 control-label">Al piso</label>
											<div class="col-md-6">
												<input class="form-control spinner-left"  id="spinner" name="alpiso" value="1" type="text">
												<p class="text-left">Número del piso en donde termina la numeración.</p>
												{!! $errors->first('alpiso', '<li style="color:red">:message</li>') !!}
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Del piso</label>
											<div class="col-md-6">
												<input class="form-control spinner-left"  id="delpiso" name="delpiso" value="1" type="text">
												<p class="text-left">Número del piso en donde inicia la numeración.</p>
												{!! $errors->first('delpiso', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
												
										<legend>
										</legend>
										
										<div class="form-group">
											<label class="col-md-3 control-label">Letras</label>
											<div class="col-md-6">
												{{ Form::text('letras', '', array('class' => 'form-control tagsinput', 'data-role' => 'tagsinput')) }}
												<p class="text-left">Escriba las letras que desea utilizar para la numeración presionando "Enter" después de cada una.</p>
											    {!! $errors->first('letras', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>
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
	<script src="{{ URL::asset('assets/backend/js/plugin/bootstrap-tags/bootstrap-tagsinput.min.js') }}"></script>
	
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

		// Spinners
		$("#spinner").spinner({
		    min: 1,
		    max: 100,
		    step: 1,
		    start: 1,
		    numberFormat: "C"
		});    
		
		// Spinners
		$("#delpiso").spinner({
		    min: 1,
		    max: 100,
		    step: 1,
		    start: 1,
		    numberFormat: "C"
		});  	
	    
	    // Tags
	    $('#tags').editable({
	        inputclass: 'input-large',
	        select2: {
	            tags: ['html', 'javascript', 'css', 'ajax'],
	            tokenSeparators: [",", " "]
	        }
	    });
	})
	</script>
@stop