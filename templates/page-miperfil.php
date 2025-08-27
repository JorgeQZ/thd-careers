<?php
/*
Template Name: Mi Perfil
*/

get_header();

// Incluir el archivo con la lógica del perfil.
include_once get_template_directory() . '/inc/users/miperfil.php';

// Verificar si hay un mensaje de éxito en la sesión.
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];


    unset($_SESSION['mensaje_exito']); // Limpiar mensaje después de mostrarlo.

    if ($mensaje_exito): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('mensajeExito').style.display = 'flex';
            });
        </script>
        <?php
    endif;

    $mensaje_exito = '';
}

if (isset($_POST['submit'])) {
    if (isset($_FILES['file_to_upload']) && $_FILES['file_to_upload']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file_to_upload'];
        $gcs_response = upload_to_gcp($file); // Llamar a la función que sube el archivo

        if ($gcs_response) {
            try {
                // Validar que la respuesta no está vacía
                if (empty($gcs_response)) {
                    throw new Exception('La respuesta del servidor está vacía.');
                }

                // Decodificar JSON con excepciones activadas
                $decoded_response = json_decode($gcs_response, true, 512, JSON_THROW_ON_ERROR);

                // Validar que la propiedad 'mediaLink' existe en la respuesta
                if (!isset($decoded_response['mediaLink']) || !isset($decoded_response['name'])) {
                    throw new Exception('La respuesta JSON no contiene los datos esperados.');
                }

                // Asignar la URL del archivo
                $gcs_url = $decoded_response['mediaLink'];
                $gcs_url_name = $decoded_response['name'];

                // Obtener el ID del usuario actual
                $user_id = get_current_user_id();

                // Guardar la URL del archivo de GCS como metadato del usuario
                update_user_meta($user_id, 'cv_gcs_url', esc_url_raw($gcs_url));
                update_user_meta($user_id, 'gcs_url_name', sanitize_text_field($gcs_url_name));

                // Mensaje de éxito (puedes activarlo si lo necesitas)
                // echo '<p>Archivo subido exitosamente y URL guardada.</p>';

            } catch (JsonException $e) {
                // Registrar el error en los logs
                error_log('Error al decodificar JSON en page-postulaciones.php: ' . $e->getMessage());

                // Mensaje seguro para el usuario
                echo '<p class="error">Hubo un problema al procesar los datos. Inténtalo de nuevo más tarde.</p>';

            } catch (Exception $e) {
                // Registrar otros errores en los logs
                error_log('Error en la respuesta de GCS: ' . $e->getMessage());

                // Mostrar un mensaje genérico al usuario
                echo '<p class="error">Hubo un error al procesar la respuesta del servidor. Por favor, inténtelo más tarde.</p>';
            }

        } else {
            echo '<p>Hubo un error al subir el archivo a GCS.</p>';
        }
    }
    // No se hace nada si no se selecciona un archivo
}
?>

<div class="popup-cont" id="mensajeExito">
    <div class="container">
        <div class="close closebtnpopup" id="close-mensaje">+</div>
        <div class="title">Tu perfil ha <br> sido actualizado<span> correctamente</span></div>
        <button class="button closebtnpopup">Cerrar</button>
    </div>
</div><!-- PopUp -->

