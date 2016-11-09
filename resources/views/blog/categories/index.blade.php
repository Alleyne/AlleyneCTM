@extends('templates.backend._layouts.smartAdmin')

@section('title', '| All Categories')

@section('content')

	<!-- widget grid -->
	<section id="widget-grid" class="">
	
		<!-- row -->
		<div class="row">
	
			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-8">
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
						<h2>Categorias </h2>
						<div class="widget-toolbar">
							@if (Cache::get('esAdminkey'))
								<a href="{{ route('categories.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Crear categoria</a>
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
							</div>
							
							<table id="dt_basic" class="table table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Nombre</th>
									</tr>
								</thead>

								<tbody>
									@foreach ($categories as $category)
									<tr>
										<th>{{ $category->id }}</th>
										<td>{{ $category->name }}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
					<!-- end widget div -->
			</article>
			<!-- WIDGET END -->
		</div>
		<!-- end row -->
	
	</section>
	<!-- end widget grid -->	
@endsection

@section('relatedplugins')
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/jquery.dataTables-cust.min.js') }}"></script>
	<script src="{{ URL::asset('assets/backend/js/plugin/datatables/DT_bootstrap.js') }}"></script>
	
	<script type="text/javascript">
	$(document).ready(function() {
		pageSetUp();

		$('#dt_basic').dataTable({
			"sPaginationType" : "bootstrap_full"
		});
	})
	</script>
@stop