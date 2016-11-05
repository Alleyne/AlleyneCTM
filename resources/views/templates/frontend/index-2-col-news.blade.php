@extends('templates.frontend._layouts.unify')

@section('title', '| Front End')

@section('content')
<div class="row magazine-page">
<!-- Magazine News -->
<div class="magazine-news">
    <div class="row">
            @foreach ($posts as $post)        
        <div class="col-md-6">

            <div class="magazine-news-img">
                <a href="#"><img class="img-responsive" src="{{ asset('/images/' . $post->image) }}" alt=""></a>
                <span class="magazine-badge label-yellow">{{ $post->category->name }}</span>                                    
            </div>
            <h3><a href="#">{{ $post->title }}</a></h3>
            <div class="by-author">
                <strong>ctmaster</strong>
                <span><i class="fa fa-calendar"></i> {{ date('M j, Y', strtotime($post->created_at)) }}</span>
            </div> 
            <p>{{ substr(strip_tags($post->body), 0, 250) }}{{ strlen(strip_tags($post->body)) > 250 ? '...' : "" }}</p>

        </div>
            @endforeach    
    </div>
</div>
<!-- End Magazine News -->
</div>




 

@stop