<?php
if ( ! function_exists( 'register_block_pattern' ) ) {
    return;
}

register_block_pattern(
    'mytheme/custom-three-column-pattern', // Identificador único del pattern
    array(
        'title'       => __('Tres Columnas con Imágenes y Descripciones', 'mytheme'), // Título del pattern
        'description' => __('Un patrón de tres columnas con imágenes, títulos y descripciones.', 'mytheme'), // Descripción del pattern
        'categories'  => array('my-custom-category'), // Categoría del pattern (asegúrate de registrarla)
        'content'     => '
            <!-- wp:columns {"className":"columna-general-s2"} -->
            <div class="wp-block-columns columna-general-s2">

                <!-- wp:column {"width":"","className":"boton1s2"} -->
                <p><a href="">
                <div class="wp-block-column boton1s2">
                    <!-- wp:group {"className":"divimgs-s2","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group divimgs-s2">
                        <!-- wp:image {"id":300,"width":"100px","sizeSlug":"full","linkDestination":"none","className":"imgs-s2 imgtienda-s2"} -->
                        <figure class="wp-block-image size-full is-resized imgs-s2 imgtienda-s2">
                            <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/logo-tienda.png" alt="" class="wp-image-300" style="width:100px"/>
                        </figure>
                        <!-- /wp:image -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:paragraph {"align":"center","className":"titulos-s2","style":{"color":{"text":"#f96302"},"elements":{"link":{"color":{"text":"#f96302"}}}}} -->
                    <p class="has-text-align-center titulos-s2 has-text-color has-link-color" style="color:#f96302">TIENDAS</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"align":"center","className":"descripciones-s2"} -->
                    <p class="has-text-align-center descripciones-s2">Cajeros, asociados de ventas, equipos de carga y más.</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:image {"id":330,"width":"35px","sizeSlug":"full","linkDestination":"none","className":"flechablanca-s2"} -->
                    <figure class="wp-block-image size-full is-resized flechablanca-s2">
                        <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/flecha-s2.png" alt="" class="wp-image-330" style="width:35px"/>
                    </figure>
                    <!-- /wp:image -->
                </div>
                </a>
                </p>
                <!-- /wp:column -->

                <!-- wp:column {"className":"boton1s2"} -->
                <div class="wp-block-column boton1s2">
                    <!-- wp:group {"className":"divimgs-s2","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group divimgs-s2">
                        <!-- wp:image {"id":306,"width":"115px","sizeSlug":"full","linkDestination":"none","className":"imgs-s2 imgcamion-s2"} -->
                        <figure class="wp-block-image size-full is-resized imgs-s2 imgcamion-s2">
                            <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/logo-camion.png" alt="" class="wp-image-306" style="width:115px"/>
                        </figure>
                        <!-- /wp:image -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:paragraph {"align":"center","className":"titulos-s2","style":{"color":{"text":"#f96302"},"elements":{"link":{"color":{"text":"#f96302"}}}}} -->
                    <p class="has-text-align-center titulos-s2 has-text-color has-link-color" style="color:#f96302">CENTROS LOGÍSTICOS</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"align":"center","className":"descripciones-s2"} -->
                    <p class="has-text-align-center descripciones-s2">Centros de distribución, ventas externas, conductores y más.</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:image {"id":330,"width":"35px","sizeSlug":"full","linkDestination":"none","className":"flechablanca-s2"} -->
                    <figure class="wp-block-image size-full is-resized flechablanca-s2">
                        <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/flecha-s2.png" alt="" class="wp-image-330" style="width:35px"/>
                    </figure>
                    <!-- /wp:image -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column {"className":"boton1s2"} -->
                <div class="wp-block-column boton1s2">
                    <!-- wp:group {"className":"divimgs-s2","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
                    <div class="wp-block-group divimgs-s2">
                        <!-- wp:image {"id":307,"width":"70px","sizeSlug":"large","linkDestination":"none","className":"imgs-s2 imgoficina-s2"} -->
                        <figure class="wp-block-image size-large is-resized imgs-s2 imgoficina-s2">
                            <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/edificio-771x1024.png" alt="" class="wp-image-307" style="width:70px"/>
                        </figure>
                        <!-- /wp:image -->
                    </div>
                    <!-- /wp:group -->

                    <!-- wp:paragraph {"align":"center","className":"titulos-s2","style":{"color":{"text":"#f96302"},"elements":{"link":{"color":{"text":"#f96302"}}}}} -->
                    <p class="has-text-align-center titulos-s2 has-text-color has-link-color" style="color:#f96302">OFICINAS DE APOYO A TIENDAS</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:paragraph {"align":"center","className":"descripciones-s2"} -->
                    <p class="has-text-align-center descripciones-s2">Tecnología, comercio electrónico, marketing y más.</p>
                    <!-- /wp:paragraph -->

                    <!-- wp:image {"id":330,"width":"35px","sizeSlug":"full","linkDestination":"none","className":"flechablanca-s2"} -->
                    <figure class="wp-block-image size-full is-resized flechablanca-s2">
                        <img src="http://localhost:8888/thd-careers/wp-content/uploads/2024/11/flecha-s2.png" alt="" class="wp-image-330" style="width:35px"/>
                    </figure>
                    <!-- /wp:image -->
                </div>
                <!-- /wp:column -->

            </div>
            <!-- /wp:columns -->
        ',
    )
);
