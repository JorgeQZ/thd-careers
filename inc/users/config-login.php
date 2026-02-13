<?php
// Función para validar la seguridad de las contraseñas.
function validate_password_security($password) {
    if (strlen($password) < 12) {
        return 'La contraseña debe tener al menos 12 caracteres.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'La contraseña debe incluir al menos una letra mayúscula.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'La contraseña debe incluir al menos una letra minúscula.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'La contraseña debe incluir al menos un número.';
    }

    return true;
}

// Manejar intentos fallidos de inicio de sesión y bloqueo de cuentas.
function handle_failed_login_attempts($username) {
    $transient_name   = 'failed_login_' . hash('sha256', $username);
    $failed_attempts  = get_transient($transient_name);

    if ($failed_attempts === false) {
        $failed_attempts = 0;
    }

    $failed_attempts++;

    if ($failed_attempts >= 5) {
        // Bloquear por 15 minutos.
        set_transient($transient_name, $failed_attempts, 15 * MINUTE_IN_SECONDS);
        return 'Demasiados intentos fallidos. Inténtalo de nuevo en 15 minutos.';
    } else {
        set_transient($transient_name, $failed_attempts, 15 * MINUTE_IN_SECONDS);
    }

    return null;
}

// ======================================
// REGISTRO (AJAX)
// ======================================
function thd_custom_ajax_register() {

    // Verificar nonce.
    if (
        ! isset($_POST['security']) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['security'] ) ),
            'custom_register_nonce'
        )
    ) {
        wp_send_json_error( array(
            'message' => 'Sesión no válida. Recarga la página e inténtalo de nuevo.',
        ) );
    }

    // Email del formulario (reg_email).
    $raw_email = isset($_POST['reg_email']) ? trim( wp_unslash( $_POST['reg_email'] ) ) : '';

    if ( '' === $raw_email ) {
        wp_send_json_error( array(
            'message' => 'El correo electrónico es obligatorio.',
        ) );
    }

    // Validación estricta de correo.
    if ( ! preg_match( '/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email ) ) {
        wp_send_json_error( array(
            'message' => 'El correo solo puede contener letras, números y los siguientes símbolos: "@", ".", "_" y "-". Otros caracteres no se pueden ingresar.',
        ) );
    }

    // Contraseña.
    $password = isset($_POST['reg_password']) ? (string) wp_unslash( $_POST['reg_password'] ) : '';

    if ( '' === $password ) {
        wp_send_json_error( array(
            'message' => 'La contraseña es obligatoria.',
        ) );
    }

    // Validar seguridad de la contraseña.
    if ( function_exists( 'validate_password_security' ) ) {
        $password_validation = validate_password_security( $password );
        if ( true !== $password_validation ) {
            wp_send_json_error( array(
                'message' => $password_validation,
            ) );
        }
    }

    // Verificar si el correo ya existe.
    if ( email_exists( $raw_email ) ) {
        wp_send_json_error( array(
            'message' => 'Este correo ya está registrado.',
        ) );
    }

    // Crear usuario.
    $user_id = wp_create_user( $raw_email, $password, $raw_email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( array(
            'message' => 'Hubo un error al registrar el usuario. Inténtalo más tarde.',
        ) );
    }

    // Loguear al usuario recién creado.
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    // Redirección después del registro (home por defecto).
    $redirect_to = home_url( '/' );

    wp_send_json_success( array(
        'redirect'         => $redirect_to,
        'registro_exitoso' => true,
    ) );
}

add_action( 'wp_ajax_nopriv_custom_ajax_register', 'thd_custom_ajax_register' );
add_action( 'wp_ajax_custom_ajax_register', 'thd_custom_ajax_register' );

// ======================================
// LOGIN (POST NORMAL, SIN AJAX)
// ======================================
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login']) ) {

    // =========================
    // 1. Correo
    // =========================
    $raw_email = isset($_POST['log'])
        ? trim( wp_unslash( $_POST['log'] ) )
        : '';

    if ( $raw_email === '' ) {
        $login_error = 'El correo electrónico es obligatorio.';
        return;
    }

    // Regex ESTRICTO (mismo criterio que antes).
    if ( ! preg_match( '/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email ) ) {
        $login_error = 'El correo solo puede contener letras, números y los caracteres especiales permitidos: "@", ".", "_" y "-".';
        return;
    }

    // =========================
    // 2. Contraseña
    // =========================
    $password = isset($_POST['pwd'])
        ? (string) wp_unslash( $_POST['pwd'] )
        : '';

    if ( $password === '' ) {
        $login_error = 'La contraseña es obligatoria.';
        return;
    }

    // =========================
    // 3. Usuario por correo
    // =========================
    $user = get_user_by( 'email', $raw_email );

    if ( ! $user ) {
        $login_error = 'El correo no está registrado.';
        return;
    }

    // =========================
    // 4. Autenticación
    // =========================
    $creds = array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => true,
    );

    $auth_user = wp_signon( $creds, false );

    if ( is_wp_error( $auth_user ) ) {

        $code         = $auth_user->get_error_code();
        $login_error  = 'No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.';

        // Manejo de intentos fallidos (bloqueos).
        if ( function_exists( 'handle_failed_login_attempts' ) ) {
            $lock_message = handle_failed_login_attempts( $raw_email );
            if ( $lock_message ) {
                $login_error = $lock_message;
                return;
            }
        }

        switch ( $code ) {
            case 'incorrect_password':
                $login_error = 'La contraseña no coincide con la cuenta ingresada.';
                break;
            default:
                // Otros errores: mantenemos el mensaje genérico.
                break;
        }

        return;
    }

    // =========================
    // 5. Redirección post-login
    // =========================
    $user_id = $auth_user->ID;

    // Helper existente para perfil completo.
    $profile_complete = function_exists( 'thd_is_profile_complete' )
        ? thd_is_profile_complete( $user_id )
        : true;

    // redirect_to: viene del formulario (login normal o popup).
    $redirect_to = isset($_POST['redirect_to'])
        ? wp_validate_redirect( wp_unslash( $_POST['redirect_to'] ), home_url( '/' ) )
        : home_url( '/' );

    // Si el perfil NO está completo, forzamos Mi Perfil.
    if ( ! $profile_complete ) {
        wp_safe_redirect( home_url( '/mi-perfil' ) );
        exit;
    }

    // Perfil completo → redirección normal (home o vacante, etc.).
    wp_safe_redirect( $redirect_to );
    exit;
}

/***
 * Configuración nueva de reset de password a useres generales
 */

add_filter('retrieve_password_message', function ($message, $key, $user_login, $user_data) {

    $custom_url = home_url(
        '/recuperar-contrasena/?key=' . $key . '&login=' . rawurlencode($user_login)
    );

    // Reemplaza cualquier URL que contenga wp-login.php?... por la personalizada
    $message = preg_replace(
        '/https?:\/\/[^\s]+wp-login\.php[^\s]*/',
        $custom_url,
        $message
    );

    return $message;

}, 10, 4);
