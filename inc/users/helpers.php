<?php
/**
 * ================================
 * PERFIL DE USUARIO – HELPERS
 * ================================
 */

function thd_is_profile_complete($user_id) {

    if (!$user_id) {
        return false;
    }

    /** =========================
     * 1. Correo (FUENTE REAL)
     * ========================= */
    $user = get_userdata($user_id);
    if (!$user || empty($user->user_email)) {
        return false;
    }

    /** =========================
     * 2. Campos ACF obligatorios
     * ========================= */
    $required_acf_fields = [
        'nombre_general',
        'apellido_paterno_general',
        'apellido_materno_general',
        'correo_general',
        'fecha_de_nacimiento_general',
        'calle_general',
        'colonia_general',
        'codigo_postal_general',
        'municipiociudad_general',
        'estado_general',
        'grado_escolaridad_general',
        'estado_civil_general',
        'nacionalidad_general',
        'pr1',
    ];

    foreach ($required_acf_fields as $field) {
        $value = get_field($field, 'user_' . $user_id);

        if ($value === null || $value === false) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && empty($value)) {
            return false;
        }
    }

    $gcs_url_name = get_user_meta($user_id, 'gcs_url_name', true);
    if(!$gcs_url_name){
        return false;
    }

    return true;
}

function thd_get_profile_complete($user_id) {

    if (!$user_id) {
        return false;
    }

    $is_complete = thd_is_profile_complete($user_id);

    // Flag solo como cache informativo
    update_user_meta(
        $user_id,
        'thd_profile_complete',
        $is_complete ? 1 : 0
    );

    return $is_complete;
}

function thd_update_profile_complete($user_id) {

    if (!$user_id) {
        return false;
    }

    $is_complete = thd_is_profile_complete($user_id);

    update_user_meta(
        $user_id,
        'thd_profile_complete',
        $is_complete ? 1 : 0
    );

    return $is_complete;
}

function thd_get_profile_missing_fields($user_id) {

    if (!$user_id) {
        return [];
    }

    $missing = [];

    // Usuario
    $user = get_userdata($user_id);
    if (!$user || empty($user->user_email)) {
        $missing[] = 'correo';
    }

    // Campos ACF requeridos (mapa campo => input name)
    $required_acf_fields = [
        'nombre_general'              => 'nombre',
        'apellido_paterno_general'    => 'apellido_paterno',
        'apellido_materno_general'    => 'apellido_materno',
        'correo_general'              => 'correo',
        'fecha_de_nacimiento_general' => 'fecha_de_nacimiento',
        'calle_general'               => 'calle',
        'colonia_general'             => 'colonia',
        'codigo_postal_general'       => 'codigo_postal',
        'municipiociudad_general'     => 'municipiociudad',
        'estado_general'              => 'estado',
        'grado_escolaridad_general'   => 'grado_escolaridad',
        'estado_civil_general'        => 'estado_civil',
        'nacionalidad_general'        => 'nacionalidad',
        'pr1'                         => 'pr1',
    ];

    foreach ($required_acf_fields as $acf_field => $input_name) {
        $value = get_field($acf_field, 'user_' . $user_id);

        if (
            $value === null ||
            $value === false ||
            (is_string($value) && trim($value) === '') ||
            (is_array($value) && empty($value))
        ) {
            $missing[] = $input_name;
        }
    }

    // CV obligatorio
    if (!get_user_meta($user_id, 'gcs_url_name', true)) {
        $missing[] = 'cv';
    }

    return array_unique($missing);
}