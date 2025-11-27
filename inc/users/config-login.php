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
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return 'La contraseña debe incluir al menos un carácter especial.';
    }

    return true;
}

// Manejar intentos fallidos de inicio de sesión y bloqueo de cuentas.
function handle_failed_login_attempts($username) {
    $transient_name = 'failed_login_' . hash('sha256', $username);
    $failed_attempts = get_transient($transient_name);

    if ($failed_attempts === false) {
        $failed_attempts = 0;
    }

    $failed_attempts++;

    if ($failed_attempts >= 5) {
        set_transient($transient_name, $failed_attempts, 15 * MINUTE_IN_SECONDS); // Bloquear por 15 minutos.
        return 'Demasiados intentos fallidos. Inténtalo de nuevo en 15 minutos.';
    } else {
        set_transient($transient_name, $failed_attempts, 15 * MINUTE_IN_SECONDS);
    }

    return null;
}


/**
 * Login por AJAX (sin recargar página)
 * Acción: custom_ajax_login
 */
function thd_custom_ajax_login() {
    // Verificar nonce
    if (
        ! isset($_POST['security']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['security'])), 'custom_login_nonce')
    ) {
        wp_send_json_error(array(
            'message' => 'Sesión no válida. Recarga la página e inténtalo de nuevo.',
        ));
    }

    // Email desde el formulario (campo name="log")
    $raw_email = isset($_POST['log']) ? trim(wp_unslash($_POST['log'])) : '';

    if ($raw_email === '') {
        wp_send_json_error(array(
            'message' => 'El correo electrónico es obligatorio.',
        ));
    }

    // Validación del formato de correo (mismo regex / mensaje que ya usas)
    if (!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email)) {
        wp_send_json_error(array(
            'message' => 'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", ".", "_" y "-". Otros caracteres no se pueden ingresar.',
        ));
    }

    // Contraseña
    $password = isset($_POST['pwd']) ? (string) wp_unslash($_POST['pwd']) : '';

    if ($password === '') {
        wp_send_json_error(array(
            'message' => 'La contraseña es obligatoria.',
        ));
    }

    // Recordar sesión
    $remember = ! empty($_POST['remember']);

    // SAML: se respeta el parámetro si viene en la petición (aunque aquí no se use directamente)
    $saml_sso = isset($_POST['saml_sso']) ? sanitize_text_field(wp_unslash($_POST['saml_sso'])) : '';

    // Manejo de intentos fallidos, misma función que ya usas en el template original
    $error_message = '';
    if (function_exists('handle_failed_login_attempts')) {
        $error_message = handle_failed_login_attempts($raw_email);
    }

    if (!empty($error_message)) {
        wp_send_json_error(array(
            'message' => $error_message,
        ));
    }

    // Buscar usuario por correo
    $user = get_user_by('email', $raw_email);
    if (! $user) {
        wp_send_json_error(array(
            'message' => 'El correo no está registrado.',
        ));
    }

    // Credenciales para wp_signon
    $credentials = array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember,
    );

       // Autenticar
    $auth_user = wp_signon($credentials, false);

    if (is_wp_error($auth_user)) {
        $error_code = $auth_user->get_error_code();
        $message = 'No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.';

        switch ($error_code) {
            case 'incorrect_password':
                // Contraseña no coincide con el usuario/correo
                $message = 'La contraseña no coincide con la cuenta ingresada.';
                break;

            case 'invalid_username':
            case 'invalid_email':
                // Usuario/correo no existe
                $message = 'El correo no está registrado.';
                break;

            default:
                // Otros errores de WP_Login (bloqueos, etc.)
                $message = 'No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.';
                break;
        }

        wp_send_json_error(array(
            'message' => $message,
        ));
    }


    // Redirección (usa redirect_to si viene desde el formulario, si no home)
    $redirect_to = isset($_POST['redirect_to'])
        ? esc_url_raw(wp_unslash($_POST['redirect_to']))
        : home_url('/');

    wp_send_json_success(array(
        'redirect' => $redirect_to,
    ));
}

// Hooks AJAX (para usuarios no logueados y logueados)
add_action('wp_ajax_nopriv_custom_ajax_login', 'thd_custom_ajax_login');
add_action('wp_ajax_custom_ajax_login', 'thd_custom_ajax_login');

/**
 * Registro por AJAX (sin recargar página)
 * Acción: custom_ajax_register
 */
function thd_custom_ajax_register() {

    // Verificar nonce
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

    // Email del formulario (reg_email)
    $raw_email = isset($_POST['reg_email']) ? trim( wp_unslash( $_POST['reg_email'] ) ) : '';

    if ( '' === $raw_email ) {
        wp_send_json_error( array(
            'message' => 'El correo electrónico es obligatorio.',
        ) );
    }

    // Misma validación estricta de correo que ya usas
    if ( ! preg_match( '/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email ) ) {
        wp_send_json_error( array(
            'message' => 'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", "." "_" y "-". Otros caracteres no se pueden ingresar.',
        ) );
    }

    // Contraseña
    $password = isset($_POST['reg_password']) ? (string) wp_unslash( $_POST['reg_password'] ) : '';

    if ( '' === $password ) {
        wp_send_json_error( array(
            'message' => 'La contraseña es obligatoria.',
        ) );
    }

    // Validar seguridad de la contraseña si existe la función
    if ( function_exists( 'validate_password_security' ) ) {
        $password_validation = validate_password_security( $password );
        if ( true !== $password_validation ) {
            wp_send_json_error( array(
                'message' => $password_validation, // mensaje que ya devuelve tu función
            ) );
        }
    }

    // Verificar si el correo ya existe
    if ( email_exists( $raw_email ) ) {
        wp_send_json_error( array(
            'message' => 'Este correo ya está registrado.',
        ) );
    }

    // Crear usuario
    $user_id = wp_create_user( $raw_email, $password, $raw_email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( array(
            'message' => 'Hubo un error al registrar el usuario. Inténtalo más tarde.',
        ) );
    }

    // Loguear al usuario recién creado
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    // Redirección (igual que antes: a home por defecto)
    $redirect_to = home_url( '/' );

    wp_send_json_success( array(
        'redirect'         => $redirect_to,
        // para replicar tu comportamiento actual:
        'registro_exitoso' => true,
    ) );
}

add_action( 'wp_ajax_nopriv_custom_ajax_register', 'thd_custom_ajax_register' );
add_action( 'wp_ajax_custom_ajax_register', 'thd_custom_ajax_register' );
