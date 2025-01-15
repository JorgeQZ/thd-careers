<?php
/*
Template Name: Login
*/

// Evitar el acceso directo al archivo.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Desregistrar los estilos del login predeterminado de WordPress.
function custom_login_dequeue_styles() {
    wp_dequeue_style('login');
}
add_action('wp_enqueue_scripts', 'custom_login_dequeue_styles', 100);


// Cerrar sesión automáticamente si el usuario está logueado al visitar esta página.
if (is_user_logged_in()) {
    wp_logout();
    wp_redirect(get_permalink()); // Redirigir a esta misma página para evitar el acceso previo.
    exit;
}

// Procesar el formulario de inicio de sesión si se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_login'])) {
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    $error_message = handle_failed_login_attempts($username);

    if (!$error_message) {
        $credentials = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => $remember,
        ];

        $user = wp_signon($credentials, false);

        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
        } else {
            wp_redirect(home_url()); // Redirigir al inicio o a una página personalizada.
            exit;
        }
    }
}

// Procesar el formulario de registro si se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
    $username = sanitize_text_field($_POST['reg_username']);
    $email = sanitize_email($_POST['reg_email']);
    $password = sanitize_text_field($_POST['reg_password']);

    $password_validation = validate_password_security($password);

    if ($password_validation !== true) {
        $register_error_message = $password_validation;
    } else {
        $userdata = [
            'user_login'    => $username,
            'user_email'    => $email,
            'user_pass'     => $password,
        ];

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            $register_error_message = $user_id->get_error_message();
        } else {
            $register_success_message = 'Registro exitoso. Ahora puedes iniciar sesión.';
        }
    }
}

get_header();
?>

<div class="custom-login-wrapper" style="max-width: 400px; margin: 50px auto;">
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($error_message)) : ?>
        <div class="error-message" style="color: red;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <p>
            <label for="username">Nombre de usuario</label>
            <input type="text" name="username" id="username" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        <p>
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        <p>
            <label>
                <input type="checkbox" name="remember"> Recuérdame
            </label>
        </p>
        <p>
            <button type="submit" name="custom_login">Iniciar Sesión</button>
        </p>
    </form>

    <hr>
    <h2>¿No tienes cuenta? <span>Regístrate</span></h2>
    <?php if (!empty($register_error_message)) : ?>
        <div class="error-message" style="color: red;">
            <?php echo $register_error_message; ?>
        </div>
    <?php elseif (!empty($register_success_message)) : ?>
        <div class="success-message" style="color: green;">
            <?php echo $register_success_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <p>
            <label for="reg_username">Nombre de usuario</label>
            <input type="text" name="reg_username" id="reg_username" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        <p>
            <label for="reg_email">Correo Electrónico</label>
            <input type="email" name="reg_email" id="reg_email" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        <p>
            <label for="reg_password">Contraseña</label>
            <input type="password" name="reg_password" id="reg_password" required style="width: 100%; padding: 8px; margin-top: 5px;">
        </p>
        <p>
            <button type="submit" name="custom_register">Registrarse</button>
        </p>
    </form>
</div>

<?php
get_footer();
?>
