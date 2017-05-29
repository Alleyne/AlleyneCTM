
<!-- Modal -->
<div class="modal fade" id="Modal_AddAjustePorError" tabindex="-2" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">Registra un Ajuste por error en seccion libro</h4>
      </div>
      <div class="modal-body">

        {{ Form::open(array('class' => 'form-horizontal', 'route' => 'addAjustePorError')) }}
          <fieldset>
            
            {{ Form::hidden('concilia_id', Request::segment(2)) }}
            
            <div class="form-group">
              <label class="col-md-3 control-label">Detalle</label>
              <div class="col-md-9">
                {{ Form::text('detalle', old('detalle'),
                  array(
                      'class' => 'form-control',
                      'id' => 'detalle',
                      'placeholder' => 'Escriba el detalle del ajuste por error!',
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
                      'placeholder' => 'Escriba el monto de ajuste',
                      'autocomplete' => 'off',
                  ))
                }} 
                {!! $errors->first('monto', '<li style="color:red">:message</li>') !!}
              </div>
            </div>  
          </fieldset>       
          
          <div class="form-actions">
            {{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
            <button type="button" class="btn btn-default" data-dismiss="modal">
              Cancel
            </button>
          </div>
        {{ Form::close() }}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->