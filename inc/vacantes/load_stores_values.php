<?php
/**
 * Carga las opciones de ubicaciones en el campo SELECT Ubicación del Post Type Vacantes. Las opciones se filtran dependiendo del role actual
 */
  function load_stores_data(){
    $user = wp_get_current_user();
    $role = $user->roles[0];
    $current_user_id = get_current_user_id();
    $table = get_field('catalogo_de_tiendas', 'option');
    switch($role):
        case 'administrator':
            return $table;
            break;
        case 'rh_oat':
            return $table;
            break;
        case 'rh_distrito':
            $field = 'distrito';
            $users_district = get_user_meta($current_user_id, $field, true);
            $stores = [];
            if($table):
                foreach($table as $row):
                    if($row['distrito'] == $users_district)
                        array_push($stores , $row);
                endforeach;
                return $stores;
            endif;
            break;
        case 'rh_general':
            $field = 'tienda';
            $users_store = get_user_meta($current_user_id, $field, true);

            $stores = [];
            if($table):
                foreach($table as $row):

                    if($row['numero_de_tienda'] === $users_store){
                        array_push($stores , $row);
                    }
                endforeach;
                return $stores;
            endif;
            break;
    endswitch;
}


/**
 * Opciones de ubicación mostradas segun el rol del user
 */
add_filter('acf/load_field/name=ubicacion','load_values_values_catalogo' );
function load_values_values_catalogo($field){
    static $is_executing = false;
    if ($is_executing) {
        return $field;
    }
    $is_executing = true;
    $stores = load_stores_data();
    $field['required'] = true;
    foreach ($stores as $store) {
        $field['choices'][$store['numero_de_tienda'].'-'.$store['distrito']] = $store['nombre_de_tienda']." (".$store['ubicacion'].")";
    }

    return $field;
}


/**
 * Configuración como ReadOnly en los campos extras Tienda y Distrito
 */
add_filter('acf/prepare_field/name=data_tienda', 'disable_message_load_field');
add_filter('acf/prepare_field/name=data_distrito', 'disable_message_load_field');
function disable_message_load_field( $field ) {
    $field['disabled'] = 1;
    return $field;
}



add_action('acf/save_post', 'fill_extra_data', 20);

function fill_extra_data($post_id){

     // Evitar revisiones, autosaves y guardados automáticos.
     if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }
    // Verifica que es el Custom Post Type específico (reemplaza 'tu_cpt' con el nombre de tu CPT).
    if ( get_post_type( $post_id ) !== 'vacantes' ) {
        return;
    }

    // Verificar si el usuario actual tiene el rol de admin.
    $current_user = wp_get_current_user();
    if (in_array('administrator', $current_user->roles)) {
        return; // Salir de la función si el usuario es admin.
    }

    // Obtener el valor del campo ACF 'ubicacion'.
    $ubicacion = get_field('ubicacion', $post_id);


    // Verificar que el campo 'ubicacion' no esté vacío y contenga el formato esperado.
    if ( !empty($ubicacion) && isset($ubicacion['value']) ) {
        $ubicacion_exploded = explode('-', $ubicacion['value']);

        // Verificar que 'ubicacion_exploded' tenga al menos dos elementos.
        if ( count($ubicacion_exploded) >= 2 ) {
            $data_tienda = $ubicacion_exploded[0];
            $data_distrito = $ubicacion_exploded[1];

            $extra_data = array(
                'data_tienda'  => $data_tienda,
                'data_distrito' => $data_distrito,
            );

            // Guardar o actualizar los valores en la base de datos como meta del post.
            update_field( 'extra_data', $extra_data, $post_id );

        }
    }

}
?>