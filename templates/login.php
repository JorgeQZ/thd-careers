<?php
/*
Template Name: Login
*/

/* ==========================================================
 * SEGURIDAD
 * ========================================================== */
if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================
 * DESREGISTRAR ESTILOS DE LOGIN WP
 * ========================================================== */
function custom_login_dequeue_styles() {
    wp_dequeue_style('login');
}
add_action('wp_enqueue_scripts', 'custom_login_dequeue_styles', 100);

/* ==========================================================
 * REDIRECCIÓN SI YA ESTÁ LOGUEADO
 * ========================================================== */
if (is_user_logged_in()) {
    wp_safe_redirect(home_url('/'));
    exit;
}

/* ==========================================================
 * REGISTRO (POST TRADICIONAL)
 * ========================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {

    $raw_email = trim($_POST['reg_email']);

    if (!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email)) {
        $register_error_message =
            'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", ".", "_" y "-". Otros caracteres no se pueden ingresar.';
    } else {

        $password = !empty($_POST['reg_password']) ? trim($_POST['reg_password']) : null;

        $password_validation = function_exists('validate_password_security')
            ? validate_password_security($password)
            : true;

        if ($password_validation !== true) {
            $register_error_message = $password_validation;
        } elseif (email_exists($raw_email)) {
            $register_error_message = 'Este correo ya está registrado.';
        } else {

            $user_id = wp_create_user($raw_email, $password, $raw_email);

            if (!is_wp_error($user_id)) {
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);

                echo "<script>
                    localStorage.setItem('registro_exitoso','true');
                    window.location.href = '".esc_url(home_url())."';
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

<!-- ==========================================================
     CONTENEDOR PRINCIPAL
========================================================== -->
<div class="custom-login-wrapper" style="max-width: 500px; margin: 50px auto;">

    <h2>Iniciar Sesión</h2>

    <!-- ======================================================
         LOGIN FORM
    ======================================================= -->
    <form action="" method="post" class="login-form" id="custom-login-form">

        <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url()); ?>" />

        <p>
            <label for="username">Correo electrónico</label>
            <input
                type="text"
                id="username"
                name="log"
                placeholder="Nombre de usuario o correo"
                required
                autocomplete="off">
        </p>

        <p>
            <label for="password">Contraseña
                <div style="position: relative;">
                    <input
                        type="password"
                        name="pwd"
                        id="password"
                        autocomplete="off"
                        placeholder="Contraseña"
                        required
                        style="width: 100%; padding: 8px; padding-right: 40px; margin-top: 5px; box-sizing: border-box;">

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password"
                        style="position: absolute; right: 0; top: 26%; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding-right: 10px;">
                        <img
                            src="<?php echo esc_url(get_template_directory_uri() . '/img/pwd-closed-eye.png'); ?>"
                            class="password-icon"
                            style="width: 20px; height: 20px;" />
                    </button>
                </div>
            </label>
        </p>

        <p style="display: flex; justify-content: space-between; align-items: center;">
            <label>
                <input type="checkbox" name="remember"> Recuérdame
            </label>
            <a class="recuperar"
               href="<?php echo esc_url(get_permalink(get_page_by_path('recuperar-contrasena'))); ?>">
                ¿Olvidaste tu contraseña?
            </a>
        </p>

        <?php if (!empty($login_error)): ?>
        <div class="login-error">
            <div id="login-error-container" class="error-message" style="color: red; margin-bottom: 15px;">
                <?php echo esc_html($login_error); ?>
            </div>
        </div>
        <?php endif; ?>

        <br>
        <p>
            <button type="submit" name="custom_login" id="login-submit-btn">Iniciar Sesión</button>
        </p>
        <br>
    </form>

    <hr>

    <!-- ======================================================
         REGISTER FORM
    ======================================================= -->
    <h2>¿No tienes cuenta? <span>Regístrate</span></h2>

    <form method="post" action="" class="register-form" id="custom-register-form">

        <p>
            <label for="reg_email">Correo electrónico</label>
            <input type="email" name="reg_email" id="reg_email" required
                style="width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box">
        </p>

        <p style="margin-bottom: 0;">
            <label for="reg_password">Contraseña
                <div style="position: relative;">
                    <input type="password" name="reg_password" id="reg_password" required
                        style="width: 100%; padding: 8px; padding-right: 40px; margin-top: 5px; box-sizing: border-box;">

                    <button type="button" class="toggle-password" data-target="reg_password"
                        style="position: absolute; right: 0; top: 10px; transform: translateY(-50%); background: none; border: none; cursor: pointer; padding-right: 10px;">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/img/pwd-closed-eye.png'); ?>"
                             class="password-icon"
                             style="width: 20px; height: 20px;">
                    </button>

                    <div class="password-rules" id="password-rules">
                        <p class="rule" data-rule="length">• Mínimo 8 caracteres</p>
                        <p class="rule" data-rule="lower">• Al menos una letra minúscula</p>
                        <p class="rule" data-rule="upper">• Al menos una letra mayúscula</p>
                        <p class="rule" data-rule="number">• Al menos un número</p>
                        <p class="rule" data-rule="special">• Al menos un carácter especial</p>
                    </div>

                    <p id="password-status" class="password-status"></p>
                </div>
            </label>
        </p>

       <?php if (!empty($register_error_message)) : ?>
            <div class="error-message" style="color:red; margin-top:8px;">
                <?php echo esc_html($register_error_message); ?>
            </div>
        <?php endif; ?>
        <br>
        <p style="margin-top: -15px">
            <button type="submit" name="custom_register" id="register-submit-btn">Registrarse</button>
        </p>
    </form>
</div>

<!-- ==========================================================
     JAVASCRIPT  ORDENADO
========================================================== -->
<script>
/* ===============================
 * PASSWORD RULES
 * =============================== */
