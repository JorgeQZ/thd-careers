<?php
function careers_roles(){
	add_role(
		'rh_general',
		'RH General',
		array(
			'read' => true,
			'moderate_comments' => false,
			'manage_options' => false, // No puede manejar configuraciones
			'edit_pages' => false,    // No puede manejar páginas
			'delete_pages' => false,  // No puede eliminar páginas

			/** POSTS */
			'delete_posts' => true,                      // Puede eliminar sus propios posts
			'delete_published_posts' => true,            // Puede eliminar sus propios posts publicados
			'delete_others_posts' => false,              // No puede eliminar posts de otros
			'delete_private_posts' => true,              // Puede eliminar sus propios posts privados
			'edit_private_posts' => true,                // Puede editar sus propios posts privados
			'read_private_posts' => true,                // Puede leer sus propios posts privados
			'edit_posts' => true,                        // Puede editar sus propios posts
			'edit_published_posts' => true,              // Puede editar sus propios posts publicados
			'edit_others_posts' => false,                // No puede editar posts de otros
			'create_posts' => true,                      // Puede crear posts
			'publish_posts' => true,                     // Puede publicar sus propios posts

			/** CUSTOM POST TYPES */
			'edit_vacantes' => true,                     // Puede editar sus propias "vacantes"
			'edit_others_vacantes' => false,             // No puede editar "vacantes" de otros
			'delete_vacantes' => true,                   // Puede eliminar sus propias "vacantes"
			'delete_others_vacantes' => false,           // No puede eliminar "vacantes" de otros
			'publish_vacantes' => true,                  // Puede publicar sus propias "vacantes"
			'read_private_vacantes' => true,             // Puede leer sus propias "vacantes" privadas

			'edit_postulaciones' => true,                // Puede editar sus propias "postulaciones"
			'edit_others_postulaciones' => false,        // No puede editar "postulaciones" de otros
			'delete_postulaciones' => true,              // Puede eliminar sus propias "postulaciones"
			'delete_others_postulaciones' => false,      // No puede eliminar "postulaciones" de otros
			'publish_postulaciones' => true,             // Puede publicar sus propias "postulaciones"
			'read_private_postulaciones' => true,        // Puede leer sus propias "postulaciones" privadas

			/** Categories */
			'manage_categories' => false,                // No puede manejar categorías

			/** Media Library */
			'upload_files' => true,                      // Puede subir archivos
		)
	);

	add_role(
		'rh_admin',
		'RH Admin',
		array(
			'read' => true,
			'moderate_comments' => true,                  // Puede moderar comentarios
			'manage_options' => false,                   // No puede manejar configuraciones avanzadas del sitio

			/** Páginas */
			'edit_pages' => true,                        // Puede editar páginas
			'edit_others_pages' => true,                 // Puede editar páginas de otros
			'delete_pages' => true,                      // Puede eliminar páginas
			'delete_others_pages' => true,               // Puede eliminar páginas de otros
			'publish_pages' => true,                     // Puede publicar páginas
			'read_private_pages' => true,                // Puede leer páginas privadas

			/** POSTS */
			'delete_posts' => true,                      // Puede eliminar sus propios posts
			'delete_published_posts' => true,            // Puede eliminar posts publicados de todos
			'delete_others_posts' => true,               // Puede eliminar posts de otros
			'delete_private_posts' => true,              // Puede eliminar posts privados de todos
			'edit_private_posts' => true,                // Puede editar posts privados
			'read_private_posts' => true,                // Puede leer posts privados
			'edit_posts' => true,                        // Puede editar sus propios posts
			'edit_published_posts' => true,              // Puede editar posts publicados de todos
			'edit_others_posts' => true,                 // Puede editar posts de otros
			'create_posts' => true,                      // Puede crear posts
			'publish_posts' => false,                    // No puede publicar posts

			/** CUSTOM POST TYPES: Vacantes */
			'edit_vacantes' => true,                     // Puede editar "vacantes"
			'edit_others_vacantes' => true,              // Puede editar "vacantes" de otros
			'delete_vacantes' => true,                   // Puede eliminar "vacantes"
			'delete_others_vacantes' => true,            // Puede eliminar "vacantes" de otros
			'publish_vacantes' => false,                 // No puede publicar "vacantes"
			'read_private_vacantes' => true,             // Puede leer "vacantes" privadas
			'create_vacantes' => true,                   // Puede crear "vacantes"

			/** CUSTOM POST TYPES: Postulaciones */
			'edit_postulaciones' => true,                // Puede editar "postulaciones"
			'edit_others_postulaciones' => true,         // Puede editar "postulaciones" de otros
			'delete_postulaciones' => true,              // Puede eliminar "postulaciones"
			'delete_others_postulaciones' => true,       // Puede eliminar "postulaciones" de otros
			'publish_postulaciones' => false,            // No puede publicar "postulaciones"
			'read_private_postulaciones' => true,        // Puede leer "postulaciones" privadas
			'create_postulaciones' => true,              // Puede crear "postulaciones"

			/** Categories and Taxonomies */
			'manage_categories' => true,                 // Puede manejar categorías
			'edit_terms' => true,                        // Puede editar términos en taxonomías
			'delete_terms' => true,                      // Puede eliminar términos en taxonomías
			'assign_terms' => true,                      // Puede asignar términos a contenidos

			/** Media Library */
			'upload_files' => true,                      // Puede subir archivos
		)
	);

	add_role(
		'rh_oat',
		'RH OAT',
		array(
			'read' => true,
			'moderate_comments' => false,
			'manage_options' => false, // No puede manejar configuraciones
			'edit_pages' => false,    // No puede manejar páginas
			'delete_pages' => false,  // No puede eliminar páginas

			/** POSTS */
			'delete_posts' => true,                      // Puede eliminar sus propios posts
			'delete_published_posts' => true,            // Puede eliminar sus propios posts publicados
			'delete_others_posts' => false,              // No puede eliminar posts de otros
			'delete_private_posts' => true,              // Puede eliminar sus propios posts privados
			'edit_private_posts' => true,                // Puede editar sus propios posts privados
			'read_private_posts' => true,                // Puede leer sus propios posts privados
			'edit_posts' => true,                        // Puede editar sus propios posts
			'edit_published_posts' => true,              // Puede editar sus propios posts publicados
			'edit_others_posts' => false,                // No puede editar posts de otros
			'create_posts' => true,                      // Puede crear posts
			'publish_posts' => true,                     // Puede publicar sus propios posts

			/** CUSTOM POST TYPES */
			'edit_vacantes' => true,                     // Puede editar sus propias "vacantes"
			'edit_others_vacantes' => false,             // No puede editar "vacantes" de otros
			'delete_vacantes' => true,                   // Puede eliminar sus propias "vacantes"
			'delete_others_vacantes' => false,           // No puede eliminar "vacantes" de otros
			'publish_vacantes' => true,                  // Puede publicar sus propias "vacantes"
			'read_private_vacantes' => true,             // Puede leer sus propias "vacantes" privadas

			'edit_postulaciones' => true,                // Puede editar sus propias "postulaciones"
			'edit_others_postulaciones' => false,        // No puede editar "postulaciones" de otros
			'delete_postulaciones' => true,              // Puede eliminar sus propias "postulaciones"
			'delete_others_postulaciones' => false,      // No puede eliminar "postulaciones" de otros
			'publish_postulaciones' => true,             // Puede publicar sus propias "postulaciones"
			'read_private_postulaciones' => true,        // Puede leer sus propias "postulaciones" privadas

			/** Categories */
			'manage_categories' => false,                // No puede manejar categorías

			/** Media Library */
			'upload_files' => true,                      // Puede subir archivos
		)
	);

	remove_role( 'rh_oat_' );
}
add_action( 'admin_init', 'careers_roles' );

