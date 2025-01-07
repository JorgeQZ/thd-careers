<?php
/*
Template Name: Postulaciones
*/

?>

<hr>
<div class="container">
    <h1>Enviar una nueva Postulación</h1>
</div>
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
    <form method="POST" enctype="multipart/form-data">
        <div class="contenedor">
            <label for="acf_postulacion_nombre">Nombre:</label>
            <input type="text" id="acf_postulacion_nombre" name="acf_postulacion_nombre"
                value="<?php echo esc_attr($nombre_rellenar); ?>" required>
        </div>

        <br>

        <div class="contenedor">
            <label for="acf_postulacion_apellidopaterno">Apellido Paterno:</label>
            <input type="text" id="acf_postulacion_apellidopaterno" name="acf_postulacion_apellidopaterno"
                value="<?php echo esc_attr($apellido_paterno_rellenar); ?>" required>
        </div>

        <br>

        <div class="contenedor">
            <label for="acf_postulacion_apellidomaterno">Apellido Materno:</label>
            <input type="text" id="acf_postulacion_apellidomaterno" name="acf_postulacion_apellidomaterno"
                value="<?php echo esc_attr($apellido_materno_rellenar); ?>" required>
        </div>

        <br>

        <div class="contenedor">
            <label for="acf_postulacion_cv">Sube tu CV:</label>

            <?php if ($cv_url): ?>
            <p>CV Actual: <a href="<?php echo esc_url($cv_url); ?>" target="_blank">Ver archivo</a></p>
            <?php else: ?>
            <p>No hay un CV subido aún.</p>
            <?php endif; ?>

            <input type="file" id="acf_postulacion_cv" name="acf_postulacion_cv">
        </div>

        <br>

        <div class="contenedor">
            <label for="acf_postulacion_correo">Correo:</label>
            <input type="text" id="acf_postulacion_correo" name="acf_postulacion_correo"
                value="<?php echo esc_attr($correo_rellenar); ?>" required>
        </div>

        <br>

        <div class="contenedor">
            <label for="acf_postulacion_telefono">Teléfono:</label>
            <input type="text" id="acf_postulacion_telefono" name="acf_postulacion_telefono"
                value="<?php echo esc_attr($telefono_rellenar); ?>" required>
        </div>

        <div class="seccion1">
            <p class="p1-s1">¿Cómo te enteraste de nosotros?</p>

            <label>
                <input type="radio" name="pr1" value="Familia o amigo"> Familia o amigo
            </label>

            <label>
                <input type="radio" name="pr1" value="Bolsa de empleo"> Bolsa de empleo
            </label>

            <label>
                <input type="radio" name="pr1" value="Facebook"> Facebook
            </label>

            <label>
                <input type="radio" name="pr1" value="Instagram"> Instagram
            </label>

            <label>
                <input type="radio" name="pr1" value="LinkedIn"> LinkedIn
            </label>

            <label>
                <input type="radio" name="pr1" value="Reclutador virtual EMI"> Reclutador virtual EMI
            </label>
        </div>

        <div class="seccion2">
            <p>Experiencia Laboral</p>

            <p>Puesto 1</p>

            <label>Puesto de trabajo</label>
            <input type="text">

            <label>Compañia</label>
            <input type="text">

            <label>Ubicación</label>
            <input type="text">

            <label> <input type="checkbox"> Actualmente trabajo aquí </label>

            <label>Desde</label>
            <input type="date">

            <label>Hasta</label>
            <input type="date">

            <label>Descripción del rol</label>
            <textarea name="" id=""></textarea>

            <p>Puesto 2</p>

            <label>Puesto de trabajo</label>
            <input type="text">

            <label>Compañia</label>
            <input type="text">

            <label>Ubicación</label>
            <input type="text">

            <label> <input type="checkbox"> Actualmente trabajo aquí </label>

            <label>Desde</label>
            <input type="date">

            <label>Hasta</label>
            <input type="date">

            <label>Descripción del rol</label>
            <textarea name="" id=""></textarea>
        </div>

        <div class="seccion3">
            <p>Educación</p>

            <label>Escuela o Universidad</label>
            <input type="text">

            <label>Titulo</label>
            <input type="text">

            <label for="grado_escolaridad">Elige tu grado de escolaridad:</label>
            <select id="grado_escolaridad" name="grado_escolaridad">
                <option value="">Escolaridad - Grado de estudios</option>
                <option value="primaria">Primaria</option>
                <option value="secundaria">Secundaria</option>
                <option value="preparatoria">Preparatoria</option>
                <option value="carrera_tecnica">Carrera técnica</option>
                <option value="licenciatura">Licenciatura</option>
                <option value="posgrado">Posgrado</option>
                <option value="maestria">Maestría</option>
                <option value="doctorado">Doctorado</option>
            </select>
        </div>

        <div class="seccion4">
            <p>Curriculum/CV</p>

            <label for="acf_postulacion_cv">Sube tu CV:</label>

            <?php if ($cv_url): ?>
            <p>CV Actual: <a href="<?php echo esc_url($cv_url); ?>" target="_blank">Ver archivo</a></p>
            <?php else: ?>
            <p>No hay un CV subido aún.</p>
            <?php endif; ?>

            <input type="file" id="acf_postulacion_cv" name="acf_postulacion_cv">
        </div>

        <div class="seccion5">
            <p>Preguntas de Aplicación</p>

            <p>¿Actualmente trabajas o has trabajdo anteriormente en The Home Depot?</p>

            <label>
                <input type="radio" name="" value="Sí"> Sí
            </label>

            <label>
                <input type="radio" name="" value="No"> No
            </label>
        </div>

        <br>
        <br>

        <div class="contenedor-boton">
            <input type="submit" value="ENVIAR POSTULACIÓN">
        </div>
    </form>
</div>