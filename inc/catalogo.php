<?php
  function load_stores_data(){
    $current_user_id = get_current_user_id(  );
    $field = 'tienda';
    $users_store = get_user_meta($current_user_id, $field, true);
    return $users_store;
}

add_filter('acf/load_field/name=ubicacion','load_values_values_catalogo' );
function load_values_values_catalogo($field){
    // $table = get_field('tiendas', 'option');
    // $field['choices'] = array();

    // if($table){
    //     foreach($table as $row){
    //         // $field['choices'][] = 'test';
    //     }
    // }
    $field['required'] = true;
    $field['choices'] = array(
        'custom'    => 'My Custom Choice',
        'custom_2'  => 'My Custom Choice 2'
    );
    return $field;
}

?>