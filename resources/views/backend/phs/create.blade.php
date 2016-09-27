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
						<h2>Crear un nuevo Ph</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'phs.store')) }}		
									<fieldset>
										{{ csrf_field() }}
										<div class="form-group">
											<label class="col-md-2 control-label">Tipo</label>
											<div class="col-md-10">
												<label class="radio radio-inline">
													<input type="radio" class="radiobox" name="tipo" value = "1">
													<span>Edificio</span> 
												</label>
												
												<label class="radio radio-inline">
													<input type="radio" class="radiobox" name="tipo" value = "2">
													<span>Residencial</span>  
												</label>
												{!! $errors->first('tipo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>										

										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre del Ph...')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Código</label>
											<div class="col-md-10">
												{{ Form::text('codigo', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el código del Ph...')) }}
												{!! $errors->first('codigo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">País</label>
											<div class="col-md-10">
												{{ Form::text('pais', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el país...')) }}
												{!! $errors->first('pais', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Provincia</label>
											<div class="col-md-10">
												{{ Form::text('provincia', '', array('class' => 'form-control input-sm', 'title' => 'Escriba la provincia...')) }}
												{!! $errors->first('provincia', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Distrito</label>
											<div class="col-md-10">
												{{ Form::text('distrito', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el distrito...')) }}
												{!! $errors->first('distrito', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Corregimiento</label>
											<div class="col-md-10">
												{{ Form::text('corregimiento', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el corregimiento...')) }}
												{!! $errors->first('corregimiento', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Comunidad</label>
											<div class="col-md-10">
												{{ Form::text('comunidad', '', array('class' => 'form-control input-sm', 'title' => 'Escriba la comunidad...')) }}
												{!! $errors->first('comunidad', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Calle</label>
											<div class="col-md-10">
												{{ Form::text('calle', '', array('class' => 'form-control input-sm', 'title' => 'Escriba la calle...')) }}
												{!! $errors->first('calle', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Teléfono</label>
											<div class="col-md-10">
												{{ Form::text('telefono', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el teléfono...')) }}
												{!! $errors->first('telefono', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Celular</label>
											<div class="col-md-10">
												{{ Form::text('celular', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el celular...')) }}
												{!! $errors->first('celular', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">email</label>
											<div class="col-md-10">
												{{ Form::text('email', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el email...')) }}
												{!! $errors->first('email', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::route('phs.index') }}" class="btn btn-large">Cancelar</a>
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

</script>
@stop