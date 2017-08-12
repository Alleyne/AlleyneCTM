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
                    {{-- <a href="{{ Cache::get('indexunallkey') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a> --}}
                    <a href="{{ URL::route('indexunall') }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>

                    @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                        <div class="btn-group">
                            <a class="btn btn-info btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Otros ingresos</a>
                            <a class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">REGISTRAR INGRESO POR:</a></li>
                                <li><a href="#">Multas</a></li>
                                <li><a href="#">Otros</a></li>
                            </ul>
                        </div><!-- /btn-group --> 

                        <div class="btn-group">
                            <a class="btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Registrar cobro de cuotas y recargos</a>
                            <a class="btn btn-primary dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ URL::route('createPago', array($un_id, 4)) }}">Banca en linea</a></li>
                               
                                <li class="divider"></li>                                
                                <li><a href="{{ URL::route('createPago', array($un_id, 5)) }}">Efectivo</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 1)) }}">Cheque</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 6)) }}">Tarjeta de debito</a></li>
                                <li><a href="{{ URL::route('createPago', array($un_id, 7)) }}">Tarjeta de credito</a></li>
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
                    
                    <table id="dt_basic" class="display compact" cellspacing="0" width="100%">
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
                                    
                                    @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                                        <td col width="175px" align="right">
                                            <ul class="demo-btns">
                                                
                                            @if ($dato->entransito==0)
                                                <li>
                                                     <a href="{{ URL::route('showRecibo', $dato->id) }}" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-list"></i></a>
                                                </li>                
                                                @if ($dato->anulado==0) 
                                                    <li>
                                                        {{Form::open(array(
                                                          'route' => array('procesaAnulacionPago',$dato->id, $un_id),
                                                          'method' => 'GET',  // or DELETE
                                                          'style' => 'display:inline'
                                                        ))}}

                                                        {{Form::button('<i class="fa fa-search"></i> Anular pago', array(
                                                          'class' => 'btn btn-warning btn-xs',
                                                          'data-toggle' => 'modal',
                                                          'data-target' => '#confirmAction',
                                                          'data-title' => 'Anular pago de propietario',
                                                          'data-message' => 'Esta seguro(a) que desea anular el presente pago?',
                                                          'data-btntxt' => 'SI, anular',
                                                          'data-btncolor' => 'btn-warning'
                                                        ))}}
                                                        {{Form::close()}}                                                    
                                                    </li>
                                                @else
                                                    <li>
                                                        <span class="label label-warning">Anulado</span>
                                                    </li>
                                                @endif
                                            @else
                                                <li>
                                                    {{Form::open(array(
                                                      'route' => array('procesaChequeRecibido', $dato->id),
                                                      'method' => 'GET',  // or DELETE
                                                      'style' => 'display:inline'
                                                    ))}}

                                                    {{Form::button(' Contabilizar', array(
                                                      'class' => 'btn btn-primary btn-xs',
                                                      'data-toggle' => 'modal',
                                                      'data-target' => '#confirmAction',
                                                      'data-title' => 'Contabiliza cheque',
                                                      'data-message' => 'Esta seguro(a) que desea contabilizar el presente cheque?',
                                                      'data-btntxt' => 'SI, contabilizar',
                                                      'data-btncolor' => 'btn-primary'
                                                    ))}}
                                                    {{Form::close()}}   
                                                </li>
                                                <div id="ask_4" class="btn btn-default btn-xs">
                                                    <a href="{{ URL::route('eliminaPagoCheque', $dato->id) }}" title="Eliminar pago"><i class="fa fa-search"></i> Eliminar</a>
                                                </div>
                                            @endif
                                            </ul>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Incluye la modal box -->
                    @include('templates.backend._partials.modal_confirm')
                </div><!-- end widget content -->
            </div><!-- end widget div -->
        </div>
        <!-- end widget -->
        <!-- WIDGET END -->
    </div>        
</div>
@stop

@section('relatedplugins')
  <script src="{{ URL::asset('assets/backend/js/modalconfirm.js') }}"></script>  
  
  <script type="text/javascript">
    $(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
        "scrollY": "393px",
        "scrollCollapse": true,
        "stateSave": true,

        "language": {
            "decimal":        "",
            "emptyTable":     "No hay datos disponibles para esta tabla",
            "info":           "&nbsp;&nbsp;  Mostrando _END_ de un total de _MAX_ registros",
            "infoEmpty":      "",
            "infoFiltered":   "",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ unidades",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No se encontro ninguna unidad con ese filtro",
            "paginate": {
              "first":      "Primer",
              "last":       "Ultimo",
              "next":       "Proximo",
              "previous":   "Anterior"
            },
            "aria": {
              "sortAscending":  ": active para ordenar ascendentemente",
              "sortDescending": ": active para ordenar descendentemente"
            }
        }
      });
    })
    </script>
@stop