@extends('backend._layouts.default')

@section('main')

	<!-- MAIN CONTENT -->
	<div id="content">
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
							<h2>Datos del usuario </h2>
		
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
													<label class="col-md-3 control-label">Nombre</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $dato->fullname }}">
													</div>
												</div>						

												<div class="form-group">
													<label class="col-md-3 control-label">Dirección</label>
													<div class="col-md-9">
														{{ Form::textarea('direccion', $dato->direccion, array('class' => 'form-control input-sm', 'rows' => '2', 'readonly' => 'readonly')) }}
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-3 control-label">País</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="pais" type="text" readonly value="{{ $dato->pais }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-3 control-label">Provincia</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="provincia" type="text" readonly value="{{ $dato->provincia }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-3 control-label">Distrito</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="distrito" type="text" readonly value="{{ $dato->distrito }}">
													</div>
												</div>	      
												<div class="form-group">
													<label class="col-md-3 control-label">Corregimiento</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="corregimiento" type="text" readonly value="{{ $dato->corregimiento }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-3 control-label">Teléfono</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="telefono" type="text" readonly value="{{ $dato->telefono }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-3 control-label">Celular</label>
													<div class="col-md-9">
														<input class="form-control input-sm" name="celular" type="text" readonly value="{{ $dato->celular }}">
													</div>
												</div>	
											</fieldset>
										
											<div class="form-actions">
									   			<a href="{{ URL::previous() }}" class="btn btn-primary btn-large">Regresar</a>
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
							<h2>Imagen del Usuario </h2>
		
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
								<img style="height: 310px; border-radius: 8px;" src="{{asset($dato->imagen)}}" class="img-responsive" alt="Responsive image">
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
<!-- PAGE RELATED PLUGIN(S) 
<script src="..."></script>-->



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