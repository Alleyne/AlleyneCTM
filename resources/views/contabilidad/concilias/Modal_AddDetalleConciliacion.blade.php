
<!-- Modal -->
<div class="modal fade" id="Modal_AddDetalleConciliacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
          <h4 class="modal-title" id="myModalLabel">Registra Nuevo Detalle de Conciliacion</h4>
      </div>
      <div class="modal-body">

        {{ Form::open(array('class' => 'form-horizontal', 'route' => 'addDetalleConciliacion')) }}
          <fieldset>
            {{ Form::hidden('concilia_id', Request::segment(2)) }}
          
            <!-- Multiple Radios (inline) -->
            <div class="form-group libroMasmenos">
              <label class="col-md-2 control-label" for="radios">Opciones</label>
              <div class="col-md-10"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="secciones_radios" id="nc" value="1" checked="checked">
                  Nota de credito
                </label> 
                <label class="radio-inline" for="radios-2">
                  <input type="radio" name="secciones_radios" id="nd" value="2">
                  Nota de debito
                </label>
                <label class="radio-inline" for="radios-3">
                  <input type="radio" name="secciones_radios" id="dt" value="3">
                  Depositos en transito
                </label> 
                <label class="radio-inline" for="radios-4">
                  <input type="radio" name="secciones_radios" id="cc" value="4">
                  Cheques en circulacion
                </label>
                <label class="radio-inline" for="radios-5">
                  <input type="radio" name="secciones_radios" id="sb" value="5">
                  Saldo en banco a {{ $concilia->f_endpresentdo }}
                </label>
              </div>
            </div>

            <hr>
            
            <div class="form-group catalogo4s">
              <label class="col-md-3 control-label">Cuentas</label>
              <div class="col-md-9">
                {{ Form::select('catalogo4_id', ['' => 'Selecione una cuenta ingresos'] + $catalogo4s, 0, ['class' => 'form-control']) }}
                {!! $errors->first('catalogo4_id', '<li style="color:red">:message</li>') !!}
              </div>
            </div> 
            
            <div class="form-group catalogo6s" style="display: none;">
              <label class="col-md-3 control-label">Cuentas</label>
              <div class="col-md-9">
                {{ Form::select('catalogo6_id', ['' => 'Selecione una cuenta de gastos!'] + $catalogo6s, 0, ['class' => 'form-control']) }}
                {!! $errors->first('catalogo6_id', '<li style="color:red">:message</li>') !!}
              </div>
            </div> 

            <div class="form-group detalle">
              <label class="col-md-3 control-label">Detalle</label>
              <div class="col-md-9">
                {{ Form::text('detalle', old('detalle'),
                  array(
                      'class' => 'form-control',
                      'id' => 'detalle',
                      'placeholder' => 'Escriba el detalle!',
                      'autocomplete' => 'off',
                  ))
                }} 
                {!! $errors->first('detalle', '<li style="color:red">:message</li>') !!}
              </div>
            </div>  
            
            <div class="form-group monto">
              <label class="col-md-3 control-label">Monto</label>
              <div class="col-md-9">
                {{ Form::text('monto', old('monto'),
                  array(
                      'class' => 'form-control',
                      'id' => 'monto',
                      'placeholder' => 'Escriba el monto!',
                      'autocomplete' => 'off',
                  ))
                }} 
                {!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
              </div>
            </div>  
          </fieldset>       
          
          <div class="form-actions text-right">
            {{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
            <button type="button" class="btn btn-default" data-dismiss="modal">
              Cancel
            </button>
          </div>
        {{ Form::close() }}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->