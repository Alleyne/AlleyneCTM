@if($errors->has())
	<div class="alert alert-error">			
		<h4>Se encontraron los siguientes errores:</h4>
		<ul>
		    @foreach($errors->all() as $message)
		    <li>{{ $message }}</li>
		    @endforeach
		</ul>
	</div>			
@endif	