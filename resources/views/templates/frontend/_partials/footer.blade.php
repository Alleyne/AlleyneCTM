<div class="row">
    <div class="col-md-4 md-margin-bottom-40">
        <!-- About -->
        <div class="headline"><h2>Acerca de</h2></div>  
        <p class="margin-bottom-25 md-margin-bottom-40">Ctmaster en un sistema informatico, en la nuve, especialmente disenado para la administracion transparente de condominios, phs, residenciales, oficinas y locales comerciales.</p>    
        <!-- End About -->

        <!-- Monthly Newsletter -->
        {{-- <div class="headline"><h2>Boletin mensual</h2></div> 
        <p>Subscribase a nuestro boletin para recibir noticias y  mantenerse al dia con las ultimas de su propiedad!</p>
        <form class="footer-subsribe">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Email">                            
                <span class="input-group-btn">
                    <button class="btn-u" type="button">Subscribase</button>
                </span>
            </div>                  
        </form> --}}                         
        <!-- End Monthly Newsletter -->
    </div><!--/col-md-4-->  
    
    <div class="col-md-4 md-margin-bottom-40">
        <!-- Social Links -->
        <div class="headline"><h2>Redes sociales</h2></div> 
        <ul class="social-icons">
            <li><a href="#" data-original-title="Feed" class="social_rss"></a></li>
            <li><a href="#" data-original-title="Facebook" class="social_facebook"></a></li>
            <li><a href="#" data-original-title="Twitter" class="social_twitter"></a></li>
            <li><a href="#" data-original-title="Goole Plus" class="social_googleplus"></a></li>
            <li><a href="#" data-original-title="Pinterest" class="social_pintrest"></a></li>
            <li><a href="#" data-original-title="Linkedin" class="social_linkedin"></a></li>
            <li><a href="#" data-original-title="Vimeo" class="social_vimeo"></a></li>
        </ul>
        <!-- End Social Links -->                    
    </div><!--/col-md-4-->

    <div class="col-md-4">
        <!-- Contact Us -->
        <div class="headline"><h2>Contactenos</h2></div> 
        <address class="md-margin-bottom-40">
            <strong>{{ Cache::get('jdkey')->nombre }}</strong><br>
            {{ Cache::get('jdkey')->calle }}, {{ Cache::get('jdkey')->corregimiento }}<br>
            {{ Cache::get('jdkey')->distrito }}, {{ Cache::get('jdkey')->provincia }}, {{ Cache::get('jdkey')->pais }} <br>
            Tel: {{ Cache::get('jdkey')->telefono }}<br>
            Email: {{ Cache::get('jdkey')->email }}
        </address>
        <!-- End Contact Us -->
    </div><!--/col-md-4-->
</div>
