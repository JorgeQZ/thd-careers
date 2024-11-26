<?php

add_action('wp_ajax_get_stores_locations', 'get_stores_locations');
add_action('wp_ajax_nopriv_get_stores_locations', 'get_stores_locations');

function get_stores_locations(){
    $stores = get_field('catalogo_de_tiendas', 'option');
    wp_send_json($stores);
}



add_action('wp_ajax_get_related_vacantes', 'get_related_vacantes');
add_action('wp_ajax_nopriv_get_related_vacantes', 'get_related_vacantes');

function get_related_vacantes() {
    // Validar que se envió el número de tienda
    if (!isset($_POST['numero_de_tienda'])) {
        wp_send_json_error('Falta el número de tienda.', 400);
    }

    $numero_de_tienda = sanitize_text_field($_POST['numero_de_tienda']);

    // Consulta para obtener los CPT relacionados
    $query_args = [
        'post_type'      => 'vacantes',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'extra_data_data_tienda',
                'value'   => $numero_de_tienda,
                'compare' => '='
            ],
        ],
    ];

    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        wp_send_json_success([]); // No hay vacantes relacionadas
    }

    $vacantes = [];

    while ($query->have_posts()) {
        $query->the_post();
        $vacantes[] = [
            'title' => get_the_title(),
            'url'   => get_permalink()
        ];
    }

    wp_reset_postdata();

    wp_send_json_success($vacantes);
}

?>