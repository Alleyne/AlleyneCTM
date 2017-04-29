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
                <h2>Pagos </h2>
                <div class="widget-toolbar">
                    <a href="{{ Cache::get('indexunallkey') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
                    @if (Cache::get('esAdminkey'))
                        <div class="btn-group">
                            <a class="btn btn-info btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Otros ingresos</a>
                            <a class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">REGISTRAR INGRESO POR:</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 4)) }}">Alquiler del area social</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 2)) }}">Multas</a></li>
                                
                                <li class="divider"></li>                                
                                <li><a href="#">COBRAR INGRESO POR:</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 4)) }}">Alquiler del area social</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 2)) }}">Multas</a></li>
                                <li><!-- <a href="#">Separated link</a> --></li>
                            </ul>
                        </div><!-- /btn-group --> 

                        <div class="btn-group">
                            <a class="btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Registrar cobro de cuotas y recargos</a>
                            <a class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ URL::route('createPago', array($un_id, 4)) }}">Banca en linea</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 2)) }}">Transferencia</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 3)) }}">ACH</a></li>
                                
                                <li class="divider"></li>                                
                                <li><a href="{{ URL::route('createPago', array($un_id, 5)) }}">Efectivo</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 1)) }}">Cheque</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 6)) }}">Tarjeta Clave</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 7)) }}">Visa</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 8)) }}">Master Card</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 9)) }}">American Express</a></li>
 
                                <li><!-- <a href="#">Separated link</a> --></li>
                            </ul>
                        </div><!-- /btn-group -->       
                    @endif 

                </div>
            </header>

            <div><!-- widget div-->
                <div class="jarviswidget-editbox"><!-- widget edit box -->
                    <!-- This area used as dropdown edit box -->
                </div><!-- end widget edit box -->
                
                <div class="widget-body no-padding"><!-- widget content -->
                    <div class="widget-body-toolbar">
                        <div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">
                        </div>
                    </div>
                    
                    <table id="dt_basic" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>TIPO</th>  
                                <th>NO</th>
                                <th>BANCO</th>                          
                                <th class="text-left">FECHA</th>                                   
                                <th class="text-right">MONTO</th> 
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $dato)
                                <tr>
                                    <td col width="60px"><strong>{{ $dato->id }}</strong></td>
                                    <td col width="100px" align="left">{{ $dato->trantipo->nombre }}</td>
                                    <td col width="70px"><strong>{{ $dato->trans_no }}</strong></td>
                                    <td>{{{ $dato->banco->nombre or '' }}} </td>
                                    <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->f_pago)->format('M j\\, Y') }}</td>
                                    <td col width="90px" align="right">{{ $dato->monto }}</td>
                                    
                                    @if (Cache::get('esAdminkey'))
                                        <td col width="175px" align="right">
                                            <ul class="demo-btns">
                                                
                                            @if ($dato->entransito==0)
                                                <li>
                                                     <a href="{{ URL::route('showRecibo', $dato->id) }}" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-list"></i></a>
                                                </li>                
                                                @if ($dato->anulado==0) 
                                                    <div id="ask_2" class="btn btn-default btn-xs">
                                                        <a href="{{ URL::route('procesaAnulacionPago', array($dato->id, $un_id)) }}" title="Anular pago"><i class="fa fa-search"></i> Anular pago</a>
                                                    </div>
                                                @else
                                                    <li>
                                                        <span class="label label-warning">Anulado</span>
                                                    </li>
                                                @endif
                                            @else
                                                <li>
                                                    <a href="{{ URL::route('procesaChequeRecibido', $dato->id) }}" class="btn btn-primary btn-xs"> Contabilizar</a>
                                                </li>
                                                <div id="ask_4" class="btn btn-default btn-xs">
                                                    <a href="{{ URL::route('eliminaPagoCheque', $dato->id) }}" title="Eliminar pago"><i class="fa fa-search"></i> Eliminar</a>
                                                </div>

                                            @endif
                                            </ul>
                                        </td>
                                    @elseif (Cache::get('esAdminDeBloquekey'))
                                        <td col width="60px" align="right">
                                            <ul class="demo-btns">
                                                <li>
                                                     <a href="{{ URL::route('showRecibo', $dato->id) }}" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-folder-open"></i></a>
                                                </li>                
                                            </ul>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                </div><!-- end widget content -->
            </div><!-- end widget div -->
        </div>
        <!-- end widget -->
        <!-- WIDGET END -->
    </div>        
</div>
@stop

@section('relatedplugins')
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/jquery.dataTables-cust.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/ColReorder.min.js') }}"></script>
    <script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
    
    <script type="text/javascript">
    $(document).ready(function() {
        pageSetUp();
 
        $('#dt_basic').dataTable({
            "sPaginationType" : "bootstrap_full"
        });
    })
    </script>
@stop