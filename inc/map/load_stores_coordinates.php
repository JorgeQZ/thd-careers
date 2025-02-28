<?php

add_action('wp_ajax_get_stores_locations', 'get_stores_locations');
add_action('wp_ajax_nopriv_get_stores_locations', 'get_stores_locations');

function get_stores_locations()
{
    $cache_key = 'cached_stores';
    $cached_stores = get_transient($cache_key);

    if (!$cached_stores) {
        $allStores = get_field('catalogo_de_tiendas', 'option');
        $stores_and_vacancies = get_vacancies_by_store();

        $filtered_stores = [];

        if(!empty($allStores) && !empty($stores_and_vacancies)){
            foreach ($allStores as $store) {
                $numero_tienda = $store['numero_de_tienda'];
                if (isset($stores_and_vacancies[$numero_tienda])) {
                    $store['vacantes'] = $stores_and_vacancies[$numero_tienda];
                    $filtered_stores[] = $store;
                }
            }
        }

        if (empty($filtered_stores)) {
            wp_send_json_error('No se encontraron tiendas con vacantes.', 404);
        }

        set_transient($cache_key, $stores, HOUR_IN_SECONDS); // Cache por 1 hora
        $stores = $filtered_stores;
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
        'post_status'    => 'publish',
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

function get_vacancies_by_store(){
    $args = array(
        'post_type'      => 'vacantes',  // Reemplaza con el nombre de tu CPT
        'post_status'    => 'publish',   // Solo obtener publicaciones publicadas
        'posts_per_page' => -1,          // Obtener todos los posts
        'fields'         => 'ids'        // Solo obtener los IDs de los posts
    );

    $posts = get_posts($args);

    $contador_tiendas = [];

    if (!empty($posts)) {
        foreach ($posts as $post_id) {
            $numero_tienda = get_field('extra_data_data_tienda', $post_id);
            if (!empty($numero_tienda)) {
                // Incrementar contador por cada número de tienda
                if (!isset($contador_tiendas[$numero_tienda])) {
                    $contador_tiendas[$numero_tienda] = 1;
                } else {
                    $contador_tiendas[$numero_tienda]++;
                }
            }
        }
    }
    return $contador_tiendas;
}
