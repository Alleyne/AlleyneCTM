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
						<h2>Editar Usuario</h2>
	
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
							{!! Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('users.update', $dato->id))) !!}
									<fieldset>
										{!! csrf_field() !!}
										<div class="form-group">
											<label class="col-md-3 control-label">Usuario</label>
											<div class="col-md-9">
												{!! Form::text('username', $dato->username, array('class' => 'form-control input-sm', 'title' => 'Escriba su usuario...')) !!}
												{!! $errors->first('username', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										<div class="form-group">
											<label class="col-md-3 control-label">Email</label>
											<div class="col-md-9">
												{!! Form::text('email', $dato->email, array('class' => 'form-control input-sm', 'title' => 'Escriba su email...')) !!}
												{!! $errors->first('email', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Primer nombre</label>
											<div class="col-md-9">
												{!! Form::text('first_name', $dato->first_name, array('class' => 'form-control input-sm', 'title' => 'Escriba su primer nombre...')) !!}
												{!! $errors->first('first_name', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Seg nombre</label>
											<div class="col-md-9">
												{!! Form::text('middle_name', $dato->middle_name, array('class' => 'form-control input-sm', 'title' => 'Escriba su segundo nombre...')) !!}
												{!! $errors->first('middle_name', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Apellido</label>
											<div class="col-md-9">
												{!! Form::text('last_name', $dato->last_name, array('class' => 'form-control input-sm', 'title' => 'Escriba su apellido paterno...')) !!}
												{!! $errors->first('last_name', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Apellido materno</label>
											<div class="col-md-9">
												{!! Form::text('sur_name', $dato->sur_name, array('class' => 'form-control input-sm', 'title' => 'Escriba su apellido materno...')) !!}
												{!! $errors->first('sur_name', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Cedula</label>
											<div class="col-md-9">
												{!! Form::text('cedula', $dato->cedula, array('class' => 'form-control input-sm', 'title' => 'Escriba su cedula...')) !!}
												{!! $errors->first('cedula', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Telefono</label>
											<div class="col-md-9">
												{!! Form::text('telefono', $dato->telefono, array('class' => 'form-control input-sm', 'title' => 'Escriba su telefono...')) !!}
												{!! $errors->first('telefono', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Celular</label>
											<div class="col-md-9">
												{!! Form::text('celular', $dato->celular, array('class' => 'form-control input-sm', 'title' => 'Escriba su celular...')) !!}
												{!! $errors->first('celular', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Activado</label>
											<div class="col-md-9">
												{!! Form::text('activated', $dato->activated, array('class' => 'form-control input-sm', 'title' => 'Usuario activado si o no...')) !!}
												{!! $errors->first('activated', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
									</fieldset>
									<div class="form-actions">
										{!! Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) !!}
										<a href="{{ URL::route('users.index') }}" class="btn btn-large">Cancelar</a>
									</div>
							{!! Form::close() !!}
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
				         	{!! Form::open(array('route' => array('subirImagenUser', $dato->id),'files'=>true)) !!}
								<div class="form-actions">
				         		<div>
									{!! Form::file('file') !!}
								</div>						
									{!! Form::submit('Subir imagen', array('class' => 'btn btn-success btn-save btn-large')) !!}
								</div>
			          		{!! Form::close() !!}			            
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