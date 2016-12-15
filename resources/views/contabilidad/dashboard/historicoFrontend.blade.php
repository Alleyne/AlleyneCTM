@extends('templates.frontend._layouts.unify')

@section('title', '| Historicos')

@section('content')
  <div class="row margin-bottom-20">
      <div class="col-md-12">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Propietarios Morosos a la fecha, adeudan un total de B/. {{ number_format($dataMorosos['totalAdeudado'],2) }}</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="morosos" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
  </div>

  <div class="row margin-bottom-20">
      <div class="col-md-12">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Estatus de Ingresos por cobrar vs Pagos recibidos</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="ingresos" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
  </div>

  <div class="row margin-bottom-20">
      <div class="col-md-12">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Descuentos por pagos anticipados</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="descuentos" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
  </div>

  <div class="row margin-bottom-20">
      <div class="col-md-12">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Analisis Comparativo</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="historicos" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
  </div>

  <div class="row margin-bottom-20">
      <div class="col-md-6">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Accusamus et iusto odio</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="gastosTotales" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
      <div class="col-md-6">
          <!-- Red Panel -->            
          <div class="panel panel-red">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> Red Panel</h3>
              </div>
              <div class="panel-body">
                  <h4>Accusamus et iusto odio</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="gastos" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Red Panel -->            
      </div>
  </div>
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