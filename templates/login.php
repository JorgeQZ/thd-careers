<?php
/*
Template Name: Login
*/

// Evitar el acceso directo al archivo.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Desregistrar los estilos del login predeterminado de WordPress.
function custom_login_dequeue_styles()
{
    wp_dequeue_style('login');
}
add_action('wp_enqueue_scripts', 'custom_login_dequeue_styles', 100);

// Cerrar sesión automáticamente si el usuario está logueado al visitar esta página.
if (is_user_logged_in()) {
    wp_logout();
    wp_redirect(get_permalink()); // Redirigir a esta misma página para evitar el acceso previo.
    exit;
}

/**
 * NOTA IMPORTANTE:
 * - El login ahora se procesa por AJAX vía admin-ajax.php (ver snippet en inc/users/conf-login.php).
 * - El formulario de registro sigue procesándose aquí por POST normal.
 */

// Procesar el formulario de registro si se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
    $raw_email = trim($_POST['reg_email']); // Email original del usuario

    // Validación estricta con regex (solo letras, números y ._%+- antes de @)
    if (!preg_match('/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $raw_email)) {
        $register_error_message = 'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", "." "_" y "-". Otros caracteres no se pueden ingresar.';
    } else {
        $password = !empty($_POST['reg_password']) ? trim($_POST['reg_password']) : null;

        // Validar seguridad de la contraseña
        if (function_exists('validate_password_security')) {
            $password_validation = validate_password_security($password);
        } else {
            $password_validation = true;
        }

        if ($password_validation !== true) {
            $register_error_message = $password_validation;
        } else {
            // Verificar si el correo ya existe
            if (email_exists($raw_email)) {
                $register_error_message = 'Este correo ya está registrado.';
            } else {
                // Crear usuario con correo validado
                $user_id = wp_create_user($raw_email, $password, $raw_email);

                if (!is_wp_error($user_id)) {
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);

                    echo "<script>
                        localStorage.setItem('registro_exitoso', 'true');
                        window.location.href = '" . esc_url(home_url()) . "';
                    </script>";
                    exit;
                } else {
                    $register_error_message = 'Hubo un error al registrar el usuario. Inténtalo más tarde.';
                }
            }
        }
    }
}

get_header();
?>

<div class="custom-login-wrapper" style="max-width: 500px; margin: 50px auto;">
    <h2>Iniciar Sesión</h2>

    <!-- IMPORTANTE: mantenemos action hacia wp-login.php para compatibilidad con SAML,
         pero interceptamos el submit con JS para hacer AJAX -->
    <form action="<?php echo esc_url(wp_login_url()); ?>" method="post" class="login-form" id="custom-login-form">
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

        <!-- Contenedor fijo para mensajes de error del login (rellenado por AJAX) -->
        <div id="login-error-container" class="error-message" style="color: red; margin-bottom: 15px; display:none;"></div>

        <br>
        <p>
            <button type="submit" name="custom_login" id="login-submit-btn">Iniciar Sesión</button>
        </p>
        <br>
    </form>

    <hr>

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
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/img/pwd-closed-eye.png' ); ?>" class="password-icon"
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

        <!-- Reemplaza el bloque PHP de $register_error_message / $register_success_message por esto: -->
        <div id="register-error-container" class="error-message" style="color:red; margin-top:8px; display:none;"></div>

        <br>
        <p style="margin-top: -15px">
            <button type="submit" name="custom_register" id="register-submit-btn">Registrarse</button>
        </p>
    </form>

</div>

<script>
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

        if (valid) {
            statusText.textContent = "Contraseña válida";
            statusText.className = "password-status valid";
            submitBtn.disabled = false;
        } else {
            statusText.textContent = "La contraseña no cumple con los requisitos";
            statusText.className = "password-status invalid";
            submitBtn.disabled = true;
        }
    }

    passwordInput.addEventListener("input", function () {
        updateRules(this.value);
    });

    // Estado inicial
    submitBtn.disabled = true;
});
</script>

