@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Informes de diario de Caja Chica')

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
                <h2>Administracion de Caja Chica </h2>
                <div class="widget-toolbar">
                    @if (Cache::get('esAdminkey'))
                        <a href="{{ URL::route('cajachicas.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Abrir nueva Caja chica</a>
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
                                <th>F_INICIO</th>  
                                <th>RESPONSABLE POR FONDO</th>
                                <th>SALDO</th>
                                <th>F_CIERRE</th> 
                                <th>CERRADA</th>
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>   
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $dato)
                                <tr>
                                    <td col width="30px">{{ $dato->id }}</td>
                                    <td col width="75px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->f_inicio)->format('M j\\, Y') }}</td>
                                    <td>{{ $dato->responsable }}</td>
                                    <td col width="60px"><strong>{{ $dato->saldo }}</strong></td>
                                    @if(is_null($dato->f_cierre))
                                        <td></td>                                    
                                    @else
                                        <td col width="75px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->f_cierre)->format('M j\\, Y') }}</td>
                                    @endif 
                                    <td col width="70px">{{ $dato->cerrada ? "Si" : 'No' }}</td>
                                    @if (Cache::get('esAdminkey'))
                                        <td col width="330px" align="right">
                                            <ul class="demo-btns">
                                                <li>
                                                    <a href="#" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Editar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ URL::route('aumentarCajachicaCreate') }}" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Aumentar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ URL::route('disminuirCajachicaCreate') }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-minus"></i> Disminuir</a>
                                                </li>                                       
                                                <li>
                                                    <a href="{{ URL::route('cerrarCajachicaCreate') }}" class="btn btn-danger btn-xs"><i class="fa fa-lock"></i> Cerrar</a>
                                                </li> 
                                                <li>
                                                    <a href="{{ URL::route('dte_cajachicas.show', $dato->id) }}" class="btn btn-warning btn-xs"> Detalles</a>
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