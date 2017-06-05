
<!-- Modal -->
<div class="modal fade" id="Modal_editarEfectivoEnCaja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
          <h4 class="modal-title" id="myModalLabel">Editar el saldo de Caja chica</h4>
      </div>
      <div class="modal-body">

        {{ Form::open(array('class' => 'form-horizontal', 'route' => array('desembolsos.update', 1),'method' => 'PUT')) }}
      
          <fieldset>
            {{ Form::hidden('desembolso_id', Request::segment(2)) }}
            <div class="alert alert-info fade in">
              <button class="close" data-dismiss="alert">
                ×
              </button>
              <i class="fa-fw fa fa-info"></i>
              <strong> Edite saldo actual de Caja chica para reflejar sobrante o faltante</strong>
             </div>

            <div class="form-group">
              <label class="col-md-3 control-label">Saldo actual</label>
              <div class="col-md-9">
                {{ Form::text('saldo', old('saldo'),
                  array(
                      'class' => 'form-control',
                      'id' => 'saldo',
                      'placeholder' => 'Escriba el saldo actual de la Caja chica!',
                      'autocomplete' => 'off',
                  ))
                }} 
                {!! $errors->first('saldo', '<li style="color:red">:message</li>') !!}
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