@extends('templates.backend._layouts.default')

@section('main')

	<!-- MAIN CONTENT -->
	<div id="content">
		<!-- widget grid -->
		<section id="widget-grid" class="">
		
			<!-- row -->
			<div class="row">
		
				<!-- NEW WIDGET START -->
				<article class="col-sm-12 col-md-12 col-lg-9">
		
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
							<h2>Datos de la Junta Directiva </h2>
		
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
													<label class="col-md-2 control-label">Nombre</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="nombre" type="text" readonly value="{{ $dato->nombre }}">
													</div>
												</div>					
												<div class="form-group">
													<label class="col-md-2 control-label">Descripción</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="descripcion" type="text" readonly value="{{ $dato->descripcion }}">
													</div>
												</div>	
<!-- 												<div class="form-group">
													<label class="col-md-2 control-label">Período</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="periodo" type="text" readonly value="{{ $dato->periodo }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-2 control-label">Presidente</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="presidente" type="text" readonly value="{{ $dato->presidente }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-2 control-label">Secretario</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="secretario" type="text" readonly value="{{ $dato->secretario }}">
													</div>
												</div>					
												<div class="form-group">
													<label class="col-md-2 control-label">Vocal</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="vocal" type="text" readonly value="{{ $dato->vocal }}">
													</div>
												</div>	
												<div class="form-group">
													<label class="col-md-2 control-label">Tesorero</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="tesorero" type="text" readonly value="{{ $dato->tesorero }}">
													</div>
												</div>	 -->
											</fieldset>
											
											<div class="form-actions">
												<a href="{{ URL::route('jds.index') }}" class="btn btn-primary btn-large">Regresar</a>
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
				<article class="col-sm-12 col-md-12 col-lg-3">
		
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
								<img style="height: 310px; border-radius: 8px;" src="{{asset($dato->imagen_L)}}" class="img-responsive" alt="Responsive image">
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