<!-- Toggle de visibilidad de password (SIN CAMBIOS DE LÓGICA) -->
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

<!-- Función para saltar el SSO de SAML para QA (SE RESPETA) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.querySelector('.login-form');
    const loginButton = loginForm ? loginForm.querySelector('button[name="custom_login"]') : null;
    const registerForm = document.querySelector('.register-form');
    const registerButton = registerForm ? registerForm.querySelector('button[name="custom_register"]') : null;

    function addSaml(form) {
        if (!form) return;
        const currentAction = form.getAttribute('action') || window.location.href;
        const url = new URL(currentAction, window.location.href);

        // Al inicio del bloque donde haces SAML
        // const SAML_TOKEN_QA   = 'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f';
        // const SAML_TOKEN_PROD = '719652f1df11814efaad458e9aa79d6f10fd2bcc81acf2b620a1063fe5537b65';

        url.searchParams.set(
            'saml_sso',
            'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f'
        );
        form.setAttribute('action', url.toString());
    }

    if (loginButton && loginForm) {
        loginButton.addEventListener('click', function() {
            addSaml(loginForm);
        });
    }

    if (registerButton && loginForm) {
        // Se mantiene el comportamiento original de tu código (usa loginForm).
        registerButton.addEventListener('click', function() {
            addSaml(loginForm);
        });
    }
});
</script>

<!-- VALIDACIÓN DE CARACTERES ESPECIALES (SIN CAMBIOS) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Mensaje de error
    const MSG_EMAIL =
        'El campo de correo electrónico solo acepta letras, números y los siguientes símbolos: "@", ".", "_" y "-". Otros caracteres no se pueden ingresar.';

    // Regex de email (caracteres permitidos en tiempo real)
    const regexEmailAllowed = /^[A-Za-z0-9._@-]+$/;

    function mostrarError(input, mensaje) {
        // Elimina mensaje previo
        let prev = input.parentElement.querySelector(".error-msg");
        if (prev) prev.remove();

        // Crear el span
        const span = document.createElement("span");
        span.className = "error-msg";
        span.style.color = "red";
        span.style.fontSize = "12px";
        span.style.marginTop = "5px";
        span.style.display = "block";
        span.textContent = mensaje;

        input.insertAdjacentElement("afterend", span);

        // Quitar mensaje después de 3s
        setTimeout(() => {
            if (span && span.parentNode) {
                span.style.transition = "opacity .3s";
                span.style.opacity = "0";
                setTimeout(() => span.remove(), 300);
            }
        }, 3000);
    }

    function filtrar(str) {
        return Array.from(str).filter(ch => regexEmailAllowed.test(ch)).join("");
    }

    // Selecciona todos los inputs de email (login y register)
    const emailFields = document.querySelectorAll('input[name="log"], input[name="reg_email"]');

    emailFields.forEach(input => {
        // Antes de insertar
        input.addEventListener("beforeinput", (e) => {
            if (!e.inputType || !e.inputType.startsWith("insert")) return;
            const data = e.data ?? (e.clipboardData ? e.clipboardData.getData("text") : "");
            if (!data) return;

            for (const ch of data) {
                if (!regexEmailAllowed.test(ch)) {
                    e.preventDefault();
                    mostrarError(input, MSG_EMAIL);
                    return;
                }
            }
        });

        // Si algo raro se cuela (ej: pegar)
        input.addEventListener("input", () => {
            const val = input.value;
            const filtrado = filtrar(val);
            if (val !== filtrado) {
                input.value = filtrado;
                mostrarError(input, MSG_EMAIL);
            }
        });
    });
});
</script>

