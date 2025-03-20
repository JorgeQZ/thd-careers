<?php
/**
 * Template Name: 404 Page
 */

get_header(); ?>
<script>
   if (window.top !== window.self) {
        const allowedOrigin = window.location.origin;
        if (new URL(window.self.location).origin === allowedOrigin) {
            window.top.location.replace(window.self.location.href);
        } else {
            console.warn("Intento de redirección a un dominio no autorizado bloqueado.");
        }
    }
</script>
<div class="page-404">
    <div class="container">
        <h1 class="error-title">404</h1>
        <p class="error-message">Lo sentimos, no pudimos encontrar la página que buscas.</p>
        <p class="error-description">Es posible que la página haya sido movida, eliminada o que nunca haya existido.</p>

        <div class="error-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Volver al inicio</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Regresar</a>
        </div>
    </div>
</div>
<?php get_footer(); ?>
