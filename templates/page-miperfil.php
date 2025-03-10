<?php
/*
Template Name: Mi Perfil
*/

get_header();

// Incluir el archivo con la lógica del perfil.
include get_template_directory() . '/inc/users/miperfil.php';

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
                // Decodificar la respuesta JSON
                $decoded_response = json_decode($gcs_response);

                // Verificar errores en la decodificación
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Error al decodificar el JSON: ' . json_last_error_msg());
                }

                // Validar que la propiedad 'mediaLink' existe en la respuesta
                if (!isset($decoded_response->mediaLink)) {
                    throw new Exception('La respuesta JSON no contiene la propiedad "mediaLink".');
                }

                // Asignar la URL del archivo
                $gcs_url = $decoded_response->mediaLink;

                // Obtener el ID del usuario actual
                $user_id = get_current_user_id();

                // Guardar la URL del archivo de GCS como metadato del usuario
                update_user_meta($user_id, 'cv_gcs_url', $gcs_url);

                echo '<p>Archivo subido exitosamente y URL guardada.</p>';
            } catch (Exception $e) {
                // Registrar el error en los logs
                error_log('Error en la respuesta de GCS: ' . $e->getMessage());

                // Mostrar un mensaje genérico al usuario
                echo '<p>Hubo un error al procesar la respuesta del servidor. Por favor, inténtelo más tarde.</p>';
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
                        $user_email = $current_user->user_email;
                    ?>
                    <input type="email" name="correo" value="<?php echo $user_email; ?>">
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
                    <input class="input-nacionalidad" type="text" name="nacionalidad" value="<?php echo esc_attr($nacionalidad_actual); ?>">
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
                    <input type="radio" name="pr1" value="Oficinas de Apoyo a Tiendas" <?php checked($pr1_actual, 'Oficinas de apoyo a tiendas'); ?>> Oficinas de Apoyo a Tiendas
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
                        ?>
                        <div class="file-wrapper">
                            <input type="file" id="cv" name="file_to_upload" class="file-input" accept=".pdf,.doc,.docx">
                            <span class="span-place">Haz clic aquí para subir un archivo</span>
                            <span class="icon-attachment"></span>
                        </div>
                        <span class="file-name <?php echo !$cv_gcs_url ? 'noactive' : ''; ?>">
                            <?php
                                $cv_gcs_url = get_user_meta(get_current_user_id(), 'cv_gcs_url', true);
                                echo $cv_gcs_url
                                    ? '<a class="a-cvguardado" href="' . esc_url($cv_gcs_url) . '" target="_blank">Haz clic aquí para ver el CV actual</a>'
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
                    onclic="mostrarSegundaPregunta(true)">
                Sí
            </label><br>

            <label>
                <input type="radio" name="pregunta1" value="No"
                    <?php checked($pregunta1_actual, 'No'); ?>
                    onclic="mostrarSegundaPregunta(false)">
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
            close[i].addEventListener('clic', cerrarPopup, false);
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

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

        $('.validar').on('keypress', function(e) {
            var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]+$/;
            var key = String.fromCharCode(event.which);

            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        $('.validar_tel').on('keypress', function(e) {
            var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ+()0-9 ]+$/;
            var key = String.fromCharCode(event.which);

            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        $('.validar_ubi').on('keypress', function(e) {
            var regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/;
            var key = String.fromCharCode(event.which);

            if (!regex.test(key)) {
                event.preventDefault();
            }
        });

        var hoy = new Date().toISOString().split('T')[0];
        $('.contenedorgeneralcampos input[type="date"]').each(function(index) {
            $(this).attr('max', hoy);
            $(this).attr('min', "1965-01-01");
        });

/*
        $('select').change(function() {
            verificarCampos();
        });
*/
        verificarCampos();

    });

</script>


<?php  get_footer(); ?>
</body>

</html>