<?php

// Hook para añadir el submenú en el panel de administración
add_action('admin_menu', 'seccion_catalogo_vacantes');

function seccion_catalogo_vacantes() {
    // Verificar los roles de usuario permitidos
        add_menu_page(
            'Catálogo de Vacantes',         // Título de la página
            'Catálogo de Vacantes',         // Título del menú
            'acceso_catalogo_vacantes',     // Capacidad mínima para acceder
            'catalogo_vacantes',            // Slug único para la página
            'display_admin_vacantes',       // Función que muestra el contenido de la página
            'dashicons-clipboard',          // Icono para el menú
            7
        );

}

// Incluir la clase WP_List_Table
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// Clase personalizada para la tabla de vacantes
class Vacantes_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => 'vacante',
            'plural'   => 'vacantes',
            'ajax'     => false,
        ]);
    }

    // Definir las columnas de la tabla
    public function get_columns() {
        return [
            'title'    => 'Título',
            'date'     => 'Fecha',
            'author'   => 'Autor',
            'actions'  => 'Acciones',
        ];
    }

    // Cargar los datos para cada columna
    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'title':
                return '<a href="' . get_permalink($item->ID) . '">' . esc_html($item->post_title) . '</a>';
            case 'date':
                return get_the_date('', $item->ID);
            case 'author':
                return get_the_author_meta('display_name', $item->post_author);
            case 'actions':
                return $this->get_actions_column($item);
            default:
                return '';
        }
    }

    // Preparar los datos para la tabla
    public function prepare_items() {
        $args = [
           'post_type'      => 'vacantes',
            'author'         => 1, // ID del administrador
            'post_status'    => 'any', // Incluir todos los estados de publicación
            'posts_per_page' => 10,
            'paged'          => $this->get_pagenum(),
        ];

        $query = new WP_Query($args);

        $this->items = $query->posts;
        $total_items = $query->found_posts;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => 10,
        ]);

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];

        wp_reset_postdata();
    }

    // Agregar acciones de duplicar y publicar
    protected function get_actions_column($item) {
        $actions = [];

        // Solo mostrar la acción de duplicado y publicación si no es administrador
        if (!current_user_can('administrator')) {
            $duplicate_url = add_query_arg([
                'action' => 'duplicate_post',
                'post'   => $item->ID,
                '_wpnonce' => wp_create_nonce('duplicate_post_' . $item->ID),
            ], admin_url('admin.php'));

            $actions['duplicate'] = '<a href="' . esc_url($duplicate_url) . '">Duplicar</a>';
        }

        return implode(' | ', $actions);
    }
}

// Función para mostrar la tabla en la sección "Catálogo de Vacantes"
function display_admin_vacantes() {
    echo '<div class="wrap">';
    echo '<h1>Catálogo de Vacantes</h1>';

    $vacantesTable = new Vacantes_List_Table();
    $vacantesTable->prepare_items();
    $vacantesTable->display();

    echo '</div>';
}

