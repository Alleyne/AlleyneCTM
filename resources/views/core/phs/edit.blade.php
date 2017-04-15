@extends('templates.backend._layouts.default')

@section('main')<!-- MAIN PANEL -->
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-7">
	
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
						<h2>Editar datos del Administrador de Propiedades Horizontales</h2>
	
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
							{{ Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('phs.update', $dato->id))) }}
									<fieldset>
										{{ csrf_field() }}
										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', $dato->nombre, array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre del Ph...')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Código</label>
											<div class="col-md-10">
												{{ Form::text('codigo', $dato->codigo, array('class' => 'form-control input-sm', 'title' => 'Escriba el codigo del Ph...')) }}
												{!! $errors->first('codigo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Tipo</label>
											<div class="col-md-10">
												{{ Form::text('tipo', $dato->tipo, array('class' => 'form-control input-sm', 'title' => 'Escriba el tipo de Ph...')) }}
												{!! $errors->first('tipo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">País</label>
											<div class="col-md-10">
												{{ Form::text('pais', $dato->pais, array('class' => 'form-control input-sm', 'title' => 'Escriba el país...')) }}
												{!! $errors->first('pais', '<li style="color:red">:message</li>') }}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Provincia</label>
											<div class="col-md-10">
												{{ Form::text('provincia', $dato->provincia, array('class' => 'form-control input-sm', 'title' => 'Escriba la provincia...')) }}
												{!! $errors->first('provincia', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Distrito</label>
											<div class="col-md-10">
												{{ Form::text('distrito', $dato->distrito, array('class' => 'form-control input-sm', 'title' => 'Escriba el distrito...')) }}
												{!! $errors->first('distrito', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Corregimiento</label>
											<div class="col-md-10">
												{{ Form::text('corregimiento', $dato->corregimiento, array('class' => 'form-control input-sm', 'title' => 'Escriba el corregimiento...')) }}
												{!! $errors->first('corregimiento', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Comunidad</label>
											<div class="col-md-10">
												{{ Form::text('comunidad', $dato->comunidad, array('class' => 'form-control input-sm', 'title' => 'Escriba la comunidad...')) }}
												{!! $errors->first('comunidad', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Calle</label>
											<div class="col-md-10">
												{{ Form::text('calle', $dato->calle, array('class' => 'form-control input-sm', 'title' => 'Escriba la calle...')) }}
												{!! $errors->first('calle', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Teléfono</label>
											<div class="col-md-10">
												{{ Form::text('telefono', $dato->telefono, array('class' => 'form-control input-sm', 'title' => 'Escriba el teléfono...')) }}
												{!! $errors->first('telefono', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">Celular</label>
											<div class="col-md-10">
												{{ Form::text('celular', $dato->celular, array('class' => 'form-control input-sm', 'title' => 'Escriba el celular...')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-2 control-label">email</label>
											<div class="col-md-10">
												{{ Form::text('email', $dato->email, array('class' => 'form-control input-sm', 'title' => 'Escriba el email...')) }}
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
			<article class="col-sm-12 col-md-12 col-lg-5">
	
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
						<h2>Imagen del Ph</h2>
	
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
			              	<p>
								<img style="height: 275px; border-radius: 8px;" src="{{asset($dato->imagen_L)}}" class="img-responsive" alt="Responsive image">
			             	</p>
				         	{{ Form::open(array('route' => array('subirImagenPh', $dato->id),'files'=>true)) }}
								<div class="form-actions">
				         		<div>
									{{ Form::file('file') }}
								</div>						
									{{ Form::submit('Subir imagen', array('class' => 'btn btn-success btn-save btn-large')) }}
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