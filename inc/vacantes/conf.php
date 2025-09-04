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
            'supports' => array('title', 'editor', 'thumbnail'), // Aquí se agrega 'thumbnail'

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

function getColorCat($term_name = 'Tiendas'){

    switch($term_name):
        case 'Centros Logísticos':
            return "#999999";
        case 'Tiendas':
            return '#f96302';
        case 'Oficinas de Apoyo a Tiendas':
            return "#272727";

        default:
            return "#f96302";
    endswitch;

}

function get_unique_locations_with_values($category_slug) {
  // Configuración de la consulta WP_Query
  $query = new WP_Query(array(
      'post_type'      => 'vacantes',
      'posts_per_page' => -1, // Recuperar todos los posts
      'tax_query'      => array(
          array(
              'taxonomy' => 'categorias_vacantes',
              'field'    => 'slug',
              'terms'    => $category_slug, // Filtrar por categoría
          ),
      ),
      'fields'         => 'ids', // Solo necesitamos los IDs para eficiencia
  ));

  $unique_locations = array(); // Almacén de ubicaciones únicas

  if ($query->have_posts()) {
      foreach ($query->posts as $post_id) {
          // Obtener el valor del campo ACF "ubicacion"
          $ubicacion = get_field('ubicacion', $post_id);

          if (is_array($ubicacion)) {
              $label = $ubicacion['label'] ?? ''; // El texto del label
              $value = $ubicacion['value'] ?? ''; // El valor completo

              // Extraer el primer conjunto de números del value
              preg_match('/^\d+/', $value, $matches);
              $numeric_value = $matches[0] ?? '';

              // Agregar la ubicación al array si es única
              if ($label && $numeric_value) {
                  $unique_locations[] = array(
                      'label' => $label,
                      'value' => $numeric_value,
                  );
              }
          }
      }
  }

  // Liberar memoria de la consulta
  wp_reset_postdata();

  return $unique_locations;
}
function get_unique_locations() {
    // Consulta solo IDs por eficiencia
    $q = new WP_Query(array(
        'post_type'      => 'vacantes',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ));

    $unique = array();

    if ($q->have_posts()) {
        foreach ($q->posts as $post_id) {
            $ubicacion = get_field('ubicacion', $post_id);

            // Normalizar a $raw_label / $raw_value
            $raw_label = '';
            $raw_value = '';
            if (is_array($ubicacion)) {
                $raw_label = isset($ubicacion['label']) ? (string) $ubicacion['label'] : '';
                $raw_value = isset($ubicacion['value']) ? (string) $ubicacion['value'] : '';
            } elseif (is_string($ubicacion)) {
                $raw_label = (string) $ubicacion;
                $raw_value = (string) $ubicacion;
            } else {
                continue;
            }
            $raw_label = trim($raw_label);
            $raw_value = trim($raw_value);

            // Extraer código de tienda: 1234 o 1234-56 al inicio
            $source_for_code = $raw_value !== '' ? $raw_value : $raw_label;
            if (!preg_match('/^\s*(\d+(?:-\d+)?)/', $source_for_code, $m)) {
                // Si no hay código al inicio, ignora este registro
                continue;
            }
            $code = $m[1];

            // Construir label humano:
            // Si el label es solo código, úsalo desde value quitando el código y separadores
            $human = $raw_label;
            if ($human === '' || preg_match('/^\d+(?:-\d+)?$/', $human)) {
                // Quitar "1234-56", separadores tipo " - | : — – "
                $human = preg_replace(
                    '/^\s*' . preg_quote($code, '/') . '\s*([:\-\|\xE2\x80\x93\xE2\x80\x94])?\s*/u',
                    '',
                    $raw_value
                );
                $human = trim($human);
            }
            if ($human === '') {
                // Último recurso: usa el code como label
                $human = $code;
            }

            // Deduplicar por código
            $unique[$code] = array(
                'label' => $human,
                'value' => $code,
            );
        }
    }
    wp_reset_postdata();

    // Orden alfabético por label (case-insensitive)
    if (!empty($unique)) {
        uasort($unique, function ($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });
    }

    return array_values($unique);
}

function get_unique_vacantes_titles_by_taxonomy($taxonomy_slug) {
  // Argumentos de la consulta
  $args = array(
      'post_type'      => 'vacantes', // CPT
      'post_status'    => 'publish', // Solo publicados
      'posts_per_page' => -1,        // Obtener todos los posts
      'tax_query'      => array(
          array(
              'taxonomy' => 'categorias_vacantes',
              'field'    => 'slug',
              'terms'    => $taxonomy_slug, // Slug de la taxonomía
          ),
      ),
  );

  // Ejecutar consulta
  $query = new WP_Query($args);
  $titles = array();

  if ($query->have_posts()) {
      while ($query->have_posts()) {
          $query->the_post();
          $titles[] = get_the_title(); // Agregar títulos al array
      }
      wp_reset_postdata(); // Resetear la consulta global
  }

  // Retornar títulos únicos y ordenados
  $unique_titles = array_unique($titles);
  sort($unique_titles, SORT_STRING); // Ordenar alfabéticamente

  return $unique_titles;
}


function get_unique_vacantes_titles() {
    // Argumentos de la consulta
    $args = array(
        'post_type'      => 'vacantes', // CPT
        'post_status'    => 'publish', // Solo publicados
        'posts_per_page' => -1,        // Obtener todos los posts
    );

    // Ejecutar consulta
    $query = new WP_Query($args);
    $titles = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $titles[] = get_the_title(); // Agregar títulos al array
        }
        wp_reset_postdata(); // Resetear la consulta global
    }

    // Retornar títulos únicos y ordenados
    $unique_titles = array_unique($titles);
    sort($unique_titles, SORT_STRING); // Ordenar alfabéticamente

    return $unique_titles;
  }
?>