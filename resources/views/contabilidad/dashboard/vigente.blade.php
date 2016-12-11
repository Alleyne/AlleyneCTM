@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Vigente')

@section('content')
	<!-- widget grid -->
	<section id="widget-grid" class="">

		<!-- row -->
		<div class="row">

			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-11" data-widget-editbutton="false">
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
						<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
						<h2>Propietarios Morosos a la fecha. Total adeudado B/. {{ number_format($data['totalAdeudado'],2) }}</h2>

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

      				<div id="morosos" style="min-width: 310px; max-width: 800px; margin: 0 auto"></div>

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

			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false">
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
						<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
						<h2>Ingresos vs gastos del periodo vigente </h2>

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
      				<div id="utilidad" style="min-width: 310px; max-width: 800px; margin: 0 auto"></div>

						</div>
						<!-- end widget content -->

					</div>
					<!-- end widget div -->

				</div>
				<!-- end widget -->

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-3" data-widget-editbutton="false">
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
						<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
						<h2></h2>

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

						</div>
						<!-- end widget content -->

					</div>
					<!-- end widget div -->

				</div>
				<!-- end widget -->

			</article>
			<!-- WIDGET END -->

			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-2" data-widget-editbutton="false">
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
						<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
						<h2>Gastos incurridos en el periodo vigente Total: B/. {{ number_format($ER_totalGastos,2) }} </h2>

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

							<div id="gastos" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
						
						</div>
						<!-- end widget content -->

					</div>
					<!-- end widget div -->

				</div>
				<!-- end widget -->

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-4" data-widget-editbutton="false">
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
						<span class="widget-icon"> <i class="fa fa-bar-chart-o"></i> </span>
						<h2></h2>

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

							<div id="stacked-bar-graph" class="chart no-padding"></div>

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

	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>

	<script type="text/javascript">
		$(function () {
		    Highcharts.chart('morosos', {
		        colors: ['#90ed7d', '#434348', '#f7a35c', '#7cb5ec'],
		        chart: {
		            type: 'bar'
		        },
		        title: {
		            text: ''
		        },
		        xAxis: {
		            categories: [{!! $data['categorias'] !!}]
		        },
		        yAxis: {
		            min: 0,
		            title: {
		                text: 'Morosidad (dolares)'
		            }
		        },
		        legend: {
		            reversed: true
		        },
		        plotOptions: {
		            series: {
		                stacking: 'normal'
		            }
		        },
		        series: [{
		            name: 'Debe Cuota Extraordinaria',
		            data: [{{ $data['ctaExtra'] }}]
		        }, {
		            name: 'Debe Recargos',
		            data: [{{ $data['recargo'] }}]
		        }, {
		            name: 'Debe Cuota Regular',
		            data: [{{ $data['ctaRegular'] }}]
		        }]
		    });
		});
		
		$(function () {
	    $(document).ready(function () {

	        // Build the chart
	        Highcharts.chart('utilidad', {
	            chart: {
	                plotBackgroundColor: null,
	                plotBorderWidth: null,
	                plotShadow: false,
	                type: 'pie'
	            },
	            title: {
	                text: 'Ingresos recibidos vs gastos efectuados'
	            },
	            tooltip: {
									pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>'
	            },
	            plotOptions: {
	                pie: {
	                    allowPointSelect: true,
	                    cursor: 'pointer',
	                    dataLabels: {
	                        enabled: false
	                    },
	                    showInLegend: true
	                }
	            },
	            series: [{
	                name: 'Brands',
	                colorByPoint: true,
	                data: [{
	                    name: 'Total de Ingresos disponibles a la fecha',
	                    y: {{ $totalIngresosDisponible }}
	                }, {
	                    name: 'Total de Gastos efectuados',
	                    y: {{ $ER_totalGastos }},
	                    sliced: true,
	                    selected: true
	                }]
	            }]
	        });
	    });
		});

		$(function () {
		    $(document).ready(function () {

		        // Build the chart
		        Highcharts.chart('gastos', {
		            chart: {
		                plotBackgroundColor: null,
		                plotBorderWidth: null,
		                plotShadow: false,
		                type: 'pie'
		            },
		            title: {
		                text: ''
		            },
		            tooltip: {
									pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>'
		            },
		            plotOptions: {
		                pie: {
		                    allowPointSelect: true,
		                    cursor: 'pointer',
		                    dataLabels: {
		                        enabled: false
		                    },
		                    showInLegend: true
		                }
		            },
		            series: [{
		                name: 'Brands',
		                colorByPoint: true,
		                data: [{
		                    name: 'Itbms',
		                    y: {{ $itbms }}
		                },
		                {!! $gastos !!}
		                ]
		            }]
		        });
		    });
		});
	
	</script>
@endsection 