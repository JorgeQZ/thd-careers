<?php
// Hook para añadir el submenú en el panel de administración
add_action('admin_menu', 'seccion_catalogo_vacantes');

function seccion_catalogo_vacantes() {
    // Verificar los roles de usuario permitidos
    if (current_user_can('administrator') || current_user_can('rh_general') || current_user_can('rh_oat') || current_user_can('rh_distrito')) {
        add_menu_page(
            'Catálogo de Vacantes',         // Título de la página
            'Catálogo de Vacantes',         // Título del menú
            'acceso_catalogo_vacantes',     // Capacidad mínima para acceder
            'catalogo_vacantes',            // Slug único para la página
            'display_admin_vacantes',       // Función que muestra el contenido de la página
            'dashicons-clipboard',          // Icono para el menú
            6
        );
    }
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
    // Verificar nonce y permisos
    if (!isset($_GET['post']) || !isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'duplicate_post_' . $_GET['post'])) {
        wp_die('No tienes permiso para realizar esta acción.');
    }

    if (!current_user_can('acceso_catalogo_vacantes')) {
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
        'post_title'   => $post->post_title . ' (Duplicado)',
        'post_content' => $post->post_content,
        'post_status'  => 'draft',
        'post_type'    => $post->post_type,
    ];

    $new_post_id = wp_insert_post($new_post);

     // Verificar si la duplicación fue exitosa
     if ($new_post_id) {
        // Redirigir al editor del nuevo post
        wp_redirect(admin_url('post.php?post=' . $new_post_id . '&action=edit'));
        exit;
    } else {
        wp_die('Error al duplicar el post.');
    }
    exit;
}

function agregar_capacidad_personalizada() {
    $roles = ['rh_general', 'rh_oat', 'rh_distrito', 'administrator'];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('acceso_catalogo_vacantes'); // Añadir capacidad personalizada
        }
    }
}
add_action('init', 'agregar_capacidad_personalizada');
?>
