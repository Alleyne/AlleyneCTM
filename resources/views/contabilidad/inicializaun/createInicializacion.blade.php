@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Inicializar Unidad')

@section('content')
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
						<h2>Formunlario para inicializar una Unidad</h2>
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
							{{ Form::open(['class' => 'form-horizontal', 'route' => 'storeInicializacion']) }}
									<fieldset>
	 									{{ csrf_field() }}
	 									{{ Form::hidden('un_id', $un_id) }}

										<div class="form-group">
											<label class="col-md-3 control-label">Total de meses adeudados</label>
											<div class="col-md-9">
												{{ Form::text('meses', old('meses'),
													array(
													    'class' => 'form-control',
													    'id' => 'meses',
													    'placeholder' => 'Escriba en numero de meses atrasados que debe la unidad...',
														'autocomplete' => 'off'
													))
												}} 
												{!! $errors->first('meses', '<li style="color:red">:message</li>') !!}
											</div>
										</div>	
										
										<div class="form-group">
											<label class="col-md-3 control-label">Monto total adeudado</label>
											<div class="col-md-9">
												{{ Form::text('monto', old('monto'),
													array(
													    'class' => 'form-control',
													    'id' => 'monto',
													    'placeholder' => 'Escriba el monto total adeudado en cuotas de mantenimiento...',
														'autocomplete' => 'off'
													))
												}} 
												{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
											</div>
										</div>					
										
										<div class="form-group">
											<label class="col-md-3 control-label">Total de pagos anticipados</label>
											<div class="col-md-9">
												{{ Form::text('anticipados', old('anticipados'),
													array(
													    'class' => 'form-control',
													    'id' => 'anticipados',
													    'placeholder' => 'Escriba el monto total adeudado en recargos ...',
														'autocomplete' => 'off'
													))
												}} 
												{!! $errors->first('anticipados', '<li style="color:red">:message</li>') !!}
											</div>
										</div>		
									</fieldset>

									<div class="form-actions">
										{{ Form::submit('Inicializar unidad', array('class' => 'btn btn-success btn-save btn-large')) }}
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
		</div>
	
		<!-- end row -->
	
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
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
		})
	</script>

	<script type="text/javascript">
	  $(document).ready(function(){
	    $("input[type='submit']").attr("disabled", false);
	    $("form").submit(function(){
	      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
	      return true;
	    })
	  })
	</script>
@stop