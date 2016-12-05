@extends('templates.frontend._layouts.unify')

@section('title', '| Front End')

@section('slider')
    <!--=== Slider ===-->
<div class="slider-inner">
    <div class="slider-inner">
        <div id="da-slider" class="da-slider">

        </div>
    </div><!--/slider-->
</div>
    <!--=== End Slider ===--> 
@stop

@section('content')

    <div class="headline"><h2>Propietarios morosos a la fecha</h2></div>
    <div class="row margin-bottom-20">
        <div class="col-md-12 col-sm-12">
            <div id="morosos" style="min-width: 310px; max-width: 1000px; margin: 0 auto"></div>
        </div>
    </div>
    
    <div class="headline"><h2>Articulos recientes</h2></div>
    <div class="row blog-page">    
        <!-- Left Sidebar -->
    	<div class="col-md-10 col-md-offset-1 md-margin-bottom-40">
            <!--Blog Post-->
            @foreach ($posts as $post)
            <div class="row blog blog-medium margin-bottom-40">
                <div class="col-md-5">
                    <img class="img-responsive" src="{{asset('/images/' . $post->image)}}" alt="">
                </div>    
                <div class="col-md-7">
                    <h2>{{ $post->title }}</h2>
                    <ul class="list-unstyled list-inline blog-info">
                        <li><i class="fa fa-calendar"></i> {{ date('M j, Y', strtotime($post->created_at)) }}</li>
                        <li><i class="fa fa-comments"></i> <a href="#">{{ $post->comments()->count() }} comentarios</a></li>
                        <li><i class="fa fa-tags"></i> Technology, Internet</li>
                    </ul>
                    <p>{{ substr(strip_tags($post->body), 0, 250) }}{{ strlen(strip_tags($post->body)) > 250 ? '...' : "" }}</p>
                    <p><a class="btn-u btn-u-small" href="{{ route('blog.single', $post->slug) }}"><i class="fa fa-location-arrow"></i> Read More</a></p>
                </div>    
            </div>
            <hr class="margin-bottom-40">            
            @endforeach
            <!--End Blog Post-->        
        </div>
        <!-- End Left Sidebar -->
    </div><!--/row-->  
@stop

@section('scripts')

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
                    data: [{{ $data['data_3'] }}]
                }, {
                    name: 'Debe Recargos',
                    data: [{{ $data['data_2'] }}]
                }, {
                    name: 'Debe Cuota Regular',
                    data: [{{ $data['data_1'] }}]
                }]
            });
        });

    </script>
@endsection 