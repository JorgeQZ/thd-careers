<?php
/**
 * Template Name: Test
 */

get_header();

echo '<pre>';
print_r(get_field('catalogo_de_tiendas', 'option'));
echo '</pre>';
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