<!-- LOGIN POR AJAX SIN FETCH (XMLHttpRequest) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var loginForm      = document.getElementById('custom-login-form');
    if (!loginForm) return;

    var loginButton    = document.getElementById('login-submit-btn');
    var errorContainer = document.getElementById('login-error-container');

    function mostrarError(msg) {
        if (errorContainer) {
            errorContainer.textContent = msg;
            errorContainer.style.display = 'block';
        } else {
            alert(msg);
        }
    }

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Evita recargar la página

        // Limpiar mensaje previo
        if (errorContainer) {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }

        if (!loginButton) {
            mostrarError('No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.');
            return;
        }

        loginButton.disabled = true;

        // Crear FormData con los campos del formulario
        var formData = new FormData(loginForm);
        formData.append('action', 'custom_ajax_login');
        formData.append('security', '<?php echo esc_js(wp_create_nonce("custom_login_nonce")); ?>');

        // Respetar el parámetro SAML si ya fue agregado al action
        var actionUrl = loginForm.getAttribute('action') || '';
        try {
            var samlUrl   = new URL(actionUrl, window.location.href);
            var samlParam = samlUrl.searchParams.get('saml_sso');
            if (samlParam) {
                formData.append('saml_sso', samlParam);
            }
        } catch (err) {
            // Si falla el parseo, simplemente no se agrega el parámetro
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo esc_url(admin_url("admin-ajax.php")); ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                loginButton.disabled = false;

                if (xhr.status === 200) {
                    var response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        // Error parseando JSON
                        mostrarError('No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.');
                        return;
                    }

                    if (response && response.success && response.data && response.data.redirect) {
                        // Login correcto
                        window.location.href = response.data.redirect;
                    } else if (response && !response.success && response.data && response.data.message) {
                        // Mensaje de error enviado desde PHP (incluye el de contraseña incorrecta)
                        mostrarError(response.data.message);
                    } else {
                        // Estructura inesperada
                        mostrarError('No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.');
                    }
                } else {
                    // Error de conexión
                    mostrarError('Error de conexión. Inténtalo de nuevo.');
                }
            }
        };

        xhr.send(formData);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    var registerForm      = document.getElementById('custom-register-form');
    if (!registerForm) return;

    var registerButton    = document.getElementById('register-submit-btn');
    var registerErrorBox  = document.getElementById('register-error-container');

    function mostrarErrorRegistro(msg) {
        if (registerErrorBox) {
            registerErrorBox.textContent = msg;
            registerErrorBox.style.display = 'block';
        } else {
            alert(msg);
        }
    }

    function limpiarErrorRegistro() {
        if (registerErrorBox) {
            registerErrorBox.textContent = '';
            registerErrorBox.style.display = 'none';
        }
    }

    registerForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Evita recarga de página

        limpiarErrorRegistro();

        if (!registerButton) {
            mostrarErrorRegistro('No fue posible completar el registro. Inténtalo de nuevo.');
            return;
        }

        registerButton.disabled = true;

        var formData = new FormData(registerForm);
        formData.append('action', 'custom_ajax_register');
        formData.append('security', '<?php echo esc_js( wp_create_nonce("custom_register_nonce") ); ?>');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?php echo esc_url( admin_url("admin-ajax.php") ); ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                registerButton.disabled = false;

                if (xhr.status === 200) {
                    var response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        mostrarErrorRegistro('No fue posible completar el registro. Inténtalo de nuevo.');
                        return;
                    }

                    if (response && response.success && response.data) {
                        // Replicamos tu comportamiento: localStorage + redirect
                        if (response.data.registro_exitoso) {
                            try {
                                localStorage.setItem('registro_exitoso', 'true');
                            } catch (err) {
                                // si falla localStorage, continuamos con el redirect
                            }
                        }

                        var redirectUrl = response.data.redirect || '<?php echo esc_url( home_url('/') ); ?>';
                        window.location.href = redirectUrl;
                    } else if (response && !response.success && response.data && response.data.message) {
                        mostrarErrorRegistro(response.data.message);
                    } else {
                        mostrarErrorRegistro('No fue posible completar el registro. Inténtalo de nuevo.');
                    }
                } else {
                    mostrarErrorRegistro('Error de conexión. Inténtalo de nuevo.');
                }
            }
        };

        xhr.send(formData);
    });
});
</script>
<?php
get_footer();
?>
