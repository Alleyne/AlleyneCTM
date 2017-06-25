
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
              &times;
            </button>
            <h4 class="modal-title" id="myModalLabel">Actualizar reservacion</h4>
          </div>
          <div class="modal-body">

            {{ Form::open(array('class' => 'form-horizontal', 'route' => 'actualizaEvento')) }}
              <fieldset>
                <input id="calendarevento_id" name="calendarevento_id" type="hidden">
                
                <style>
                  .datepicker{z-index:1151 !important;}
                </style>

                <div class="form-group">
                  <label class="col-md-3 control-label">Unidad</label>
                  <div class="col-md-9">
                    <input type="text" name="un" id="un" class="form-control" readonly>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-md-3 control-label">Amenidad</label>
                  <div class="col-md-9">
                    <input type="text" name="am" id="am" class="form-control" readonly>
                  </div>
                </div>

                <div class="form-group">
                  <label class="col-md-3 control-label">Inicia</label>
                  <div class="col-md-9">
                  
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
                  <label class="col-md-3 control-label">Termina</label>
                  <div class="col-md-9">
                  
                        <div class='input-group date' id='datetimepicker7'>
                            <input type='text' name="end" id="end" class="form-control">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    {!! $errors->first('end', '<li style="color:red">:message</li>') !!} 
                  
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