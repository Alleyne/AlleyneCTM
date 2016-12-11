@extends('templates.frontend._layouts.unify')

@section('title', '| Vigente')

@section('content')
  <div class="row margin-bottom-20">
      <div class="col-md-12">
          <!-- Grey Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Propietarios Morosos a la fecha, adeudan un total de B/. {{ number_format($data['totalAdeudado'],2) }}</h4>   
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
      						<div id="morosos" style="margin: 0 auto"></div>
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
                <h4>Ingresos vs gastos del periodo vigente</h4>   
                <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</p>
    						<div id="utilidad" style="margin: 0 auto"></div>
              </div>
          </div>
          <!-- End Grey Panel -->            
      </div>
      <div class="col-md-6">
          <!-- Red Panel -->            
          <div class="panel panel-grey">
              <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-tasks"></i> </h3>
              </div>
              <div class="panel-body">
                  <h4>Gastos incurridos en el periodo vigente Total: B/. {{ number_format($ER_totalGastos,2) }} </h4>   
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