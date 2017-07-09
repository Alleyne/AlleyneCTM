<div class="row">
    <div class="col-md-4 md-margin-bottom-40">
        <!-- About -->
        <div class="headline"><h2>Acerca de</h2></div>  
        <p class="margin-bottom-25 md-margin-bottom-40">Sistema para la administracion transparente de Condominios, Phs, Residenciales y Locales comerciales.</p>    
        <!-- End About -->

        <!-- Monthly Newsletter -->
        <div class="headline"><h2>Boletin mensual</h2></div> 
        <p>Subscribase a nuestro boletin para recibir noticias y  mantenerse al dia con las ultimas de su propiedad!</p>
        <form class="footer-subsribe">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Email">                            
                <span class="input-group-btn">
                    <button class="btn-u" type="button">Subscribase</button>
                </span>
            </div>                  
        </form>                         
        <!-- End Monthly Newsletter -->
    </div><!--/col-md-4-->  
    
    <div class="col-md-4 md-margin-bottom-40">
        <!-- Recent Blogs -->
        <div class="posts">
            <div class="headline"><h2>Mas Recientes</h2></div>
            
            @foreach ($posts as $post)
                <dl class="dl-horizontal">
                    <dt><a href="#"><img src="{{asset('/images/' . $post->image)}}" alt="" /></a></dt>
                    <dd>
                        <p><a href="{{ route('blog.single', $post->slug) }}">{{ $post->title }}</a></p> 
                    </dd>
                </dl>
            @endforeach

        </div>
        <!-- End Recent Blogs -->                    
    </div><!--/col-md-4-->

    <div class="col-md-4">
        <!-- Contact Us -->
        <div class="headline"><h2>Contactenos</h2></div> 
        <address class="md-margin-bottom-40">
            25, Lorem Lis Street, Orange <br />
            California, US <br />
            Phone: 800 123 3456 <br />
            Fax: 800 123 3456 <br />
            Email: <a href="mailto:info@anybiz.com" class="">info@anybiz.com</a>
        </address>
        <!-- End Contact Us -->

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
</div>
