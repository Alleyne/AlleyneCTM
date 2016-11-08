@extends('templates.backend._layouts.smartAdmin')

@section('title', '| All Posts')

@section('content')
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
                    @if (count($imps)==0 && count($recs)==0 && $data['activa']==1)
                      @if (count($ants)>0)
                        <div class="col-xs-12">
                              <div class="row">
                                  <table class="table table-bordered table-striped">
                                      <thead>
                                          <strong><tr>
                                              <th>Detalle</th>
                                              <th class="text-center">Importe</th>                    
                                              <th class="text-center">Descuento</th>
                                              <th class="text-center">Total</th>
                                          </tr></strong>
                                      </thead>
                                      <tbody>
                                          @if (count($ants)>0)
                                            <tr>
                                              <td>==== Pagos anticipados con descuento ====</td>
                                              <td></td>
                                              <td></td>                                        
                                              <td></td> 
                                            </tr>                                    
        
                                            @foreach ($ants as $ant)
                                              <tr>
                                                <td>{{ $ant->detalle }}</td>
                                                <td col width="70px" align="right">{{ $ant->montoCuota }}</td>                       
                                                <td col width="70px" align="right">{{ $ant->descuento }}</td>
                                                <td col width="70px" align="right"><strong>{{ $ant->importe }}</strong></td>
                                              </tr>
                                            @endforeach
                                          @endif
                                      </tbody>
                                  </table>    
                              </div><!-- end row -->
                              
 
                        </div>
                      @endif
                      
                      @if ($data['anticipado']>0)
                        <HR WIDTH=100% ALIGN=CENTER COLOR="BLACK">
                        <div class="row">
                              <div class="col-xs-10 col-md-offset-1">
                                <p class="text-justify">Estimado propietario, su cuenta de pagos por anticipados refleja un saldo a su favor de B/. <strong>{{ $data['pagos_anticipados'] }}</strong>. Este saldo lo padra utilizar para completar futuros pagos. Gracias por mantener sus pagos al dia.</p>
                              </div>
                        </div>
                      @endif
                      
                      <div class="row">
                            <div class="col-xs-12">
                              <h3><p class="text-center"><strong>PAZ Y SALVO</strong></p></h3>
                              <p class="text-left">Gracias por mantener su estado de cuenta al dia.</p>
                            </div>
                      </div>

                    @elseif (count($imps)>0 || count($recs)>0 && $data['activa']==1)
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

                            <div class="row">
                                  <div class="col-xs-10">
                                    <p class="text-right"><strong>Deuda acumulada a cancelar</strong></p>
                                  </div>
                                  <div class="col-xs-2">
                                    <p class="text-right"><strong>{{ $data['total'] }}</strong></p>
                                  </div>
                            </div>
                          
                            <div class="row">
                                  <div class="col-xs-10">
                                    <p class="text-right"><strong>(-) Saldo a favor por pagos anticipados</strong></p>
                                  </div>
                                  <div class="col-xs-2">
                                    <p class="text-right"><strong>{{ $data['pagos_anticipados'] }}</strong></p>
                                  </div>
                            </div>
                            
                            <div class="row">
                                  <div class="col-xs-10">
                                    <p class="text-right"></p>
                                  </div>
                                  <div class="col-xs-2">
                                    <p class="text-right">========</p>
                                  </div>
                            </div>
                            
                            <div class="row">
                                  <div class="col-xs-10">
                                    <p class="text-right"><strong>Total de la deuda</strong></p>
                                  </div>
                                  <div class="col-xs-2">
                                    <p class="text-right"><strong>{{ $data['total_adeudado'] }}</strong></p>
                                  </div>
                            </div>
                      </div>

                      @if ($data['anticipado']>0)
                        <HR WIDTH=100% ALIGN=CENTER COLOR="BLACK">
                        <div class="row">
                              <div class="col-xs-10 col-md-offset-1">
                                <p class="text-justify">Estimado propietario, su cuenta de pagos por anticipados refleja un saldo a su favor de B/. <strong>{{ $data['pagos_anticipados'] }}</strong>. Este saldo lo padra utilizar para completar futuros pagos. Gracias por mantener sus pagos al dia.</p>
                              </div>
                        </div>
                      @endif
                    @else
                      <div class="row">
                            <div class="col-xs-12">
                              <h3><p class="text-center"><strong>CUENTA INACTIVA</strong></p></h3>
                            </div>
                      </div>
                    @endif
                </div><!-- end widget content -->
            <HR WIDTH=95% ALIGN=CENTER COLOR="BLACK">

            <div class="row">
              <div class="col-xs-12">
                <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
              </div>
            </div>   

            </div><!-- end widget div -->
        </div>
        <!-- end widget -->
        <!-- WIDGET END -->
    </div>        
</div>
@stop