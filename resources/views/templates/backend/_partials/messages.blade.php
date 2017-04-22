
@if (count($errors) > 0)

	<div class="alert alert-danger" role="alert">
		<strong>Se encontraron errores en su formulario, favor interntar nuevamente!</strong>
{{-- 		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach  
		</ul> --}}
	</div>

@endif