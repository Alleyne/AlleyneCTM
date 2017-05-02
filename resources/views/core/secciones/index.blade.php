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
        <h2>Secciones </h2>
        <div class="widget-toolbar">
            <a href="{{ URL::route('indexblqplus', $jd->id) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>
             
            @if (Cache::get('esAdminkey'))
                <div class="btn-group">
                    <a class="btn btn-success btn-xs" href="javascript:void(0);"><i class="fa fa-plus"></i> Agregar Sección administrativa</a>
                    <a class="btn btn-success dropdown-toggle btn-xs" data-toggle="dropdown" href="javascript:void(0);"><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 1)) }}">Apartamentos</a></li>
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 2)) }}">Residencias</a></li>
                        <!-- <li><a href="{{ URL::route('createsec', array($bloque->id, 3)) }}">Locales u oficinas en Edificios</a></li>
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 4)) }}">Locales u oficinas en Residenciales</a></li>
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 5)) }}">Amenidades propias</a></li>
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 6)) }}">Amenidades comunes</a></li>
                        <li><a href="{{ URL::route('createsec', array($bloque->id, 7)) }}">Estacionamientos alquilables</a></li> -->
                        <li class="divider"></li>
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
            
            <table id="dt_basic" class="table table-hover">
                <thead>
                    <tr>
                        <th>CODIGO</th>
                        <th>NOMBRE</th>
                        <th>TIPO</th>                        
                        <th class="text-center"><i class="fa fa-gear fa-lg"></i></th>                                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secciones as $seccion)
                        <tr>
                            <td col width="60px" ><strong> {{ $seccion->codigo }} </strong></td>
                            <td>{{ $seccion->nombre }}</td>
                            <td col width="200px">
                                @if($seccion->tipo==1)    
                                    Apartamentos                                        
                                @elseif($seccion->tipo==2)    
                                    Residencias                                        
                                @elseif($seccion->tipo==3)    
                                    Locales u ofincinas en edificios                                        
                                @elseif($seccion->tipo==4)    
                                    Locales u oficinas en residenciales                                        
                                @elseif($seccion->tipo==5)    
                                    Amenidades propias                                
                                @elseif($seccion->tipo==6)    
                                    Amenidades comunes    
                                @elseif($seccion->tipo==7)    
                                    Estacionamientos alquilables    
                                @endif    
                            </td>
                            @if (Cache::get('esAdminkey'))
                                <td col width="150px" align="right">
                                    <ul class="demo-btns">
                                        <li>
                                            @if($seccion->tipo==1 or $seccion->tipo==2 or $seccion->tipo==3 or $seccion->tipo==4)    
                                                <a href="{{ URL::route('indexunplus', array($seccion->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Uns</a>
                                            @elseif($seccion->tipo==5 or $seccion->tipo==6)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Ame</a>
                                            @elseif($seccion->tipo==7)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Est</a>
                                            @endif    
                                        </li>
                                        <li>
                                            <a href="{{ URL::route('showsecplus', array($seccion->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
                                        </li>                
                                        <li>
                                            <a href="{{ URL::route('secciones.edit', array($seccion->id)) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
                                        </li>
                                        <li>
                                            {{ Form::open(array('route' => array('secciones.destroy', $seccion->id), 'method' => 'delete', 'data-confirm' =>
                                            'Deseas borrar la seccion administrativa '. $seccion->nombre. ' permanentemente?')) }}
                                            <button type="submit" href="{{ URL::route('secciones.destroy', $seccion->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>
                                            {{ Form::close() }}
                                         </li>
                                    </ul>
                                </td>
                            @elseif (Cache::get('esJuntaDirectivakey'))
                                <td col width="150px" align="right">
                                    <ul class="demo-btns">
                                        <li>
                                            @if($seccion->tipo==1 or $seccion->tipo==2 or $seccion->tipo==3 or $seccion->tipo==4)    
                                                <a href="{{ URL::route('indexunplus', array($seccion->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Uns</a>
                                            @elseif($seccion->tipo==5 or $seccion->tipo==6)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Ame</a>
                                            @elseif($seccion->tipo==7)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Est</a>
                                            @endif    
                                        </li>
                                        <li>
                                            <a href="{{ URL::route('showsecplus', array($seccion->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
                                        </li>                
                                        <li>
                                            <a href="{{ URL::route('secciones.edit', array($seccion->id)) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
                                        </li>
                                    </ul>
                                </td>
                           @elseif (Cache::get('esAdministradorkey'))
                                <td col width="150px" align="right">
                                    <ul class="demo-btns">
                                        <li>
                                            @if($seccion->tipo==1 or $seccion->tipo==2 or $seccion->tipo==3 or $seccion->tipo==4)    
                                                <a href="{{ URL::route('indexunplus', array($seccion->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Uns</a>
                                            @elseif($seccion->tipo==5 or $seccion->tipo==6)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Ame</a>
                                            @elseif($seccion->tipo==7)    
                                                <a href="#" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Est</a>
                                            @endif    
                                        </li>
                                        <li>
                                            <a href="{{ URL::route('showsecplus', array($seccion->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
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