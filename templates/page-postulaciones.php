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
        $hasta1 = isset($_POST['puesto-1_hasta-1']) ? sanitize_text_field($_POST['puesto-1_hasta-1']) : '';
        $descripciondelrol1 = sanitize_text_field($_POST['puesto-1_descripcion-del-rol-1']);
        $actualmente_trabajo_aqui = isset($_POST['puesto-1_actualmente-trabajo-aqui-1']) && $_POST['puesto-1_actualmente-trabajo-aqui-1'] === 'Sí' ? 'Sí' : 'No';
        $puesto2 = sanitize_text_field($_POST['puesto-2_puesto-de-trabajo-2']);
        $compania2 = sanitize_text_field($_POST['puesto-2_compania-2']);
        $ubicacion2 = sanitize_text_field($_POST['puesto-2_ubicacion-2']);
        $desde2 = sanitize_text_field($_POST['puesto-2_desde-2']);
        $hasta2 = isset($_POST['puesto-2_hasta-2']) ? sanitize_text_field($_POST['puesto-2_hasta-2']) : '';
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

            // Guardar el ID del post principal en el campo 'id_vacante' de la postulación
            update_field('id_vacante', $post_id, $postulacion_id);

            // Obtener el ID del usuario actual
            $user_id = get_current_user_id();

            // Guardar el ID del usuario en el campo 'id_postulante'
            update_field('id_postulante', $user_id, $postulacion_id);

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
            if (!empty($_FILES['cv_file']['name'])) {
                if (!function_exists('wp_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }

                $upload_overrides = array('test_form' => false);

                $file = $_FILES['cv_file'];
                $gcs_response = upload_to_gcp($file);  // Llamar a la función para subir a GCS

                if ($gcs_response) {
                    try {
                        // Decodificar la respuesta JSON
                        $decoded_response = json_decode($gcs_response);

                        // Verificar errores en la decodificación JSON
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            throw new Exception('Error al decodificar el JSON: ' . json_last_error_msg());
                        }

                        // Validar que la propiedad 'mediaLink' existe en la respuesta
                        if (!isset($decoded_response->mediaLink)) {
                            throw new Exception('La respuesta JSON no contiene la propiedad "mediaLink".');
                        }

                        // Asignar la URL del archivo
                        $gcs_url = $decoded_response->mediaLink;

                        // Actualizar el campo personalizado 'CV' con la URL del archivo en GCS
                        update_field('CV', $gcs_url, $postulacion_id);

                        // Mostrar mensaje de éxito
                        ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                document.getElementById('mensajeExito').style.display = 'flex';
                            });
                        </script>
                        <?php
                    } catch (Exception $e) {
                        // Registrar el error en los logs
                        error_log('Error en la respuesta de GCS: ' . $e->getMessage());

                        // Mostrar un mensaje genérico al usuario
                        echo '<p>Hubo un error al procesar la respuesta del servidor. Por favor, inténtelo más tarde.</p>';
                    }
                } else {
                    echo '<p>Hubo un error al subir el archivo a GCS.</p>';
                }
            } else {
                // Si no se sube un nuevo CV, usar el CV del perfil del usuario
                $current_user = wp_get_current_user(); // Obtener el usuario actual

                if ($current_user->exists()) {
                    // Obtener el CV desde los metadatos del usuario
                    $cv_perfil = get_user_meta($current_user->ID, 'cv_gcs_url', true);

                    if ($cv_perfil) {
                        // Guardar el CV en el campo correspondiente de la postulación
                        update_field('CV', $cv_perfil, $postulacion_id);
                        echo '<div class="container"><p>¡Postulación enviada correctamente!</p></div>';
                    }
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

    // $cv = get_field('cv_general', 'user_' . $user_id); // Obtener el ID del archivo
    // // Obtener la URL del archivo CV si existe
    // $cv_url = $cv ? wp_get_attachment_url($cv['ID']) : '';

    $nombre_rellenar = get_field('nombre_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $apellido_paterno_rellenar = get_field('apellido_paterno_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $apellido_materno_rellenar = get_field('apellido_materno_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $correo_rellenar = get_field('correo_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $telefono_rellenar = get_field('telefono_celular_general', 'user_' . $user_id); // Usar el nombre exacto del campo
    $ar1 = get_field('ar1', 'user_' . $user_id); // Usar el nombre exacto del campo
    $ar2 = get_field('ar2', 'user_' . $user_id);
    $otroar2 = get_field('otroar2', 'user_' . $user_id); // Obtener el valor del campo otroar2
    $escolaridad_rellenar = get_field('grado_escolaridad_general', 'user_' . $user_id);

    $cv_gcs_url = get_user_meta(get_current_user_id(), 'cv_gcs_url', true);
?>

<div class="container">

    <form method="POST" action="" enctype="multipart/form-data" id="formularioPostulacion">

        <div class="contenedorgeneralcampos">
            <div class="seccion">

                <p class="titulo">DATOS GENERALES</p>

                <div class="campos">

                    <div>
                        <label>Nombre(s)<span class="obligatorio">*</span></label>
                        <input type="text" class="req validar" name="acf_postulacion_nombre" value="<?php echo esc_attr($nombre_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Apellido paterno<span class="obligatorio">*</span></label>
                        <input type="text" class="req validar" name="acf_postulacion_apellidopaterno" value="<?php echo esc_attr($apellido_paterno_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Apellido materno<span class="obligatorio">*</span></label>
                        <input type="text" class="req validar" name="acf_postulacion_apellidomaterno" value="<?php echo esc_attr($apellido_materno_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Correo electrónico<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="acf_postulacion_correo" value="<?php echo esc_attr($correo_rellenar); ?>" required>
                    </div>

                    <div>
                        <label>Celular<span class="obligatorio">*</span></label>
                        <input type="text" class="req validar_tel" name="acf_postulacion_telefono" value="<?php echo esc_attr($telefono_rellenar); ?>" required>
                    </div>

                    <div class="divall">
                        <label>¿Cómo te enteraste de nosotros?</label>
                        <select name="Select1-postulacion">
                            <option value="Sin selección">Selecciona una opción</option>
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

                <p class="titulo">EXPERIENCIA LABORAL</p>

                <p class="subtitulo puesto1">Puesto 1</p>

                <div class="campos">

                    <div>
                        <label>Puesto de trabajo<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="puesto-1_puesto-de-trabajo-1" required>
                    </div>

                    <div>
                        <label>Compañía<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="puesto-1_compania-1" required>
                    </div>

                    <div>
                        <label>Ubicación<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="puesto-1_ubicacion-1" required>
                    </div>

                    <div class="div-checkbox">
                        <input type="checkbox" id="checkbox1" name="puesto-1_actualmente-trabajo-aqui-1" value="Sí">
                        <label for="checkbox1">Actualmente trabajo aquí</label>
                    </div>

                    <div>
                        <label>Desde<span class="obligatorio">*</span></label>
                        <div class="custom-date-input">
                            <input type="date" class="req" name="puesto-1_desde-1" required>
                            <span class="icon-calendar"></span>
                        </div>
                    </div>

                    <div>
                        <label>Hasta<span class="obligatorio hasta">*</span></label>
                        <div class="custom-date-input">
                            <input type="date" class="req" name="puesto-1_hasta-1" required>
                            <span class="icon-calendar"></span>
                        </div>
                    </div>

                    <div></div>

                    <div class="div-allarea">
                        <label>Descripción del rol<span class="obligatorio">*</span></label>
                        <textarea class="req" name="puesto-1_descripcion-del-rol-1" required></textarea>
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

                    <div class="div-checkbox dos">
                        <input type="checkbox" id="checkbox2" name="puesto-2_actualmente-trabajo-aqui-2" value="Sí">
                        <label for="checkbox2">Actualmente trabajo aquí</label>
                    </div>

                    <div>
                        <label>Desde</label>
                        <div class="custom-date-input">
                            <input type="date" name="puesto-2_desde-2">
                            <span class="icon-calendar"></span>
                        </div>
                    </div>

                    <div>
                        <label>Hasta</label>
                        <div class="custom-date-input">
                            <input type="date" name="puesto-2_hasta-2">
                            <span class="icon-calendar"></span>
                        </div>
                    </div>

                    <div></div>

                    <div class="div-allarea">
                        <label>Descripción del rol</label>
                        <textarea name="puesto-2_descripcion-del-rol-2"></textarea>
                    </div>

                    <div class="trabajasaqui">
                        <label style="width: 200%;">¿Actualmente trabajas o has trabajado en The Home Depot?</label>
                        <select name="has-trabajado">
                            <option value="Sin Selección">Selecciona una respuesta</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">EDUCACIÓN</p>

                <div class="campos">

                    <div>
                        <label>Escuela o universidad<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="escuela-o-universidad" required>
                    </div>

                    <div>
                        <label>Título<span class="obligatorio">*</span></label>
                        <input type="text" class="req" name="titulo-1" required>
                    </div>

                    <div class="divall">
                        <label>Último grado de estudios</label>
                        <select name="ultimo-grado-de-estudios">
                            <option value="Sin seleccion" <?php selected($escolaridad_rellenar, 'Sin seleccion'); ?>>Escolaridad - Grado de estudios</option>
                            <option value="Primaria" <?php selected($escolaridad_rellenar, 'Primaria'); ?>>Primaria</option>
                            <option value="Secundaria" <?php selected($escolaridad_rellenar, 'Secundaria'); ?>>Secundaria</option>
                            <option value="Preparatoria" <?php selected($escolaridad_rellenar, 'Preparatoria'); ?>>Preparatoria</option>
                            <option value="Carrera técnica" <?php selected($escolaridad_rellenar, 'Carrera técnica'); ?>>Carrera técnica</option>
                            <option value="Licenciatura" <?php selected($escolaridad_rellenar, 'Licenciatura'); ?>>Licenciatura</option>
                            <option value="Posgrado" <?php selected($escolaridad_rellenar, 'Posgrado'); ?>>Posgrado</option>
                            <option value="Maestría" <?php selected($escolaridad_rellenar, 'Maestría'); ?>>Maestría</option>
                            <option value="Doctorado" <?php selected($escolaridad_rellenar, 'Doctorado'); ?>>Doctorado</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo titulo-cv">CURRICULUM/CV</p>

                <div class="div-cvguardado">
                    <span class="file-saved <?php echo !$cv_gcs_url ? 'noactive' : ''; ?>">
                        <?php
                            $cv_gcs_url = get_user_meta(get_current_user_id(), 'cv_gcs_url', true);
                            echo $cv_gcs_url
                                ? '<a class="a-cvguardado" href="' . esc_url($cv_gcs_url) . '" target="_blank">Haz click aquí para ver el CV guardado del perfil (de no subir un archivo nuevo se enviara el CV guardado)</a>'
                                : '';
                        ?>
                    </span>
                </div>

                <div class="campos">

                    <div class="div-cv">
                        <img class="img-file-icon" src="<?php echo get_template_directory_uri(); ?>/imgs/attach-file.svg" alt="file icon">
                        <span class="file-name">Haz clic aquí para subir un CV</span>
                        <input id="file-input" type="file" name="cv_file">
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">AJUSTES RAZONABLES</p>

                <div class="campos">

                    <div class="div-allarea">
                        <label class="requieres" style="width: 1000px;">¿Requieres algún tipo de apoyo para que tu proceso de selección sea incluyente? </label>

                        <span>
                            <input type="radio" name="tipo-de-apoyo" value="Sí" style="margin-bottom: 15px;">
                            Sí
                        </span>

                        <span>
                            <input type="radio" name="tipo-de-apoyo" value="No">
                            No
                        </span>
<!--
                        <select id="support-select" name="tipo-de-apoyo">
                            <option value="Sin Selección" <?php echo ($ar1 === 'Sin Selección') ? 'selected' : ''; ?>>Seleccione una respuesta</option>
                            <option value="Sí" <?php echo ($ar1 === 'Sí') ? 'selected' : ''; ?>>Sí</option>
                            <option value="No" <?php echo ($ar1 === 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
-->
                    </div>

                    <div class="div-allarea" id="support-details" style="display: none;">
                        <label class="requieres" style="width: 1000px;">¿Qué tipo de apoyo o ajuste requieres para tu proceso de selección?</label>

                        <span>
                        <input type="radio" name="que-tipo"
                            value="Uso de notas escritas mediante libreta o pizarra de comunicación">
                            Uso de notas escritas mediante libreta o pizarra de comunicación
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Intérprete de LSM (lengua de señas mexicana)">
                            Intérprete de LSM (lengua de señas mexicana)
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo"
                                value="Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)">
                            Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Poder tomar asiento con frecuencia">
                            Poder tomar asiento con frecuencia
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Magnificadores de pantalla o lupa portátil">
                            Magnificadores de pantalla o lupa portátil
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Uso de lector de pantalla">
                            Uso de lector de pantalla
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Control de estímulos sonoros, como aislamiento de ruido">
                            Control de estímulos sonoros, como aislamiento de ruido
                        </span><br>

                        <span>
                            <input type="radio" name="que-tipo" value="Otro">
                            Otro
                        </span><br>


<!--                    <select id="support-type-select" name="que-tipo">
                            <option value="Sin Selección" <?php echo ($ar2 === 'Sin Selección') ? 'selected' : ''; ?>>Seleccione una respuesta</option>
                            <option value="Uso de notas escritas mediante libreta o pizarra de comunicación" <?php echo ($ar2 === 'Uso de notas escritas mediante libreta o pizarra de comunicación') ? 'selected' : ''; ?>>Uso de notas escritas mediante libreta o pizarra de comunicación</option>
                            <option value="Intérprete de LSM (lengua de señas mexicana)" <?php echo ($ar2 === 'Intérprete de LSM (lengua de señas mexicana)') ? 'selected' : ''; ?>>Intérprete de LSM (lengua de señas mexicana)</option>
                            <option value="Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)" <?php echo ($ar2 === 'Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)') ? 'selected' : ''; ?>>Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)</option>
                            <option value="Poder tomar asiento con frecuencia" <?php echo ($ar2 === 'Poder tomar asiento con frecuencia') ? 'selected' : ''; ?>>Poder tomar asiento con frecuencia</option>
                            <option value="Magnificadores de pantalla o lupa portátil" <?php echo ($ar2 === 'Magnificadores de pantalla o lupa portátil') ? 'selected' : ''; ?>>Magnificadores de pantalla o lupa portátil</option>
                            <option value="Uso de lector de pantalla" <?php echo ($ar2 === 'Uso de lector de pantalla') ? 'selected' : ''; ?>>Uso de lector de pantalla</option>
                            <option value="Control de estímulos sonoros, como aislamiento de ruido" <?php echo ($ar2 === 'Control de estímulos sonoros, como aislamiento de ruido') ? 'selected' : ''; ?>>Control de estímulos sonoros, como aislamiento de ruido</option>
                            <option value="Otro" <?php echo ($ar2 === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                        </select>
-->
                    </div>

                    <div class="div-allarea" id="textareaotro" style="display: none;">
                        <label>Por favor, especifique:</label>
                        <textarea name="especifique-otro"><?php echo esc_html($otroar2); ?></textarea>
                    </div>

                </div>
            </div>

            <div class="seccion">

                <p class="titulo">TÉRMINOS Y CONDICIONES</p>

                <div class="campos">

                    <div class="div-checkbox">
                        <input type="checkbox" class="check_terminos" name="acepto-voluntariamente" value="Sí" required>
                        <label>Acepto voluntariamente los Términos y Condiciones para enviar mi solicitud a The Home Depot.<span class="obligatorio">*</span></label>
                    </div>

                    <a href="<?php echo esc_url(home_url('/carreras/aviso-de-privacidad-candidatos/')); ?>" class="a-tyc" target="_blank" rel="noopener noreferrer">
                        Haz click aquí para conocer los Términos y Condiciones de nuestros Candidatos(as)
                    </a>


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
            fileNameSpan.textContent = 'Haz click aquí para subir un CV';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const supportSelect = document.getElementById('support-select');
        const supportDetails = document.getElementById('support-details');

        var supportRadio = document.getElementsByName('tipo-de-apoyo');
/*
        for (var i = 0; i < supportRadio.length; i++) {
            if (supportRadio[i].checked) {
                console.log('Valor seleccionado: ' + supportRadio[i].value);
            }
        }
*/
        // Función para manejar la visibilidad de supportDetails
        function handleSupportDetails() {
            if (supportRadio === 'Sí') {
                supportDetails.style.display = 'flex';
            } else {
                supportDetails.style.display = 'none';
            }
        }

        // Ejecutar la lógica inicial
        handleSupportDetails();

        // Evento change para actualizar la visibilidad
        //supportSelect.addEventListener('change', handleSupportDetails);

        // Configurar eventos para la primera pregunta (mostrar/ocultar segunda pregunta)
        document.querySelectorAll('input[name="tipo-de-apoyo"]').forEach(radio => {
            radio.addEventListener('change', function () {
                supportRadio = this.value;
                handleSupportDetails();
            });
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

        var supportTypeRadio = document.getElementsByName('que-tipo');

        // Función para manejar la visibilidad de textareaOtro
        function handleTextareaOtro() {
            if (supportTypeRadio == 'Otro') {
                textareaOtro.style.display = 'flex';
            } else {
                textareaOtro.style.display = 'none';
            }
        }

        // Ejecutar la lógica inicial
        handleTextareaOtro();

        // Evento change para actualizar la visibilidad
        //supportTypeSelect.addEventListener('change', handleTextareaOtro);

        document.querySelectorAll('input[name="que-tipo"]').forEach(radio => {
            radio.addEventListener('change', function () {
                supportTypeRadio = this.value;
                handleTextareaOtro();
            });
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox1 = document.getElementById('checkbox1');
        const hasta1 = document.querySelector('input[name="puesto-1_hasta-1"]');
        const spanHasta1 = document.querySelector('.obligatorio.hasta');

        const checkbox2 = document.getElementById('checkbox2');
        const hasta2 = document.querySelector('input[name="puesto-2_hasta-2"]');

        function toggleHastaField(checkbox, hastaField, spanElement, isRequired = true) {
            if (checkbox.checked) {
                hastaField.disabled = true;
                if (isRequired) {
                    hastaField.removeAttribute('required');
                }
                hastaField.value = ''; // Limpia el valor del campo si estaba lleno
                if (spanElement && checkbox === checkbox1) {
                    spanElement.style.display = 'none'; // Oculta el span solo si es checkbox1
                }
            } else {
                hastaField.disabled = false;
                if (isRequired) {
                    hastaField.setAttribute('required', 'required');
                }
                if (spanElement && checkbox === checkbox1) {
                    spanElement.style.display = ''; // Muestra el span solo si es checkbox1
                }
            }
        }

        function handleCheckboxToggle(activeCheckbox, otherCheckbox, hastaField, spanElement, isRequired) {
            // Si el checkbox actual se marca, desmarcar el otro
            if (activeCheckbox.checked) {
                otherCheckbox.checked = false;
                toggleHastaField(
                    otherCheckbox,
                    otherCheckbox === checkbox1 ? hasta1 : hasta2,
                    otherCheckbox === checkbox1 ? spanHasta1 : null,
                    otherCheckbox === checkbox1
                ); // Asegurarse de no pasar un span no existente y considerar si es requerido
            }
            toggleHastaField(activeCheckbox, hastaField, spanElement, isRequired); // Ajustar el estado del campo actual
        }

        // Inicializar el estado al cargar la página
        toggleHastaField(checkbox1, hasta1, spanHasta1, true); // hasta1 es required
        toggleHastaField(checkbox2, hasta2, null, false); // hasta2 nunca es required

        // Escuchar cambios en los checkboxes
        checkbox1.addEventListener('change', function () {
            handleCheckboxToggle(checkbox1, checkbox2, hasta1, spanHasta1, true);
        });

        checkbox2.addEventListener('change', function () {
            handleCheckboxToggle(checkbox2, checkbox1, hasta2, null, false); // hasta2 nunca es required
        });
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

    $(document).ready(function() {

        function verificarCampos() {
            let todosLlenos = true;

            $('.contenedorgeneralcampos input.req, .contenedorgeneralcampos input.req, .contenedorgeneralcampos textarea.req').each(function(index) {
                if ($(this).val() === '') {
                    todosLlenos = false;
                }
            });

            if (!$('input.check_terminos').is(':checked')){
                todosLlenos = false;
            }

/*
            $('.contenedorgeneralcampos select').each(function(index) {
                if ($(this).children('option:first-child').is(':selected')) {
                    todosLlenos = false;
                }
            });
*/

/*
            if (todosLlenos) {
                $('.boton-postulacion').prop('disabled', false);
            } else {
                $('.boton-postulacion').prop('disabled', true);
            }
*/
        }

        $('.contenedorgeneralcampos input.req, .contenedorgeneralcampos textarea.req, .contenedorgeneralcampos input.check_terminos').on('input', function() {
            verificarCampos();
        });

        $('.validar').on('keypress', function(e) {
            var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]+$/;
            var key = String.fromCharCode(event.which);

            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        $('.validar_tel').on('keypress', function(e) {
            var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ+()0-9 ]+$/;
            var key = String.fromCharCode(event.which);

            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        var hoy = new Date().toISOString().split('T')[0];
        $('.contenedorgeneralcampos input[type="date"]').each(function(index) {
            $(this).attr('max', hoy);
            $(this).attr('min', "1965-01-01");
        });

/*
        $('select').change(function() {
            verificarCampos();
        });
*/
        verificarCampos();

    });

</script>