// Acción para duplicar el post
add_action('admin_action_duplicate_post', 'duplicate_post_action');
function duplicate_post_action() {
    if (!current_user_can('acceso_catalogo_vacantes')) {
        wp_die('No tienes permiso para realizar esta acción.');
    }

    // Verificar nonce y permisos
    if (!isset($_GET['post']) || !isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'duplicate_post_' . $_GET['post'])) {
        wp_die('No tienes permiso para realizar esta acción.');
    }

    // Obtener el post original
    $post_id = absint($_GET['post']);
    $post = get_post($post_id);

    if (!$post) {
        wp_die('Publicación no válida.');
    }

    // Duplicar el post
    $new_post = [
        'post_title'   => $post->post_title,
        'post_content' => $post->post_content,
        'post_status'  => 'draft',
        'post_type'    => $post->post_type,
    ];

    $new_post_id = wp_insert_post($new_post);

    // Verificar si la duplicación fue exitosa
    if ($new_post_id) {
        // Copiar campos de ACF
        $acf_fields = [
            'codigo_de_vacante',
            'descripcion',
            'video',
            'ubicacion',
            'tipo_de_jornada',
            'beneficios',
        ];

        // Valores predeterminados de beneficios
        $default_benefits = [
            'sueldo' => 'Sueldo aprox.',
            'vales' => 'Vales de despensa',
            'bonos' => 'Bono Variable',
            'seguro' => 'Seguro de vida',
            'fondo' => 'Fondo de ahorro',
        ];

        foreach ($acf_fields as $field) {
            $value = get_field($field, $post_id); // Obtiene el valor del campo original

            if ($field === 'video' && strpos($value, '<iframe') !== false) {
                // Procesar el campo "video" para extraer el src del iframe
                preg_match('/src="([^"]+)"/', $value, $matches);
                if (!empty($matches[1])) {
                    // Transformar la URL de "embed" a "watch?v="
                    $url = $matches[1];
                    if (strpos($url, 'https://www.youtube.com/embed/') === 0) {
                        $video_id = str_replace('https://www.youtube.com/embed/', '', $url);
                        $video_id = explode('?', $video_id)[0]; // Eliminar parámetros adicionales
                        $value = 'https://www.youtube.com/watch?v=' . $video_id;
                    }
                }
            }

            // Asegurarnos de que el valor no sea nulo
            if ($value !== null) {
                // Asignar el valor copiado al nuevo post
                update_field($field, $value, $new_post_id);
            }

            // Manejo específico para el campo 'tipo_de_jornada' (select)
            if ($field === 'tipo_de_jornada') {
                if ($value !== null) {
                    update_field($field, $value, $new_post_id);
                }
            }

            // Manejo específico para el campo 'beneficios' (checkbox con múltiples opciones)
            if ($field === 'beneficios' && is_array($value)) {
                // Si no hay beneficios seleccionados, asignar los valores predeterminados
                if (empty($value)) {
                    $value = array_keys($default_benefits); // Asignamos todos los valores predeterminados
                }

                // Copiar los beneficios seleccionados o predeterminados
                update_field($field, $value, $new_post_id);
            }
        }

        // Copiar la taxonomía personalizada "categorias_vacantes"
        $categories = wp_get_post_terms($post_id, 'categorias_vacantes');
        if (!is_wp_error($categories) && !empty($categories)) {
            $category_ids = wp_list_pluck($categories, 'term_id');
            wp_set_post_terms($new_post_id, $category_ids, 'categorias_vacantes'); // Asigna las categorías al nuevo post
        }

        // Redirigir al editor del nuevo post
        wp_redirect(admin_url('post.php?post=' . $new_post_id . '&action=edit'));
        exit;
    } else {
        wp_die('Error al duplicar el post.');
    }

    exit;
}

/**
 * Generación de slug personalizado al guardar el post
 */
add_filter('wp_unique_post_slug', 'custom_vacante_slug_with_admin_check', 10, 6);
function custom_vacante_slug_with_admin_check($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    // Solo modificar el slug para el CPT "vacantes"
    if ($post_type === 'vacantes') {
        $codigo_de_vacante = get_field('codigo_de_vacante', $post_ID);
        $extra_data_data_tienda = get_field('extra_data_data_tienda', $post_ID);

        // Verificar que ambos campos tengan valores
        if (!empty($codigo_de_vacante) && !empty($extra_data_data_tienda)) {
            // Crear el slug base
            $base_slug = sanitize_title($codigo_de_vacante . '-' . $extra_data_data_tienda);

            // Verificar si el usuario es administrador
            if (current_user_can('administrator')) {
                $slug = $base_slug . '-admin'; // Añadir sufijo para admins
            } else {
                $slug = $base_slug; // Mantener el slug estándar
            }
        }
    }

    return $slug;
}

/**
 * Prevenir la creación de duplicados al guardar
 */
add_action('save_post', 'prevent_duplicate_vacantes_creation_conditional', 10, 3);
function prevent_duplicate_vacantes_creation_conditional($post_ID, $post, $update) {
    // Solo actuar sobre el CPT 'vacantes'
    if ($post->post_type === 'vacantes') {
        // Evitar auto-guardados y revisiones
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Obtener los valores de los campos ACF
        $codigo_de_vacante = get_field('codigo_de_vacante', $post_ID);
        $extra_data_data_tienda = get_field('extra_data_data_tienda', $post_ID);

        // Si ambos campos están vacíos, permitir duplicar
        if (empty($codigo_de_vacante) || empty($extra_data_data_tienda)) {
            return; // No se realiza ninguna validación adicional
        }

        // Construir el slug propuesto
        $proposed_slug = sanitize_title($codigo_de_vacante . '-' . $extra_data_data_tienda);

        // Buscar si ya existe otro post con la misma combinación de campos
        $existing_posts = get_posts([
            'post_type'   => 'vacantes',
            'post_status' => ['publish', 'draft', 'pending'], // Verificar todos los estados
            'meta_query'  => [
                'relation' => 'AND',
                [
                    'key'   => 'codigo_de_vacante',
                    'value' => $codigo_de_vacante,
                ],
                [
                    'key'   => 'extra_data_data_tienda',
                    'value' => $extra_data_data_tienda,
                ],
            ],
            'exclude' => [$post_ID], // Excluir el post actual
            'fields' => 'ids', // Solo recuperar IDs
        ]);

        // Si existe un duplicado, detener la acción y mostrar un error
        if (!empty($existing_posts)) {
            wp_delete_post($post_ID, true); // Elimina el post actual permanentemente

            // URL para redirigir a "Mis Vacantes"
            $mis_vacantes_url = admin_url('admin.php?page=mis_vacantes');

            wp_die(
                 sprintf(
                    __(
                        'Error: No se puede crear este post porque ya existe otro con el mismo código y tienda. El post actual ha sido eliminado.<br><br>
                        <a href="%s" class="button button-primary">Regresar a Mis Vacantes</a>',
                        'tu-text-domain'
                    ),
                    esc_url($mis_vacantes_url)
                ),
                __('Error de creación', 'tu-text-domain'),
                ['response' => 403]
            );
        }
    }
}
add_action('save_post', 'update_slug_after_save', 10, 3);
function update_slug_after_save($post_ID, $post, $update) {
    // Evitar que se ejecute en actualizaciones recursivas
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if ($post->post_type === 'vacantes') {
        $codigo_de_vacante = get_field('codigo_de_vacante', $post_ID);
        $extra_data_data_tienda = get_field('extra_data_data_tienda', $post_ID);

        if (!empty($codigo_de_vacante) && !empty($extra_data_data_tienda)) {
            $base_slug = sanitize_title($codigo_de_vacante . '-' . $extra_data_data_tienda);
            $slug = current_user_can('administrator') ? $base_slug . '-admin' : $base_slug;

            // Verificar si el slug realmente ha cambiado
            if ($post->post_name !== $slug) {
                // Actualizar el slug directamente en la base de datos
                wp_update_post(array(
                    'ID' => $post_ID,
                    'post_name' => $slug
                ));
            }
        }
    }
}


