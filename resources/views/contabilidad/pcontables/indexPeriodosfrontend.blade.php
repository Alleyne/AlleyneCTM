@extends('templates.frontend._layouts.unify')

@section('title', '| Pagos efectuados')

@section('content')
<!--Hover Rows-->


    <div class="alert alert-success" role="alert">
        <strong>Estimado Propietario</strong>, acontinuacion se muestran todos los Periodos contables a la fecha.
    </div>
        <table id="dt_basic" class="table table-hover">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>PERIODO</th>
                    <th>F_CIERRE</th>
                    <th>CERRADO</th>
                    <th class="text-right"><i class="fa fa-gear fa-lg"></i></th>                                       
                </tr>
            </thead>
            <tbody>
                @foreach ($datos as $dato)
                    <tr>
                        <td col width="20px" align="right"><strong>{{ $dato->id }}</strong></td>
                        <td col width="50px" align="left"><strong>{{ $dato->periodo }}</strong></td>
                        <td col width="60px" align="left">{{ $dato->f_cierre }}</td>
                        <td col width="60px" align="center">{{ $dato->cerrado ? 'Si' : 'No' }}</td>
                        <td col width="510px" align="right">
                            @if ( $dato->cerrado == 0 )
                                <a href="{{ URL::route('hojadetrabajos.show', $dato->id) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-search"></i> Hoja trabajo</a>
                                <a href="{{ URL::route('ctdiarios.show', $dato->id) }}" class="btn bg-color-green txt-color-white btn-xs"><i class="fa fa-search"></i> Diario</a>
                                <a href="{{ URL::route('estadoderesultado', $dato->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i> Estado Resultado Proyectado</a>
                                <a href="{{ URL::route('balancegeneral', array($dato->id, $dato->periodo)) }}" class="btn btn-warning btn-xs"><i class="fa fa-search"></i> Balance General Proyectado</a>
                            @else
                                <a href="{{ URL::route('hojadetrabajo', $dato->id) }}" class="btn btn-default txt-color-purple btn-xs"><i class="glyphicon glyphicon-lock"></i> Hoja de trabajo</a>
                                <a href="{{ URL::route('diarioFinal', $dato->id) }}" class="btn btn-default txt-color-green btn-xs"><i class="glyphicon glyphicon-lock"></i> Diario</a>
                                <a href="{{ URL::route('er', $dato->id) }}" class="btn btn-default txt-color-blue btn-xs"><i class="glyphicon glyphicon-lock"></i> Estado Resultado Final</a>
                                <a href="{{ URL::route('bg', $dato->id) }}" class="btn btn-default txt-color-yellow btn-xs"><i class="glyphicon glyphicon-lock"></i> Balance General Final</a>
                            @endif                                                          

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    <div class="row">
      <div class="col-xs-12">
        <p class="text-center">© Copyright 2016-2025 ctmaster.net - All Rights Reserved</p>
      </div>
    </div> 
<!--End Hover Rows-->
@stop

@section('relatedplugins')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script> 
    <script src="http://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
 
    <script>
        $(document).ready(function() {
            // Setup - add a text input to each footer cell
            $('#dt_basic tfoot th').each( function () {
                var title = $('#dt_basic thead th').eq( $(this).index() ).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            } );
         
            // DataTable
            var table = $('#dt_basic').DataTable( {
                stateSave: true,
          
                 "language": {
                    "decimal":        "",
                    "emptyTable":     "No hay datos disponibles para esta tabla",
                    "info":           "Mostrando _END_ de un total de _MAX_ pagos",
                    "infoEmpty":      "",
                    "infoFiltered":   "",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "lengthMenu":     "Mostrar _MENU_ pagos",
                    "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                    "search":         "Buscar:",
                    "zeroRecords":    "No se encontro ningun pago con ese filtro",
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
            } );           

            // Restore state
            if ( state ) {
              table.columns().eq( 0 ).each( function ( colIdx ) {
                var colSearch = state.columns[colIdx].search;
                
                if ( colSearch.search ) {
                  $( 'input', table.column( colIdx ).footer() ).val( colSearch.search );
                }
              } );
              
              table.draw();
            }
         
            // Apply the search
            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
                    table
                        .column( colIdx )
                        .search( this.value )
                        .draw();
                } );
            } );
        } );
    </script>
@stop