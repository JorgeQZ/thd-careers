<?php

add_action('wp_ajax_get_stores_locations', 'get_stores_locations');
add_action('wp_ajax_nopriv_get_stores_locations', 'get_stores_locations');

function get_stores_locations(){
    $stores = get_field('tiendas', 'option');
    wp_send_json($stores);
}


?>