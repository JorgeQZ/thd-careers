<?php

if (!function_exists('wp_handle_upload')) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
}

// Obtener el ID del usuario conectado.
$user_id = get_current_user_id();

// Manejo del formulario para actualizar ACF.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        update_field('nombre_general', $nombre, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['apellido_paterno'])) {
        $apellido_paterno = sanitize_text_field($_POST['apellido_paterno']);
        update_field('apellido_paterno_general', $apellido_paterno, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['apellido_materno'])) {
        $apellido_materno = sanitize_text_field($_POST['apellido_materno']);
        update_field('apellido_materno_general', $apellido_materno, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['correo'])) {
        $correo = sanitize_text_field($_POST['correo']);
        update_field('correo_general', $correo, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['fecha_de_nacimiento'])) {
        $fecha_de_nacimiento = sanitize_text_field($_POST['fecha_de_nacimiento']);
        update_field('fecha_de_nacimiento_general', $fecha_de_nacimiento, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['calle'])) {
        $calle = sanitize_text_field($_POST['calle']);
        update_field('calle_general', $calle, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['numero_interiorexterior'])) {
        $numero_interiorexterior = sanitize_text_field($_POST['numero_interiorexterior']);
        update_field('numero_interiorexterior_general', $numero_interiorexterior, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['colonia'])) {
        $colonia = sanitize_text_field($_POST['colonia']);
        update_field('colonia_general', $colonia, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['codigo_postal'])) {
        $codigo_postal = sanitize_text_field($_POST['codigo_postal']);
        update_field('codigo_postal_general', $codigo_postal, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['municipiociudad'])) {
        $municipiociudad = sanitize_text_field($_POST['municipiociudad']);
        update_field('municipiociudad_general', $municipiociudad, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['estado'])) {
        $estado = sanitize_text_field($_POST['estado']);
        update_field('estado_general', $estado, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['linkedin'])) {
        $linkedin = sanitize_text_field($_POST['linkedin']);
        update_field('linkedin_general', $linkedin, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['facebook'])) {
        $facebook = sanitize_text_field($_POST['facebook']);
        update_field('facebook_general', $facebook, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['instagram'])) {
        $instagram = sanitize_text_field($_POST['instagram']);
        update_field('instagram_general', $instagram, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['telefono_celular'])) {
        $telefono_celular = sanitize_text_field($_POST['telefono_celular']);
        update_field('telefono_celular_general', $telefono_celular, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['telefono_fijo'])) {
        $telefono_fijo = sanitize_text_field($_POST['telefono_fijo']);
        update_field('telefono_fijo_general', $telefono_fijo, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['correo2'])) {
        $correo2 = sanitize_text_field($_POST['correo2']);
        update_field('correo_general_2', $correo2, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['grado_escolaridad'])) {
        $grado_escolaridad = sanitize_text_field($_POST['grado_escolaridad']);
        update_field('grado_escolaridad_general', $grado_escolaridad, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['estado_civil'])) {
        $estado_civil = sanitize_text_field($_POST['estado_civil']);
        update_field('estado_civil_general', $estado_civil, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }

    if (isset($_POST['nacionalidad'])) {
        $nacionalidad = sanitize_text_field($_POST['nacionalidad']);
        update_field('nacionalidad_general', $nacionalidad, 'user_' . $user_id); // Guardar en ACF.

        // Establecer mensaje de éxito en la sesión.
        $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
    }
}

// Obtener el valor actual del campo ACF.
$nombre_actual = get_field('nombre_general', 'user_' . $user_id);
$apellido_paterno_actual = get_field('apellido_paterno_general', 'user_' . $user_id);
$apellido_materno_actual = get_field('apellido_materno_general', 'user_' . $user_id);
$correo_actual = get_field('correo_general', 'user_' . $user_id);
$fecha_de_nacimiento_actual = get_field('fecha_de_nacimiento_general', 'user_' . $user_id);
$calle_actual = get_field('calle_general', 'user_' . $user_id);
$numero_interiorexterior_actual = get_field('numero_interiorexterior_general', 'user_' . $user_id);
$colonia_actual = get_field('colonia_general', 'user_' . $user_id);
$codigo_postal_actual = get_field('codigo_postal_general', 'user_' . $user_id);
$municipiociudad_actual = get_field('municipiociudad_general', 'user_' . $user_id);
$estado_actual = get_field('estado_general', 'user_' . $user_id);
$linkedin_actual = get_field('linkedin_general', 'user_' . $user_id);
$facebook_actual = get_field('facebook_general', 'user_' . $user_id);
$instagram_actual = get_field('instagram_general', 'user_' . $user_id);
$telefono_celular_actual = get_field('telefono_celular_general', 'user_' . $user_id);
$telefono_fijo_actual = get_field('telefono_fijo_general', 'user_' . $user_id);
$correo2_actual = get_field('correo_general_2', 'user_' . $user_id);
$grado_escolaridad_actual = get_field('grado_escolaridad_general', 'user_' . $user_id);
$estado_civil_actual = get_field('estado_civil_general', 'user_' . $user_id);
$nacionalidad_actual = get_field('nacionalidad_general', 'user_' . $user_id);

// Verificar si la fecha existe y convertirla al formato `Y-m-d`.
if ($fecha_de_nacimiento_actual) {
    $fecha_de_nacimiento_actual = DateTime::createFromFormat('d/m/Y', $fecha_de_nacimiento_actual) ->format('Y-m-d');
} else {
    $fecha_de_nacimiento_actual = ''; // Si no hay fecha, dejar el campo vacío.
}
?>
