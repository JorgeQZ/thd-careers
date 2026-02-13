<?php
/**
 * Template Name: Lost Password
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$key   = $_GET['key'] ?? '';
$login = $_GET['login'] ?? '';
$is_reset_flow = (!empty($key) && !empty($login));

$success_message = '';
$error_message   = '';

/*
|--------------------------------------------------------------------------
| SOLICITAR CORREO
|--------------------------------------------------------------------------
*/
if (!$is_reset_flow && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_recovery'])) {

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
                $error_message = 'Hubo un error al procesar tu solicitud.';
            } else {
                $success_message = 'Revisa tu correo electrónico para las instrucciones.';
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| NUEVA CONTRASEÑA
|--------------------------------------------------------------------------
*/
if ($is_reset_flow) {

    $user = check_password_reset_key($key, $login);

    if (is_wp_error($user)) {
        $error_message = 'El enlace es inválido o ha expirado.';
        $is_reset_flow = false;
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $pass1 = $_POST['pass1'] ?? '';
            $pass2 = $_POST['pass2'] ?? '';

            if (empty($pass1) || empty($pass2)) {
                $error_message = 'Debes completar ambos campos.';
            } elseif ($pass1 !== $pass2) {
                $error_message = 'Las contraseñas no coinciden.';
            } else {

                // 🔐 Usamos EXACTAMENTE la misma función del registro
                if (function_exists('validate_password_security')) {

                    $validation = validate_password_security($pass1);

                    if ($validation !== true) {
                        $error_message = $validation;
                    } else {

                        reset_password($user, $pass1);

                        wp_set_auth_cookie($user->ID, true);

                        wp_safe_redirect(home_url('/mi-perfil'));
                        exit;
                    }

                } else {

                    // Fallback por seguridad (no debería pasar)
                    reset_password($user, $pass1);
                    wp_set_auth_cookie($user->ID, true);
                    wp_safe_redirect(home_url('/mi-perfil'));
                    exit;
                }
            }
        }
    }
}
?>

<div class="password-recovery-wrapper" style="max-width: 400px; margin: 50px auto;">
    <h2>Recuperar Contraseña</h2>

    <?php if (!empty($error_message)) : ?>
        <div class="error-message">
            <?php echo esc_html($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_message)) : ?>
        <div class="success-message">
            <?php echo esc_html($success_message); ?>
        </div>
    <?php endif; ?>


    <?php if (!$is_reset_flow) : ?>

        <!-- FORM ORIGINAL (NO TOCADO) -->

        <form method="post">
            <p>
                <label for="user_email">Correo electrónico</label>
                <input type="email" name="user_email" id="user_email" required>
            </p>
            <p>
                <button type="submit" name="password_recovery">Enviar Instrucciones</button>
            </p>
        </form>

    <?php else : ?>

        <!-- NUEVA CONTRASEÑA CON MISMA ESTRUCTURA -->
         <form method="post" class="login-form">

            <div class="custom-login-wrapper">

                <p>
                    <label for="reg_password">
                        Contraseña
                    </label>

                    <input
                        type="password"
                        name="pass1"
                        id="reg_password"
                        required
                    >
                </p>

                <ul class="password-rules">
                    <li class="rule" data-rule="length">
                        Mínimo 12 caracteres
                    </li>
                    <li class="rule" data-rule="lower">
                        Al menos una letra minúscula
                    </li>
                    <li class="rule" data-rule="upper">
                        Al menos una letra mayúscula
                    </li>
                    <li class="rule" data-rule="number">
                        Al menos un número
                    </li>
                </ul>

                <div id="password-status" class="password-status"></div>

                <p>
                    <label for="pass2">
                        Confirmar contraseña
                    </label>

                    <input
                        type="password"
                        name="pass2"
                        id="pass2"
                        required
                    >
                </p>

                <div id="confirm-status" class="password-status"></div>
                <br>
                <button
                    type="submit"
                    name="new_password"
                    id="register-submit-btn"
                    disabled
                >
                    Guardar nueva contraseña
                </button>

            </div>

        </form>
    <?php endif; ?>

</div>

<!-- ==========================================================
     JAVASCRIPT  ORDENADO
========================================================== -->
<script>
/* ===============================
 * PASSWORD RULES
 * =============================== */

document.addEventListener("DOMContentLoaded", function () {

    const pass1      = document.getElementById("reg_password");
    const pass2      = document.getElementById("pass2");
    const submitBtn  = document.getElementById("register-submit-btn");
    const statusText = document.getElementById("password-status");
    const confirmTxt = document.getElementById("confirm-status");

    if (!pass1 || !pass2 || !submitBtn) return;

    const rules = {
        length:  val => val.length >= 12,
        lower:   val => /[a-z]/.test(val),
        upper:   val => /[A-Z]/.test(val),
        number:  val => /[0-9]/.test(val)
    };

    function validatePass1(value) {

        let valid = true;

        Object.keys(rules).forEach(rule => {
            const ruleEl = document.querySelector(`[data-rule="${rule}"]`);
            if (!ruleEl) return;

            if (rules[rule](value)) {
                ruleEl.classList.add("valid");
                ruleEl.classList.remove("invalid");
            } else {
                ruleEl.classList.add("invalid");
                ruleEl.classList.remove("valid");
                valid = false;
            }
        });

        statusText.textContent = valid
            ? "Contraseña válida"
            : "La contraseña no cumple con los requisitos";

        return valid;
    }

    function validatePass2() {

        if (pass2.value.length === 0) {
            confirmTxt.textContent = "";
            pass2.classList.remove("valid", "invalid");
            return false;
        }

        if (pass1.value === pass2.value) {
            confirmTxt.textContent = "Las contraseñas coinciden";
            confirmTxt.classList.add("valid");
            confirmTxt.classList.remove("invalid");
            return true;
        } else {
            confirmTxt.textContent = "Las contraseñas no coinciden";
            confirmTxt.classList.add("invalid");
            confirmTxt.classList.remove("valid");
            return false;
        }
    }

    function updateButton() {
        const valid1 = validatePass1(pass1.value);
        const valid2 = validatePass2();
        submitBtn.disabled = !(valid1 && valid2);
    }

    pass1.addEventListener("input", updateButton);
    pass2.addEventListener("input", updateButton);

    submitBtn.disabled = true;

});
</script>

<?php get_footer(); ?>