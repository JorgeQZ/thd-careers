<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('wp_handle_upload')) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
}

$user_id = get_current_user_id();
$errores = [];

// --- FUNCIÓN DE VALIDACIÓN DE CARACTERES ---
if (!function_exists('es_valido')) {
    function es_valido($valor) {
        return preg_match('/^[\p{L}0-9 ._\-áéíóúÁÉÍÓÚñÑ]+$/u', $valor);
    }
}

// --- CAMPOS A VALIDAR (caracteres permitidos) ---
$campos_validados = [
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'correo',
    'correo2',
    'calle',
    'colonia',
    'municipiociudad',
    'estado',
    'codigo_postal',
    'telefono_celular',
    'telefono_fijo',
    'linkedin',
    'facebook',
    'instagram',
    'nacionalidad',
    'pais',
    'numero_interiorexterior',
    'grado_escolaridad',
    'estado_civil',
    'pregunta1',
    'pregunta2',
    'pr1',
    'pr2',
    'otro_detalle',
];

// --- VALIDAR CAMPOS ---
foreach ($campos_validados as $campo) {
    if (isset($_POST[$campo]) && !es_valido($_POST[$campo])) {
        $errores[] = $campo;
    }
}

// --- GUARDADO DE CAMPOS SI NO HAY ERRORES ---
if (is_user_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST' && empty($errores)) {
    $campos_acf = [
        'nombre_general' => 'nombre',
        'apellido_paterno_general' => 'apellido_paterno',
        'apellido_materno_general' => 'apellido_materno',
        'correo_general' => 'correo',
        'correo_general_2' => 'correo2',
        'fecha_de_nacimiento_general' => 'fecha_de_nacimiento',
        'calle_general' => 'calle',
        'numero_interiorexterior_general' => 'numero_interiorexterior',
        'colonia_general' => 'colonia',
        'codigo_postal_general' => 'codigo_postal',
        'municipiociudad_general' => 'municipiociudad',
        'estado_general' => 'estado',
        'linkedin_general' => 'linkedin',
        'facebook_general' => 'facebook',
        'instagram_general' => 'instagram',
        'telefono_celular_general' => 'telefono_celular',
        'telefono_fijo_general' => 'telefono_fijo',
        'grado_escolaridad_general' => 'grado_escolaridad',
        'estado_civil_general' => 'estado_civil',
        'nacionalidad_general' => 'nacionalidad',
        'pais_general' => 'pais',
        'ar1' => 'pregunta1',
        'ar2' => 'pregunta2',
        'otroar2' => 'otro_detalle',
        'pr1' => 'pr1',
        'pr2' => 'pr2',
    ];

    foreach ($campos_acf as $acf_key => $post_key) {
        if (isset($_POST[$post_key])) {
            $valor = is_array($_POST[$post_key])
                ? array_map('sanitize_text_field', $_POST[$post_key])
                : sanitize_text_field($_POST[$post_key]);
            update_field($acf_key, $valor, 'user_' . $user_id);
        }
    }

    $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente.';
}

// --- OBTENER VALORES ACTUALES PARA RELLENAR CAMPOS ---
if (!function_exists('obtener_valor_actual')) {
    function obtener_valor_actual($key, $user_id) {
        return get_field($key, 'user_' . $user_id);
    }

    $nombre_actual = obtener_valor_actual('nombre_general', $user_id);
    $apellido_paterno_actual = obtener_valor_actual('apellido_paterno_general', $user_id);
    $apellido_materno_actual = obtener_valor_actual('apellido_materno_general', $user_id);
    $correo_actual = obtener_valor_actual('correo_general', $user_id);
    $correo2_actual = obtener_valor_actual('correo_general_2', $user_id);
    $fecha_de_nacimiento_actual = obtener_valor_actual('fecha_de_nacimiento_general', $user_id);
    $calle_actual = obtener_valor_actual('calle_general', $user_id);
    $numero_interiorexterior_actual = obtener_valor_actual('numero_interiorexterior_general', $user_id);
    $colonia_actual = obtener_valor_actual('colonia_general', $user_id);
    $codigo_postal_actual = obtener_valor_actual('codigo_postal_general', $user_id);
    $municipiociudad_actual = obtener_valor_actual('municipiociudad_general', $user_id);
    $estado_actual = obtener_valor_actual('estado_general', $user_id);
    $telefono_celular_actual = obtener_valor_actual('telefono_celular_general', $user_id);
    $telefono_fijo_actual = obtener_valor_actual('telefono_fijo_general', $user_id);
    $linkedin_actual = obtener_valor_actual('linkedin_general', $user_id);
    $facebook_actual = obtener_valor_actual('facebook_general', $user_id);
    $instagram_actual = obtener_valor_actual('instagram_general', $user_id);
    $grado_escolaridad_actual = obtener_valor_actual('grado_escolaridad_general', $user_id);
    $estado_civil_actual = obtener_valor_actual('estado_civil_general', $user_id);
    $nacionalidad_actual = obtener_valor_actual('nacionalidad_general', $user_id);
    $pais_actual = obtener_valor_actual('pais_general', $user_id);
    $pregunta1_actual = obtener_valor_actual('ar1', $user_id);
    $pregunta2_actual = obtener_valor_actual('ar2', $user_id);
    $otroar2_actual = obtener_valor_actual('otroar2', $user_id);
    $pr1_actual = obtener_valor_actual('pr1', $user_id);
    $pr2_actual = obtener_valor_actual('pr2', $user_id);

    // Formato para fecha
    if ($fecha_de_nacimiento_actual) {
        $fecha_de_nacimiento_actual = DateTime::createFromFormat('d/m/Y', $fecha_de_nacimiento_actual)?->format('Y-m-d') ?: '';
    } else {
        $fecha_de_nacimiento_actual = '';
    }
}
?>