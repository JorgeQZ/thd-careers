<?php
add_action ('init', 'vacantes_cpt');
function vacantes_cpt(){
    register_post_type(
        'vacantes',
        array(
            'labels' => array(
                'name' => 'Todas las vacantes',
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

// Añade la columna de "Autor" en el Custom Post Type
add_filter('manage_vacantes_posts_columns', 'add_author_column');
function add_author_column($columns) {
  $columns['author'] = 'Autor'; // Añade la columna de autor con el título "Autor"
  return $columns;
}

// Muestra el autor en la columna de "Autor" en el Custom Post Type
add_action('manage_vacantes_posts_custom_column', 'display_author_table_column', 10, 2);
function display_author_table_column($column, $post_id) {
  if ($column == 'author') {
      // Obtener el nombre del autor
      $author = get_the_author_meta('display_name', get_post_field('post_author', $post_id));

      // Mostrar el nombre del autor
      echo esc_html($author);
  }
}


// Agregar campo de autor en tabla de Vacantes
add_filter('manage_vacantes_posts_columns', 'add_store_column');
function add_store_column($columns) {
  $columns['acf_field'] = 'Tienda';
  return $columns;
}

// Añade la columna tienda a la tabla de vacantes
add_action('manage_vacantes_posts_custom_column', 'display_store_table_column', 10, 2);
function display_store_table_column($column, $post_id) {
  if ($column == 'acf_field') {
      $num_tienda = get_field('extra_data_data_tienda', $post_id);
      echo getStoreByCode($num_tienda);
  }
}

// Agregar campo de Tienda en tabla de Vacantes
function getStoreByCode($store_code){
  $table = get_field('catalogo_de_tiendas', 'option');
  foreach($table as $store){
    if($store['numero_de_tienda'] == $store_code){
      return $store['numero_de_tienda'].'.- '.$store['nombre_de_tienda'].' <br><br><small>('.$store['ubicacion'].')</small>';
    }
  }
}



?>