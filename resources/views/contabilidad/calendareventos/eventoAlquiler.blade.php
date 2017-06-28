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
						<h2>Registra pago por alquiler de amenidades</h2>
	
					</header>
	
					<!-- widget div-->
					<div>
	
						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
	
						</div>
						<!-- end widget edit box -->
						
						{{-- 	{{dd($dato->toArray())}} --}}
						
							<!-- widget content -->
							<div class="widget-body">

								{{ Form::open(array('class' => 'form-horizontal','route' => array('eventoAlquilerUpdate', $dato->id))) }}
									<fieldset>
										
										{{ csrf_field() }}
		                {{ Form::hidden('calendarevento_id', $dato->id) }}

										<div class="alert alert-info fade in">
											<button class="close" data-dismiss="alert">
												×
											</button>
											<h5><i class="fa-fw fa fa-warning"></i>
											<strong>Atencion: </strong> {{ $mensaje }}</h5>
										</div>

		                <div class="form-group">
		                  <label class="col-md-2 control-label">Unidad</label>
		                  <div class="col-md-10">
		                    <input type="text" name="un" id="un" class="form-control" readonly value="{{ $dato->un_id }}">
		                  </div>
		                </div>										
		                
		                <div class="form-group">
		                  <label class="col-md-2 control-label">Amenidad</label>
		                  <div class="col-md-10">
		                    <input type="text" name="am" id="am" class="form-control" readonly value="{{ $dato->am_id }}">
		                  </div>
		                </div>	
                    
										{{-- <!-- Multiple Radios (inline) -->
										<div class="form-group">
										  <label class="col-md-2 control-label" for="radios">Tipo de devolucion:</label>
										  <div class="col-md-10"> 
										    <label class="radio-inline" for="radios-0">
										      <input type="radio" name="tipodev_radios" id="tipodoc-1" value="1" checked="checked">
										      Devolver deposito
										    </label> 
										    <label class="radio-inline" for="radios-1">
										      <input type="radio" name="tipodev_radios" id="tipodoc-2" value="2">
										      Devolver alquiler
										    </label>
										  </div>
										</div> --}}
										
										<hr />
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Fecha</label>
                        <div class="col-md-10">
													<div class="input-group">
														<input type="text" name="fecha" placeholder="Fecha en que se hizo el pago del alquiler" class="form-control datepicker" data-dateformat="yy/mm/dd" value={{ old('fecha') }}>
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
													</div>
                        	<p>{!! $errors->first('fecha', '<li style="color:red">:message</li>') !!}</p> 
                        </div>
                    </div>  

				            <div class="form-group">
				              <label class="col-md-2 control-label">Tipo de pago</label>
				              <div class="col-md-10">
				                <select name="trantipo_id" id="trantipo_id" class="form-control" onclick="createUserJsObject.ShowtipoDePago;">
				                  @foreach ($trantipos as $trantipo)
				                    <option id="{{ $trantipo->id }}" value="{{ $trantipo->id }}">{{ $trantipo->nombre }}</option>                 
				                  @endforeach
				                </select>
				              </div>    
				            </div>
										
										<div class="bancos form-group">
											<label class="col-md-2 control-label">Banco</label>
											<div class="col-md-10">
												{{ Form::select('banco_id', ['' => 'Selecione una Institucion Bancaria ...'] + $bancos, 0, ['class' => 'form-control']) }}
											</div>
										</div>
				            
				            <div class="form-group chequeNo" style=" display: none;">
				              <label class="col-md-2 control-label">Cheque No.</label>
				              <div class="col-md-10">
				                {{ Form::text('chqno', old('chqno'),
				                  array(
				                      'class' => 'form-control',
				                      'id' => 'chqno',
				                      'placeholder' => 'Escriba el numero del cheque...',
				                      'autocomplete' => 'off',
				                  ))
				                }} 
				                {!! $errors->first('chqno', '<li style="color:red">:message</li>') !!}
				              </div>
				            </div>  
				            
				            <div class="form-group transaccionNo">
				              <label class="col-md-2 control-label">Transaccion No.</label>
				              <div class="col-md-10">
				                {{ Form::text('transno', old('transno'),
				                  array(
				                      'class' => 'form-control',
				                      'id' => 'transno',
				                      'placeholder' => 'Escriba el numero de la transaccion...',
				                      'autocomplete' => 'off',
				                  ))
				                }} 
				                {!! $errors->first('transno', '<li style="color:red">:message</li>') !!}
				              </div>
				            </div>  
										
										{{-- <div class="form-group">
											<label class="col-md-2 control-label">Monto</label>
											<div class="col-md-10">
												{{ Form::text('monto', old('monto'),
													array(
													    'class' => 'form-control',
													    'id' => 'monto',
													    'placeholder' => 'Escriba el monto a devolver...',
														'autocomplete' => 'off',
													))
												}} 
												{!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
											</div>
										</div> --}}
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
						            'data-message' => 'Esta seguro(a) que desea proceder con el registro del pago por alquiler de amenidades?',
						            'data-btntxt' => 'Si, proceder registro de pago',
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
					var trantipo_id = jQuery('#trantipo_id');
					var select = this.value;
					
					trantipo_id.change(function () {
				    if ($(this).val() == 1) {
			        $('.bancos').show();
			        $('.chequeNo').show();
			    		$('.transaccionNo').hide();
				    
				    } else if ($(this).val() == 5) {
				    	$('.bancos').hide();
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').hide();
			    
				    }	else {
				    	$('.bancos').show();
				    	$('.chequeNo').hide();
				    	$('.transaccionNo').show();
				    }
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