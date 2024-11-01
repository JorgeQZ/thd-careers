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
		'rh_tienda',
		'RH Tienda',
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

	remove_role( 'author' );
	remove_role( 'editor' );
	remove_role( 'contributor' );
}
add_action( 'admin_init', 'careers_roles' );

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

?>