<div class="miperfil">

    <form method="POST" action="" enctype="multipart/form-data">

        <div class="contenedor-form">

            <h1 class="h1-top">DATOS GENERALES</h1>

            <div class="contenedor detres">
                <div>
                    <label for="nombre">Nombre(s)</label>
                    <input type="text" class="validar" name="nombre" value="<?php echo esc_attr($nombre_actual); ?>">
                </div>
                <div>
                    <label for="apellido_paterno">Apellido paterno</label>
                    <input type="text" class="validar" name="apellido_paterno" value="<?php echo esc_attr($apellido_paterno_actual); ?>">
                </div>
                <div>
                    <label for="apellido_materno">Apellido materno</label>
                    <input type="text" class="validar" name="apellido_materno" value="<?php echo esc_attr($apellido_materno_actual); ?>">
                </div>
            </div>

            <div class="contenedor">
                <div>
                    <label for="correo">Correo electrónico</label>
                    <?php
                    $current_user = wp_get_current_user();
                    $user_email   = $current_user->user_email;
                    $correo_acf   = get_field('correo_general', 'user_' . $user_id);
                    $correo_val   = $correo_acf !== '' && $correo_acf !== null ? $correo_acf : $user_email;
                    ?>
                    <input type="email" id="correo" name="correo" value="<?php echo esc_attr($correo_val); ?>">
                </div>
            </div>

            <div class="contenedor dedos">
                <div>
                    <label for="fecha_de_nacimiento">Fecha de nacimiento</label>
                    <div class="custom-date-input">
                        <input type="date" name="fecha_de_nacimiento" value="<?php echo esc_attr($fecha_de_nacimiento_actual); ?>">
                        <span class="icon-calendar"></span>
                    </div>
                </div>
                <div>
                    <label for="nacionalidad">Nacionalidad</label>
                    <input class="input-nacionalidad validar" type="text" name="nacionalidad" value="<?php echo esc_attr($nacionalidad_actual); ?>">
                </div>
            </div>

            <div class="contenedor dedos">
                <div>
                    <label for="grado_escolaridad">Elige tu grado de escolaridad</label>
                    <select id="grado_escolaridad" name="grado_escolaridad">
                        <option value="">Selecciona una opción</option>
                        <option value="Primaria" <?php selected($grado_escolaridad_actual, 'Primaria'); ?>>Primaria</option>
                        <option value="Secundaria" <?php selected($grado_escolaridad_actual, 'Secundaria'); ?>>Secundaria
                        </option>
                        <option value="Preparatoria" <?php selected($grado_escolaridad_actual, 'Preparatoria'); ?>>
                            Preparatoria</option>
                        <option value="Carrera técnica" <?php selected($grado_escolaridad_actual, 'Carrera técnica'); ?>>
                            Carrera técnica</option>
                        <option value="Licenciatura" <?php selected($grado_escolaridad_actual, 'Licenciatura'); ?>>
                            Licenciatura</option>
                        <option value="Posgrado" <?php selected($grado_escolaridad_actual, 'Posgrado'); ?>>Posgrado</option>
                        <option value="Maestría" <?php selected($grado_escolaridad_actual, 'Maestría'); ?>>Maestría</option>
                        <option value="Doctorado" <?php selected($grado_escolaridad_actual, 'Doctorado'); ?>>Doctorado
                        </option>
                    </select>
                </div>
                <div>
                    <label for="estado_civil">Estado civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Selecciona una opción</option>
                        <option value="soltero" <?php selected($estado_civil_actual, 'soltero'); ?>>Soltero/a</option>
                        <option value="casado" <?php selected($estado_civil_actual, 'casado'); ?>>Casado/a</option>
                        <option value="divorciado" <?php selected($estado_civil_actual, 'divorciado'); ?>>Divorciado/a</option>
                        <option value="viudo" <?php selected($estado_civil_actual, 'viudo'); ?>>Viudo/a</option>
                    </select>
                </div>
            </div>

            <div class="contenedor-cdt">
                <p>¿En qué centro de trabajo estás interesado(a)?</p>

                <label>
                    <input type="radio" name="pr1" value="Oficinas de Apoyo a Tiendas" <?php checked($pr1_actual, 'Oficinas de Apoyo a Tiendas'); ?>> Oficinas de Apoyo a Tiendas
                </label>

                <label>
                    <input type="radio" name="pr1" value="Tiendas" <?php checked($pr1_actual, 'Tiendas'); ?>> Tiendas
                </label>

                <label>
                    <input type="radio" name="pr1" value="Centros de Distribución" <?php checked($pr1_actual, 'Centros de Distribución'); ?>> Centros de distribución
                </label>

                <div class="contenedor-cdt2" style="display: none;">
                    <p>¿En qué área estás interesado(a)?</p>

                    <label>
                    <input type="radio" name="pr2" value="Operaciones" <?php checked($pr2_actual, 'Operaciones'); ?>> Operaciones
                    </label>

                    <label>
                    <input type="radio" name="pr2" value="Recursos Humanos" <?php checked($pr2_actual, 'Recursos Humanos'); ?>> Recursos humanos
                    </label>

                    <label>
                    <input type="radio" name="pr2" value="Finanzas" <?php checked($pr2_actual, 'Finanzas'); ?>> Finanzas
                    </label>

                    <label>
                    <input type="radio" name="pr2" value="Marketing" <?php checked($pr2_actual, 'Marketing'); ?>> Marketing
                    </label>
                </div>

                <div class="contenedor">
                    <div class="custom-file">
                        <label for="cv">CV</label>
                        <?php
                            $cv_gcs_url = get_user_meta(get_current_user_id(), 'cv_gcs_url', true);
                            $gcs_url_name = get_user_meta(get_current_user_id(), 'gcs_url_name', true);
                            $link_cv = obtener_url_archivo($gcs_url_name);
                        ?>
                        <div class="file-wrapper">
                            <input type="file" id="cv" name="file_to_upload" class="file-input" accept=".pdf,.doc,.docx">
                            <span class="span-place">Haz clic aquí para subir un archivo</span>
                            <span class="icon-attachment"></span>
                        </div>
                        <span class="file-name <?php echo !$cv_gcs_url ? 'noactive' : ''; ?>">
                            <?php
                                $gcs_url_name = get_user_meta(get_current_user_id(), 'gcs_url_name', true);
                                $link_cv = obtener_url_archivo($gcs_url_name);

                                echo $link_cv
                                    ? '<a class="a-cvguardado" rel="noopener noreferrer" href="' . esc_url($link_cv) . '" target="_blank">Haz click aquí para ver el CV actual</a>'
                                    : 'Sin archivo seleccionado';
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <h1>DIRECCIÓN</h1>

            <div class="contenedor detres">
                <div>
                    <label for="calle">Calle y número interior</label>
                    <input type="text" name="calle" value="<?php echo esc_attr($calle_actual); ?>">
                </div>

                <div>
                    <label for="colonia">Colonia</label>
                    <input type="text" name="colonia" value="<?php echo esc_attr($colonia_actual); ?>">
                </div>

                <div>
                    <label for="codigo_postal">Código postal</label>
                    <input type="text" name="codigo_postal" value="<?php echo esc_attr($codigo_postal_actual); ?>">
                </div>

            </div>

            <div class="contenedor detres">

                <div>
                    <label for="municipiociudad">Ciudad</label>
                    <input type="text" class="validar_ubi" name="municipiociudad" value="<?php echo esc_attr($municipiociudad_actual); ?>">
                </div>

                <div>
                    <label for="estado">Estado</label>
                    <input type="text" class="validar_ubi" name="estado" value="<?php echo esc_attr($estado_actual); ?>">
                </div>

                <div></div>