function add_admin_ti_careers_role() {
    // Obtener los permisos del rol administrador
    $admin_capabilities = get_role('administrator')->capabilities;

    // Agregar el nuevo rol con los mismos permisos
    add_role('admin_ti_careers', 'Admin TI Careers', $admin_capabilities);
}
add_action('init', 'add_admin_ti_careers_role');

function personalizar_menus_admin() {
    // Obtener el usuario actual
    $usuario_actual = wp_get_current_user();

    // Verificar si el usuario tiene el rol "admin_ti_careers" y NO es Super Admin
    if (in_array('admin_ti_careers', $usuario_actual->roles) && !is_super_admin()) {
        // Ocultar estos menús solo para "admin_ti_careers"
        remove_menu_page('tools.php');             // Herramientas
        remove_menu_page('options-general.php');   // Ajustes
        remove_menu_page('users.php');             // Usuarios
        remove_menu_page('plugins.php');           // Plugins
    }

    // Asegurar que "Administrador" y "Super Admin" vean todo
    if (in_array('administrator', $usuario_actual->roles) || is_super_admin()) {
        // Restaurar menús en caso de que hubieran sido ocultados por otras configuraciones
        add_menu_page('edit.php', 'Entradas', 'edit_posts', 'edit.php', '', 'dashicons-admin-post', 5);
        add_menu_page('upload.php', 'Medios', 'upload_files', 'upload.php', '', 'dashicons-admin-media', 10);
        add_menu_page('edit.php?post_type=page', 'Páginas', 'edit_pages', 'edit.php?post_type=page', '', 'dashicons-admin-page', 20);
        add_menu_page('tools.php', 'Herramientas', 'manage_options', 'tools.php', '', 'dashicons-admin-tools', 30);
        add_menu_page('options-general.php', 'Ajustes', 'manage_options', 'options-general.php', '', 'dashicons-admin-settings', 60);
    }
}

