@extends('backend._layouts.default')

@section('main')
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
                <h2>Pagos </h2>
                <div class="widget-toolbar">
                    <a href="{{ URL::route('uns.show', $data['un_id']) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>            
                    @if (Cache::get('esAdminkey') || Cache::get('esAdminDeBloquekey'))
                        <a href="{{ URL::route('indexPagos', $data['un_id']) }}" class="btn btn-success"><i class="fa fa-plus"></i> Registrar pago</a>
                    @endif 
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
                        <h7><p class="text-center">A {{ $data['fecha'] }}</p></h7>
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
                                        <th col width="90px">Factura</th>
                                        <th>Detalle</th>
                                        <th col width="110px">Vence</th>                    
                                        <th col width="75px">Cargo</th>
                                        <th col width="30px">Pagado</th>
                                        <th col width="90px">Total</th>
                                    </tr></strong>
                                </thead>
                                <tbody>
                                    @foreach ($imps as $imp)
                                        <tr>
                                            <td><strong>{{ $imp->fecha }}</strong></td>                                      
                                            <td>{{ $imp->detalle }}</td>
                                            <td>{{ $imp->f_vencimiento }}</td>                      
                                            <td>{{ $imp->importe }}</td>
                                            
                                            @if($imp->pagada==0)
                                                <td><mark>{{ $imp->pagada ? 'Si' : 'No'}}</mark></td>
                                            @else 
                                                <td>{{ $imp->pagada ? 'Si' : 'No'}}</td>
                                            @endif
                                            
                                            <td>{{ $imp->importe }}</td>                                        
                                        </tr>
                                        @foreach ($recs as $rec)
                                            <tr>
                                                @if($imp->id==$rec->id) 
                                                    <td><strong>{{ $imp->fecha }}</strong></td>                                      
                                                    <td>====> Recargo por pago atrasado</td>
                                                    <td></td>                       
                                                    <td>{{ $imp->recargo}}</td>
                                                    
                                                    @if($imp->recargo_pagado==0)    
                                                        <td><mark>{{ $imp->recargo_pagado ? 'Si' : 'No'}}</mark></td>
                                                    @else   
                                                        <td>{{ $imp->recargo_pagado ? 'Si' : 'No'}}</td>
                                                    @endif

                                                    <td>{{ $imp->recargo_pagado ?  '':$imp->recargo}}</td>                                      
                                                @endif
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>    
                        </div><!-- end row -->
                        
                        <div class="row">
                              <div class="col-xs-10">
                                <p class="text-right"><strong>Total Adeudado</strong></p>
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
                                <p class="text-right"><strong>Total a pagar</strong></p>
                              </div>
                              <div class="col-xs-2">
                                <p class="text-right"><strong>{{ $data['total_adeudado'] }}</strong></p>
                              </div>
                        </div>
                    </div>
                
                </div><!-- end widget content -->
            </div><!-- end widget div -->
        </div>
        <!-- end widget -->
        <!-- WIDGET END -->
    </div>        
</div>
@stop