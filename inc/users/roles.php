<?php
function careers_roles(){
	add_role(
		'rh_oat',
		'RH OAT',
		array(
			'read' => true,
			'moderate_comments' => false,
			'manage_options' => false,
			'edit_pages' => true,

			/** POSTS */
			'delete_posts' => true,
			'delete_published_posts' => true,
			'delete_others_posts' => true,
			'delete_private_posts' => true,
			'edit_private_posts' => true,
			'read_private_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'edit_others_posts' => true,
			'create_posts' => true,
			'publish_posts' => true,

			/** Categories */
			'manage_categories' => true,
			'upload_files' => true,
		)
	);

	add_role(
		'rh_distrito',
		'RH Distrital',
		array(
			'read' => true,
			'moderate_comments' => false,
			'manage_options' => false,
			'edit_pages' => true,

			/** POSTS */
			'delete_posts' => true,
			'delete_published_posts' => true,
			'delete_others_posts' => true,
			'delete_private_posts' => true,
			'edit_private_posts' => true,
			'read_private_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'edit_others_posts' => true,
			'create_posts' => true,
			'publish_posts' => true,

			/** Categories */
			'manage_categories' => true,
			'upload_files' => true,
		)
	);


	add_role(
		'rh_general',
		'RH General',
		array(
			'read' => true,
			'moderate_comments' => false,
			'manage_options' => false,
			'edit_pages' => false,

			/** POSTS */
			'delete_posts' => true,
			'delete_published_posts' => false,
			'delete_others_posts' => false,
			'delete_private_posts' => true,
			'edit_private_posts' => true,
			'read_private_posts' => true,
			'edit_posts' => true,
			'edit_published_posts' => true,
			'edit_others_posts' => false,
			'create_posts' => true,
			'publish_posts' => false,

			/** Categories */
			'manage_categories' => false,
			'upload_files' => true,
		)
	);



	remove_role( 'author' );
	remove_role( 'editor' );
	remove_role( 'contributor' );
	remove_role( 'rh_tienda' );
}
add_action( 'admin_init', 'careers_roles' );

function actualizar_rol_para_no_publicar() {
    $rh_general = get_role('rh_general');
    if ($rh_general) {
        $rh_general->add_cap('publish_posts');  // Permite publicar sus propios posts
        $rh_general->add_cap('edit_posts');     // Permite editar sus propios posts

        $rh_general->remove_cap('edit_others_posts'); // No puede editar los posts de otros
        $rh_general->remove_cap('delete_others_posts'); // No puede eliminar posts de otros
        $rh_general->remove_cap('manage_categories');
    }

	$rolDistrito = get_role('rh_distrito');
    if ($rolDistrito) {
		$rolDistrito->add_cap('publish_posts');
		$rolDistrito->add_cap('delete_others_posts');
		$rolDistrito->add_cap('edit_others_posts');
    }
}
add_action('init', 'actualizar_rol_para_no_publicar');

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


	if (current_user_can('rh_distrito')) {
		remove_menu_page('edit.php');
		remove_menu_page('tools.php');
		remove_menu_page('edit-comments.php');
		remove_menu_page('edit.php?post_type=page');
		remove_menu_page('admin.php?page=catalogo-de-tiendas-y-distritos');
		remove_menu_page('acf-options-catalogo-de-tiendas-y-distritos');
	}
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