@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Editar serviproducto')

@section('content')

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
						<h2>Editar serviproducto</h2>
	
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
							{{ Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('calendareventos.update', $dato->id))) }}
									<fieldset>
										{{ csrf_field() }}
		                
		                <div class="form-group">
		                  <label class="col-md-4 control-label">Unidad</label>
		                  <div class="col-md-8">
		                    <input type="text" name="un" id="un" class="form-control" readonly value="{{ $dato->un_id }}">
		                  </div>
		                </div>										
		                
		                <div class="form-group">
		                  <label class="col-md-4 control-label">Amenidad</label>
		                  <div class="col-md-8">
		                    <input type="text" name="am" id="am" class="form-control" readonly value="{{ $dato->am_id }}">
		                  </div>
		                </div>	
										
										<div class="form-group organizaciones">
											<label class="col-md-4 control-label">Propietario(s)</label>
											<div class="col-md-8">
												{{ Form::select('user_id', ['' => 'Selecione un propietario ...'] + $props, 0, ['class' => 'form-control']) }}
												{!! $errors->first('user_id', '<li style="color:red">:message</li>') !!}
											</div>
										</div>
		                
{{-- 		                <div class="form-group">
		                  <label class="col-md-4 control-label">Iniciaba <strong>{{ $dato->start }}</strong></label>
		                  <div class="col-md-8">
		                  
		                        <div class='input-group date' id='datetimepicker6'>
		                            <input type='text' name="start" id="start" class="form-control">
		                            <span class="input-group-addon">
		                                <span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    {!! $errors->first('start', '<li style="color:red">:message</li>') !!} 
		                  
		                  </div>
		                </div>  

		                <div class="form-group">
		                  <label class="col-md-4 control-label">Terminaba <strong>{{ $dato->end }}</strong> </label>
		                  <div class="col-md-8">
		                  
		                        <div class='input-group date' id='datetimepicker7'>
		                            <input type='text' name="end" id="end" class="form-control">
		                            <span class="input-group-addon">
		                                <span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    {!! $errors->first('end', '<li style="color:red">:message</li>') !!} 
		                  
		                  </div>
		                </div>  --}}
									</fieldset>
									
									<div class="form-actions">
										{{Form::open(array(
											'route' => array('calendareventos.update', $dato->id),
											'method' => 'GET',
											'style' => 'display:inline'
										))}}
						        
						        {{Form::button('Salvar', array(
						            'class' => 'btn btn-success btn-xs',
						            'data-toggle' => 'modal',
						            'data-target' => '#confirmAction',
						            'data-title' => 'Salvar cambios al evento',
						            'data-message' => 'Esta seguro(a) que desea salvar los cambios al evento?',
						            'data-btntxt' => 'Si, salvar cambios',
						            'data-btncolor' => 'btn-success'
						        ))}}
						        {{Form::close()}} 
										
										<a href="{{ URL::route('calendareventos.index') }}" class="btn btn-large">Cancelar</a>
									</div>
							{{ Form::close() }}
							</div>
							<!-- end widget content -->
					</div>
					<!-- end widget div -->
				</div>
				<!-- end widget -->
		    <!-- Incluye la modal box -->
		    @include('templates.backend._partials.modal_confirm')			

			</article>
			<!-- WIDGET END -->
	</section>
	<!-- end widget grid -->
@stop

@section('relatedplugins')
	<!-- PAGE RELATED PLUGIN(S) -->
	<script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script> 

    <script>
      $(document).ready(function() {
		    $(function () {
	        $('#datetimepicker6').datetimepicker({
      			format: 'DD/MM/YYYY hh:mm A'
	        });
	        
	        $('#datetimepicker7').datetimepicker({
            format: 'DD/MM/YYYY hh:mm A',
            useCurrent: false //Important! See issue #1075
	        });
	        
	        $("#datetimepicker6").on("dp.change", function (e) {
	          $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
	        });
	        
	        $("#datetimepicker7").on("dp.change", function (e) {
	          $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	        });
		    })

	      $("input[type='submit']").attr("disabled", false);
		    $("form").submit(function(){
		      $("input[type='submit']").attr("disabled", true).val("Por favor espere mientras se envia la informacion . . .");
		      return true;
		    });

      });
		</script>
@stop