<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>email</title>
	<link href="{{ URL::asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" media="screen">			
</head>
<body>

<div class="container">

	{!! Form::open(['url' => 'create', 'method' => 'post']) !!}
	  {!! csrf_field() !!}

		<div class="form-group">
		    {!! Form::label('Subject') !!}
		    {!! Form::text('subject', null, 
		        array('required', 
		              'class'=>'form-control', 
		              'placeholder'=>'Subject')) !!}
		</div>	  
		
		<div class="form-group">
		    {!! Form::label('Sender name') !!}
		    {!! Form::text('sender_name', null, 
		        array('required', 
		              'class'=>'form-control', 
		              'placeholder'=>'Sender name')) !!}
		</div>

		<div class="form-group">
		    {!! Form::label('Sender email') !!}
		    {!! Form::email('sender_email', null, 
		        array('required',
		              'class'=>'form-control', 
		              'placeholder'=>'Sender email')) !!}
		</div>	  

		<div class="form-group">
		    {!! Form::label('Recipiente name') !!}
		    {!! Form::text('recipient_name', null, 
		        array('required', 
		              'class'=>'form-control', 
		              'placeholder'=>'Recipiente name')) !!}
		</div>	  

		<div class="form-group">
		    {!! Form::label('Recipient email') !!}
		    {!! Form::email('recipient_email', null, 
		        array('required', 
		              'class'=>'form-control', 
		              'placeholder'=>'Recipient email')) !!}
		</div>	

		<div class="form-group">
		    {!! Form::label('Content') !!}
		    {!! Form::textarea('content', 'null', 
		        array('required', 
		              'class'=>'form-control', 
		              'cols'=> 30,
		              'rows'=>10,
		              'placeholder'=>'Content')) !!}
		</div>
	  
		<div class="form-group">
		    {!! Form::submit('Send', 
		      array('class'=>'btn btn-primary')) !!}
		</div>
	{!! Form::close() !!}
</div>

	
</body>
</html>

