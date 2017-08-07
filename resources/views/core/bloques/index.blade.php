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
		<h2>Bloques </h2>
		<div class="widget-toolbar">
			@if (Cache::get('esAdminkey'))
				<a href="{{ URL::route('createblq', $jd->id) }}" class="btn btn-success"><i class="fa fa-plus"></i> Agregar Bloque administrativo</a>
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
						<th>CODIGO</th>
						<th>NOMBRE</th>
						<th class="text-center"><i class="fa fa-gear fa-lg"></i></th>										
					</tr>
				</thead>
				<tbody>
					@foreach ($bloques as $bloque)
						<tr>
							@if (Cache::get('esAdminkey'))
								<td col width="60px" ><strong>{{ $bloque->codigo }}</strong></td>
								<td>{{ $bloque->nombre }}</td>
								<td col width="290px" align="right">
									<ul class="demo-btns">
										<li>
											<a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Secciones</a>
										</li>
										<li>
											<a href="{{ URL::route('indexblqadmin', array($bloque->id)) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-search"></i> Admins</a>
										</li>										
										<li>
											<a href="{{ URL::route('showblqplus', array($bloque->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
										</li>				
										<li>
											<a href="{{ URL::route('bloques.edit', array($bloque->id)) }}" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i></a>
										</li>
										<li>
											{{ Form::open(array('route' => array('bloques.destroy', $bloque->id), 'method' => 'delete', 'data-confirm' => 'Deseas borrar el Bloque '. $bloque->nombre. ' permanentemente?')) }}
												<button type="submit" href="{{ URL::route('bloques.destroy', $bloque->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button>
											{{ Form::close() }}										
										</li>				
							@elseif (Cache::get('esAdministradorkey') || Cache::get('esContadorkey'))
								<td col width="60px" ><strong>{{ $bloque->codigo }}</strong></td>
								<td>{{ $bloque->nombre }}</td>
								<td col width="190px" align="right">
									<ul class="demo-btns">
										<li>
											<a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Secciones</a>
										</li>
										<li>
											<a href="{{ URL::route('showblqplus', array($bloque->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
										</li>				
							@elseif (Cache::get('esJuntaDirectiva'))
								<td col width="60px" ><strong>{{ $bloque->codigo }}</strong></td>
								<td>{{ $bloque->nombre }}</td>
								<td col width="190px" align="right">
									<ul class="demo-btns">
										<li>
											<a href="{{ URL::route('indexsecplus', array($bloque->id)) }}" class="btn btn-primary btn-xs"><i class="fa fa-search"></i> Secciones</a>
										</li>
										<li>
											<a href="{{ URL::route('indexblqadmin', array($bloque->id)) }}" class="btn bg-color-purple txt-color-white btn-xs"><i class="fa fa-search"></i> Admins</a>
										</li>										
										<li>
											<a href="{{ URL::route('showblqplus', array($bloque->id)) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
										</li>				
							@endif
									</ul>
								</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		
		</div><!-- end widget content -->
	</div><!-- end widget div -->
</div>
<!-- end widget -->
<!-- WIDGET END -->