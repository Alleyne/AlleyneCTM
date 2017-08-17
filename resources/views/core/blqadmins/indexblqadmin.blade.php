@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Administradores')

@section('content')

		<!-- widget grid -->
		<section id="widget-grid" class="">

			<!-- row -->
			<div class="row">
				<!-- NEW WIDGET START -->
				<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
						data-widget-sortable="false"
						-->
						<header>
							<span class="widget-icon"> <i class="fa fa-table"></i> </span>
							<h2>Administradores de Bloques </h2>
							<div class="widget-toolbar">
								<a href="{{ URL::route('indexblqplus', $jd_id) }}" class="btn btn-default btn-large"><i class="glyphicon glyphicon-arrow-left"></i></a>

								<button class="btn btn-success" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>
									 Vincular Administrador
								</button>
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
											<th>NOMBRE</th>
											<th>APELLIDO</th>
											<th>CARGO</th>
											<th>ORGANIZACIÓN</th>
											<th>RES</th>											
											<th>BLOQUES</th>
											<th>ACCIONES</th>										
										</tr>
									</thead>
									<tbody>
										@foreach ($datos as $dato)
											<tr>
												<td col width="40px"><strong>{{ $dato->id }}</strong></td>
												<td col width="120px">{{ $dato->user->first_name }}</td>
												<td col width="120px">{{ $dato->user->last_name }}</td>
												<td col width="130px">{{ $dato->cargo ? 'Persona Jurídica' : 'Persona Natural' }}</td>
												<td>{{{ $dato->org->nombre or '' }}}</td>
												<td col width="10px">{{ $dato->encargado ? 'Si' : 'No' }}</td>
												<td>{{ $dato->bloque->nombre }}</td>
												<td col width="160px" align="center">
													<ul class="demo-btns">
														<li>
															<a href="{{ URL::route('users.show', $dato->user->id) }}" class="btn btn-info btn-xs"><i class="fa fa-search"></i></a>
														</li>				
														<div id="ask_1" class="btn btn-warning btn-xs">
															<a href="{{ URL::route('desvincularblqdmin', array($dato->id)) }}" title="Desvincular"><i class="fa fa-search"></i> Desvincular</a>
														</div>
														<!-- <li>
															<a href="{{ URL::route('desvincularblqdmin', array($dato->id)) }}" class="btn btn-warning btn-xs"><i class="fa fa-search"></i> Desvincular</a>
														</li> -->
													</ul>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
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
		<!-- Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="myModalLabel">Vincular Administrador de Bloque</h4>
					</div>
					<div class="modal-body">
		
						{{ Form::open(array('class' => 'form-horizontal', 'route' => 'blqadmins.store')) }}
							<fieldset>
								{{ Form::hidden('bloque_id', $bloque_id) }}
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="cargo"> Usuarios</label>
											{{ Form::select('user_id', array('' => 'Escoja usuario que desea vincular...') + $usuarios, array('title' => 'Escoja el usuario que desea vincular')) }}
											{!! $errors->first('user_id', '<li style="color:red">:message</li>') !!}
										</div>
									</div>
								</div>
								
								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="cargo"> Cargo</label>
											{{ Form::select('cargo', array('0' => 'Persona Natural', '1' => 'Persona Jurídica'), array('title' => 'Escoja el tipo de propietario')) }}
 											{!! $errors->first('cargo', '<li style="color:red">:message</li>') !!}										
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-12">
										<div class="form-group">
											<label for="category"> Organización</label>
											{{ Form::select('org_id', array('' => 'Escoja la organización...') + $orgs, array('title' => 'Escoja la organización a la cual pertenece')) }}
											{!! $errors->first('org_id', '<li style="color:red">:message</li>') !!}
										</div>
									</div>
								</div>
							
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-3 control-label">Encargado</label>
											<div class="col-md-9">
												{{ Form::checkbox('encargado','1', false) }}
											</div>
										</div>
									</div>
								</div>
							</fieldset>				
							
							<div class="form-actions">
								{{ Form::submit('Salvar', array('class' => 'btn btn-success btn-save btn-large')) }}
								<button type="button" class="btn btn-default" data-dismiss="modal">
									Cancel
								</button>
							</div>
						{{ Form::close() }}
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
@stop

@section('relatedplugins')

  <script type="text/javascript">
    $(document).ready(function() {

      $('#dt_basic').dataTable({
        "paging": false,
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