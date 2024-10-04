<?php
add_action('show_user_profile', 'careers_profile_fields');
add_action('edit_user_profile', 'careers_profile_fields');


function careers_profile_fields($user){

    $tienda = get_user_meta($user->ID, 'tienda', true);
    $distrito = get_user_meta($user->ID, 'distrito', true);
    $idempleado = get_user_meta($user->ID, 'idempleado', true);

    ?>
    <h3>Información adicional</h3>
    <table class="form-table">
        <tr>
            <th><label for="tienda">Tienda</label></th>
            <td>
                <input  type="text" name="tienda" id="tienda" value="<?php echo esc_attr($tienda)?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="distrito">Distrito</label></th>
            <td>
                <input  type="text" name="distrito" id="distrito" value="<?php echo esc_attr($distrito)?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="idempleado">ID Empleado</label></th>
            <td>

                <input  type="text" name="idempleado" id="idempleado" value="<?php echo esc_attr($idempleado)?>" class="regular-text">
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'careers_save_profile_fields');
add_action('edit_user_profile_update', 'careers_save_profile_fields');

function careers_save_profile_fields($user_id){
    if( ! isset( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'update-user_' . $user_id ) ) {
		return;
	}

	if( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
    update_user_meta( $user_id, 'tienda', sanitize_text_field( $_POST[ 'tienda' ] ) );
	update_user_meta( $user_id, 'distrito', sanitize_text_field( $_POST[ 'distrito' ] ) );
	update_user_meta( $user_id, 'idempleado', sanitize_text_field( $_POST[ 'idempleado' ] ) );
}



// function careers_get_user_meta_key($meta_key = 'departamento'){
//     global $wpdb;

//     $select = "SELECT distinct $wpdb->usermeta.meta_value FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key = '$meta_key' AND $wpdb->usermeta.meta_value != '' ORDER BY  $wpdb->usermeta.meta_value ASC";
//     $usermeta = $wpdb->get_results($select, ARRAY_N);

//     return $usermeta;
// }
