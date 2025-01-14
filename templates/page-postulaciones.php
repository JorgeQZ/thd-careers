<?php
/*
Template Name: Postulaciones
*/

?>

<hr>

<?php
    if (isset($_POST['acf_postulacion_nombre'], $_POST['acf_postulacion_correo']) &&
        !empty($_POST['acf_postulacion_nombre']) &&
        !empty($_POST['acf_postulacion_correo'])) {

        $nombre = sanitize_text_field($_POST['acf_postulacion_nombre']);
        $correo = sanitize_text_field($_POST['acf_postulacion_correo']);
        $apellidopaterno = sanitize_text_field($_POST['acf_postulacion_apellidopaterno']);
        $apellidomaterno = sanitize_text_field($_POST['acf_postulacion_apellidomaterno']);
        $telefono = sanitize_text_field($_POST['acf_postulacion_telefono']);
        $pr1 = sanitize_text_field($_POST['pr1']);

        // Crear la nueva postulación en el post-type 'postulaciones'
        $nueva_postulacion = array(
            'post_title'    => wp_strip_all_tags($nombre . ' ' . $apellidopaterno . ' ' . $apellidomaterno),
            'post_type'     => 'postulaciones',
            'post_status'   => 'publish',
        );

        // Insertar el post en la base de datos
        $postulacion_id = wp_insert_post($nueva_postulacion);

        if ($postulacion_id) {
            // Guardar los campos en ACF
            update_field('Nombre', $nombre, $postulacion_id);
            update_field('Apellidopaterno', $apellidopaterno, $postulacion_id);
            update_field('Apellidomaterno', $apellidomaterno, $postulacion_id);
            update_field('Correo', $correo, $postulacion_id);
            update_field('Estado', 'Postulado', $postulacion_id);
            update_field('Telefono', $telefono, $postulacion_id);
            update_field('pr1-postulacion', $pr1, $postulacion_id);

            // Obtener el ID del post principal actual
            $post_id = get_queried_object_id();


            // Guardar el título del post en el campo 'vacante' de la postulación
            update_field('vacante_vacante', get_the_title(get_queried_object_id()), $postulacion_id);

            // Obtener el valor del campo ACF 'ubicacion' del post principal
            $ubicacion = get_field('ubicacion', $post_id);

            $label = $ubicacion['label'];

            // Decodificar los caracteres especiales
            $label_decoded = htmlspecialchars_decode($label, ENT_QUOTES);

            // Obtener el valor del campo 'extra_data' del post principal
            $extra_data = get_field('extra_data', $post_id);

            // Obtener el valor del campo ACF 'data_tienda' del post principal
            $numero_tienda = $extra_data['data_tienda'];

            // Obtener el valor del campo ACF 'data_distrito' del post principal
            $numero_distrito = $extra_data['data_distrito'];

            // Obtener el valor del campo ACF 'data_correo' del post principal
            $correo_rh = $extra_data['data_correo'];

            // Guardar la 'ubicacion' en el campo 'ubicacion_vacante' de la postulación si tiene valor
            if (!empty($ubicacion)) {
                update_field('ubicacion_vacante', $label_decoded, $postulacion_id);
            }

            // Guardar el numero de tienda en el campo 'numero_de_tienda_vacante' de la postulación si tiene valor
            if (!empty($numero_tienda)) {
                update_field('numero_de_tienda_vacante', $numero_tienda, $postulacion_id);
            }

            // Guardar el numero de distrito en el campo 'numero_de_distrito_vacante' de la postulación si tiene valor
            if (!empty($numero_distrito)) {
                update_field('distrito_vacante', $numero_distrito, $postulacion_id);
            }

            // Guardar el numero de distrito en el campo 'numero_de_distrito_vacante' de la postulación si tiene valor
            if (!empty($correo_rh)) {
                update_field('correo_vacante', $correo_rh, $postulacion_id);
            }

            // Manejar la subida del archivo CV
            if (!empty($_FILES['acf_postulacion_cv']['name'])) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }

                $upload_overrides = array('test_form' => false);
                $cv_file = wp_handle_upload($_FILES['acf_postulacion_cv'], $upload_overrides);

                if ($cv_file && !isset($cv_file['error'])) {
                    $attachment = array(
                        'post_mime_type' => $cv_file['type'],
                        'post_title'     => sanitize_file_name($cv_file['file']),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    );

                    $attachment_id = wp_insert_attachment($attachment, $cv_file['file'], $postulacion_id);

                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attachment_id, $cv_file['file']);
                    wp_update_attachment_metadata($attachment_id, $attach_data);

                    update_field('CV', $attachment_id, $postulacion_id);
                    echo '<div class="container"><p>¡Postulación enviada correctamente!</p></div>';
                } else {
                    echo '<p>Hubo un error al subir el archivo: ' . $cv_file['error'] . '</p>';
                }
            } else {
                // Si no se sube un nuevo CV, usar el CV del perfil del usuario
                $user_id = get_current_user_id();
                $cv_perfil = get_field('cv_general', 'user_' . $user_id); // Obtener el CV del usuario

                if ($cv_perfil) {
                    update_field('CV', $cv_perfil, $postulacion_id);
                    echo '<div class="container"><p>¡Postulación enviada correctamente!</p></div>';
                } else {
                    echo '<p>No se ha subido un CV y tampoco hay uno guardado en el perfil.</p>';
                }
            }

            // Enviar notificación por correo después de cambiar el estado
            enviar_correo_cambio_estado($postulacion_id);

        } else {
            echo '<p>Hubo un error al enviar la postulación.</p>';
        }
    }
?>

<?php
    $user_id = get_current_user_id(); // Obtener el ID del usuario actual
    $user_data = get_userdata($user_id); // Obtener la información del usuario

    $nombre = $user_data ? $user_data->first_name : ''; // Obtener el nombre del usuario
    $mail = $user_data ? $user_data->user_email : ''; // Obtener el nombre del usuario
    $apellido_paterno = get_field('apellido_paterno', 'user_' . $user_id); // Usar el nombre exacto del campo
    $apellido_materno = get_field('apellido_materno', 'user_' . $user_id); // Usar el nombre exacto del campo

    $cv = get_field('cv_general', 'user_' . $user_id); // Obtener el ID del archivo
    // Obtener la URL del archivo CV si existe
    $cv_url = $cv ? wp_get_attachment_url($cv['ID']) : '';

    $nombre_rellenar = get_field('nombre_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $apellido_paterno_rellenar = get_field('apellido_paterno_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $apellido_materno_rellenar = get_field('apellido_materno_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $correo_rellenar = get_field('correo_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $telefono_rellenar = get_field('telefono_celular_general', 'user_' . $user_id); // Usar el nombre exacto del campo
?>

<div class="container">

    <div class="contenedorgeneralcampos">
        <div class="seccion">

            <p class="titulo">Titulo 1</p>

            <div class="campos">

                <div>
                    <label>Nombre(s)</label>
                    <input type="text">
                </div>

                <div>
                    <label>Apellido Paterno</label>
                    <input type="text">
                </div>

                <div>
                    <label>Apellido Materno</label>
                    <input type="text">
                </div>

                <div>
                    <label>Correo</label>
                    <input type="text">
                </div>

                <div>
                    <label>¿Cómo te enteraste de nosotros?</label>
                    <select>
                        <option>Seleccione una opción</option>
                        <option>Familia o amigo</option>
                        <option>Bolsa de empleo</option>
                        <option>Facebook</option>
                        <option>Instagram</option>
                        <option>LinkedIn</option>
                        <option>Reclutador virtual EMI</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

</div>