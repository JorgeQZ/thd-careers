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

    // Puedes integrar aquí un servicio externo para verificar contraseñas comprometidas, como Have I Been Pwned.

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