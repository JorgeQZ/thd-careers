<?php
try {
    if (function_exists('is_active_sidebar') && is_active_sidebar('sidebar-principal')) {
        dynamic_sidebar('sidebar-principal');
    }
    wp_footer();
} catch (Exception $e) {
    // Opcional: Registrar el error en los logs sin mostrarlo al usuario
    error_log('Error en footer.php: ' . $e->getMessage());
    // Opcional: Mostrar un mensaje genérico si es necesario
    echo '<!-- Ocurrió un error al cargar el footer -->';
}
?>

<?php
    if(is_user_logged_in(  )){
?>
    <style>
        .footer-fondo-s1{
            display: none;
        }
    </style>
<?php
    }
?>

</body>
</html>