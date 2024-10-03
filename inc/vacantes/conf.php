<?php
add_action ('init', 'vacantes_cpt');
function vacantes_cpt(){
    register_post_type(
        'vacantes',
        array(
            'labels' => array(
                'name' => 'Vacantes',
                'singular_name' =>'Vacante',
            ),
            'hierarchical'        => false,
            'public' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'menu_position'       => 5,
            'show_in_rest' => true,
            'rewrite' => array(
              'slug' => 'vacantes',
              'with_front' => FALSE
            ),
            'menu_icon' => 'dashicons-id-alt',

        )
    );
}



add_action('init', 'vacantes_taxonomy');
function vacantes_taxonomy(){
  $labels = array(
    'name' => _( 'Categorías de vacantes'),
    'singular_name' => _( 'Categoría'),
    'search_items' =>  __( 'Buscar categorías' ),
    'all_items' => __( 'Todas las categorías' ),
    'parent_item' => __( 'Categoría padre' ),
    'parent_item_colon' => __( 'Categoría padre:' ),
    'edit_item' => __( 'Editar categoría' ),
    'update_item' => __( 'Actualizar categoría' ),
    'add_new_item' => __( 'Añadir nuevo categoría' ),
    'new_item_name' => __( 'Nuevo vategoría' ),
    'menu_name' => __( 'Categorías' ),
  );

  register_taxonomy(
    'categorias_vacantes',
    array('vacantes'),
    array(
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'show_in_rest' => true,
      'show_admin_column' => true,
      'query_var' => true,
      'rewrite' => array( 'slug' => 'categorias_vacantes' ),
    )
  );
}

?>