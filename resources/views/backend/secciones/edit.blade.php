@extends('backend._layouts.default')

@section('main')<!-- MAIN PANEL -->
    <!-- widget grid -->
    <section id="widget-grid" class="">
    
        <!-- row -->
        <div class="row">
    
            <!-- NEW WIDGET START -->
            <article class="col-sm-12 col-md-12 col-lg-7">
    
                <!-- Widget ID (each widget will need unique ID)-->
                <div class="jarviswidget jarviswidget-color-orange" id="wid-id-0" data-widget-editbutton="false" data-widget-deletebutton="false">
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
    
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
                        <span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
                        <h2>Editar datos de la Seccion</h2>
    
                    </header>
    
                    <!-- widget div-->
                    <div>
    
                        <!-- widget edit box -->
                        <div class="jarviswidget-editbox">
                            <!-- This area used as dropdown edit box -->
    
                        </div>
                        <!-- end widget edit box -->
    
                            <!-- widget content -->
                            <div class="widget-body">
                            {{ Form::model($dato, array('class' => 'form-horizontal', 'method' => 'put', 'route' => array('secciones.update', $dato->id))) }}
                                    <fieldset> 
                                        {{ csrf_field() }}
                                        {{ Form::hidden('bloque_id', $dato->bloque_id) }}
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Sección Nombre</label>
                                            <div class="col-md-9">
                                                {{ Form::text('nombre', $dato->nombre, array('class' => 'form-control','title' => 'Escriba el nombre del Bloque administrativo...', 'autocomplete' => 'off')) }}
                                                {!! $errors->first('nombre', '<li style="color:red">:message</li>') !!}
                                            </div>
                                        </div>                    

                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Descripción</label>
                                            <div class="col-md-9">
                                                {{ Form::textarea('descripcion', $dato->descripcion, array('class' => 'form-control input-sm', 'rows' => '7', 'title' => 'Escriba el Ruc o número de cédula del Administrador...')) }}
                                                {!! $errors->first('descripcion', '<li style="color:red">:message</li>') !!}
                                            </div>
                                        </div>                                                    
                                        
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Ph</label>
                                            <div class="col-md-9">
                                                {{ Form::select('ph_id', array('' => 'Selecione un Ph') + $phs, $dato->ph_id, array('class' => 'form-control', 'title' => 'Escoja el Ph al cual pertenece la presente Sección administrativa')) }}
                                                {!! $errors->first('ph_id', '<span class="label label-important">  *</span>') !!}
                                            </div>
                                        </div>      
                                       
                                        @if ($dato->tipo==1) <!-- Apartamentos -->
                                            <legend>Sección tipo apartamentos</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Cuartos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuartos', $dato->secapto->cuartos, array('class' => 'form-control','title' => 'Escriba el número de cuartos que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuartos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                        

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de baños</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('banos', $dato->secapto->banos, array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Agua caliente</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('agua_caliente', $dato->secapto->agua_caliente, array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                    
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Estacionamientos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('estacionamientos', $dato->secapto->estacionamientos, array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Area/m2</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('area', $dato->secapto->area, array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('area', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>    
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Cuota mant (B/.)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuota_mant', $dato->secapto->cuota_mant, array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Recargo (B/.)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('recargo', $dato->secapto->recargo, array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Descuento (B/.)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('descuento', $dato->secapto->descuento, array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Genera Orden de cobro</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value= "{{ $dato->secapto->d_registra_cmpc }}" type="text">
                                                    <p class="text-left">Día del mes en que se registra en el ctdiario la cuota de mantenimiento por cobrar.</p>
                                                    {!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                            
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Dias de gracias</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner2" name="d_gracias" value= "{{ $dato->secapto->d_gracias }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Meses para descuento</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner3" name="m_descuento" value= "{{ $dato->secapto->m_descuento }}" type="text">
                                                    <p class="text-left">Cantidad de meses que se debera pagar por adelantado para obtener descuento por pagos anticipados.</p>
                                                    {!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>  
                                        
                                        @elseif ($dato->tipo==2) <!-- Residencias -->
                                            <legend>Sección tipo residencias</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Avenida</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('avenida', $dato->secre->avenida, array('class' => 'form-control','title' => 'Escriba la avenida en donde se encuentra localizada la residencia...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('avenida', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                    

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Cuartos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuartos', $dato->secre->cuartos, array('class' => 'form-control','title' => 'Escriba el número de cuartos que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuartos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                        

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de baños</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('banos', $dato->secre->banos, array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Agua caliente</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('agua_caliente', $dato->secre->agua_caliente, array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                    
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Estacionamientos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('estacionamientos', $dato->secre->estacionamientos, array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Area/m2</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('area', $dato->secre->area, array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('area', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>    

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Cuota mantenimiento</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuota_mant', $dato->secre->cuota_mant, array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Recargo (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('recargo', $dato->secre->recargo, array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Descuento (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('descuento', $dato->secre->descuento, array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Genera Orden de cobro</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value= "{{ $dato->secre->d_registra_cmpc }}" type="text">
                                                    <p class="text-left">Día del mes en que se registra en el ctdiario la cuota de mantenimiento por cobrar.</p>
                                                    {!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
                                                </div>                                            
                                            </div>                                            
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Dias de gracias</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner2" name="d_gracias" value= "{{ $dato->secre->d_gracias }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Meses para descuento</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner3" name="m_descuento" value= "{{ $dato->secapto->m_descuento }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div> 
 
                                        @elseif ($dato->tipo==3) <!-- Local comercial en edificio -->
                                            <legend>Sección tipo Oficina o Local comercial en edificio</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de baños</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('banos', $dato->seclced->banos, array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Agua caliente</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('agua_caliente', $dato->seclced->agua_caliente, array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                    
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Estacionamientos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('estacionamientos', $dato->seclced->estacionamientos, array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Area/m2</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('area', $dato->seclced->area, array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('area', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Cuota mantenimiento</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuota_mant', $dato->seclced->cuota_mant, array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Recargo (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('recargo', $dato->seclced->recargo, array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Descuento (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('descuento', $dato->seclced->descuento, array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Genera Orden de cobro</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value= "{{ $dato->seclced->d_registra_cmpc }}" type="text">
                                                    <p class="text-left">Día del mes en que se registra en el ctdiario la cuota de mantenimiento por cobrar.</p>
                                                    {!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                            
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Dias de gracias</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner2" name="d_gracias" value= "{{ $dato->seclced->d_gracias }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Meses para descuento</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner3" name="m_descuento" value= "{{ $dato->secapto->m_descuento }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div> 

                                        @elseif ($dato->tipo==4) <!-- Local comercial en residencial -->
                                            <legend>Sección tipo Oficina o Local comercial en residencial</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Avenida</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('avenida', $dato->seclcre->avenida, array('class' => 'form-control','title' => 'Escriba la avenida en donde se encuentra localizada la residencia...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('avenida', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de baños</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('banos', $dato->seclcre->banos, array('class' => 'form-control','title' => 'Escriba el número de baños que tiene la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('banos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Agua caliente</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('agua_caliente', $dato->seclcre->agua_caliente, array('class' => 'form-control','title' => 'Tiene Agua caliente?...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('agua_caliente', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                    
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">No de Estacionamientos</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('estacionamientos', $dato->seclcre->estacionamientos, array('class' => 'form-control','title' => 'Escriba el número de estacionamientos que posee la unidad administrada...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('estacionamientos', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Area/m2</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('area', $dato->seclcre->area, array('class' => 'form-control','title' => 'Escriba el área en metros cuadrados de la unidad...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('area', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                               
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Cuota mantenimiento</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('cuota_mant', $dato->seclcre->couta_mant, array('class' => 'form-control','title' => 'Escriba la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('cuota_mant', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Recargo (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('recargo', $dato->seclcre->recargo, array('class' => 'form-control','title' => 'Escriba el porcentaje a cobrar por atraso en pago de la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('recargo', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Descuento (%)</label>
                                                <div class="col-md-9">
                                                    {{ Form::text('descuento', $dato->seclcre->descuento, array('class' => 'form-control','title' => 'Escriba el porcentaje de descuento por pagos adelantados en la cuota de mantenimento mensual...', 'autocomplete' => 'off')) }}
                                                    {!! $errors->first('descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Genera Orden de cobro</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner1" name="d_registra_cmpc" value= "{{ $dato->seclcre->d_registra_cmpc }}" type="text">
                                                    <p class="text-left">Día del mes en que se registra en el ctdiario la cuota de mantenimiento por cobrar.</p>
                                                    {!! $errors->first('d_registra_cmpc', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>                                            
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Dias de gracias</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner2" name="d_gracias" value= "{{ $dato->seclcre->d_gracias }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('d_gracias', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Meses para descuento</label>
                                                <div class="col-md-9">
                                                    <input class="form-control spinner-left"  id="spinner3" name="m_descuento" value= "{{ $dato->secapto->m_descuento }}" type="text">
                                                    <p class="text-left">Días de gracias despues de la fecha de vencimiento de pago.</p>
                                                    {!! $errors->first('m_descuento', '<li style="color:red">:message</li>') !!}
                                                </div>
                                            </div> 

                                        @endif
                                    </fieldset>
                                    
                                    <div class="form-actions">
                                        {{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
                                        <a href="{{ URL::route('indexsecplus', array($dato->bloque_id)) }}" class="btn btn-large">Cancelar</a>
                                    </div>
                                {{ Form::close() }}
                            </div>
                            <!-- end widget content -->
                    </div>
                    <!-- end widget div -->
                </div>
                <!-- end widget -->
            </article>
            <!-- WIDGET END -->
    
            <!-- NEW WIDGET START -->
            <article class="col-sm-12 col-md-12 col-lg-5">
    
                <!-- Widget ID (each widget will need unique ID)-->
                <div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-editbutton="false" data-widget-deletebutton="false">
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
    
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
                        <span class="widget-icon"> <i class="fa fa-sitemap"></i> </span>
                        <h2>Imagen</h2>
    
                    </header>
    
                    <!-- widget div-->
                    <div>
    
                        <!-- widget edit box -->
                        <div class="jarviswidget-editbox">
                            <!-- This area used as dropdown edit box -->
    
                        </div>
                        <!-- end widget edit box -->
    
                        <!-- widget content -->
                         <div class="widget-body">
                              <p>
                                <img style="height: 275px; border-radius: 8px;" src="{{asset($dato->imagen_L)}}" class="img-responsive" alt="Responsive image">
                             </p>
                             {{ Form::open(array('route' => array('subirImagenSeccion', $dato->id),'files'=>true)) }}
                                <div class="form-actions">
                                 <div>
                                    {{ Form::file('file') }}
                                </div>                        
                                    {{ Form::submit('Subir imagen', array('class' => 'btn btn-success btn-save btn-large')) }}
                                </div>
                              {{ Form::close() }}                        
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
    
        <!-- row -->
    
        <div class="row">
    
        </div>
    
        <!-- end row -->
    
    </section>
    <!-- end widget grid -->
@stop

@section('relatedplugins')
<!-- PAGE RELATED PLUGIN(S) -->

<script type="text/javascript">
// DO NOT REMOVE : GLOBAL FUNCTIONS!
$(document).ready(function() {
    pageSetUp();
    
    // PAGE RELATED SCRIPTS

    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function(e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').removeClass().addClass('fa fa-lg fa-plus-circle');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').removeClass().addClass('fa fa-lg fa-minus-circle');
        }
        e.stopPropagation();
    });            
    
    // Spinners
    $("#spinner1").spinner({
        min: 1,
        max: 16,
        step: 15,
        start: 1,
        numberFormat: "C"
    });   

    // Spinners
    $("#spinner2").spinner({
        min: 0,
        max: 16,
        step: 1,
        start: 1,
        numberFormat: "C"
    });  
    
    // Spinners
    $("#spinner3").spinner({
        min: 0,
        max: 60,
        step: 1,
        start: 1,
        numberFormat: "C"
    }); 

})
</script>
@stop