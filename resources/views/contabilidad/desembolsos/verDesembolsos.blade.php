@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Desembolsos de caja chica')

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
                <h2>Desembolsos de Caja Chica</h2>
                <div class="widget-toolbar">
                    <a href="{{ URL::route('cajachicas.index') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>

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
                                <th>FECHA</th>  
                                <th>CHEQUE</th>
                                <th>MONTO</th>
                                <th>APROBADO</th>
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $dato)
                                <tr>
                                    <td col width="30px"><strong>{{ $dato->id }}</strong></td>
                                    <td col width="70px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->fecha)->format('M j\\, Y') }}</td>
                                    <td col width="60px"><strong>{{ $dato->cheque }}</strong></td>
                                    <td col width="60px"><strong>{{ $dato->monto }}</strong></td>                                    
                                    <td col width="40px"><strong>{{ $dato->aprobado ? "Si" : 'No' }}</strong></td>
                                    @if (Cache::get('esAdminkey'))
                                        <td col width="170px" align="right">
                                            @if($dato->aprobado == 0)
                                                <ul class="demo-btns">
                                                    <li>
                                                        <a href="{{ URL::route('desembolsos.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe de Desembolo</a>
                                                    </li>                
                                                    <li>
                                                        <a href="{{ URL::route('aprobarInforme', array($dato->id, $cajachica_id)) }}" class="btn btn-xs btn-warning"><span class="glyphicon glyphicon-list-alt"></span> Aprobar Desembolso</a>
                                                    </li> 
                                                </ul>
                                            @else
                                                <ul class="demo-btns">
                                                    <li>
                                                        <a href="{{ URL::route('desembolsos.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe de Desembolo</a>
                                                    </li>                
                                                </ul>
                                            @endif
                                        </td>
                                    @elseif (Cache::get('esContadorkey'))
                                        <td col width="100px" align="right">
                                            <ul class="demo-btns">
                                                <li>
                                                    <a href="{{ URL::route('desembolsos.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe de Desembolo</a>
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