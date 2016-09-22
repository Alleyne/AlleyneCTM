
<div class="row show-grid">
    <div class="col-xs-12 col-sm-6 col-md-12">        
        <!-- NEW WIDGET START -->
        <!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="true" data-widget-deletebutton="false">
            <!-- widget options:
            usage: <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">

            data-widget-colorbutton="false"
            data-widget-editbutton="false"
            data-widget-togglebutton="false"
            data-widget-deletebutton="false"
            data-widget-fullscreenbutton="false"
            data-widget-custombutton="false"
            data-widget-collapsed="true"
            data-widget-sortable="false"-->
            
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <h2>Estado de cuentas </h2>
                <div class="widget-toolbar">
                    <a href="{{ URL::route('uns.show', $data['un_id']) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>            
                </div>
            </header>

            <div><!-- widget div-->
                <div class="jarviswidget-editbox"><!-- widget edit box -->
                    <!-- This area used as dropdown edit box -->
                </div><!-- end widget edit box -->
                
                <div class="widget-body padding"><!-- widget content -->
                    
                    <div class="row"><!-- row -->
                        <div class="col-xs-10">
                            <address>
                              <strong>{{ $data['phnombre'] }}</strong><br>
                              795 Folsom Ave, Suite 600<br>
                              San Francisco, CA 94107<br>
                              <abbr title="Phone">P:</abbr> (123) 456-7890
                            </address>
                        </div>
                        <div class="col-xs-2">
                            <img style="height: 40px; border-radius: 8px;" src="{{asset('assets/backend/img/logo.png')}}" class="img-responsive" alt="Responsive image">
                        </div>
                    </div><!-- end row -->

                    <!-- <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK"> -->

                    <div class="row">
                      <div class="col-xs-12">
                        <h4><p class="text-center"><strong>ESTADO DE CUENTAS</strong></p></h4>
                        <h7><p class="text-center"><strong>UNIDAD NO. {{ $data['codigo'] }}</strong></p></h7>       
                        <h7><p class="text-center">al dia  {{ $data['fecha'] }}</p></h7>
                      </div>
                    </div>  

                    <div class="row">
                        <div class="col-xs-4">
                            <address>
                              <strong>{{ $data['propnombre'] }}</strong><br>
                              795 Folsom Ave, Suite 600<br>
                              San Francisco, CA 94107<br>
                              <abbr title="Phone">P:</abbr> (123) 456-7890
                            </address>
                        </div>
                            
                        <div class="col-xs-2">
                        </div>
                    </div>

                    <div class="col-xs-12">
                        <div class="row">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <strong><tr>
                                        <th>Detalle</th>
                                        <th col width="110px" class="text-center">Pagar antes de:</th>                    
                                        <th col width="110px" class="text-center">Deuda</th>
                                    </tr></strong>
                                </thead>
                                <tbody>
                                    @if (count($imps)>0)
                                      <tr>
                                        <td>==== Cuotas de mantenimiento por pagar ====</td>
                                        <td></td>
                                        <td></td>                                        
                                      </tr>                                    
  
                                      @foreach ($imps as $imp)
                                        <tr>
                                          <td>Debe cuota de mantenimiento del mes de <strong>{{ $imp->mes_anio }}</strong></td>
                                          <td align="right">{{ $imp->f_vencimiento }}</td>                      
                                          <td col width="90px" align="right"><mark>{{ $imp->importe }}</mark></td>                                        
                                        </tr>
                                      @endforeach
                                    @endif
                                    
                                    @if (count($recs)>0)
                                      <tr>
                                        <td>==== Recargos por pagar ====</td>
                                        <td></td>
                                        <td></td>                                        
                                      </tr>                                    
  
                                      @foreach ($recs as $rec)
                                        <tr>
                                          <td>Debe recargo del mes de <strong>{{ $rec->mes_anio }}</strong></td>
                                          <td></td>                       
                                          <td col width="90px" align="right"><mark>{{ $rec->recargo}}</mark></td>
                                        </tr>
                                      @endforeach
                                    @endif
                                </tbody>
                            </table>    
                        </div><!-- end row -->
                    <br>    
                    <div class="col-xs-12">
                        <div class="row">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                  <tr>
                                    <td><strong>Deuda acumulada a cancelar</strong></td>
                                    <td col width="200px"></td>
                                    <td col width="90px" align="right"><strong>{{ $data['total'] }}</strong></td>                                        
                                  </tr>                                    
                                  <tr>
                                    <td><strong>(-) Saldo a favor por pagos anticipados</strong></td>
                                    <td col width="200px"></td>
                                    <td col width="90px" align="right"><strong>{{ $data['pagos_anticipados'] }}</strong></td>                                        
                                  </tr>   
                                  <tr>
                                    <td></td>
                                    <td col width="200px"></td>
                                    <td col width="90px" align="right">========</td>                                        
                                  </tr>   
                                  <tr>
                                    <td><strong>Total de la deuda</strong></td>
                                    <td col width="200px"></td>
                                    <td col width="90px" align="right"><strong>{{ $data['total_adeudado'] }}</strong></td>                                                                    
                                  </tr>   
                                </tbody>
                            </table>    
                        </div><!-- end row -->
                    </div>
                
                </div><!-- end widget content -->
            </div><!-- end widget div -->
        </div>
        <!-- end widget -->
        <!-- WIDGET END -->
    </div>        
</div>
