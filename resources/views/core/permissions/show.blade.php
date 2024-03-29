@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Ver permiso')

@section('content')

	<!-- MAIN CONTENT -->
	<div id="content">
		<!-- widget grid -->
		<section id="widget-grid" class="">
		
			<!-- row -->
			<div class="row">
		
				<!-- NEW WIDGET START -->
				<article class="col-sm-12 col-md-12 col-lg-12">
		
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
							<h2>Permisos </h2>
		
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
														<input class="form-control input-sm" name="name" type="text" readonly value="{{ $dato->name }}">
													</div>
												</div>					
												<div class="form-group">
													<label class="col-md-2 control-label">Valor</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="value" type="text" readonly value="{{ $dato->value }}">
													</div>
												</div>
												<div class="form-group">
													<label class="col-md-2 control-label">Descripción</label>
													<div class="col-md-10">
														<input class="form-control input-sm" name="descripcion" type="text" readonly value="{{ $dato->description }}">
													</div>
												</div> 						
											</fieldset>
											
											<div class="form-actions">
												<a href="{{ URL::route('permissions.index') }}" class="btn btn-primary btn-large">Regresar</a>
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
@stop

@section('relatedplugins')
<!-- PAGE RELATED PLUGIN(S) 
<script src="..."></script>-->



<script type="text/javascript">

// DO NOT REMOVE : GLOBAL FUNCTIONS!

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