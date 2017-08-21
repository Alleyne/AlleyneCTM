
		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="{{ URL::asset('assets/backend/js/libs/jquery-2.0.2.min.js') }}"></script>
		<script src="{{ URL::asset('assets/backend/js/libs/jquery-ui-1.10.3.min.js') }}"></script>
		
		<!-- BOOTSTRAP JS -->
		<script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap-3.3.7.min.js') }}"></script>
		
		<!-- CUSTOM NOTIFICATION -->
		<script src="{{ URL::asset('assets/backend/js/notification/SmartNotification.min.js') }}"></script>
		
		<script src="{{ URL::asset('assets/backend/js/toastr/toastr.js') }}"></script>
		
		<!-- MAIN APP JS FILE-->
		<script src="{{ URL::asset('assets/backend/js/app.js') }}"></script> 

		{{-- <script src="{{ URL::asset('assets/backend/js/datatables/jquery-dataTables-1-10-15-min.js') }}"></script> Nota: NO TRABAJA EN SERVIDOR DIGITAL OCEAN--}} 
		<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

		<!-- NOTIFICACIONES VIA TOASTR-->
		<script>
		  @if(Session::has('success'))
		      toastr.success("{{ Session::get('success') }}", '<< FELICIDADES >>', {timeOut: 7000});
		  @endif

		  @if(Session::has('info'))
		      toastr.info("{{ Session::get('info') }}", '<< ATENCION >>', {timeOut: 7000});
		  @endif

		  @if(Session::has('warning'))
		      toastr.warning("{{ Session::get('warning') }}", '<< PRECAUCION >>', {timeOut: 7000});
		  @endif

		  @if(Session::has('danger'))
		      toastr.error("{{ Session::get('danger') }}", '<< ERROR >>', {timeOut: 7000});
		  @endif
		</script>