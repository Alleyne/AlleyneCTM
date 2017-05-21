
<!-- Modal -->
<div class="modal fade" id="myModalPagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">Pagar y contabilizar factura de egresos de caja general</h4>
      </div>
      <div class="modal-body">

        {{ Form::open(array('class' => 'form-horizontal', 'route' => 'pagarContabilizar')) }}
          <fieldset>
            
            {{ Form::hidden('detallepagofactura_id', '', array('id' => 'detallepagofactura_id')) }}
            {{-- <input id="detallepagofactura_id" name="detallepagofactura_id" type="hidden"> --}}
            
            <div class="form-group">
              <label class="col-md-3 control-label">Tipo de pago</label>
              <div class="col-md-9">
                <select name="trantipo_id" id="trantipo_id" class="form-control" onclick="createUserJsObject.ShowtipoDePago;">
                  @foreach ($trantipos as $trantipo)
                    <option id="{{ $trantipo->id }}" value="{{ $trantipo->id }}">{{ $trantipo->nombre }}</option>                 
                  @endforeach
                </select>
              </div>    
            </div>
            
            <div class="form-group chequeNo" style=" display: none;">
              <label class="col-md-3 control-label">Cheque No.</label>
              <div class="col-md-9">
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
              <label class="col-md-3 control-label">Transaccion No.</label>
              <div class="col-md-9">
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