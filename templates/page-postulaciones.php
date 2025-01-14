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
        $select1 = sanitize_text_field($_POST['Select1-postulacion']);
        $puesto1 = sanitize_text_field($_POST['puesto-1_puesto-de-trabajo-1']);
        $compania1 = sanitize_text_field($_POST['puesto-1_compania-1']);
        $ubicacion1 = sanitize_text_field($_POST['puesto-1_ubicacion-1']);
        $desde1 = sanitize_text_field($_POST['puesto-1_desde-1']);
        $hasta1 = sanitize_text_field($_POST['puesto-1_hasta-1']);
        $descripciondelrol1 = sanitize_text_field($_POST['puesto-1_descripcion-del-rol-1']);
        $actualmente_trabajo_aqui = isset($_POST['puesto-1_actualmente-trabajo-aqui-1']) && $_POST['puesto-1_actualmente-trabajo-aqui-1'] === 'Sí' ? 'Sí' : 'No';
        $puesto2 = sanitize_text_field($_POST['puesto-2_puesto-de-trabajo-2']);
        $compania2 = sanitize_text_field($_POST['puesto-2_compania-2']);
        $ubicacion2 = sanitize_text_field($_POST['puesto-2_ubicacion-2']);
        $desde2 = sanitize_text_field($_POST['puesto-2_desde-2']);
        $hasta2 = sanitize_text_field($_POST['puesto-2_hasta-2']);
        $descripciondelrol2 = sanitize_text_field($_POST['puesto-2_descripcion-del-rol-2']);
        $actualmente_trabajo_aqui_2 = isset($_POST['puesto-2_actualmente-trabajo-aqui-2']) && $_POST['puesto-2_actualmente-trabajo-aqui-2'] === 'Sí' ? 'Sí' : 'No';
        $has_trabajado = sanitize_text_field($_POST['has-trabajado']);
        $escuela = sanitize_text_field($_POST['escuela-o-universidad']);
        $titulo = sanitize_text_field($_POST['titulo-1']);
        $gradodeestudios = sanitize_text_field($_POST['ultimo-grado-de-estudios']);
        $tipo_apoyo = sanitize_text_field($_POST['tipo-de-apoyo']);
        $que_tipo = sanitize_text_field($_POST['que-tipo']);
        $especifique = sanitize_text_field($_POST['especifique-otro']);
        $acepto = isset($_POST['acepto-voluntariamente']) && $_POST['acepto-voluntariamente'] === 'Sí' ? 'Sí' : 'No';


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
            update_field('Select1-postulacion', $select1, $postulacion_id);
            update_field('puesto-1_puesto-de-trabajo-1', $puesto1, $postulacion_id);
            update_field('puesto-1_compania-1', $compania1, $postulacion_id);
            update_field('puesto-1_ubicacion-1', $ubicacion1, $postulacion_id);
            update_field('puesto-1_desde-1', $desde1, $postulacion_id);
            update_field('puesto-1_hasta-1', $hasta1, $postulacion_id);
            update_field('puesto-1_descripcion-del-rol-1', $descripciondelrol1, $postulacion_id);
            update_field('puesto-1_actualmente-trabajo-aqui-1', $actualmente_trabajo_aqui, $postulacion_id);
            update_field('puesto-2_puesto-de-trabajo-2', $puesto2, $postulacion_id);
            update_field('puesto-2_compania-2', $compania2, $postulacion_id);
            update_field('puesto-2_ubicacion-2', $ubicacion2, $postulacion_id);
            update_field('puesto-2_desde-2', $desde2, $postulacion_id);
            update_field('puesto-2_hasta-2', $hasta2, $postulacion_id);
            update_field('puesto-2_descripcion-del-rol-2', $descripciondelrol2, $postulacion_id);
            update_field('puesto-2_actualmente-trabajo-aqui-2', $actualmente_trabajo_aqui_2, $postulacion_id);
            update_field('has-trabajado', $has_trabajado, $postulacion_id);
            update_field('escuela-o-universidad', $escuela, $postulacion_id);
            update_field('titulo-1', $titulo, $postulacion_id);
            update_field('ultimo-grado-de-estudios', $gradodeestudios, $postulacion_id);
            update_field('tipo-de-apoyo', $tipo_apoyo, $postulacion_id);
            update_field('que-tipo', $que_tipo, $postulacion_id);
            update_field('especifique-otro', $especifique, $postulacion_id);
            update_field('acepto-voluntariamente', $acepto, $postulacion_id);

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

    <form method="POST" action="" enctype="multipart/form-data">

        <div class="contenedorgeneralcampos">
            <div class="seccion">

                <p class="titulo">Titulo 1</p>

                <div class="campos">

                    <div>
                        <label>Nombre(s)</label>
                        <input type="text" name="acf_postulacion_nombre" value="<?php echo esc_attr($nombre_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Apellido Paterno</label>
                        <input type="text" name="acf_postulacion_apellidopaterno" value="<?php echo esc_attr($apellido_paterno_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Apellido Materno</label>
                        <input type="text" name="acf_postulacion_apellidomaterno" value="<?php echo esc_attr($apellido_materno_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Correo</label>
                        <input type="text" name="acf_postulacion_correo" value="<?php echo esc_attr($correo_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Celular</label>
                        <input type="text" name="acf_postulacion_telefono" value="<?php echo esc_attr($telefono_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>¿Cómo te enteraste de nosotros?</label>
                        <select name="Select1-postulacion">
                            <option value="Sin selección">Seleccione una opción</option>
                            <option value="Familia o amigo">Familia o amigo</option>
                            <option value="Bolsa de empleo">Bolsa de empleo</option>
                            <option value="Facebook">Facebook</option>
                            <option value="Instagram">Instagram</option>
                            <option value="LinkedIn">LinkedIn</option>
                            <option value="Reclutador virtual EMI">Reclutador virtual EMI</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">Titulo 2</p>

                <p class="subtitulo puesto1">Puesto 1</p>

                <div class="campos">

                    <div>
                        <label>Puesto de trabajo</label>
                        <input type="text" name="puesto-1_puesto-de-trabajo-1">
                    </div>

                    <div>
                        <label>Compañia</label>
                        <input type="text" name="puesto-1_compania-1">
                    </div>

                    <div>
                        <label>Ubicación</label>
                        <input type="text" name="puesto-1_ubicacion-1">
                    </div>

                    <div>
                        <label>Desde</label>
                        <input type="date" name="puesto-1_desde-1">
                    </div>

                    <div>
                        <label>Hasta</label>
                        <input type="date" name="puesto-1_hasta-1">
                    </div>

                    <div></div>

                    <div class="div-allarea">
                        <label>Descripción del rol</label>
                        <textarea name="puesto-1_descripcion-del-rol-1"></textarea>
                    </div>

                    <div class="div-checkbox">
                        <input type="checkbox" id="checkbox1" name="puesto-1_actualmente-trabajo-aqui-1" value="Sí">
                        <label for="checkbox1">Actualmente trabajo aquí</label>
                    </div>

                </div>

                <p class="subtitulo puesto2">Puesto 2</p>

                <div class="campos">

                    <div>
                        <label>Puesto de trabajo</label>
                        <input type="text" name="puesto-2_puesto-de-trabajo-2">
                    </div>

                    <div>
                        <label>Compañia</label>
                        <input type="text" name="puesto-2_compania-2">
                    </div>

                    <div>
                        <label>Ubicación</label>
                        <input type="text" name="puesto-2_ubicacion-2">
                    </div>

                    <div>
                        <label>Desde</label>
                        <input type="date" name="puesto-2_desde-2">
                    </div>

                    <div>
                        <label>Hasta</label>
                        <input type="date" name="puesto-2_hasta-2">
                    </div>

                    <div></div>

                    <div class="div-allarea">
                        <label>Descripción del rol</label>
                        <textarea name="puesto-2_descripcion-del-rol-2"></textarea>
                    </div>

                    <div class="div-checkbox dos">
                        <input type="checkbox" id="checkbox2" name="puesto-2_actualmente-trabajo-aqui-2" value="Sí">
                        <label for="checkbox2">Actualmente trabajo aquí</label>
                    </div>

                    <div>
                        <label style="width: 200%;">¿Actualmente trabajas o has trabajado en The Home Depot?</label>
                        <select name="has-trabajado">
                            <option value="Sin Selección">Seleccione una respuesta</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">Titulo 3</p>

                <div class="campos">

                    <div>
                        <label>Escuela o Universidad</label>
                        <input type="text" name="escuela-o-universidad">
                    </div>

                    <div>
                        <label>Título</label>
                        <input type="text" name="titulo-1">
                    </div>

                    <div>
                        <label>Último grado de estudios</label>
                        <select name="ultimo-grado-de-estudios">
                            <option value="Sin seleccion">Escolaridad - Grado de estudios</option>
                            <option value="Primaria">Primaria</option>
                            <option value="Secundaria">Secundaria</option>
                            <option value="Preparatoria">Preparatoria</option>
                            <option value="Carrera técnica">Carrera técnica</option>
                            <option value="Licenciatura">Licenciatura</option>
                            <option value="Posgrado">Posgrado</option>
                            <option value="Maestría">Maestría</option>
                            <option value="Doctorado">Doctorado</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo titulo-cv">Titulo 4</p>

                <div class="campos">

                    <div class="div-cv">
                        <img class="img-file-icon" src="<?php echo get_template_directory_uri(); ?>/imgs/attach-file.svg" alt="file icon">
                        <span class="file-name">Sin archivo seleccionado</span>
                        <input id="file-input" type="file">
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">Titulo 5</p>

                <div class="campos">

                    <div class="div-allarea">
                        <label style="width: 200%;">¿Requieres algún tipo de apoyo para que tu proceso de selección sea incluyente? </label>
                        <select id="support-select" name="tipo-de-apoyo">
                            <option value="Sin Selección">Seleccione una respuesta</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="div-allarea" id="support-details" style="display: none;">
                        <label style="width: 200%;">¿Qué tipo de apoyo o ajuste requieres para tu proceso de selección?</label>
                        <select id="support-type-select" name="que-tipo">
                            <option value="Sin Selección">Seleccione una respuesta</option>
                            <option value="Uso de notas escritas mediante libreta o pizarra de comunicación">Uso de notas escritas mediante libreta o pizarra de comunicación</option>
                            <option value="Intérprete de LSM (lengua de señas mexicana)">Intérprete de LSM (lengua de señas mexicana)</option>
                            <option value="Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)">Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)</option>
                            <option value="Poder tomar asiento con frecuencia">Poder tomar asiento con frecuencia</option>
                            <option value="Magnificadores de pantalla o lupa portátil">Magnificadores de pantalla o lupa portátil</option>
                            <option value="Uso de lector de pantalla">Uso de lector de pantalla</option>
                            <option value="Control de estímulos sonoros, como aislamiento de ruido">Control de estímulos sonoros, como aislamiento de ruido</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="div-allarea" id="textareaotro" style="display: none;">
                        <label>Por favor, especifique:</label>
                        <textarea name="especifique-otro"></textarea>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">Titulo 6</p>

                <div class="campos">

                    <div class="div-checkbox">
                        <input type="checkbox" name="acepto-voluntariamente" value="Sí">
                        <label>Acepto voluntariamente los Términos y Condiciones para enviar mi solicitud a THD.</label>
                    </div>

                </div>
            </div>
        </div>

        <button class="boton-postulacion" type="submit">ENVIAR POSTULACIÓN</button>

    </form>

</div>

<script>
    const fileInput = document.getElementById('file-input');
    const fileNameSpan = document.querySelector('.file-name');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            fileNameSpan.textContent = fileInput.files[0].name;
        } else {
            fileNameSpan.textContent = 'Sin archivo seleccionado';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const supportSelect = document.getElementById('support-select');
        const supportDetails = document.getElementById('support-details');

        supportSelect.addEventListener('change', function () {
            if (supportSelect.value === 'Sí') {
                supportDetails.style.display = 'flex';
            } else {
                supportDetails.style.display = 'none';
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox1 = document.getElementById('checkbox1');
        const checkbox2 = document.getElementById('checkbox2');

        checkbox1.addEventListener('change', function () {
            if (checkbox1.checked) {
                checkbox2.checked = false;
            }
        });

        checkbox2.addEventListener('change', function () {
            if (checkbox2.checked) {
                checkbox1.checked = false;
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const supportTypeSelect = document.getElementById('support-type-select');
        const textareaOtro = document.getElementById('textareaotro');

        supportTypeSelect.addEventListener('change', function () {
            if (supportTypeSelect.value === 'Otro') {
                textareaOtro.style.display = 'flex';
            } else {
                textareaOtro.style.display = 'none';
            }
        });
    });
</script>