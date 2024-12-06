<?php
if (function_exists('register_block_pattern_category')) {
    register_block_pattern_category(
        'thd_careers', // Identificador único de la categoría
        array(
            'label' => __('THD - Careers', 'text-domain'), // Nombre de la categoría
        )
    );
}

register_block_pattern(
    'thd_careers/columns-with-gallery',
    array(
        'title'       => __('Texto e Imágenes Superpuestas', 'text-domain'),
        'description' => __('Un bloque con una columna de texto y otra con una galería de imágenes superpuestas.', 'text-domain'),
        'content'     => '
         <!-- wp:columns {"metadata":{"categories":["thd_careers"],"patternName":"thd_careers/columns-with-gallery","name":"Texto e Imágenes Superpuestas"},"className":"content"} -->
<div class="wp-block-columns content"><!-- wp:column {"className":"column"} -->
<div class="wp-block-column column"><!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Id numquam dicta, ad ducimus quidem architecto atque ipsum modi quaerat aliquid ab vero dolor? Velit in rerum facilis odit doloremque ratione. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fuga, accusantium asperiores quos enim, quisquam nihil eius quo rem provident quaerat ratione? Veniam molestias obcaecati pariatur corporis officia molestiae laudantium placeat? Lorem ipsum dolor sit, amet consectetur adipisicing elit. Reiciendis enim quae autem, quos rem neque quo perferendis rerum cumque aut! Suscipit repellat tempora possimus quasi sed officia, veritatis minima earum!</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Id numquam dicta, ad ducimus quidem architecto atque ipsum modi quaerat aliquid ab vero dolor? Velit in rerum facilis odit doloremque ratione. Lorem ipsum dolor sit amet consectetur adipisicing elit. Fuga, accusantium asperiores quos enim, quisquam nihil eius quo rem provident quaerat ratione? Veniam molestias obcaecati pariatur corporis officia molestiae laudantium placeat? Lorem ipsum dolor sit, amet consectetur adipisicing elit. Reiciendis enim quae autem, quos rem neque quo perferendis rerum cumque aut! Suscipit repellat tempora possimus quasi sed officia, veritatis minima earum!</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"column"} -->
<div class="wp-block-column column"><!-- wp:group {"className":"img-gallery-cont","layout":{"type":"constrained"}} -->
<div class="wp-block-group img-gallery-cont"><!-- wp:image {"id":514,"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"galery-1 is-style-default"} -->
<figure class="wp-block-image size-full galery-1 is-style-default"><img src="'.get_template_directory_uri(  ).'/imgs/asociada-the_home_depot.jpg" alt="" class="wp-image-514" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"id":515,"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"galery-2"} -->
<figure class="wp-block-image size-full galery-2"><img src="'.get_template_directory_uri(  ).'/imgs/personal-the_home_depot.jpg" alt="" class="wp-image-515" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
        ',
        'categories'  => array('thd_careers'),
        'keywords'    => array('texto', 'imágenes', 'columnas'),
    )
);
