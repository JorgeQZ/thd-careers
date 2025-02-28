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
    $email = sanitize_email($_POST['email']);
    $password = !empty($_POST['password']) ? trim($_POST['password']) : null;
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    $error_message = handle_failed_login_attempts($email);

    if (!$error_message && $password) {
        $user = get_user_by('email', $email);
        if ($user) {
            $credentials = [
                'user_login'    => $user->user_login,
                'user_password' => $password,
                'remember'      => $remember,
            ];
            $auth_user = wp_signon($credentials, false);
            if (!is_wp_error($auth_user)) {
                wp_redirect(home_url());
                exit;
            } else {
                $error_message = 'Credenciales incorrectas. Inténtalo de nuevo.';
            }
        } else {
            $error_message = 'El correo no está registrado.';
        }
    }
}

// Procesar el formulario de registro si se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
    $email = sanitize_email($_POST['reg_email']);
    $password = !empty($_POST['reg_password']) ? trim($_POST['reg_password']) : null;

    $password_validation = validate_password_security($password);
    if ($password_validation !== true) {
        $register_error_message = $password_validation;
    } else {
        if (email_exists($email)) {
            $register_error_message = 'Este correo ya está registrado.';
        } else {
            $user_id = wp_create_user($email, $password, $email);
            if (!is_wp_error($user_id)) {
                // Iniciar sesión automáticamente
                $user = get_user_by('ID', $user_id);
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                // wp_redirect(home_url());
                echo "<script>
                    localStorage.setItem('registro_exitoso', 'true');
                    window.location.href = '".home_url()."';
                </script>";
                exit;

            } else {
                $register_error_message = 'Hubo un error al registrar el usuario. Inténtalo más tarde.';
            }
        }
    }
}

get_header();
?>

<div class="custom-login-wrapper" style="max-width: 500px; margin: 50px auto;">
    <h2>Iniciar Sesión</h2>
    <form method="post" action="" class="login-form">
        <p>
            <label for="username">Correo electrónico</label>
            <input type="text" name="email" id="email" required>
        </p>
        <p>
            <label for="password">Contraseña</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" required style="width: 100%; padding: 8px; padding-right: 40px; margin-top: 5px; box-sizing: border-box;">
                <button type="button" class="toggle-password" data-target="password" style="position: absolute; right: 0; top: 26%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding-right: 10px;">
                    <img src="<?php echo get_template_directory_uri().'/img/pwd-closed-eye.png'; ?>" class="password-icon" style="width: 20px; height: 20px;" />
                </button>
            </div>
        </p>
        <p style="display: flex; justify-content: space-between; align-items: center;">
            <label>
                <input type="checkbox" name="remember"> Recuérdame
            </label>
            <a class="recuperar" href="<?php echo get_permalink(get_page_by_path('recuperar-contrasena')); ?>">¿Olvidaste tu contraseña?</a>
        </p>
        <?php if (!empty($error_message)) : ?>
            <div class="error-message" style="color: red;">
                <?php if (!empty($error_message)) : ?>
                    <div class="error-message" style="color: red;">
                        <?php echo sanitize_text_field($error_message); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <br>
        <p>
            <button type="submit" name="custom_login">Iniciar Sesión</button>
        </p>
        <br>

    </form>

    <hr>
    <h2>¿No tienes cuenta? <span>Regístrate</span></h2>
    <form method="post" action="">
        <p>
            <label for="reg_email">Correo electrónico</label>
            <input type="email" name="reg_email" id="reg_email" required style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box">
        </p>
        <p style="margin-bottom: 0;">
            <label for="reg_password">Contraseña</label>
            <div style="position: relative;">
                <input type="password" name="reg_password" id="reg_password" required style="width: 100%; padding: 8px; padding-right: 40px; margin-top: 5px; box-sizing: border-box;">
                <button type="button" class="toggle-password" data-target="reg_password" style="position: absolute; right: 0; top: 26%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding-right: 10px;">
                    <img src="<?php echo get_template_directory_uri().'/img/pwd-closed-eye.png'; ?>" class="password-icon" style="width: 20px; height: 20px;">
                </button>
            </div>
        </p>
        <?php if (!empty($register_error_message)) : ?>
            <div class="error-message" style="color: red;">
                <?php echo esc_html($register_error_message); ?>
            </div>
        <?php elseif (!empty($register_success_message)) : ?>
            <div class="success-message" style="color: green;">
                <?php echo esc_html($register_success_message); ?>
            </div>
        <?php endif; ?>
        <br>
        <p style="margin-top: 0px">
            <button type="submit" name="custom_register">Registrarse</button>
        </p>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let hide_icon = "<?php echo get_template_directory_uri().'/img/pwd-open-eye.png'; ?>";
    let show_icon = "<?php echo get_template_directory_uri().'/img/pwd-closed-eye.png'; ?>";

    document.querySelectorAll(".toggle-password").forEach(button => {
        button.addEventListener("click", function() {
            let target = document.getElementById(this.dataset.target);
            let icon = this.querySelector(".password-icon");

            if (target.type === "password") {
                target.type = "text";
                icon.src = hide_icon;
            } else {
                target.type = "password";
                icon.src = show_icon;
            }
        });
    });
});
</script>


<?php
get_footer();
?>
