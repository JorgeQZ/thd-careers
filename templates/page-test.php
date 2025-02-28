<?php
/**
 * Template Name: Test
 */

get_header();



    // return $numeros_tienda_unicos;

echo '<pre>';
// Uso del array:
$stores = get_field('catalogo_de_tiendas', 'option');
        $stores_and_vacancies = get_vacancies_by_store();

        $filtered_stores = [];

        if(!empty($stores) && !empty($stores_and_vacancies)){
            foreach ($stores as $store) {
                $numero_tienda = $store['numero_de_tienda'];
                if (isset($stores_and_vacancies[$numero_tienda])) {
                    $store['vacantes'] = $stores_and_vacancies[$numero_tienda];
                    $filtered_stores[] = $store;
                }
            }
        }

        print_r($filtered_stores);
        echo '</pre>';
// $userID = get_current_user_id();

// $numero_tienda_objetivo = get_user_meta($userID, 'tienda', true);
// $tipo_negocio = '';

// echo '<pre>';
// // $stores = get_field('catalogo_de_tiendas', 'option');
// // print_r($stores);

// if ( have_rows('catalogo_de_tiendas', 'option') ) {
//     while ( have_rows('catalogo_de_tiendas', 'option') ) {
//         the_row();
//         $numero_tienda = get_sub_field('numero_de_tienda');
//         if ( $numero_tienda == $numero_tienda_objetivo ) {
//             $tipo_negocio = get_sub_field('tipo_de_negocio');
//             break;
//         }
//     }
// }
// switch ($tipo_negocio) {
//     case 'Tienda':
//         update_user_meta( $userID, 'tipo_de_negocio', sanitize_text_field( 'Tiendas' ) );
//         break;
//     case 'Centros Logísticos':
//         update_user_meta( $userID, 'tipo_de_negocio', sanitize_text_field( 'Centros Logísticos' ) );
//         break;
//     case 'Oficina de Apoyo a tiendas':
//         update_user_meta( $userID, 'tipo_de_negocio', sanitize_text_field( 'Oficinas de Apoyo a Tiendas' ) );
//         break;
//     default:
//         echo 'No se encontró el tipo de negocio';
//         break;
// }
// echo '</pre>';
// global $wpdb;

// $blog_id = get_current_blog_id(); // Obtiene el ID del subsitio actual
// $table_name = $wpdb->get_blog_prefix($blog_id) . 'options'; // Asegura que se use la tabla correcta

// echo $table_name . '<br>';

// $query = "SELECT * FROM {$table_name}  WHERE option_name LIKE %s";
// $results = $wpdb->get_results($wpdb->prepare($query, 'options_catalogo%'));

// if (!empty($results)) {
//     foreach ($results as $row) {
//         echo 'Option Name: ' . esc_html($row->option_name) . '<br>';
//         echo 'Option Value: ' . esc_html($row->option_value) . '<br><br>';
//     }
// } else {
//     echo 'No results found.';
// }

// echo '========================<br>';
// $query = "SELECT * FROM {$table_name}  WHERE option_name LIKE %s";
// $results = $wpdb->get_results($wpdb->prepare($query, '%options_catalogo%'));

// if (!empty($results)) {
//     foreach ($results as $row) {
//         echo 'Option Name: ' . esc_html($row->option_name) . '<br>';
//         echo 'Option Value: ' . esc_html($row->option_value) . '<br><br>';
//     }
// } else {
//     echo 'No results found.';
// }
// $field = 'imagen_qr';

// $value = get_field($field, 682);
// echo $value;

// if ($field === 'imagen_qr' && !empty($value)) {
//     // Convierte el ID a URL si es necesario
//     $value = wp_get_attachment_url($value);
//     echo $value;
//     // update_field($field, $value, $new_post_id);
// }

// echo '<pre>';
// print_r(get_field('catalogo_de_tiendas', 'option'));
// echo '</pre>';
// // Valores que deseas insertar en el repeater de la option page
// $nuevas_filas = [
//     [
//         'numero_de_tienda' => '001',
//         'nombre_de_tienda' => 'Tienda A',
//         'ubicacion' => 'Calle 1, Ciudad',
//         'coordenadas' => '19.432608, -99.133209',
//         'distrito' => 'Distrito 1',
//         'tipo_de_negocio' => 'Tienda',
//     ],
//     [
//         'numero_de_tienda' => '002',
//         'nombre_de_tienda' => 'Tienda B',
//         'ubicacion' => 'Calle 2, Ciudad',
//         'coordenadas' => '19.432700, -99.133300',
//         'distrito' => 'Distrito 2',
//         'tipo_de_negocio' => 'Centros Logísticos',
//     ],
//     // Agrega más filas aquí
// ];

// // Obtener las filas actuales del repeater en la option page
// // $repeater_actual = get_field('catalogo_de_tiendas', 'option');

// // Asegurarse de que el repeater actual sea un array
// if (!$repeater_actual) {
//     $repeater_actual = [];
// }

// // Combinar las filas actuales con las nuevas
// $repeater_actual = array_merge($repeater_actual, $nuevas_filas);

// // Actualizar el campo repeater en la option page
// update_field('catalogo_de_tiendas', $repeater_actual, 'option');

// echo 'Filas insertadas con éxito en la página de opciones.';

get_footer();
