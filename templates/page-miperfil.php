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
    <!-- Botón de Cerrar Sesión -->
    <form method="POST" action="<?php echo wp_logout_url(home_url()); ?>">
        <button type="submit" class="cerrar-sesion">Cerrar sesión</button>
    </form>

    <?php if ($mensaje_exito): ?>
        <div class="mensaje-exito" id="mensajeExito"><?php echo esc_html($mensaje_exito); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <h1>Datos Personales</h1>

        <div class="contenedor">
            <label for="nombre">Nombre</label>
            <br>
            <input type="text" name="nombre" value="<?php echo esc_attr($nombre_actual); ?>">
        </div>

        <div class="contenedor contenedor-apellidos">
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

        <div class="contenedor">
            <label for="fecha_de_nacimiento">Fecha de nacimiento</label>
            <br>
            <input type="date" name="fecha_de_nacimiento" value="<?php echo esc_attr($fecha_de_nacimiento_actual); ?>">
        </div>

        <div class="contenedor">
            <label for="cv">CV</label>
            <br>
            <input type="file">
        </div>

        <h1>Dirección</h1>

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

        <div class="contenedor">
            <label for="correo2">Correo</label>
            <br>
            <input type="text" name="correo2" value="<?php echo esc_attr($correo2_actual); ?>">
        </div>

        <button type="submit">Actualizar Datos</button>

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