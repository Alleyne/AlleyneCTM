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
                <h2>Unidades </h2>
                <div class="widget-toolbar">
                    @if (Cache::get('esAdminkey'))
                        <a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>            
 
                        @if ($seccion->tipo==2 or $seccion->tipo==4) 
                            <a href="{{ URL::route('createun', $seccion->id) }}" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Unidad individual</a>
                        @endif
                        
                        @if ($seccion->tipo==1 or $seccion->tipo==3) 
                            <a href="{{ URL::route('createungrupo', array($seccion->id)) }}" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Unidades en grupo</a>
                        @endif            

                    @else
                        @if (Cache::get('esJuntaDirectivakey') || Cache::get('esAdminDeBloquekey'))
                            <a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>            
                        @endif 
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
                                <th>CÓDIGO</th>                          
                                <th>ESTATUS</th>                                   
                                <th>PROPIETARIOS</th> 
                                <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datos as $dato)
                                <tr>
                                    <td col width="40px">{{ $dato->id }}</td>
                                    <td col width="100px"><strong>{{ $dato->codigo }}</strong></td>

                                    @if ($dato->estatus == 'Paz y salvo')
                                        <td col width="60px"><span class="label label-success">Paz y salvo</span></td>
                                    @else
                                        <td col width="60px"><span class="label label-danger">Moroso</span></td>
                                    @endif
                                    <td>{{ $dato->propietarios }}</td>

                                    @if (Cache::get('esAdminkey'))
                                        <td col width="60px" align="right">
                                            <ul class="demo-btns">
                                                @if ($dato->inicializada==0)
                                                    <li>
                                                         <a href="{{ URL::route('inicializaUn', $dato->id) }}" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-fire"></i></a>
                                                    </li> 
                                                @endif
                                                <li>
                                                     <a href="{{ URL::route('uns.show', $dato->id) }}" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-folder-open"></i></a>
                                                </li>                
                                            </ul>
                                        </td>
                                    @elseif (Cache::get('esAdminDeBloquekey'))
                                        <td col width="70px" align="right">
                                            <ul class="demo-btns">
                                                <li>
                                                    <a href="{{ URL::route('createPago', $dato->id) }}" class="btn bg-color-yellow txt-color-white btn-xs" rel="tooltip" data-placement="top" data-original-title="Registrar pago"><i class="glyphicon glyphicon-edit"></i></a>
                                                </li>

                                                <li>
                                                    <a href="{{ URL::route('ecuentas', array($dato->id, 'completo')) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="glyphicon glyphicon-file"></i></a>
                                                </li>                                                
                                                
                                                <li>
                                                     <a href="{{ URL::route('indexprops', array($dato->id, $seccion->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-male"></i></a>
                                                </li>        

                                                <li>
                                                     <a href="{{ URL::route('uns.show', $dato->id) }}" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a>
                                                </li>                
                                                
                                                <li>
                                                     <a href="{{ URL::route('uns.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
                                                </li>
                                            </ul>
                                        </td>
                                   
                                    @elseif (Cache::get('esJuntaDirectivakey'))
                                        <td col width="170px" align="right">
                                            <ul class="demo-btns">
                                                <li>
                                                    <a href="{{ URL::route('createPago', $dato->id) }}" class="btn bg-color-yellow txt-color-white btn-xs" rel="tooltip" data-placement="top" data-original-title="Registrar pago"><i class="glyphicon glyphicon-edit"></i></a>
                                                </li>

                                                <li>
                                                    <a href="{{ URL::route('ecuentas', array($dato->id, 'completo')) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="glyphicon glyphicon-file"></i></a>
                                                </li>                                                
                                                
                                                <li>
                                                     <a href="{{ URL::route('indexprops', array($dato->id, $seccion->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-male"></i></a>
                                                </li>        

                                                <li>
                                                     <a href="{{ URL::route('uns.show', $dato->id) }}" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i></a>
                                                </li>                
                                                
                                                <li>
                                                     <a href="{{ URL::route('uns.edit', $dato->id) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
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