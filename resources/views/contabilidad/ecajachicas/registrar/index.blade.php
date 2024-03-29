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
                        <div class="alert alert-info alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>ATENCION! </strong>
                            Caja chica saldo actual <strong> B/.{{ $saldoCajaChica }} </strong>
                        </div>  
                    @endif

                <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-fullscreenbutton="false" data-widget-togglebutton="false" data-widget-editbutton="true" data-widget-deletebutton="false">
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
                        <h2>Facturas de compras por Caja chica</h2>
                        <div class="widget-toolbar">
                            @if ($status == 4)                                          
                                @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                                    <a href="{{ URL::route('ecajachicas.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Registrar factura de compra por Caja Chica</a>
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
                            
                            <table id="dt_basic" class="display compact" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>FECHA</th>  
                                        <th>A FAVOR DE</th>
                                        <th class="text-right" col width="75px">T FACTURA</th>
                                        <th class="text-right" col width="85px">T DETALLES</th>
                                        <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($datos as $dato)
                                        <td col width="40px"><strong>{{ $dato->id }}</strong></td>
                                        <td col width="90px" align="left">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $dato->fecha)->format('M j\\, Y') }}</td>
                                        <td><strong>{{ $dato->afavorde }}</strong></td>
                                        <td align="right">{{ $dato->total }}</td>
                                        @if ($dato->total == $dato->totaldetalle)
                                            <td align="right">{{ $dato->totaldetalle }}</td>
                                        @else
                                            <td align="right"><mark>{{ $dato->totaldetalle }}</mark></td>
                                        @endif
                                        <td col width="195px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->etapa == 1)
                                                    <li>
                                                        <span class="label label-info">Registrando</span>
                                                    </li>
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', $dato->id) }}" class="btn btn-primary btn-xs"> Detalles</a>
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
                                                          'data-target' => '#confirmAction',
                                                          'data-title' => 'Borrar factura',
                                                          'data-message' => 'Esta seguro(a) que desea borrar el presente de factura de compras por Caja chica?',
                                                          'data-btntxt' => 'SI, borrar factura',
                                                          'data-btncolor' => 'btn-danger'
                                                      ))}}
                                                      {{Form::close()}}
                                                    </li>   

                                                @elseif ($dato->etapa == 2)
                                                    <li>
                                                        {{Form::open(array(
                                                            'route' => array('contabilizaDetallesEcajachica', $dato->id),
                                                            'method' => 'GET',
                                                            'style' => 'display:inline'
                                                        ))}}

                                                        {{Form::button('Contabilizar', array(
                                                            'class' => 'btn btn-warning btn-xs',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#confirmAction',
                                                            'data-title' => 'Contabilizar egreso de Caja chica',
                                                            'data-message' => 'Esta seguro(a) que desea contabilizar la presente factura de compra por Caja chica?',
                                                            'data-btntxt' => 'Contabilizar egreso de caja Chica',
                                                            'data-btncolor' => 'btn-warning'
                                                        ))}}
                                                        {{Form::close()}}                                                    
                                                    </li>
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', $dato->id) }}" class="btn btn-primary btn-xs"> Detalles</a>
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
                                                          'data-target' => '#confirmAction',
                                                          'data-title' => 'Borrar factura',
                                                          'data-message' => 'Esta seguro(a) que desea borrar el presente de factura de compra por Caja chica?',
                                                          'data-btntxt' => 'SI, borrar factura',
                                                          'data-btncolor' => 'btn-danger'
                                                      ))}}
                                                      {{Form::close()}}
                                                    </li>   
                                                @elseif ($dato->etapa == 3)
                                                    <li>
                                                        <span class="label label-success">Pagada y Contabilizada</span>
                                                    </li>                                                           
                                                    <li>
                                                        <a href="{{ URL::route('dte_ecajachicas.show', $dato->id) }}" class="btn btn-primary btn-xs"> Detalles</a>
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