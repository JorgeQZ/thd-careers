<?php
/**
 * Template Name: Frontpage
 */
get_header();
?>



<div class="hero-banner">
    <video autoplay loop muted playsinline>
        <source src="<?php echo get_template_directory_uri(  ).'/video/HOME.webm'; ?>" type="video/mp4">
        <source src="video.webm" type="video/webm">
        Your browser does not support the video tag.
    </video>
    <div class="hero-content">
        <div class="title">Somos sangre <span>naranja</span></div>
        <div class="search-form">
            <input type="text" placeholder="Ingresa palabra clave del puesto">
            <input type="text" placeholder="Ingresa tu ubicación">
            <input type="submit" value="Buscar vacantes">
        </div>
    </div>
</div>
<?php
the_content( );
?>

<?php get_footer(); ?>