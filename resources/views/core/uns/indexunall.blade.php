@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Unidades')

@section('content')
    <div class="row show-grid">
        <div class="col-xs-12 col-sm-6 col-md-12">        
            <!-- NEW WIDGET START -->
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-colorbutton="true" data-widget-fullscreenbutton="true">
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
                    <h2>Unidades </h2>
                    <div class="widget-toolbar">
                     </div>
                </header>

                <div><!-- widget div-->
                    <div class="jarviswidget-editbox"><!-- widget edit box -->
                        <!-- This area used as dropdown edit box -->
                    </div><!-- end widget edit box -->
                    
                    <div class="widget-body"><!-- widget content -->
                        <div class="widget-body-toolbar">
                        </div>
                        <table id="dt_basic" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>CÓDIGO</th>                          
                                    <th>ESTATUS</th>                                   
                                    <th>PROPIETARIOS</th> 
                                    <th>ACTIVA</th>
                                    <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datos as $dato)
                                    <tr>
                                        <td col width="40px">{{ $dato->id }}</td>
                                        <td col width="80px"><strong>{{ $dato->codigo }}</strong></td>

                                        @if ($dato->estatus == 'Paz y salvo')
                                            <td col width="60px"><span class="label label-success">Paz y salvo</span></td>
                                        @else
                                            <td col width="60px"><span class="label label-danger">Moroso</span></td>
                                        @endif
                                        <td>{{ $dato->propietarios }}</td>
                                        <td col width="10px">{{ $dato->activa ? '' : 'inactiva' }}</td>
                                        @if (Cache::get('esAdminkey') || Cache::get('esAdministradorkey'))
                                            <td col width="70px" align="right">
                                                <ul class="demo-btns">
                                                    <li>
                                                        <a href="{{ URL::route('indexPagos', $dato->id) }}" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-usd"></span></a>
                                                    </li> 
                                                    <li>
                                                        <a href="{{ URL::route('uns.show', $dato->id) }}" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-folder-open"></span></a>
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
    {{-- <script src="{{ URL::asset('assets/backend/js/libs/jquery-1.12.4.min.js') }}"></script> --}}
    {{-- <script src="{{ URL::asset('assets/backend/js/datatables/jquery.dataTables-1.10.15.min.js') }}"></script> --}}
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
         
            // DataTable
            $('#dt_basic').DataTable( {
                "paging": false,
                "scrollY": "385px",
                "scrollCollapse": true,
                "stateSave": true,
          
                 "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay datos disponibles para esta tabla",
                    "info":           "Mostrando _END_ de un total de _MAX_ unidades",
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
            } )           
         
 
        } );
    </script>
@stop