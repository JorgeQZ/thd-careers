<?php
/**
 * Template Name: Test
 */


 get_header(); // Incluir el encabezado de WordPress
// echo get_user_meta(1, 'gcs_url_name', true);

$cv_url = obtener_url_archivo(get_user_meta(1, 'gcs_url_name', true));
echo $cv_url;
 get_footer(); // Incluir el pie de página de WordPress

?>