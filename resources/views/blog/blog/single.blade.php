@extends('templates.frontend._layouts.unify')

<?php $titleTag = htmlspecialchars($post->title); ?>
@section('title', "| $titleTag")

@section('stylesheets')
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endsection

@section('content')

    <!--=== Content Part ===-->
    <div class="container content">		
    	<div class="row blog-page blog-item">
            <!-- Left Sidebar -->
        	<div class="col-md-8 col-md-offset-2 md-margin-bottom-60">
                <!--Blog Post-->        
                <div class="blog">
                    <div class="blog-img">
                        <img class="img-responsive" src="{{asset('/images/' . $post->image)}}" alt="">
                    </div>
                    <br>
                    <div class="blog-post-tags">
                        <ul class="list-unstyled list-inline blog-info">
                            <li><i class="fa fa-calendar"></i> {{ date('M j, Y', strtotime($post->created_at)) }}</li>
                            <li><i class="fa fa-pencil"></i> ctmaster</li>
                            <li><i class="fa fa-comments"></i> <a href="#">{{ $post->comments()->count() }} comentarios</a></li>
                            <li><i class="fa fa-book"></i> Categoria: {{ $post->category->name }}</li>
                        </ul>
                    </div>
                    <h2>{{ $post->title }}</h2>
                    <p>{!! $post->body !!}</p>                
                    <ul class="list-unstyled list-inline blog-tags">
                        @foreach ($post->tags as $tag)
                            <li><a href="#"><i class="fa fa-tags"></i> {{ $tag->name }}</a></li>
                        @endforeach
                    </ul>                
                </div>
                <!--End Blog Post-->        

    			<HR WIDTH=85% ALIGN=CENTER COLOR="BLACK">

                <!-- Recent Comments -->                
                <div class="media">
                    <h3>Commentarios</h3>
                    @foreach($post->comments as $comment)
	                    <a class="pull-left" href="#">
	                        <img class="media-object" src="{{ "https://www.gravatar.com/avatar/" . md5(strtolower(trim($comment->email))) . "?s=50&d=monsterid" }}" alt="" />
	                    </a>
	                    <div class="media-body">
	                        <h4 class="media-heading">{{ $comment->name }} &nbsp;&nbsp; <small>{{ date('F nS, Y - g:iA' ,strtotime($comment->created_at)) }}</small></h4>
	                        <p>{{ $comment->comment }}</p>
	                    </div>
                		<hr>                	
                	@endforeach                
                </div><!--/media-->
                <!-- End Recent Comments -->

                @if (Auth::check())
                    <!-- Comment Form -->
                    <div class="post-comment">
                        <h3>Haga un comentario</h3>
                        {{ Form::open(['route' => ['comments.store', $post->id], 'method' => 'POST']) }}

                            <div class="row">
                                <!--<div class="col-md-6">
                                    {{ Form::label('name', "Name:") }}
                                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                                </div>

                                <div class="col-md-6">
                                    {{ Form::label('email', 'Email:') }}
                                    {{ Form::text('email', null, ['class' => 'form-control']) }}
                                </div> -->

                                <div class="col-md-12">
                                    {{ Form::label('comment', "Commentario:") }}
                                    {{ Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '5']) }}
                                    <br>
                                    <div class="g-recaptcha" data-sitekey="6LfPuwsUAAAAADUHG1HdmOh_p2mIi9II9a4vGTyX"></div>
                            
                                    {{ Form::submit('Agregar Commentario', ['class' => 'btn btn-success btn-block', 'style' => 'margin-top:15px;']) }}
                                </div>
                            </div>

                        {{ Form::close() }}
                    </div>
                    <!-- End Comment Form -->
                @else
                    <div class="alert alert-info fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>Atención!</strong> Para hacer comentarios deberá <a class="alert-link" href="{{ url('/login') }}"> loquease</a> al Sistema en primera instancia..
                    </div>
                @endif
            </div>
            <!-- End Left Sidebar -->

        </div><!--/row-->        
    </div><!--/container-->		
    <!--=== End Content Part ===-->
@endsection