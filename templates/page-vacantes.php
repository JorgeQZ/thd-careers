<?php
/**
 * Template Name: Vacantes
 */

get_header();
// echo get_the_post_thumbnail();
// echo '<br>';
the_content();
$stores = get_field('catalogo_de_tiendas', 'option');
echo '<pre>';
// print_r($stores);

$valor_buscado = '02'; // Reemplaza esto con el valor que deseas buscar.

$query_args = [
    'post_type'      => 'vacantes', // Slug del CPT.
    'posts_per_page' => -1, // Traer todos los resultados. Cambia según sea necesario.
    'meta_query'     => [
        [
            'key'   => 'extra_data_data_tienda', // El nombre exacto del campo ACF.
            'value' => $valor_buscado,          // El valor que quieres buscar.
            'compare' => '=',                   // Comparación exacta.
        ],
    ],
];

$vacantes_query = new WP_Query($query_args);

if ($vacantes_query->have_posts()) {
    while ($vacantes_query->have_posts()) {
        $vacantes_query->the_post();
        // Muestra el título o cualquier información del post.
        echo '<h2>' . get_the_title() . '</h2>';
    }
} else {
    echo 'No se encontraron resultados.';
}

// Resetea el query global después de un WP_Query personalizado.
wp_reset_postdata();
echo '</pre>';


// $tax_name = "categorias_vacantes";
// $term = get_field($tax_name);

// $args = array(
//     'post_type' => 'vacantes',
//     'order' => 'DESC',
//     'orderby' => 'date',
//     'tax_query' => array(
//         array(
//             'taxonomy' => $tax_name,
//             'field'    => 'slug',
//             'terms'    => $term->slug
//         ),
//     ),
// );
// $query = new WP_Query($args);

// if($query->have_posts()):
//     ?>
// <div>
    // <ul>
        // <?php
//             while($query->have_posts()):
//                 $query->the_post();
//                 ?>
        //
        <!-- <li>
//                     <?php
//                     // echo get_the_title().'</br>';
//                     // echo get_the_id().'</br>';
//                     // echo get_field('ubicacion').'<br>';
//                     // echo get_field('tipo_de_jornada');
//                     ?>
//                 </li> -->
        // <?php
//             endwhile;
//             ?>

        //
    </ul>
    // </div>
// <?php
// else:
//     echo 'No hay vacantes en esta categoría';
// endif;

get_footer();
?>