add_action('admin_menu', 'personalizar_menus_admin', 999);

function otorgar_capacidad_publicar_a_rh_oat() {
    $role = get_role('rh_admin');

    if ($role) {
        // Darles la capacidad de publicar
        $role->add_cap('publish_posts');
    }

	$role_oat = get_role('rh_oat');
    if ($role_oat) {
        // Darles la capacidad de publicar
        $role_oat->add_cap('publish_posts');
        $role_oat->add_cap('edit_posts');
		$role_oat->add_cap('edit_vacantes');
        $role_oat->add_cap('delete_posts');

    }
}
add_action('init', 'otorgar_capacidad_publicar_a_rh_oat');

add_action('init', 'update_role_name');
function update_role_name(){
    global $wp_roles;
    $wp_roles->roles['subscriber']['name'] = 'General';
    $wp_roles->role_names['subscriber'] = 'General';
}

// Redirige a los usuarios con el rol 'General' al home después de iniciar sesión
function redirect_general_users($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles) && in_array('subscriber', $user->roles)) {
        return home_url(); // Redirige al home
    }
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_general_users', 10, 3);

// Oculta la barra de administración para usuarios con el rol 'General'
add_filter('show_admin_bar', function ($show) {
    if ( ! is_user_logged_in() ) {
        return false;
    }

    $allowed_roles = [
        'administrator',
        'rh_admin',
        'rh_general',
        'rh_oat',
    ];

    $user = wp_get_current_user();

    foreach ( $allowed_roles as $role ) {
        if ( in_array( $role, (array) $user->roles, true ) ) {
            return true;
        }
    }

    return false;
}, 999);
// Evita el acceso al área administrativa para usuarios con el rol 'General'
function restrict_admin_access_by_capabilities() {
    if (is_admin() && !defined('DOING_AJAX') && !current_user_can('edit_posts')) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'restrict_admin_access_by_capabilities');
