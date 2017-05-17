@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Informes de diario de caja')

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
                <h2>Informes de Diarios de cajas </h2>
                <div class="widget-toolbar">
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
                                <th>APROBADO</th>
                                <th>DEPOSITADO</th>
                                <th>APROBADO POR</th>
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $dato)
                                <tr>
                                    <td col width="50px"><strong>{{ $dato->id }}</strong></td>
                                    <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->fecha)->format('M j\\, Y') }}</td>
                                    <td col width="90px"><strong>{{ $dato->aprobado ? "Si" : 'No' }}</strong></td>
                                    <td col width="90px"><strong>{{ $dato->aprobado ? "Si" : 'No' }}</strong></td>
                                    <td><strong>{{ $dato->aprobadopor }}</strong></td>

                                    @if (Cache::get('esAdminkey'))
                                        <td col width="340px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->aprobado == 0)
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li> 
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.edit', $dato->id) }}" class="btn btn-xs btn-warning"><span class="glyphicon glyphicon-list-alt"></span> Aprobar Depositar</a>
                                                    </li> 
                                                    <li>
                                                        <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-wrench"></span> Ajustar</a>
                                                    </li> 
                                                @else
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li>  
                                                @endif
                                            </ul>
                                        </td>
                                    @elseif (Cache::get('esAdministradorkey'))
                                        <td col width="340px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->aprobado == 0)
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li>                
                                                    <li>
                                                        <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-wrench"></span> Ajustar</a>
                                                    </li> 
                                                @else
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li>  
                                                @endif
                                            </ul>
                                        </td>
                                    @elseif (Cache::get('esContadorkey'))
                                        <td col width="340px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->aprobado == 0)
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li>                
                                                    <li>
                                                        <a href="#" class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-wrench"></span> Ajustar</a>
                                                    </li> 
                                                @else
                                                    <li>
                                                        <a href="{{ URL::route('diariocajas.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Informe Diario de Caja</a>
                                                    </li>  
                                                @endif
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