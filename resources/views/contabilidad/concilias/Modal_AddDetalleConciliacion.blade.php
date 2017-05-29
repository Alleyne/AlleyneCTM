
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
              <label class="col-md-2 control-label" for="radios">Secciones</label>
              <div class="col-md-10"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="secciones_radios" id="libro-mas-1" value="1" checked="checked">
                  Libro mas
                </label> 
                <label class="radio-inline" for="radios-2">
                  <input type="radio" name="secciones_radios" id="libro-menos-2" value="2">
                  Libro menos
                </label>
                <label class="radio-inline" for="radios-3">
                  <input type="radio" name="secciones_radios" id="banco-mas-1" value="3">
                  Banco mas
                </label> 
                <label class="radio-inline" for="radios-4">
                  <input type="radio" name="secciones_radios" id="banco-menos-2" value="4">
                  Banco menos
                </label>
              </div>
            </div>

            <!-- Multiple Radios (inline) -->
            <div class="form-group DteLibroMas">
              <label class="col-md-3 control-label" for="radios">Libro mas</label>
              <div class="col-md-9"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="DteLibroMas_radios" id="ncDteLibroMas-1" value="1" checked="checked">
                  Nota de credito
                </label> 
                <label class="radio-inline" for="radios-2">
                  <input type="radio" name="DteLibroMas_radios" id="ajDteLibroMas-2" value="2">
                  Ajuste por error
                </label>
              </div>
            </div>

            <!-- Multiple Radios (inline) -->
            <div class="form-group DteLibroMenos" style="display: none;">
              <label class="col-md-3 control-label" for="radios">Libro menos</label>
              <div class="col-md-9"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="DteLibroMenos_radios" id="ndDteLibroMenos-1" value="1" checked="checked">
                  Nota de debito
                </label> 
                <label class="radio-inline" for="radios-2">
                  <input type="radio" name="DteLibroMenos_radios" id="ajDteLibroMenos-2" value="2">
                  Ajuste por error
                </label>
              </div>
            </div>            

            <!-- Multiple Radios (inline) -->
            <div class="form-group DteBancoMas" style="display: none;">
              <label class="col-md-3 control-label" for="radios">Banco mas</label>
              <div class="col-md-9"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="DteBanvoMas_radios" id="dtDteBanvoMas-1" value="1" checked="checked">
                  Depositos en transito
                </label> 
              </div>
            </div>

            <!-- Multiple Radios (inline) -->
            <div class="form-group DteBancoMenos" style="display: none;">
              <label class="col-md-3 control-label" for="radios">Banco menos</label>
              <div class="col-md-9"> 
                <label class="radio-inline" for="radios-1">
                  <input type="radio" name="DteBancoMenos_radios" id="ccDteBancoMenos-1" value="1" checked="checked">
                  Cheques en circulacion
                </label> 
              </div>
            </div>            

            <hr>

            <div class="form-group">
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
            
            <div class="form-group">
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