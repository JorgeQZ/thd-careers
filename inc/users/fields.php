<?php
add_action('show_user_profile', 'careers_profile_fields');
add_action('edit_user_profile', 'careers_profile_fields');


function careers_profile_fields($user){

    $tienda = get_user_meta($user->ID, 'tienda', true);
    $distrito = get_user_meta($user->ID, 'distrito', true);
    $idempleado = get_user_meta($user->ID, 'idempleado', true);
    $tipo_de_negocio = get_user_meta($user->ID, 'tipo_de_negocio', true);
    $cv_gcs_url = get_user_meta($user->ID, 'cv_gcs_url', true);

    ?>
    <h3>Información adicional</h3>
    <table class="form-table">
        <tr>
            <th><label for="tipo_de_negocio">Tipo de negocio</label></th>
            <td>
                <input  type="text" name="tipo_de_negocio" id="tipo_de_negocio" value="<?php echo esc_attr($tipo_de_negocio)?>" class="regular-text">
            </td>
        </tr>
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

        <tr>
            <th><label for="cv">CV</label></th>
            <td>

                <input  type="text" name="cv" id="cv" value="<?php echo esc_attr($cv_gcs_url)?>" class="regular-text">

                <?php
        // Verificar si el enlace del CV es válido
        if (!empty($cv_gcs_url)) {
            // Verificar si es un archivo PDF
            $file_extension = pathinfo($cv_gcs_url, PATHINFO_EXTENSION);

            if ($file_extension == 'pdf') {
                // Si es un PDF, mostrarlo en un iframe
                echo '<div><strong>Vista previa del CV:</strong><br>';
                echo '<iframe src="' . esc_url($cv_gcs_url) . '" width="600" height="400"></iframe></div>';
            } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                // Si es una imagen, mostrarla como una imagen
                echo '<div><strong>Vista previa del CV:</strong><br>';
                echo '<img src="' . esc_url($cv_gcs_url) . '" width="200" alt="Vista previa del CV" /></div>';
            } else {
                // Si no es un archivo previsible, mostrar un enlace al archivo
                echo '<div><strong>Vista previa no disponible. </strong><a href="' . esc_url($cv_gcs_url) . '" target="_blank">Ver CV completo</a></div>';
            }
        }
        ?>
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
	update_user_meta( $user_id, 'tipo_de_negocio', sanitize_text_field( $_POST[ 'tipo_de_negocio' ] ) );
}



// function careers_get_user_meta_key($meta_key = 'departamento'){
//     global $wpdb;

//     $select = "SELECT distinct $wpdb->usermeta.meta_value FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key = '$meta_key' AND $wpdb->usermeta.meta_value != '' ORDER BY  $wpdb->usermeta.meta_value ASC";
//     $usermeta = $wpdb->get_results($select, ARRAY_N);

//     return $usermeta;
// }
