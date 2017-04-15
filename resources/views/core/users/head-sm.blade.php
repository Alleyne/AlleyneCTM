<!-- NEW WIDGET START -->
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget jarviswidget-color-darken" id="wid-id-52" data-widget-editbutton="false" data-widget-deletebutton="false">
	<!-- widget options:
	usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

	data-widget-colorbutton="false"
	data-widget-editbutton="false"
	data-widget-togglebutton="false"
	data-widget-deletebutton="false"
	data-widget-fullscreenbutton="false"
	data-widget-custombutton="false"
	data-widget-collapsed="true"
	data-widget-sortable="false"-->

	<header>
		<span class="widget-icon"> <i class="fa fa-lg fa-calendar"></i> </span>
		<h2>Bloque administrado por:</h2>
	</header>
	<div><!-- widget div-->
		<div class="jarviswidget-editbox"><!-- widget edit box -->
			<!-- This area used as dropdown edit box -->
		</div><!-- end widget edit box -->

		<div class="widget-body"><!-- widget content -->
			<table id="dt_basic" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th>NOMBRE</th>
						<th>CARGO</th>										
						<th>ORG</th>
						<th>ENC</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($phadmins as $phadmin)
						<tr>
							<td col width="120px" align="left"><strong>{{ $phadmin->admin_nombre }}</strong></td>
							<td col width="80px" align="left">{{ $phadmin->cargo }}</td>
							@if (is_null($phadmin->org)) 

								<td col width="260px" align="left"> </td>
							@else 
								<td col width="260px" align="left">{{ $phadmin->org->nombre }}</td>
							@endif

							<td col width="40Px" align="center">{{ $phadmin->encargado ? 'Si' : 'No' }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div><!-- end widget content -->
	</div><!-- end widget div -->
</div><!-- end widget -->