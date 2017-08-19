@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Ver bitácora')

@section('content')

	<!-- MAIN CONTENT -->
	<div id="content">
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

							<h2>Descripcion completa de la Bitácora </h2>

							<h2>Descripción completa de la Bitácora </h2>

		
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
				
										<form class="form-horizontal">
											<fieldset>
												<div class="form-group">
													<label class="col-md-2 control-label">Fecha</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->fecha }}">
													</div>
												</div>					
												<div class="form-group">
													<label class="col-md-2 control-label">Hora</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->hora }}">
													</div>
												</div>		
												<div class="form-group">
													<label class="col-md-2 control-label">Usuario</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $usuario->fullName }}">
													</div>
												</div>		
												<div class="form-group">
													<label class="col-md-2 control-label">IP</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->ip }}">
													</div>
												</div>		
												<div class="form-group">
													<label class="col-md-2 control-label">Acción</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->accion }}">
													</div>
												</div>				
												<div class="form-group">
														<label class="col-md-2 control-label">Tabla</label>
														<div class="col-md-10">
															<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->tabla }}">
														</div>
													</div>	
												<div class="form-group">
														<label class="col-md-2 control-label">Registro</label>
														<div class="col-md-10">
															<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $bitacora->registro }}">
														</div>
												</div>	
												<div class="form-group">
													<label class="col-md-2 control-label">Detalle</label>
													<div class="col-md-10">
														{{ Form::textarea('detalle', $bitacora->detalle, array('class' => 'form-control input-sm', 'rows' => '8', 'readonly' => 'readonly')) }}
													</div>
												</div>	
											</fieldset>
											
											<div class="form-actions">
												<!-- Escoje la navegación de acuerdo al grupo al que pertenece el usuario -->
											   	<a href="{{ URL::route('bitacoras.index') }}" class="btn btn-primary btn-large">Regresar</a>
											</div>			
										</form>					
				
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
						data-widget-sortable="false" -->
						
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
				              	<p>
								<img style="height: 310px; border-radius: 8px;" src="{{asset($usuario->imagen)}}" class="img-responsive" alt="Responsive image">
				             	</p>
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
		</section>
		<!-- end widget grid -->
	</div>
@stop

@section('relatedplugins')
<script type="text/javascript">
	$(document).ready(function() {
		pageSetUp();
		
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