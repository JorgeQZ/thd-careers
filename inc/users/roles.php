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
function hide_admin_bar_for_general_users() {
    if (current_user_can('subscriber')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_general_users');

// Evita el acceso al área administrativa para usuarios con el rol 'General'
function restrict_admin_access_for_general_users() {
    if (current_user_can('subscriber') && is_admin()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'restrict_admin_access_for_general_users');


add_action('admin_menu', function() {
	if (current_user_can('rh_general')) {
		remove_menu_page('edit.php');
		remove_menu_page('tools.php');
		remove_menu_page('edit-comments.php');
		remove_menu_page('edit.php?post_type=page');
		remove_menu_page('admin.php?page=catalogo-de-tiendas-y-distritos');
		remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');
	}


	// if (current_user_can('rh_distrito')) {
	// 	remove_menu_page('edit.php');
	// 	remove_menu_page('tools.php');
	// 	remove_menu_page('edit-comments.php');
	// 	remove_menu_page('edit.php?post_type=page');
	// 	remove_menu_page('admin.php?page=catalogo-de-tiendas-y-distritos');
	// 	remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');
	// }
});

function hide_acf_options_page_for_specific_role() {
    // Check if the user has the restricted role
    if (current_user_can('rh_general')) {
        // Replace 'acf-options-page-slug' with the actual slug of the ACF options page you want to hide
        remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');
    }
}
add_action('admin_menu', 'hide_acf_options_page_for_specific_role', 99);

?>