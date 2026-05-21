<?php
/**
 * ================================
 * PERFIL DE USUARIO – HELPERS
 * ================================
 */

/**
 * Obtiene la URL pública base del micrositio de carreras.
 *
 * Orden de resolución:
 * 1) vip_get_env_var()
 * 2) \Automattic\VIP\Environment::get_var()
 * 3) getenv()
 * 4) home_url('/') como fallback seguro.
 *
 * @return string URL base sin slash final.
 */
function thd_get_public_site_url() {
    $public_site_url = '';

    if ( function_exists( 'vip_get_env_var' ) ) {
        $public_site_url = (string) vip_get_env_var( 'CARRERAS_PUBLIC_SITE_URL', '' );
    }

    if (
        '' === $public_site_url &&
        class_exists( '\Automattic\VIP\Environment' ) &&
        method_exists( '\Automattic\VIP\Environment', 'get_var' )
    ) {
        $public_site_url = (string) \Automattic\VIP\Environment::get_var( 'CARRERAS_PUBLIC_SITE_URL' );
    }

    if ( '' === $public_site_url ) {
        $public_site_url = (string) getenv( 'CARRERAS_PUBLIC_SITE_URL' );
    }

    $public_site_url = trim( $public_site_url );

    if ( '' !== $public_site_url && false !== wp_http_validate_url( $public_site_url ) ) {
        return untrailingslashit( $public_site_url );
    }

    return untrailingslashit( home_url( '/' ) );
}

/**
 * Obtiene el nombre público del micrositio para correos y textos visibles.
 *
 * Orden de resolución:
 * 1) vip_get_env_var()
 * 2) \Automattic\VIP\Environment::get_var()
 * 3) getenv()
 * 4) get_bloginfo( 'name' ) como fallback.
 *
 * @return string
 */
function thd_get_public_site_name() {
    $public_site_name = '';

    if ( function_exists( 'vip_get_env_var' ) ) {
        $public_site_name = (string) vip_get_env_var( 'CARRERAS_PUBLIC_SITE_NAME', '' );
    }

    if (
        '' === $public_site_name &&
        class_exists( '\Automattic\VIP\Environment' ) &&
        method_exists( '\Automattic\VIP\Environment', 'get_var' )
    ) {
        $public_site_name = (string) \Automattic\VIP\Environment::get_var( 'CARRERAS_PUBLIC_SITE_NAME' );
    }

    if ( '' === $public_site_name ) {
        $public_site_name = (string) getenv( 'CARRERAS_PUBLIC_SITE_NAME' );
    }

    $public_site_name = wp_strip_all_tags( trim( $public_site_name ) );

    if ( '' !== $public_site_name ) {
        return $public_site_name;
    }

    return wp_strip_all_tags( (string) get_bloginfo( 'name' ) );
}

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

function thd_set_profile_redirect_pending( $user_id ) {
    if ( ! $user_id ) {
        return;
    }
    update_user_meta( $user_id, 'thd_profile_redirect_pending', 1 );
}

function thd_clear_profile_redirect_pending( $user_id ) {
    if ( ! $user_id ) {
        return;
    }
    delete_user_meta( $user_id, 'thd_profile_redirect_pending' );
}

/**
 * Marca redirección pendiente justo después del login si el perfil sigue incompleto.
 */
function thd_mark_incomplete_profile_redirect_pending( $user_login, $user ) {

    if ( ! ( $user instanceof WP_User ) || empty( $user->ID ) ) {
        return;
    }

    $user_id = (int) $user->ID;
    $is_complete = function_exists( 'thd_is_profile_complete' )
        ? thd_is_profile_complete( $user_id )
        : true;

    if ( $is_complete ) {
        thd_clear_profile_redirect_pending( $user_id );
        return;
    }

    thd_set_profile_redirect_pending( $user_id );
}
add_action( 'wp_login', 'thd_mark_incomplete_profile_redirect_pending', 20, 2 );

/**
 * Fuerza Mi Perfil en post-login cuando el perfil está incompleto.
 * Actúa como respaldo cuando el flujo de login respeta login_redirect.
 */
function thd_login_redirect_incomplete_profile( $redirect_to, $requested_redirect_to, $user ) {

    if ( ! ( $user instanceof WP_User ) || empty( $user->ID ) ) {
        return $redirect_to;
    }

    $user_id = (int) $user->ID;
    $is_complete = function_exists( 'thd_is_profile_complete' )
        ? thd_is_profile_complete( $user_id )
        : true;

    if ( $is_complete ) {
        return $redirect_to;
    }

    thd_set_profile_redirect_pending( $user_id );
    return home_url( '/mi-perfil/' );
}
add_filter( 'login_redirect', 'thd_login_redirect_incomplete_profile', 999, 3 );

/**
 * Ejecuta redirección one-shot a Mi Perfil solo cuando hay bandera pendiente.
 */
function thd_enforce_complete_profile_redirect() {

    if ( ! is_user_logged_in() ) {
        return;
    }

    // Skip non-frontend and non-interactive contexts.
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
        return;
    }

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return;
    }

    $action = isset( $_REQUEST['action'] )
        ? sanitize_key( wp_unslash( $_REQUEST['action'] ) )
        : '';

    // Keep auth edge flows untouched.
    if ( in_array( $action, array( 'logout', 'lostpassword', 'rp', 'resetpass' ), true ) ) {
        return;
    }

    // Keep password reset page untouched.
    if ( is_page_template( 'templates/lost-password.php' ) ) {
        return;
    }

    $user_id = get_current_user_id();

    if ( ! $user_id ) {
        return;
    }

    $is_complete = function_exists( 'thd_is_profile_complete' )
        ? thd_is_profile_complete( $user_id )
        : true;

    // Respaldo para social callbacks: primera request frontend de sesión nueva.
    $current_session_token = function_exists( 'wp_get_session_token' ) ? (string) wp_get_session_token() : '';
    $known_session_token   = (string) get_user_meta( $user_id, 'thd_profile_redirect_session_token', true );
    $is_new_session        = ( $current_session_token !== '' && ! hash_equals( $known_session_token, $current_session_token ) );

    if ( $is_new_session ) {
        update_user_meta( $user_id, 'thd_profile_redirect_session_token', $current_session_token );

        if ( ! $is_complete ) {
            thd_set_profile_redirect_pending( $user_id );
        } else {
            thd_clear_profile_redirect_pending( $user_id );
        }
    }

    $pending_redirect = (int) get_user_meta( $user_id, 'thd_profile_redirect_pending', true ) === 1;
    if ( ! $pending_redirect ) {
        return;
    }

    $current_path = isset( $_SERVER['REQUEST_URI'] )
        ? wp_parse_url( home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH )
        : '';

    $profile_path = wp_parse_url( home_url( '/mi-perfil/' ), PHP_URL_PATH );

    if (
        is_string( $current_path ) &&
        is_string( $profile_path ) &&
        untrailingslashit( $current_path ) === untrailingslashit( $profile_path )
    ) {
        // Ya llegó a Mi Perfil: consumimos la bandera para no encerrar al usuario.
        thd_clear_profile_redirect_pending( $user_id );
        return;
    }

    if ( $is_complete ) {
        thd_clear_profile_redirect_pending( $user_id );
        return;
    }

    thd_clear_profile_redirect_pending( $user_id );
    wp_safe_redirect( home_url( '/mi-perfil/' ) );
    exit;
}
add_action( 'template_redirect', 'thd_enforce_complete_profile_redirect', 1 );
