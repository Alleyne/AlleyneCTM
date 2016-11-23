
@if (count($errors) > 0)

	<div class="alert alert-danger" role="alert">
		<strong>Se encontraron los siguiente errores en el formulario:</strong>
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach  
		</ul>
	</div>

@endif