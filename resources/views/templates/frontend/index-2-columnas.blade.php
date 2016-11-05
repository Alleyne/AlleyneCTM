@extends('templates.frontend._layouts.unify')

@section('title', '| Front End')

@section('content')

	<div class="row blog-page">    
        <!-- Left Sidebar -->
    	<div class="col-md-9 md-margin-bottom-40">
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

        <!-- Right Sidebar -->
    	<div class="col-md-3">
            <!-- Search Bar -->
            <div class="headline headline-md"><h2>Buscar</h2></div>            
            <div class="input-group margin-bottom-40">
                <input type="text" class="form-control" placeholder="Search">
                <span class="input-group-btn">
                    <button class="btn-u" type="button">Go</button>
                </span>
            </div>
            <!-- End Search Bar -->

            <!-- Social Icons -->
            <div class="magazine-sb-social margin-bottom-30">
                <div class="headline headline-md"><h2>Redes Sociales</h2></div>
                <ul class="social-icons social-icons-color">
                    <li><a class="social_facebook" data-original-title="Facebook" href="#"></a></li>
                    <li><a class="social_twitter" data-original-title="Twitter" href="#"></a></li>
                    <li><a class="social_googleplus" data-original-title="Goole Plus" href="#"></a></li>
                    <li><a class="social_pintrest" data-original-title="Pinterest" href="#"></a></li>
                    <li><a class="social_linkedin" data-original-title="Linkedin" href="#"></a></li>
                    <li><a class="social_picasa" data-original-title="Picasa" href="#"></a></li>
                </ul>
                <div class="clearfix"></div>                
            </div>
            <!-- End Social Icons -->

            <!-- Posts -->
            <div class="posts margin-bottom-40">
                <div class="headline headline-md"><h2>Mas Recientes</h2></div>
	            @foreach ($posts as $post)
	                <dl class="dl-horizontal">
	                    <dt><a href="#"><img src="{{asset('/images/' . $post->image)}}" alt="" /></a></dt>
	                    <dd>
	                        <p><a href="#">{{ $post->title }}</a></p> 
	                    </dd>
	                </dl>	       
	            @endforeach
            </div><!--/posts-->
            <!-- End Posts -->

        	<!-- Blog Tags -->
        	<div class="headline headline-md"><h2>Blog Tags</h2></div>
            <ul class="list-unstyled blog-tags margin-bottom-30">
            	<li><a href="#"><i class="fa fa-tags"></i> Business</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Music</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Internet</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Money</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Google</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> TV Shows</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Education</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> People</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> People</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Math</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Photos</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Electronics</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Apple</a></li>
            	<li><a href="#"><i class="fa fa-tags"></i> Canada</a></li>
            </ul>
            <!-- End Blog Tags -->

        	<!-- Photo Stream -->
        	<div class="headline headline-md"><h2>Galeria de Fotos</h2></div>
            <ul class="list-unstyled blog-photos margin-bottom-30">
	            @foreach ($posts as $post)                
                	<li><a href="#"><img class="hover-effect" alt="" src="{{asset('/images/' . $post->image)}}"></a></li>
	            @endforeach
            </ul>
            <!-- End Photo Stream -->
        </div>
        <!-- End Right Sidebar -->
    </div><!--/row-->  

@stop