<!--
                <div style="visibility: hidden;">
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" value="<?php echo esc_attr($estado_actual); ?>">
                </div>
-->
            </div>

            <h1>INFORMACIÓN DE CONTACTO</h1>

            <div class="contenedor detres">
                <div>
                    <label for="linkedin">LinkedIn</label>
                    <input type="url" name="linkedin" value="<?php echo esc_attr($linkedin_actual); ?>">
                </div>

                <div>
                    <label for="facebook">Facebook</label>
                    <input type="url" name="facebook" value="<?php echo esc_attr($facebook_actual); ?>">
                </div>

                <div>
                    <label for="instagram">Instagram</label>
                    <input type="url" name="instagram" value="<?php echo esc_attr($instagram_actual); ?>">
                </div>
            </div>

            <div class="contenedor detres">
                <div>
                    <label for="telefono_celular">Celular</label>
                    <input type="text" class="validar_tel" name="telefono_celular" value="<?php echo esc_attr($telefono_celular_actual); ?>">
                </div>

                <div>
                    <label for="telefono_fijo">Teléfono fijo</label>
                    <input type="text" class="validar_tel" name="telefono_fijo" value="<?php echo esc_attr($telefono_fijo_actual); ?>">
                </div>

                <div>
                    <label for="correo2">Correo</label>
                    <input type="text" name="correo2" value="<?php echo esc_attr($correo2_actual); ?>">
                </div>
            </div>

        </div>

        <h1>AJUSTES RAZONABLES</h1>

        <div class="ajustesr">

            <p>¿Requieres algún ajuste razonable o apoyo para que tu proceso de selección sea incluyente?</p>

            <label>
                <input type="radio" name="pregunta1" value="Sí"
                    <?php checked($pregunta1_actual, 'Sí'); ?>
                    onclick="mostrarSegundaPregunta(true)">
                Sí
            </label><br>

            <label>
                <input type="radio" name="pregunta1" value="No"
                    <?php checked($pregunta1_actual, 'No'); ?>
                    onclick="mostrarSegundaPregunta(false)">
                No
            </label><br>

            <div id="segundaPregunta" style="display: none;">
                <p>¿Qué tipo de apoyo o ajuste requieres para tu proceso de selección?</p>

                <label>
                    <input type="radio" name="pregunta2"
                        value="Uso de notas escritas mediante libreta o pizarra de comunicación"
                        <?php checked($pregunta2_actual, 'Uso de notas escritas mediante libreta o pizarra de comunicación'); ?>>
                    Uso de notas escritas mediante libreta o pizarra de comunicación
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Intérprete de LSM (lengua de señas mexicana)"
                        <?php checked($pregunta2_actual, 'Intérprete de LSM (lengua de señas mexicana)'); ?>>
                    Intérprete de LSM (lengua de señas mexicana)
                </label><br>

                <label>
                    <input type="radio" name="pregunta2"
                        value="Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)"
                        <?php checked($pregunta2_actual, 'Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)'); ?>>
                    Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Poder tomar asiento con frecuencia"
                        <?php checked($pregunta2_actual, 'Poder tomar asiento con frecuencia'); ?>>
                    Poder tomar asiento con frecuencia
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Magnificadores de pantalla o lupa portátil"
                        <?php checked($pregunta2_actual, 'Magnificadores de pantalla o lupa portátil'); ?>>
                    Magnificadores de pantalla o lupa portátil
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Uso de lector de pantalla"
                        <?php checked($pregunta2_actual, 'Uso de lector de pantalla'); ?>>
                    Uso de lector de pantalla
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Control de estímulos sonoros, como aislamiento de ruido"
                        <?php checked($pregunta2_actual, 'Control de estímulos sonoros, como aislamiento de ruido'); ?>>
                    Control de estímulos sonoros, como aislamiento de ruido
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Otro">
                    Otro
                </label><br>

                <div id="cajaTextoOtro" style="display: none; margin-top: 10px;">
                    <label for="otro_detalle">Por favor, especifique:</label><br>
                    <textarea name="otro_detalle" id="otro_detalle" rows="4"
                        cols="50"><?php echo esc_textarea($otroar2_actual); ?></textarea>
                </div>
            </div>

        </div>

        <button type="submit" name="submit" class="act-datos">ACTUALIZAR DATOS</button>

    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.querySelector("#cv");
        const spanPlace = document.querySelector(".span-place");

        fileInput.addEventListener("change", function () {
            if (fileInput.files.length > 0) {
                // Obtener el nombre del archivo subido
                const fileName = fileInput.files[0].name;
                // Actualizar el texto de span-place con el nombre del archivo
                spanPlace.textContent = fileName;
            } else {
                // Restaurar el texto original si no hay archivo
                spanPlace.textContent = "Haz clic aquí para subir un CV";
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mostrar el mensaje de éxito por 5 segundos

        //const close = document.getElementById("close-mensaje");

        const mensaje = document.getElementById('mensajeExito');

        var close = document.getElementsByClassName("closebtnpopup");

        var cerrarPopup = function() {
            mensaje.style.display = "none";
        };

        for (var i = 0; i < close.length; i++) {
            close[i].addEventListener('click', cerrarPopup, false);
        }
/*
        if(close){
            close.addEventListener("clic", function() {
                mensaje.style.display = "none";
            });
        }
*/
        // Configurar visibilidad inicial según los valores actuales
        const pregunta1Actual = "<?php echo esc_js($pregunta1_actual); ?>";
        const pregunta2Actual = "<?php echo esc_js($pregunta2_actual); ?>";
        const cajaTextoOtro = document.getElementById("cajaTextoOtro");
        const contenedorCdt2 = document.querySelector('.contenedor-cdt2');
        const radioButtonsPr1 = document.querySelectorAll('input[name="pr1"]');

        // Mostrar la segunda pregunta si "Sí" está seleccionado
        mostrarSegundaPregunta(pregunta1Actual === "Sí");

        // Mostrar caja de texto "Otro" si está seleccionado
        if (pregunta2Actual === "Otro") {
            cajaTextoOtro.style.display = "block";
            const otroRadio = document.querySelector('input[name="pregunta2"][value="Otro"]');
            if (otroRadio) {
                otroRadio.checked = true;
            }
        }

        // Configurar visibilidad inicial para "Oficinas de apoyo a tiendas"
        radioButtonsPr1.forEach(radio => {
            if (radio.value === "Oficinas de apoyo a tiendas" && radio.checked) {
                contenedorCdt2.style.display = "flex"; // Mostrar contenedor-cdt2 si está seleccionado inicialmente
            }
        });

        // Configurar eventos para la primera pregunta (mostrar/ocultar segunda pregunta)
        document.querySelectorAll('input[name="pregunta1"]').forEach(radio => {
            radio.addEventListener('change', function () {
                mostrarSegundaPregunta(this.value === "Sí");
            });
        });

        // Configurar eventos para la segunda pregunta (mostrar/ocultar caja de texto "Otro")
        document.querySelectorAll('input[name="pregunta2"]').forEach(radio => {
            radio.addEventListener('change', function () {
                cajaTextoOtro.style.display = this.value === "Otro" ? "block" : "none";
            });
        });

        // Configurar eventos para mostrar/ocultar contenedor-cdt2
        radioButtonsPr1.forEach(radio => {
            radio.addEventListener('change', () => {
                contenedorCdt2.style.display = (radio.value === "Oficinas de apoyo a tiendas" && radio.checked) ? "flex" : "none";
            });
        });
    });

    // Función para mostrar/ocultar la segunda pregunta
    function mostrarSegundaPregunta(mostrar) {
        const segundaPregunta = document.getElementById("segundaPregunta");
        if (segundaPregunta) {
            segundaPregunta.style.display = mostrar ? "block" : "none";
        }
    }
</script>

<script src="<?php echo get_template_directory_uri().'/js/jquery.min.js' ?>"></script>

<script>

    $ = jQuery;
    $(document).ready(function() {

        function verificarCampos() {
            let todosLlenos = true;

            $('.contenedorgeneralcampos input.req, .contenedorgeneralcampos input.req, .contenedorgeneralcampos textarea.req').each(function(index) {
                if ($(this).val() === '') {
                    todosLlenos = false;
                }
            });

            if (!$('input.check_terminos').is(':checked')){
                todosLlenos = false;
            }

/*
            $('.contenedorgeneralcampos select').each(function(index) {
                if ($(this).children('option:first-child').is(':selected')) {
                    todosLlenos = false;
                }
            });
*/
            if (todosLlenos) {
                $('.boton-postulacion').prop('disabled', false);
            } else {
                $('.boton-postulacion').prop('disabled', true);
            }
        }

        $('.contenedorgeneralcampos input.req, .contenedorgeneralcampos textarea.req, .contenedorgeneralcampos input.check_terminos').on('input', function() {
            verificarCampos();
        });

        // $('.validar').on('keypress', function(e) {
        //     var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/;
        //     var key = String.fromCharCode(event.which);

        //     if (!regex.test(key)) {
        //         event.preventDefault();
        //     }
        // });

        // $('.validar_tel').on('keypress', function(e) {
        //     var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ+()0-9 ]+$/;
        //     var key = String.fromCharCode(event.which);

        //     if (!regex.test(key)) {
        //         event.preventDefault();
        //     }
        // });

        // $('.validar_ubi').on('keypress', function(e) {
        //     var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/;
        //     var key = String.fromCharCode(event.which);

        //     if (!regex.test(key)) {
        //         event.preventDefault();
        //     }
        // });

        var hoy = new Date().toISOString().split('T')[0];
        $('.contenedorgeneralcampos input[type="date"]').each(function(index) {
            $(this).attr('max', hoy);
            $(this).attr('min', "1965-01-01");
        });

        $('.closebtnpopup').on('click', function() {
            $('#mensajeExito').hide();
        });

/*
        $('select').change(function() {
            verificarCampos();
        });
*/
        verificarCampos();

    });

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Mensajes
  const MSG_SIMBOLOS = 'Este campo solo acepta los siguientes símbolos: "@", ".", "_" , "-" y "´". Otros caracteres no se pueden ingresar.';
  const MSG_TEL     = 'Este campo solo acepta números y los símbolos: "+", "(", ")" y espacio.';
  const MSG_EMAIL   = 'El correo solo acepta letras, números y los símbolos: "@", ".", "_" y "-".';
  const MSG_URL     = 'Ingresa una URL válida (https). No se permiten espacios ni los caracteres <, >, \' o ".';

  // Reglas
  // Texto general: letras (Unicode) + marcas combinadas (acentos), dígitos, espacio y . _ - @ ´
  const permitido       = /^[\p{L}\p{M}0-9 ._\-@´]+$/u;
  // Teléfono: 0-9 + ( ) espacio
  const permitidoTel    = /^[0-9+() ]+$/;
  // Email (en vivo): letras, números y . _ - @
  const permitidoEmail  = /^[A-Za-z0-9._@-]+$/;
  // URL: bloquear espacios, comillas y < >
  const URL_CHARS       = /^[^\s"'<>]+$/;

  // Teclas muertas (para componer á, é, etc.)
  const deadKeys = /[\u00B4\u0060\u005E\u007E\u02C6\u02DC]/; // ´ ` ^ ~ ˆ ˜

  function mostrarError(input, mensaje) {
    const prev = input.parentElement.querySelector('.error-msg');
    if (prev) prev.remove();

    const span = document.createElement('span');
    span.className = 'error-msg';
    span.setAttribute('role', 'alert');
    span.style.color = 'red';
    span.style.fontSize = '12px';
    span.style.display = 'block';
    span.style.marginTop = '5px';
    span.textContent = mensaje;

    input.insertAdjacentElement('afterend', span);

    setTimeout(() => {
      if (span && span.parentNode) {
        span.style.transition = 'opacity .3s';
        span.style.opacity = '0';
        setTimeout(() => span.remove(), 300);
      }
    }, 3000);
  }

  const filtrar = (str, re) => Array.from(str).filter(ch => re.test(ch)).join('');

  function addFilter(el, re, msg) {
    // Permite composición (para á, é...) y dead keys
    el.addEventListener('beforeinput', (e) => {
      if (e.inputType && e.inputType.includes('Composition')) return;

      const data =
        e.data ??
        (e.clipboardData && e.clipboardData.getData && e.clipboardData.getData('text')) ??
        '';

      if (!data) return;
      if (data.length === 1 && deadKeys.test(data)) return;

      for (const ch of data) {
        if (!re.test(ch)) {
          e.preventDefault();
          mostrarError(el, msg);
          return;
        }
      }
    });

    el.addEventListener('input', () => {
      const val = el.value;
      const filtrado = filtrar(val, re);
      if (val !== filtrado) {
        el.value = filtrado;
        mostrarError(el, msg);
      }
    });
  }

  // === Asignación por tipo de campo ===

  // 1) Texto general (excluye teléfonos, emails y URLs)
  const generalSelector =
    'input[type="text"]:not([name="telefono_celular"]):not([name="telefono_fijo"]):not([name="correo"]):not([name="correo2"]):not([type="url"]), textarea';
  document.querySelectorAll(generalSelector).forEach(el => addFilter(el, permitido, MSG_SIMBOLOS));

  // 2) Teléfonos
  ['telefono_celular', 'telefono_fijo'].forEach(name => {
    const el = document.querySelector(`input[name="${name}"]`);
    if (el) addFilter(el, permitidoTel, MSG_TEL);
  });

  // 3) Emails (correo principal + correo2)
  ['correo', 'correo2'].forEach(name => {
    const el = document.querySelector(`input[name="${name}"]`);
    if (el) addFilter(el, permitidoEmail, MSG_EMAIL);
  });

  document.querySelector('input[name="codigo_postal"]')
  ?.addEventListener('input', e => e.target.value = e.target.value.replace(/\D/g, '').slice(0, 5));

    // 4) URLs (LinkedIn, Facebook, Instagram)
    const expectedDomains = {
    linkedin:  ['linkedin.com'],
    facebook:  ['facebook.com', 'fb.com'],
    instagram: ['instagram.com']
    };

    ['linkedin','facebook','instagram'].forEach(name => {
    const el = document.querySelector(`input[name="${name}"]`);
    if (!el) return;

    addFilter(el, URL_CHARS, MSG_URL);

        el.addEventListener('blur', () => {
            let v = (el.value || '').trim();
            if (!v) return;

            if (!/^https:\/\//i.test(v)) {
            mostrarError(el, MSG_URL);
            el.value = '';
            return;
            }

            try {
            const u = new URL(v);

            if (!/^https:$/i.test(u.protocol)) {
                mostrarError(el, MSG_URL);
                el.value = '';
                return;
            }

            const host = u.hostname.replace(/^www\./i, '');
            const domains = expectedDomains[name] || [];
            const hostOk = domains.some(dom => host === dom || host.endsWith('.' + dom));

            if (!hostOk) {
                mostrarError(el, MSG_SOCIAL_DOMAIN);
                el.value = '';
                return;
            }

            el.value = v;
            } catch (e) {
            mostrarError(el, MSG_URL);
            el.value = '';
            }
        });
    });
});
</script>

<?php  get_footer(); ?>
</body>

</html>