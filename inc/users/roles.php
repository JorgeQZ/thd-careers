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

?>