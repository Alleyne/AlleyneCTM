@extends('templates.frontend._layouts.unify')

@section('title', '| Front End')

@section('content')

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
                    <p><a class="btn-u btn-u-small" href="{{ route('blog.single', $post->id) }}"><i class="fa fa-location-arrow"></i> Read More</a></p>
                </div>    
            </div>
            <hr class="margin-bottom-40">            
            @endforeach
            <!--End Blog Post-->        
        </div>
        <!-- End Left Sidebar -->
    </div><!--/row-->  
@stop