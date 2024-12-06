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
}
?>

<?php if ($mensaje_exito): ?>
<div class="mensaje-exito" id="mensajeExito"><?php echo esc_html($mensaje_exito); ?></div>
<?php endif; ?>

<div class="miperfil">

    <form method="POST" action="" enctype="multipart/form-data">

        <div class="contenedor-form">

            <h1 class="h1-top">DATOS GENERALES</h1>

            <div class="contenedor detres">
                <div>
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" value="<?php echo esc_attr($nombre_actual); ?>">
                </div>
                <div>
                    <label for="apellido_paterno">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?php echo esc_attr($apellido_paterno_actual); ?>">
                </div>
                <div>
                    <label for="apellido_materno">Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?php echo esc_attr($apellido_materno_actual); ?>">
                </div>
            </div>

            <div class="contenedor">
                <div>
                    <label for="correo">Correo</label>
                    <input type="email" name="correo" value="<?php echo esc_attr($correo_actual); ?>">
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

            <div class="contenedor">
                <div class="custom-file">
                    <label for="cv">CV</label>
                    <div class="file-wrapper">
                        <!-- <label>CV</label> -->
                        <input type="file">
                        <span class="icon-attachment"></span>
                    </div>
                </div>
            </div>

            <div class="contenedor dedos">
                <div>
                    <label for="grado_escolaridad">Elige tu grado de escolaridad:</label>
                    <select id="grado_escolaridad" name="grado_escolaridad">
                        <option value="">Escolaridad - Grado de estudios</option>
                        <option value="primaria" <?php selected($grado_escolaridad_actual, 'primaria'); ?>>Primaria</option>
                        <option value="secundaria" <?php selected($grado_escolaridad_actual, 'secundaria'); ?>>Secundaria
                        </option>
                        <option value="preparatoria" <?php selected($grado_escolaridad_actual, 'preparatoria'); ?>>
                            Preparatoria</option>
                        <option value="carrera_tecnica" <?php selected($grado_escolaridad_actual, 'carrera_tecnica'); ?>>
                            Carrera técnica</option>
                        <option value="licenciatura" <?php selected($grado_escolaridad_actual, 'licenciatura'); ?>>
                            Licenciatura</option>
                        <option value="posgrado" <?php selected($grado_escolaridad_actual, 'posgrado'); ?>>Posgrado</option>
                        <option value="maestria" <?php selected($grado_escolaridad_actual, 'maestria'); ?>>Maestría</option>
                        <option value="doctorado" <?php selected($grado_escolaridad_actual, 'doctorado'); ?>>Doctorado
                        </option>
                    </select>
                </div>
                <div>
                    <label for="estado_civil">Estado Civil</label>
                    <select id="estado_civil" name="estado_civil">
                        <option value="">Seleccione su estado civil</option>
                        <option value="soltero" <?php selected($estado_civil_actual, 'soltero'); ?>>Soltero/a</option>
                        <option value="casado" <?php selected($estado_civil_actual, 'casado'); ?>>Casado/a</option>
                        <option value="divorciado" <?php selected($estado_civil_actual, 'divorciado'); ?>>Divorciado/a
                        </option>
                        <option value="viudo" <?php selected($estado_civil_actual, 'viudo'); ?>>Viudo/a</option>
                    </select>
                </div>
            </div>

            <h1>DIRECCIÓN</h1>

            <div class="contenedor detres">
                <div>
                    <label for="calle">Calle</label>
                    <input type="text" name="calle" value="<?php echo esc_attr($calle_actual); ?>">
                </div>

                <div>
                    <label for="numero_interiorexterior">Número interior/exterior</label>
                    <input type="text" name="numero_interiorexterior"
                        value="<?php echo esc_attr($numero_interiorexterior_actual); ?>">
                </div>

                <div>
                    <label for="colonia">Colonia</label>
                    <input type="text" name="colonia" value="<?php echo esc_attr($colonia_actual); ?>">
                </div>
            </div>

            <div class="contenedor detres">
                <div>
                    <label for="codigo_postal">Código Postal</label>
                    <input type="text" name="codigo_postal" value="<?php echo esc_attr($codigo_postal_actual); ?>">
                </div>

                <div>
                    <label for="municipiociudad">Municipio/Ciudad</label>
                    <input type="text" name="municipiociudad" value="<?php echo esc_attr($municipiociudad_actual); ?>">
                </div>

                <div>
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" value="<?php echo esc_attr($estado_actual); ?>">
                </div>
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
                    <label for="telefono_celular">Teléfono Celular</label>
                    <input type="text" name="telefono_celular" value="<?php echo esc_attr($telefono_celular_actual); ?>">
                </div>

                <div>
                    <label for="telefono_fijo">Teléfono Fijo</label>
                    <input type="text" name="telefono_fijo" value="<?php echo esc_attr($telefono_fijo_actual); ?>">
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
                <input type="radio" name="pregunta1" value="Sí requiero algún apoyo o ajuste razonable."
                    <?php checked($pregunta1_actual, 'Sí requiero algún apoyo o ajuste razonable.'); ?>
                    onclick="mostrarSegundaPregunta(true)">
                Sí requiero algún apoyo o ajuste razonable.
            </label><br>

            <label>
                <input type="radio" name="pregunta1" value="No gracias, no necesito apoyos."
                    <?php checked($pregunta1_actual, 'No gracias, no necesito apoyos.'); ?>
                    onclick="mostrarSegundaPregunta(false)">
                No gracias, no necesito apoyos.
            </label><br>

            <div id="segundaPregunta" style="display: none;">
                <p>¿Qué tipo de apoyo o ajuste requieres para tu proceso de selección?</p>

                <label>
                    <input type="radio" name="pregunta2"
                        value="Uso de notas escritas mediante libreta o pizarra de comunicación."
                        <?php checked($pregunta2_actual, 'Uso de notas escritas mediante libreta o pizarra de comunicación.'); ?>>
                    Uso de notas escritas mediante libreta o pizarra de comunicación.
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Intérprete de LSM (lengua de señas mexicana)."
                        <?php checked($pregunta2_actual, 'Intérprete de LSM (lengua de señas mexicana).'); ?>>
                    Intérprete de LSM (lengua de señas mexicana).
                </label><br>

                <label>
                    <input type="radio" name="pregunta2"
                        value="Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.)."
                        <?php checked($pregunta2_actual, 'Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.).'); ?>>
                    Rutas accesibles para los desplazamientos (rampas, sitio de trabajo ubicado en primer piso, etc.).
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Poder tomar asiento con frecuencia."
                        <?php checked($pregunta2_actual, 'Poder tomar asiento con frecuencia.'); ?>>
                    Poder tomar asiento con frecuencia.
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Magnificadores de pantalla o lupa portátil."
                        <?php checked($pregunta2_actual, 'Magnificadores de pantalla o lupa portátil.'); ?>>
                    Magnificadores de pantalla o lupa portátil.
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Uso de lector de pantalla."
                        <?php checked($pregunta2_actual, 'Uso de lector de pantalla.'); ?>>
                    Uso de lector de pantalla.
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Control de estímulos sonoros, como aislamiento de ruido."
                        <?php checked($pregunta2_actual, 'Control de estímulos sonoros, como aislamiento de ruido.'); ?>>
                    Control de estímulos sonoros, como aislamiento de ruido.
                </label><br>

                <label>
                    <input type="radio" name="pregunta2" value="Otro.">
                    Otro.
                </label><br>

                <div id="cajaTextoOtro" style="display: none; margin-top: 10px;">
                    <label for="otro_detalle">Por favor, especifique:</label><br>
                    <textarea name="otro_detalle" id="otro_detalle" rows="4"
                        cols="50"><?php echo esc_textarea($otroar2_actual); ?></textarea>
                </div>
            </div>

        </div>

        <button type="submit" class="act-datos">ACTUALIZAR DATOS</button>

    </form>

    <!-- Botón de Cerrar Sesión -->
    <form method="POST" action="<?php echo wp_logout_url(home_url()); ?>">
        <button type="submit" class="cerrar-sesion">CERRAR SESIÓN</button>
    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar el mensaje de éxito por 5 segundos.
    const mensaje = document.getElementById('mensajeExito');
    if (mensaje) {
        mensaje.style.display = 'block';
        setTimeout(() => {
            mensaje.style.display = 'none';
        }, 5000); // 5 segundos
    }

    // Verificar si "Sí requiero algún apoyo o ajuste razonable." está seleccionado
    const pregunta1Actual = "<?php echo esc_js($pregunta1_actual); ?>";
    if (pregunta1Actual === "Sí requiero algún apoyo o ajuste razonable.") {
        mostrarSegundaPregunta(true);
    }

    // Verificar si "Otro." está seleccionado en pregunta2 para mostrar la caja de texto
    const pregunta2Actual = "<?php echo esc_js($pregunta2_actual); ?>";
    if (pregunta2Actual === "Otro.") {
        document.getElementById("cajaTextoOtro").style.display = "block";
        const otroRadio = document.querySelector('input[name="pregunta2"][value="Otro."]');
        if (otroRadio) {
            otroRadio.checked = true;
        }
    }

    // Evento de cambio para mostrar/ocultar la caja de texto "Otro" en la segunda pregunta
    document.querySelectorAll('input[name="pregunta2"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cajaTextoOtro = document.getElementById("cajaTextoOtro");
            cajaTextoOtro.style.display = this.value === "Otro." ? "block" : "none";
        });
    });
});

// Función para mostrar u ocultar la segunda pregunta
function mostrarSegundaPregunta(mostrar) {
    const segundaPregunta = document.getElementById("segundaPregunta");
    if (segundaPregunta) {
        segundaPregunta.style.display = mostrar ? "block" : "none";
    }
}
</script>


<?php  get_footer(); ?>
</body>

</html>