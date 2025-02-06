<?php

add_action('wp_ajax_get_stores_locations', 'get_stores_locations');
add_action('wp_ajax_nopriv_get_stores_locations', 'get_stores_locations');

function get_stores_locations()
{
    $cache_key = 'cached_stores';
    $stores = get_transient($cache_key);

    if (!$stores) {
        $stores = get_field('catalogo_de_tiendas', 'option');
        if (!$stores) {
            wp_send_json_error('No se encontraron tiendas.', 404);
        }
        set_transient($cache_key, $stores, HOUR_IN_SECONDS); // Cache por 1 hora
    }


    wp_send_json($stores);
}

add_action('acf/save_post', function($post_id) {
    if (get_field('catalogo_de_tiendas', 'option')) {
        delete_transient('cached_stores'); // Eliminar caché cuando se actualiza el catálogo
    }
});

add_action('wp_ajax_get_related_vacantes', 'get_related_vacantes');
add_action('wp_ajax_nopriv_get_related_vacantes', 'get_related_vacantes');

function get_related_vacantes() {
    if (!isset($_POST['numero_de_tienda']) || !isset($_POST['page'])) {
        wp_send_json_error(['message' => 'Faltan parámetros.', 'code' => 'missing_params'], 400);
    }

    $numero_de_tienda = sanitize_text_field($_POST['numero_de_tienda']);
    $page = intval($_POST['page']);

    if (!is_numeric($numero_de_tienda)) {
        wp_send_json_error(['message' => 'El número de tienda debe ser numérico.', 'code' => 'invalid_store_number'], 400);
    }

    $vacantes_per_page = 5;  // Vacantes por página
    $offset = ($page - 1) * $vacantes_per_page;

    $query_args = [
        'post_type' => 'vacantes',
        'posts_per_page' => $vacantes_per_page,
        'offset' => $offset,
        'meta_query' => [
            [
                'key' => 'extra_data_data_tienda',
                'value' => (string) $numero_de_tienda,
                'compare' => '='
            ]
        ]
    ];

    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        wp_send_json_success([]);
    }

    $vacantes = [];
    while ($query->have_posts()) {
        $query->the_post();
        $vacantes[] = [
            'title' => get_the_title(),
            'url' => get_permalink(),
        ];
    }

    wp_reset_postdata();

    wp_send_json_success($vacantes);
}
