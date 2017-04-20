@extends('templates.backend._layouts.smartAdmin')

@section('title', '| Historicos')

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
						<h2>Propietarios Morosos a la fecha. Total adeudado B/. {{ number_format($dataMorosos['totalAdeudado'],2) }}</h2>

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
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
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
						<h2>Estatus de Ingresos por cobrar vs Pagos recibidos</h2>
					</header>

					<!-- widget div-->
					<div>

						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
							<input type="text">
						</div>
						<!-- end widget edit box -->

						<!-- widget content -->
						<div class="widget-body no-padding">
								<div id="ingresos" style="min-width: 310px; margin: 0 auto"></div>
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
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
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
						<h2>Descuentos por pagos anticipados</h2>
					</header>

					<!-- widget div-->
					<div>

						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
							<input type="text">
						</div>
						<!-- end widget edit box -->

						<!-- widget content -->
						<div class="widget-body no-padding">
								<div id="descuentos" style="min-width: 310px; height: 250px; margin: 0 auto"></div>
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
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
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
						<h2>Analisis Comparativo</h2>
					</header>

					<!-- widget div-->
					<div>

						<!-- widget edit box -->
						<div class="jarviswidget-editbox">
							<!-- This area used as dropdown edit box -->
							<input type="text">
						</div>
						<!-- end widget edit box -->

						<!-- widget content -->
						<div class="widget-body no-padding">
								<div id="historicos" style="min-width: 310px; margin: 0 auto"></div>
						</div>
						<!-- end widget content -->

					</div>
					<!-- end widget div -->

				</div>
				<!-- end widget -->

			</article>
			<!-- WIDGET END -->

		</div>			

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
						<h2> </h2>

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
							<div id="gastosTotales" style="min-width: 310px; height: 900px; margin: 0 auto"></div>
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
						<h2> </h2>

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
						<h2> </h2>

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
							<div id="gastos" style="min-width: 310px; height: 900px; margin: 0 auto"></div>
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
						<h2> </h2>

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
		            categories: [{!! $dataMorosos['categorias'] !!}]
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
		            data: [{{ $dataMorosos['ctaExtra'] }}]
		        }, {
		            name: 'Debe Recargos',
		            data: [{{ $dataMorosos['recargo'] }}]
		        }, {
		            name: 'Debe Cuota Regular',
		            data: [{{ $dataMorosos['ctaRegular'] }}]
		        }]
		    });
		});

		$(function () {
		    Highcharts.chart('ingresos', {
		        colors: ['#7cb5ec', '#434348', '#90ed7d', '#f7a35c'],
		        chart: {
		            type: 'column'
		        },
		        title: {
		            text: '~'
		        },
		        xAxis: {
		            categories: [{!! $pdo !!}]
		        },
		        yAxis: {
		            min: 0,
		            title: {
		                text: 'PH El Marquez'
		            }
		        },
		        tooltip: {
		            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
		            shared: true
		        },
		        plotOptions: {
		            column: {
		                stacking: 'percent'
		            }
		        },
		        series: [{
		            name: 'Ingresos por cobrar',
		            data: [{{ $totalIngresoPorCobrarCD }}]
		        }, {
		            name: 'Recargos pagados',
		            data: [{{ $pagRecargos }}]
		        }, {
		            name: 'Cuotas extraordinarias pagadas',
		            data: [{{ $pagExtraordinarias }}]
		        }, {
		            name: 'Cuotas regulares pagadas',
		            data: [{{ $pagRegulares }}]
		        }]
		    });
		});

		$(function () {
		    Highcharts.chart('descuentos', {
		        chart: {
		            type: 'areaspline'
		        },
		        title: {
		            text: 'DESCUENTOS OTORGADOS POR PAGOS ANTICIPADOS'
		        },
		        subtitle: {
		            text: 'ctmaster.net'
		        },
		        xAxis: {
		            categories: [{!! $pdo !!}],
		            tickmarkPlacement: 'on',
		            title: {
		                enabled: false
		            }
		        },
		        yAxis: {
		            title: {
		                text: 'Balboas'
		            },
		            labels: {
		                formatter: function () {
		                    return this.value;
		                }
		            }
		        },
		        tooltip: {
		            split: true,
		            valueSuffix: ' balboas'
		        },
		        plotOptions: {
		            area: {
		                stacking: 'normal',
		                lineColor: '#666666',
		                lineWidth: 1,
		                marker: {
		                    lineWidth: 1,
		                    lineColor: '#666666'
		                }
		            }
		        },
		        series: [{
		            name: 'Descuentos otorgados',
		            data: [{{ $descuentos }}]
		        }]
		    });
		});

		$(function () {
		    Highcharts.chart('gastosTotales', {
		        chart: {
		          type: 'bar'
		        },
		        title: {
		          text: 'Gastos Totales por Periodo contable'
		        },
		        subtitle: {
		          text: ''
		        },
		        xAxis: {
	            categories: [{!! $pdo !!}],
	            title: {
	              text: null
	            }
		        },
		        yAxis: {
	            min: 0,
	            title: {
	                text: 'Gasto (Balboas)',
	                align: 'high'
	            },
	            labels: {
	                overflow: 'justify'
	            }
		        },
		        tooltip: {
		            valueSuffix: ' Balboas'
		        },
		        plotOptions: {
	            bar: {
                dataLabels: {
                  enabled: true
                }
	            }
		        },
		        legend: {
	            layout: 'horizontal',
	            align: 'center',
	            verticalAlign: 'bottom',
	            borderWidth: 0
		        },
		        credits: {
		          enabled: false
		        },
		        series: [{!! $dataGastosTotales !!}]
		    });
		});	

		$(function () {
		    Highcharts.chart('historicos', {
		        chart: {
		            type: 'areaspline'
		        },
		        title: {
		            text: 'ANALISIS COMPARATIVO POR PERIODO CONTABLE'
		        },
				        legend: {
				            layout: 'horizontal',
				            align: 'center',
				            verticalAlign: 'bottom',
				            borderWidth: 0
				        },
		        xAxis: {
		            categories: [{!! $pdo !!}],
		            plotBands: [{ // visualize the weekend
		                from: 'Ene-2016',
		                to: 'Feb-2016',
		                color: 'rgba(68, 170, 213, .2)'
		            }]
		        },
		        yAxis: {
		            title: {
		                text: 'Balboas (B/.)'
		            }
		        },
		        tooltip: {
		        },
		        credits: {
		            enabled: false
		        },
		        plotOptions: {
		            areaspline: {
		                fillOpacity: 0.4
		            }
		        },
		        series: [{
		            name: 'Ingreso esp',
		            data: [{{ $totalIngresoEsperadoSD }}]
		        }, {
		            name: 'Ingreso esp -desc',
		            data: [{{ $totalIngresoEsperadoCD }}]
		        }, {
		            name: 'Ingreso recibido',
		            data: [{{ $totalPagado }}]
		        }, {
		            name: 'Cuotas reg',
		            data: [{{ $espRegularesSD }}]
		        }, {
		            name: 'Cuotas reg -desc',
		            data: [{{ $espRegularesCD }}]
		        }, {
		            name: 'Cuotas extras',
		            data: [{{ $espExtraordinarias }}]
		        }, {
		            name: 'Recargos',
		            data: [{{ $espRecargos }}]
		        }, {
		            name: 'Descuentos',
		            data: [{{ $descuentos }}]
						}, {
		            name: 'Gastos',
		            data: [{{ $totalGastos }}]
		        }]


		    });
		});

		$(function () {
		    Highcharts.chart('gastos', {
		        chart: {
		          type: 'bar'
		        },
		        title: {
		          text: 'Gastos por Periodo contable'
		        },
		        subtitle: {
		          text: ''
		        },
		        xAxis: {
	            categories: [{!! $pdo !!}],
	            title: {
	              text: null
	            }
		        },
		        yAxis: {
	            min: 0,
	            title: {
	                text: 'Gasto (Balboas)',
	                align: 'high'
	            },
	            labels: {
	                overflow: 'justify'
	            }
		        },
		        tooltip: {
		            valueSuffix: ' Balboas'
		        },
		        plotOptions: {
	            bar: {
                dataLabels: {
                  enabled: true
                }
	            }
		        },
		        legend: {
	            layout: 'horizontal',
	            align: 'center',
	            verticalAlign: 'bottom',
	            borderWidth: 0
		        },
		        credits: {
		          enabled: false
		        },
		        series: [{!! $dataGastos !!}]
		    });
		});		

	</script>
@endsection 