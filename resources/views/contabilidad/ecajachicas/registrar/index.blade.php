@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Egreso de diario de Caja Chica')

@section('content')

    <!-- widget grid -->
    <section id="widget-grid" class="">
    
        <!-- row -->
        <div class="row">
    
            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!-- Widget ID (each widget will need unique ID)-->

                
                    @if ($status == 1)                                          
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>ATENCION! </strong>
                            La Caja chica no existe, favor crear una!
                        </div>                    
                    @elseif ($status == 2)   
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>ATENCION! </strong>
                            La Caja chica se encuentra cerrada!
                        </div>                    
                    @elseif ($status == 3)       
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>ATENCION! </strong>
                            La Caja chica no tiene saldo!
                        </div>  
                    @else
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>ATENCION! </strong>
                            Caja chica saldo actual <strong> B/.{{ $saldoCajaChica }} </strong>
                        </div>  
                    @endif

                <div class="jarviswidget jarviswidget-color-greenLight" id="wid-id-1" data-widget-editbutton="true" data-widget-deletebutton="false">
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">
    
                    data-widget-colorbutton="false"
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true"
                    data-widget-sortable="false"
                    -->
                    <header>
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>Facturas de Egreso de Caja</h2>
                        <div class="widget-toolbar">
                            @if ($status == 4)                                          
                                @if (Cache::get('esAdminkey'))
                                    <a href="{{ URL::route('ecajachicas.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Registrar factura de egreso de Caja Chica</a>
                                @endif
                            @endif
                        </div>
                    </header>
    
                    <!-- widget div-->
                    <div>
    
                        <!-- widget edit box -->
                        <div class="jarviswidget-editbox">
                            <!-- This area used as dropdown edit box -->
    
                        </div>
                        <!-- end widget edit box -->
    
                        <!-- widget content -->
                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar">
                                <div class="col-xs-3 col-sm-7 col-md-7 col-lg-11 text-right">

                                </div>
                            </div>
                            
                            <table id="dt_basic" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>FECHA</th>  
                                        <th>A FAVOR DE</th>
                                        <th col width="20px">TOTAL FAC</th>
                                        <th col width="20px">TOTAL DETALLES</th>
                                        <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $dato)
                                        <td col width="40px"><strong>{{ $dato->id }}</strong></td>
                                        <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->fecha)->format('M j\\, Y') }}</td>
                                        <td><strong>{{ $dato->afavorde }}</strong></td>
                                        <td>{{ $dato->total }}</td>
                                        @if ($dato->total == $dato->totaldetalle)
                                            <td>{{ $dato->totaldetalle }}</td>
                                        @else
                                            <td><mark>{{ $dato->totaldetalle }}</mark></td>
                                        @endif
                                        <td col width="240px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->etapa == 1)
                                                    <li>
                                                        <span class="label label-warning">Registrando</span>
                                                    </li>
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', array($dato->id)) }}" class="btn btn-info btn-xs"> Detalles</a>
                                                    </li>                   
                                                    <li>
                                                        {{Form::open(array(
                                                            'route' => array('ecajachicas.destroy', $dato->id),
                                                            'method' => 'DELETE',
                                                            'style' => 'display:inline'
                                                        ))}}

                                                            {{Form::button('Borrar', array(
                                                                'class' => 'btn btn-danger btn-xs',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#confirmDelete',
                                                                'data-title' => 'Borrar egreso de caja menuda',
                                                                'data-message' => 'Esta seguro(a) que desea borrar el presente egreso de caja menuda?',
                                                                'data-btncancel' => 'btn-default',
                                                                'data-btnaction' => 'btn-danger',
                                                                'data-btntxt' => 'Borrar egreso de caja menuda'
                                                            ))}}
                                                        {{Form::close()}}
                                                    </li>
                                                
                                                @elseif ($dato->etapa == 2)
                                                    <li>
                                                        <div id="ask_5">
                                                            <a href="{{ URL::route('contabilizaDetallesEcajachica', $dato->id) }}" class="btn btn-success btn-xs"> Pagar y contabilizar</a>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', $dato->id) }}" class="btn btn-info btn-xs"> Detalles</a>
                                                    </li>                   
                                                    <li>
                                                        {{Form::open(array(
                                                            'route' => array('ecajachicas.destroy', $dato->id),
                                                            'method' => 'DELETE',
                                                            'style' => 'display:inline'
                                                        ))}}

                                                            {{Form::button('Borrar', array(
                                                                'class' => 'btn btn-danger btn-xs',
                                                                'data-toggle' => 'modal',
                                                                'data-target' => '#confirmDelete',
                                                                'data-title' => 'Borrar egreso de caja menuda',
                                                                'data-message' => 'Esta seguro(a) que desea borrar el presente egreso de caja menuda?',
                                                                'data-btncancel' => 'btn-default',
                                                                'data-btnaction' => 'btn-danger',
                                                                'data-btntxt' => 'Borrar egreso de caja menuda'
                                                            ))}}
                                                        {{Form::close()}}
                                                    </li>
                                                @elseif ($dato->etapa == 3)
                                                    <li>
                                                        <span class="label label-success">Pagada y Contabilizada</span>
                                                    </li>                                                           
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', $dato->id) }}" class="btn btn-info btn-xs"> Detalles</a>
                                                    </li>
                                                @endif  
                                            </ul>                                               
                                        </td>
                                        </tr>
                                    @endforeach
                                </tbody>
     
                            </table>
                            <!-- Incluye la modal box -->
                            @include('templates.backend._partials.modal_confirm')
                        </div>
                        <!-- end widget content -->
                    </div>
                    <!-- end widget div -->
                </div>
                <!-- end widget -->
            </article>
            <!-- WIDGET END -->
        </div>
        <!-- end row -->
    
    </section>
    <!-- end widget grid -->

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