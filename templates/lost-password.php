<?php
/**
 * Template Name: Lost Password
 */



// Evitar el acceso directo al archivo.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Inicializar mensajes.
$success_message = '';
$error_message = '';

// Procesar el formulario de recuperación si se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_recovery'])) {
    $user_email = sanitize_email($_POST['user_email']);

    if (!is_email($user_email)) {
        $error_message = 'Por favor, ingresa un correo electrónico válido.';
    } else {
        $user = get_user_by('email', $user_email);

        if (!$user) {
            $error_message = 'No se encontró ninguna cuenta con ese correo electrónico.';
        } else {
            $reset_result = retrieve_password($user_email);

            if (is_wp_error($reset_result)) {
                $error_message = 'Hubo un error al procesar tu solicitud. Inténtalo de nuevo más tarde.';
            } else {
                $success_message = 'Revisa tu correo electrónico para las instrucciones de recuperación.';
            }
        }
    }
}
get_header( );

?>


<div class="password-recovery-wrapper" style="max-width: 400px; margin: 50px auto;">
    <h2>Recuperar Contraseña</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="error-message" style="color: red;">
            <?php echo $error_message; ?>
        </div>
    <?php elseif (!empty($success_message)) : ?>
        <div class="success-message" style="color: green;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <p>
            <label for="user_email">Correo Electrónico</label>
            <input type="email" name="user_email" id="user_email" required >
        </p>
        <p>
            <button type="submit" name="password_recovery">Enviar Instrucciones</button>
        </p>
    </form>
</div>

<?php get_footer( ); ?>