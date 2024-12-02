<?php

require_once 'load_stores_coordinates.php';

function display_map()
{

    wp_enqueue_style('leaflet-styles', get_template_directory_uri() . '/css/leaflet.css');
    wp_enqueue_style('leaflet-marker', get_template_directory_uri() . '/css/MarkerCluster.css');
    wp_enqueue_style('leaflet-marker-default', get_template_directory_uri() . '/css/MarkerCluster.Default.css');
    wp_enqueue_style('map', get_template_directory_uri() . '/css/map.css');
    wp_enqueue_script('leaflet', get_template_directory_uri() . '/js/leaflet.js');
    wp_enqueue_script('leaflet-markergroup', get_template_directory_uri() . '/js/leaflet.markercluster.js');

    wp_enqueue_script('map', get_template_directory_uri() . '/js/map.js');
    wp_localize_script('map', 'map_vars', array(
        'theme_uri' => get_template_directory_uri(),
        'ajax_url' => admin_url('admin-ajax.php'),
    ));

    $output = '<div id="map" style="height: 400px;"></div>';

    return $output; // Devuelve el contenido
}

add_shortcode('map_thd', 'display_map');
