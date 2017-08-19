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
                <h2>Pagos no indentificados </h2>
                <div class="widget-toolbar">
                    @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                        <a href="{{ URL::route('pagosnoids.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Registrar pago no identificado</a>
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
                                <th>FECHA</th>  
                                <th>BANCO</th>
                                <th>TIPO</th>
                                <th>MONTO</th> 
                                <th>UNIDAD</th>
                                <th>PROPIETARIOS</th>
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>   
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pagos as $pago)
                                <tr>
                                    <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $pago->f_pago)->format('M j\\, Y') }}</td>
                                    <td>{{ $pago->banco }}</td>
                                    <td col width="20px" align="left">{{ $pago->tipo }}</td>                                    
                                    <td col width="50px" align="left">{{ $pago->monto }}</td>  
                                    <td col width="80px"><strong>{{ $pago->codigo }}</strong></td>
                                    <td>{{ $pago->propietarios }}</td>
                                    @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                                        <td col width="125px" align="right">
                                            <ul class="demo-btns">
                                                @if ($pago->identificado == 0 && $pago->contabilizado == 0)
                                                    <li>
                                                        <a href="{{ URL::route('identificarPago', $pago->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i> </a>
                                                    </li> 
                                                @elseif ($pago->identificado == 1 && $pago->contabilizado == 0)
                                                    <li>
                                                        <a href="{{ URL::route('identificarPago', $pago->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i> </a>
                                                    </li> 
                                                    <li>
                                                        
                                                        {{Form::open(array(
                                                            //'route' => array('contabilizaPagonoid',$dato->id, $dato->f_pago, $dato->un_id, $dato->monto, $dato->banco_id, $dato->doc_no),
                                                            'route' => array('contabilizaPagonoid', $pago->id),
                                                            'method' => 'GET',
                                                            'style' => 'display:inline'
                                                        ))}}

                                                        {{Form::button('Contabilizar', array(
                                                            'class' => 'btn btn-warning btn-xs',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#confirmAction',
                                                            'data-title' => 'Contabilizar pago identificado',
                                                            'data-message' => 'ATENCION: Para contabilizar el pago ya identificado, el sistema tomara la fecha del dia de hoy como fecha real de pago, asi que existira la posibilidad de que el propietario sea penalizado con recargo por pago tardio. Es responsabilidad del propietario presentar el comprobante de pago el mismo dia en que efectua el deposito en el banco. Esta seguro(a) que desea contabilizar el presente pago identificado?',
                                                            'data-btntxt' => 'SI, contabilizar pago',
                                                            'data-btncolor' => 'btn-success'
                                                        ))}}
                                                        {{Form::close()}} 
                                                    </li>
                                                @elseif ($pago->identificado == 1 && $pago->contabilizado == 1)
                                                    <li>
                                                        <span class="label label-success">Contabilizado</span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </td>
                                    @elseif (Cache::get('esContadorkey'))
                                        <td col width="125px" align="right">
                                            <ul class="demo-btns">
                                                @if ($pago->identificado == 0)
                                                    <li>
                                                        <span class="label label-warning">Pendiente identificar</span>
                                                    </li>
                                                @elseif ($pago->identificado == 1 && $pago->contabilizado == 0)
                                                    <li>
                                                        <span class="label label-info">Pendiente contabilizar</span>
                                                    </li>
                                                @elseif ($pago->identificado == 1 && $pago->contabilizado == 1)
                                                    <li>
                                                        <span class="label label-success">Contabilizado</span>
                                                    </li>
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
            "zeroRecords":    "No se encontró ninguna unidad con ese filtro",
            "paginate": {
              "first":      "Primer",
              "last":       "Último",
              "next":       "Próximo",
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