add_action('admin_menu', function() {
    if (current_user_can('rh_general')) {
        remove_menu_page('upload.php');
        remove_menu_page('profile.php');
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('tools.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('admin.php?page=catalogo-de-tiendas-y-distritos');
        remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');

		// Verificar el slug exacto de la página de ACF
		remove_menu_page('catalogo-tiendas-acf');
		remove_menu_page('catalogo-de-tiendas-y-distritos');

		// Intentar remover también desde un submenú si es parte de ACF
		remove_submenu_page('acf-options', 'catalogo-tiendas-acf');
		remove_submenu_page('acf-options', 'catalogo-de-tiendas-y-distritos');
    }

    if (current_user_can('rh_oat')) {
        remove_menu_page('index.php');
        remove_menu_page('edit.php');
        remove_menu_page('tools.php');
        remove_menu_page('profile.php');
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=page');
        remove_menu_page('admin.php?page=catalogo-de-tiendas-y-distritos');
        remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');

        // Verificar el slug exacto de la página de ACF
        remove_menu_page('catalogo-tiendas-acf');
        remove_menu_page('catalogo-de-tiendas-y-distritos');

        // Intentar remover también desde un submenú si es parte de ACF
        remove_submenu_page('acf-options', 'catalogo-tiendas-acf');
        remove_submenu_page('acf-options', 'catalogo-de-tiendas-y-distritos');
    }
}, 99);


function hide_acf_options_page_for_specific_role() {
    // Check if the user has the restricted role
    if (current_user_can('rh_general')) {
        // Replace 'acf-options-page-slug' with the actual slug of the ACF options page you want to hide
        remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');
        remove_menu_page('catalogo-tiendas-acf');
        remove_menu_page('catalogo-de-tiendas-y-distritos');
    }
}
add_action('admin_menu', 'hide_acf_options_page_for_specific_role', 99);


/** Verificación de tipo de negocio asignado a roles */
function verify_tn_role($user_login, $user){
	$roles_permitidos = array('rh_general', 'rh_admin', 'rh_oat'); // Roles permitidos para asignar tipo de negocio
	if(array_intersect($roles_permitidos, $user->roles)){
		$tipo_negocio = get_user_meta($user->ID, 'tipo_de_negocio', true); // Obtener el tipo de negocio del usuario
		if(empty($tipo_negocio)){ // Verificación si el campo esta vacío
			$numero_tienda_objetivo = get_user_meta($user->ID, 'tienda', true); // Obtener el número de tienda del usuario
			$tipo_negocio = ''; // Inicializar variable para asignar el tipo de negocio

			if ( have_rows('catalogo_de_tiendas', 'option') ) {
				while ( have_rows('catalogo_de_tiendas', 'option') ) {
					the_row();
					$numero_tienda = get_sub_field('numero_de_tienda'); // Obtener el número de tienda del catálogo
					if ( $numero_tienda == $numero_tienda_objetivo ) {
						$tipo_negocio = get_sub_field('tipo_de_negocio'); // Obtener el tipo de negocio del catálogo
						break;
					}
				}
			}

			/** Switch para asignar el tipo de negocio al user */
			switch ($tipo_negocio) {
				case 'Tienda':
					update_user_meta( $user->ID, 'tipo_de_negocio', sanitize_text_field( 'Tiendas' ) ); // Asignar el tipo de negocio a la meta del usuario
					break;
				case 'Centros Logísticos':
					update_user_meta( $user->ID, 'tipo_de_negocio', sanitize_text_field( 'Centros Logísticos' ) ); // Asignar el tipo de negocio a la meta del usuario
					break;
				case 'Oficina de Apoyo a tiendas':
					update_user_meta( $user->ID, 'tipo_de_negocio', sanitize_text_field( 'Oficinas de Apoyo a Tiendas' ) ); // Asignar el tipo de negocio a la meta del usuario
					break;
				default:
					echo 'No se encontró el tipo de negocio'; // Mensaje de error
					break;
			}
		}
	}
}
add_action( 'wp_login', 'verify_tn_role', 10, 2 ); // Ejecutar la función al iniciar sesión

function allow_super_admin_access_to_menus() {
    if (is_super_admin()) {
        add_filter('user_has_cap', function ($allcaps, $cap, $args) {
            $required_caps = [
                'edit_posts',
                'edit_pages',
                'upload_files',
                'edit_others_posts',
                'publish_posts',
                'manage_categories',
                'edit_published_posts',
                'edit_private_posts',
                'edit_others_pages',
                'edit_private_pages',
                'edit_published_pages',
                'publish_pages',
                'delete_posts',
                'delete_published_posts',
                'delete_others_posts',
                'delete_pages',
                'delete_others_pages',
                'delete_private_pages',
                'delete_published_pages',
            ];

            foreach ($required_caps as $capability) {
                if (!empty($cap) && in_array($capability, $cap)) {
                    $allcaps[$capability] = true;
                }
            }

            return $allcaps;
        }, 10, 3);
    }
}
add_action('init', 'allow_super_admin_access_to_menus');


?>