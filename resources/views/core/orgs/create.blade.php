@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Crear Proveedor')

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
						<h2>Crear un nuevo Proveedor</h2>
	
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
								{{ Form::open(array('class' => 'form-horizontal', 'route' => 'orgs.store')) }}		
									<fieldset>
										{{ csrf_field() }}
										<div class="form-group">
											<label class="col-md-2 control-label">Nombre</label>
											<div class="col-md-10">
												{{ Form::text('nombre', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el nombre de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Tipo</label>
											<div class="col-md-10">
												{{ Form::text('tipo', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el tipo de organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('tipo', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">RUC</label>
											<div class="col-md-10">
												{{ Form::text('ruc', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el RUC de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('ruc', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Dígito Verif.</label>
											<div class="col-md-10">
												{{ Form::text('digitov', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el dígito verificador de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('digitov', '<li style="color:red">:message</li>') !!}
											</div>
										</div>									
										<div class="form-group">
											<label class="col-md-2 control-label">País</label>
											<div class="col-md-10">
												{{ Form::text('pais', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el país de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('pais', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Provincia</label>
											<div class="col-md-10">
												{{ Form::text('provincia', '', array('class' => 'form-control input-sm', 'title' => 'Escriba la provincia de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('provincia', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Distrito</label>
											<div class="col-md-10">
												{{ Form::text('distrito', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el distrito de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('distrito', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Corregimiento</label>
											<div class="col-md-10">
												{{ Form::text('corregimiento', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el corregimiento de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('corregimiento', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Comunidad</label>
											<div class="col-md-10">
												{{ Form::text('comunidad', '', array('class' => 'form-control input-sm', 'title' => 'Escriba la comunidad de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('comunidad', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Teléfono</label>
											<div class="col-md-10">
												{{ Form::text('telefono', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el teléfono de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('telefono', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-2 control-label">Celular</label>
											<div class="col-md-10">
												{{ Form::text('celular', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el número celular de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('celular', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-2 control-label">Email</label>
											<div class="col-md-10">
												{{ Form::text('email', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el email de la organización...', 'autocomplete' => 'off')) }}
												{!! $errors->first('email', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										{{-- <div class="form-group">
											<label class="col-md-2 control-label">Image</label>
											<div class="col-md-10">
												{{ Form::text('imagen', '', array('class' => 'form-control input-sm', 'title' => 'Escriba el celular de la organizacion...', 'autocomplete' => 'off')) }}
												{!! $errors->first('imagen', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	 --}}
									</fieldset>
									
									<div class="form-actions">
										{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
										<a href="{{ URL::route('orgs.index') }}" class="btn btn-large">Cancelar</a>
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