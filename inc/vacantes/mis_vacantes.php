<?php

function agregar_capacidades_personalizadas() {

    $admin_role = get_role('administrator');

    $admin_role->add_cap('acceso_mis_vacantes');
    $admin_role->add_cap('ver_mis_vacantes');
    $admin_role->add_cap('publicar_vacante');


    $roles_permitidos = ['rh_general', 'rh_oat_', 'rh_admin'];

    foreach ($roles_permitidos as $role_name) {
        $role = get_role($role_name);
        if ($role) {
            $role->add_cap('acceso_mis_vacantes');
            $role->add_cap('ver_mis_vacantes');
            $role->add_cap('publicar_vacante');
        }
    }
}
add_action('admin_init', 'agregar_capacidades_personalizadas');


add_action('admin_menu', 'seccion_mis_vacantes');

function seccion_mis_vacantes() {

    if (current_user_can('acceso_mis_vacantes')) {
        add_menu_page(
            'Mis Vacantes',
            'Mis Vacantes',
            'ver_mis_vacantes',
            'mis_vacantes',
            'display_admin_mis_vacantes',
            'dashicons-clipboard',
            7
        );
    }
}


function display_admin_mis_vacantes() {
    echo '<div class="wrap">';
    echo '<h1>Mis Vacantes</h1>';


    $vacantesTable = new Mis_Vacantes_List_Table();
    $vacantesTable->prepare_items();
    $vacantesTable->display();

    echo '</div>';
}


class Mis_Vacantes_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => 'vacante',
            'plural'   => 'vacantes',
            'ajax'     => false
        ]);
    }


    public function get_columns() {
        return [
            'title'    => 'Título',
            'date'     => 'Fecha',
            'author'   => 'Autor',
            'status'   => 'Estado',
            'actions'  => 'Acciones'
        ];
    }


    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'title':

                    return '<a href="' . get_edit_post_link($item->ID) . '">' . esc_html($item->post_title) . '</a>';

            case 'date':
                return get_the_date('', $item->ID);
            case 'author':
                return get_the_author_meta('display_name', $item->post_author);
            case 'status':
                return ucfirst(get_post_status($item->ID));
            case 'actions':
                return $this->get_actions_column($item);
            default:
                return '';
        }
    }


    private function get_actions_column($item) {
        $actions = '';


        if (get_post_status($item->ID) !== 'publish' && current_user_can('publicar_vacante')) {
            $actions .= '<a href="' . esc_url(add_query_arg(['action' => 'publish', 'post_id' => $item->ID], admin_url('admin-post.php'))) . '">Publicar</a>';
        }

        return $actions;
    }


    public function prepare_items() {
        $args = [
            'post_type'      => 'vacantes',
            'post_status'    => 'any',
            'posts_per_page' => 20,
            'paged'          => $this->get_pagenum(),
            'author'         => get_current_user_id(),
        ];

        $query = new WP_Query($args);

        $this->items = $query->posts;
        $total_items = $query->found_posts;

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => 20,
        ]);

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];

        wp_reset_postdata();
    }
}


add_action('admin_post_publish', 'publicar_vacante_action');
function publicar_vacante_action() {
    if (isset($_GET['post_id']) && current_user_can('publicar_vacante')) {
        $post_id = intval($_GET['post_id']);


        $post = get_post($post_id);
        if ($post && $post->post_status !== 'publish') {

            wp_update_post([
                'ID'          => $post_id,
                'post_status' => 'publish'
            ]);
        }
    }


    wp_redirect(admin_url('admin.php?page=mis_vacantes'));
    exit;
}


// function eliminar_capacidades_personalizadas() {
//     $roles_permitidos = ['administrator', 'rh_general', 'rh_oat', 'rh_distrito'];

//     foreach ($roles_permitidos as $role_name) {
//         $role = get_role($role_name);
//         if ($role) {
//             $role->remove_cap('acceso_mis_vacantes');
//             $role->remove_cap('ver_mis_vacantes');
//             $role->remove_cap('publicar_vacante');
//         }
//     }
// }
// register_deactivation_hook(__FILE__, 'eliminar_capacidades_personalizadas');
