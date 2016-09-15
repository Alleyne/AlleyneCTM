@extends('backend._layouts.default')

@section('main')<!-- MAIN PANEL -->
	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-sm-12 col-md-12 col-lg-12">
	
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
					data-widget-sortable="false" -->
	
					<header>
						<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
						<h2>Registrar nueva factura</h2>
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
							{!! Form::open(array('class' => 'form-horizontal', 'route' => 'facturas.store')) !!}		
									<fieldset>
	 									{!! csrf_field() !!}
										<div class="form-group">
											<label class="col-md-3 control-label">Proveedor</label>
											<div class="col-md-9">
												{!! Form::select('org_id', ['' => 'Selecione un proveedor ...'] + $proveedores, 0, ['class' => 'form-control']) !!}
												{!! $errors->first('org_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label">Factura No.</label>
											<div class="col-md-9">
												{!! Form::text('no', old('no'),
													array(
													    'class' => 'form-control',
													    'id' => 'no',
													    'placeholder' => 'Escriba el numero de la factura...',
														'autocomplete' => 'off',
													))
												!!} 
												{!! $errors->first('no', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Fecha</label>
                                            <div class="col-md-9">
												<div class="input-group">
													<input type="text" name="fecha" placeholder="Seleccione la fecha de la factura ..." class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>
                                            	{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                                            </div>
                                        </div>  
										<div class="form-group">
											<label class="col-md-3 control-label">Total</label>
											<div class="col-md-9">
												{!! Form::text('total', old('total'),
													array(
													    'class' => 'form-control',
													    'id' => 'total',
													    'placeholder' => 'Escriba el monto total de la factura...',
														'autocomplete' => 'off',
													))
												!!} 
												{!! $errors->first('total', '<li style="color:red">:message</li>') !!}
											</div>
									</fieldset>
									
									<div class="form-actions">
										{!! Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) !!}
										<a href="{{ URL::previous() }}" class="btn btn-large">Cancelar</a>
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