document.addEventListener("DOMContentLoaded", function () {
    const passwordInput  = document.getElementById("reg_password");
    const submitBtn      = document.getElementById("register-submit-btn");
    const statusText     = document.getElementById("password-status");

    if (!passwordInput || !submitBtn) return;

    const rules = {
        length:  val => val.length >= 8,
        lower:   val => /[a-z]/.test(val),
        upper:   val => /[A-Z]/.test(val),
        number:  val => /[0-9]/.test(val),
        special: val => /[^A-Za-z0-9]/.test(val)
    };

    function updateRules(value) {
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

        if (value.length === 0) {
            statusText.textContent = "";
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = !valid;
        statusText.textContent = valid
            ? "Contraseña válida"
            : "La contraseña no cumple con los requisitos";
    }

    passwordInput.addEventListener("input", function () {
        updateRules(this.value);
    });

    submitBtn.disabled = true;
});
</script>

<!-- ===============================
     TOGGLE PASSWORD
=============================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let hide_icon = "<?php echo esc_js(get_template_directory_uri() . '/img/pwd-open-eye.png'); ?>";
    let show_icon = "<?php echo esc_js(get_template_directory_uri() . '/img/pwd-closed-eye.png'); ?>";

    document.querySelectorAll(".toggle-password").forEach(function(button) {
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

<!-- ===============================
     SAML QA
=============================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.querySelector('.login-form');
    const loginButton = loginForm ? loginForm.querySelector('button[name="custom_login"]') : null;
    const registerButton = document.querySelector('button[name="custom_register"]');

    // const SAML_TOKEN_QA   = 'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f';
    // const SAML_TOKEN_PROD = '719652f1df11814efaad458e9aa79d6f10fd2bcc81acf2b620a1063fe5537b65';
    function addSaml(form) {
        if (!form) return;
        const url = new URL(form.getAttribute('action') || window.location.href, window.location.href);
        url.searchParams.set(
            'saml_sso',
            'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f'
        );
        form.setAttribute('action', url.toString());
    }

    if (loginButton) {
        loginButton.addEventListener('click', () => addSaml(loginForm));
    }
    if (registerButton) {
        registerButton.addEventListener('click', () => addSaml(loginForm));
    }
});
</script>

<!-- ===============================
     VALIDACIÓN EMAIL
=============================== -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const MSG_EMAIL =
        'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", ".", "_" y "-". Otros caracteres no se pueden ingresar.';
    const regexEmailAllowed = /^[A-Za-z0-9._@-]+$/;

    function mostrarError(input, mensaje) {
        let prev = input.parentElement.querySelector(".error-msg");
        if (prev) prev.remove();

        const span = document.createElement("span");
        span.className = "error-msg";
        span.style.color = "red";
        span.style.fontSize = "12px";
        span.style.marginTop = "5px";
        span.textContent = mensaje;
        input.insertAdjacentElement("afterend", span);

        setTimeout(() => span.remove(), 3000);
    }

    const emailFields = document.querySelectorAll('input[name="log"], input[name="reg_email"]');

    emailFields.forEach(input => {
        input.addEventListener("beforeinput", e => {
            if (e.data && !regexEmailAllowed.test(e.data)) {
                e.preventDefault();
                mostrarError(input, MSG_EMAIL);
            }
        });
    });
});
</script>

<?php get_footer(); ?>
