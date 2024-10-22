<?php
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
        case 'rh_tienda':
            $field = 'tienda';
            $users_store = get_user_meta($current_user_id, $field, true);
            $stores = [];
            if($table):
                foreach($table as $row):
                    if($row['numero_de_tienda'] == $users_store){
                        array_push($stores , $row);
                    }
                endforeach;
                return $stores;
            endif;
            break;
    endswitch;
}


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
        $field['choices'][$store['numero_de_tienda']] = $store['nombre_de_tienda']." (".$store['ubicacion'].")";
    }
    return $field;
}

?>