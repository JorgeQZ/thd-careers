<?php
/**
 * Template Name: Frontpage
 */
get_header();
?>



<div class="hero-banner">
    <video autoplay loop muted playsinline>
        <source src="<?php echo get_template_directory_uri(  ).'/video/HOME2.webm'; ?>" type="video/mp4">
        <source src="video.webm" type="video/webm">
        Your browser does not support the video tag.
    </video>
    <div class="hero-content">
        <div class="title">Somos sangre <span>naranja</span></div>

        <form class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
            <input type="text" name="s" placeholder="Ingresa palabras clave del puesto" value="<?php echo get_search_query(); ?>" />
            <!--<input type="text" name="custom_field" placeholder="Buscar por campo personalizado" />-->
            <select name="ubicacion_key">
                <option value="">Ingresa tu ubicación</option>
            <?php 
                $field = get_field_object('ubicacion', 687); // Nombre del campo ACF

                // Verificamos si hemos recibido el objeto correctamente
                if ($field) {
                    $options = $field['choices']; // Las opciones del dropdown
                    // Mostrar las opciones
                    if ($options) {
                        foreach ($options as $key => $value) {
                            echo '<option value="'.esc_html($key).'">'.esc_html($value).'</option>';
                        }
                    } else {
                        echo 'No hay opciones disponibles en este campo.';
                    }
                } else {
                    echo 'No se encontró el campo.';
                }
                $haha = esc_html($value);
            ?>
            </select>
            <input type="hidden" name="ubicacion" value="">
            <button type="submit">Buscar vacantes</button>
        </form>

        <!--
        <div class="search-form">
            <input type="text" placeholder="Ingresa palabra clave del puesto">
            <input type="text" placeholder="Ingresa tu ubicación">
            <input type="submit" value="Buscar vacantes">
        </div>
        -->
    </div>
</div>

<?php
the_content( );
?>

<?php get_footer(); ?>

<script>
    $ = jQuery;
    $(document).ready(function(){
        $('.hero-banner .hero-content .search-form select').on('change', function() {
            var ubi = $(this).find("option:selected").text(); 
            $(".hero-banner .hero-content .search-form input[type='hidden']").val(ubi);
        });
    });
</script>