function restringir_campos_acf_por_rol($field) {
    // Verificar si el usuario no es un administrador
    if (!current_user_can('administrator')) {
        // Para los campos de tipo "checkbox", deshabilitar las opciones
        if ($field['type'] == 'checkbox') {
            // Añadir una clase específica para identificar el campo en JS
            $field['wrapper']['class'] .= ' deshabilitado-checkbox';
        }
             // Para los campos de tipo "editor WYSIWYG", los ponemos en solo lectura
        elseif ($field['type'] == 'wysiwyg') {
            $field['readonly'] = true;  // Solo lectura
            $field['disabled'] = true;  // Desactivado completamente

        } else {
            // Para otros campos, los ponemos en solo lectura
            $field['readonly'] = true;  // Solo lectura
            $field['disabled'] = true;  // Desactivado completamente
        }
    }

    return $field;
}

// Aplicar la función a los campos específicos del CPT `vacantes`
add_filter('acf/load_field/name=codigo_de_vacante', 'restringir_campos_acf_por_rol');
add_filter('acf/load_field/name=descripcion', 'restringir_campos_acf_por_rol');
add_filter('acf/load_field/name=video', 'restringir_campos_acf_por_rol');
// add_filter('acf/load_field/name=beneficios', 'restringir_campos_acf_por_rol');

function agregar_script_desactivar_checkboxes() {
    // Asegurarse de que solo cargue en el admin
    if (!current_user_can('administrator')) {
        wp_enqueue_script('block-inputs', get_template_directory_uri() . '/js/block-inputs.js', array('jquery'), null, true);

        wp_enqueue_style('vacantes-admin-block-inputs', get_stylesheet_directory_uri() . '/css/vacantes-admin-block-inputs.css', [], '1.0.0');

    }
}
add_action('admin_enqueue_scripts', 'agregar_script_desactivar_checkboxes');


// Deshabilitar la edición del título en el editor para usuarios que no sean administradores
function restringir_titulo_cpt_vacantes($title, $post_id) {
    if (!current_user_can('administrator')) {
        // Restringir solo si es un post de tipo 'vacantes'
        if (get_post_type($post_id) == 'vacantes') {
            return '';  // No mostrar título editable
        }
    }
    return $title;
}
add_filter('input_post_title', 'restringir_titulo_cpt_vacantes', 10, 2);

// Función para restringir la publicación de vacantes solo a borrador para administradores
add_action('save_post', 'restrict_publish_for_admin_vacantes', 10, 3);
function restrict_publish_for_admin_vacantes($post_ID, $post, $update) {
    // Asegurarnos de que solo se aplique al CPT 'vacantes'
    if ($post->post_type === 'vacantes') {
        // Verificar si el autor del post es un administrador
        if (current_user_can('administrator')) {
            // Si el post está siendo publicado (estado 'publish'), cambiarlo a 'draft'
            if ($post->post_status === 'publish') {
                // Evitar la ejecución de la función si es un auto-guardado
                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

                // Actualizar el estado del post a 'draft'
                $post_data = [
                    'ID'          => $post_ID,
                    'post_status' => 'draft', // Cambiar el estado a 'draft'
                ];
                wp_update_post($post_data);
            }
        }
    }
}



?>