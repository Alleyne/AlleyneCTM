
		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="{{ URL::asset('assets/backend/js/libs/jquery-2.0.2.min.js') }}"></script>
		<script src="{{ URL::asset('assets/backend/js/libs/jquery-ui-1.10.3.min.js') }}"></script>
		
		<!-- BOOTSTRAP JS -->
		<script src="{{ URL::asset('assets/backend/js/bootstrap/bootstrap-3.3.7.min.js') }}"></script>
		
		<!-- CUSTOM NOTIFICATION -->
		<script src="{{ URL::asset('assets/backend/js/notification/SmartNotification.min.js') }}"></script>
		
		<!-- JARVIS WIDGETS
		<script src="{{ URL::asset('assets/backend/js/smartwidgets/jarvis.widget.min.js') }}"></script> -->
		
		<!-- EASY PIE CHARTS
		<script src="{{ URL::asset('assets/backend/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js') }}"></script> --> 
		
		<!-- SPARKLINES
		<script src="{{ URL::asset('assets/backend/js/plugin/sparkline/jquery.sparkline.min.js') }}"></script> -->
		
		<!-- JQUERY MASKED INPUT
		<script src="{{ URL::asset('assets/backend/js/plugin/masked-input/jquery.maskedinput.min.js') }}"></script> -->
		
		<!-- JQUERY SELECT2 INPUT
		<script src="{{ URL::asset('assets/backend/js/plugin/select2/select2.min.js') }}"></script> -->
		
		<!-- JQUERY UI + Bootstrap Slider
		<script src="{{ URL::asset('assets/backend/js/plugin/bootstrap-slider/bootstrap-slider.min.js') }}"></script> -->
		
		<!-- browser msie issue fix -->
		<script src="{{ URL::asset('assets/backend/js/plugin/msie-fix/jquery.mb.browser.min.js') }}"></script>
		
		<!-- FastClick: For mobile devices
		<script src="{{ URL::asset('assets/backend/js/plugin/fastclick/fastclick.js') }}"></script> -->
		
		<!--[if IE 7]>

		<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

		<![endif]-->
		{{-- <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script> --}}
		<script src="{{ URL::asset('assets/backend/js/toastr/toastr.js') }}"></script>
		
		<!-- Demo purpose only
		<script src="{{ URL::asset('assets/backend/js/demo.js') }}"></script>-->
		
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