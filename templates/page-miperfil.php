<?php
/*
Template Name: Mi Perfil
*/

// Incluir el archivo con la lógica del perfil.
include get_template_directory() . '/inc/users/miperfil.php';

// Verificar si hay un mensaje de éxito en la sesión.
$mensaje_exito = '';
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']); // Limpiar mensaje después de mostrarlo.
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/miperfil.css">
</head>

<body>

    <?php if ($mensaje_exito): ?>
        <div class="mensaje-exito" id="mensajeExito"><?php echo esc_html($mensaje_exito); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <h1>Datos Generales</h1>

        <div class="contenedor">
            <label for="nombre">Nombre</label>
            <br>
            <input type="text" name="nombre" value="<?php echo esc_attr($nombre_actual); ?>">
        </div>

        <div class="contenedor contenedor-doscampos">
            <div>
                <label for="apellido_paterno">Apellido Paterno</label>
                <br>
                <input type="text" name="apellido_paterno" value="<?php echo esc_attr($apellido_paterno_actual); ?>">
            </div>
            <div>
                <label for="apellido_materno">Apellido Materno</label>
                <br>
                <input type="text" name="apellido_materno" value="<?php echo esc_attr($apellido_materno_actual); ?>">
            </div>
        </div>

        <div class="contenedor">
            <label for="correo">Correo</label>
            <br>
            <input type="email" name="correo" value="<?php echo esc_attr($correo_actual); ?>">
        </div>

        <div class="contenedor contenedor-doscampos">
            <div>
                <label for="fecha_de_nacimiento">Fecha de nacimiento</label>
                <br>
                <input type="date" name="fecha_de_nacimiento" value="<?php echo esc_attr($fecha_de_nacimiento_actual); ?>">
            </div>
            <div>
                <label for="nacionalidad">Nacionalidad</label>
                <br>
                <input type="text" name="nacionalidad" value="<?php echo esc_attr($nacionalidad_actual); ?>">
            </div>
        </div>

        <div class="contenedor">
            <label for="cv">CV</label>
            <br>
            <input type="file">
        </div>

        <div class="contenedor contenedor-doscampos">
            <div class="contenedor">
                <label for="grado_escolaridad">Elige tu grado de escolaridad:</label>
                <br>
                <select id="grado_escolaridad" name="grado_escolaridad">
                    <option value="">Escolaridad - Grado de estudios</option>
                    <option value="primaria" <?php selected($grado_escolaridad_actual, 'primaria'); ?>>Primaria</option>
                    <option value="secundaria" <?php selected($grado_escolaridad_actual, 'secundaria'); ?>>Secundaria</option>
                    <option value="preparatoria" <?php selected($grado_escolaridad_actual, 'preparatoria'); ?>>Preparatoria</option>
                    <option value="carrera_tecnica" <?php selected($grado_escolaridad_actual, 'carrera_tecnica'); ?>>Carrera técnica</option>
                    <option value="licenciatura" <?php selected($grado_escolaridad_actual, 'licenciatura'); ?>>Licenciatura</option>
                    <option value="posgrado" <?php selected($grado_escolaridad_actual, 'posgrado'); ?>>Posgrado</option>
                    <option value="maestria" <?php selected($grado_escolaridad_actual, 'maestria'); ?>>Maestría</option>
                    <option value="doctorado" <?php selected($grado_escolaridad_actual, 'doctorado'); ?>>Doctorado</option>
                </select>
            </div>

            <div class="contenedor">
                <label for="estado_civil">Estado Civil</label>
                <br>
                <select id="estado_civil" name="estado_civil">
                    <option value="">Seleccione su estado civil</option>
                    <option value="soltero" <?php selected($estado_civil_actual, 'soltero'); ?>>Soltero/a</option>
                    <option value="casado" <?php selected($estado_civil_actual, 'casado'); ?>>Casado/a</option>
                    <option value="divorciado" <?php selected($estado_civil_actual, 'divorciado'); ?>>Divorciado/a</option>
                    <option value="viudo" <?php selected($estado_civil_actual, 'viudo'); ?>>Viudo/a</option>
                    <!-- <option value="union_libre" <?php selected($estado_civil_actual, 'union_libre'); ?>>Unión libre</option>
                    <option value="separado" <?php selected($estado_civil_actual, 'separado'); ?>>Separado/a</option>
                    <option value="comprometido" <?php selected($estado_civil_actual, 'comprometido'); ?>>Comprometido/a</option>
                    <option value="pareja_de_hecho" <?php selected($estado_civil_actual, 'pareja_de_hecho'); ?>>Pareja de hecho</option> -->
                </select>
            </div>
        </div>

        <h1>Dirección</h1>

        <div class="contenedor contenedor-doscampos">
            <div class="contenedor">
                <label for="calle">Calle</label>
                <br>
                <input type="text" name="calle" value="<?php echo esc_attr($calle_actual); ?>">
            </div>

            <div class="contenedor">
                <label for="numero_interiorexterior">Número interior/exterior</label>
                <br>
                <input type="text" name="numero_interiorexterior" value="<?php echo esc_attr($numero_interiorexterior_actual); ?>">
            </div>
        </div>

        <div class="contenedor contenedor-doscampos">
            <div class="contenedor">
                <label for="colonia">Colonia</label>
                <br>
                <input type="text" name="colonia" value="<?php echo esc_attr($colonia_actual); ?>">
            </div>

            <div class="contenedor">
                <label for="codigo_postal">Código Postal</label>
                <br>
                <input type="text" name="codigo_postal" value="<?php echo esc_attr($codigo_postal_actual); ?>">
            </div>
        </div>

        <div class="contenedor contenedor-doscampos">
            <div class="contenedor">
                <label for="municipiociudad">Municipio/Ciudad</label>
                <br>
                <input type="text" name="municipiociudad" value="<?php echo esc_attr($municipiociudad_actual); ?>">
            </div>

            <div class="contenedor">
                <label for="estado">Estado</label>
                <br>
                <input type="text" name="estado" value="<?php echo esc_attr($estado_actual); ?>">
            </div>
        </div>

        <h1>Información de contacto</h1>

        <div class="contenedor">
            <label for="linkedin">LinkedIn</label>
            <br>
            <input type="url" name="linkedin" value="<?php echo esc_attr($linkedin_actual); ?>">
        </div>

        <div class="contenedor">
            <label for="facebook">Facebook</label>
            <br>
            <input type="url" name="facebook" value="<?php echo esc_attr($facebook_actual); ?>">
        </div>

        <div class="contenedor">
            <label for="instagram">Instagram</label>
            <br>
            <input type="url" name="instagram" value="<?php echo esc_attr($instagram_actual); ?>">
        </div>

        <div class="contenedor contenedor-doscampos">
            <div class="contenedor">
                <label for="telefono_celular">Teléfono Celular</label>
                <br>
                <input type="text" name="telefono_celular" value="<?php echo esc_attr($telefono_celular_actual); ?>">
            </div>

            <div class="contenedor">
                <label for="telefono_fijo">Teléfono Fijo</label>
                <br>
                <input type="text" name="telefono_fijo" value="<?php echo esc_attr($telefono_fijo_actual); ?>">
            </div>
        </div>

        <div class="contenedor">
            <label for="correo2">Correo</label>
            <br>
            <input type="text" name="correo2" value="<?php echo esc_attr($correo2_actual); ?>">
        </div>

        <button type="submit" class="act-datos">Actualizar Datos</button>

    </form>

    <!-- Botón de Cerrar Sesión -->
    <form method="POST" action="<?php echo wp_logout_url(home_url()); ?>">
        <button type="submit" class="cerrar-sesion">Cerrar sesión</button>
    </form>

    <script>
        // Mostrar el mensaje por 5 segundos.
        document.addEventListener('DOMContentLoaded', function () {
            const mensaje = document.getElementById('mensajeExito');
            if (mensaje) {
                mensaje.style.display = 'block';
                setTimeout(() => {
                    mensaje.style.display = 'none';
                }, 5000); // 5 segundos
            }
        });
    </script>

</body>

</html>