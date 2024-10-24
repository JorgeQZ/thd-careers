<?php
get_header();
?>

<style>
    header{
        display: none;
    }
</style>

<?php
the_title();
the_content();
?>

<?php include get_template_directory() . '/templates/page-postulaciones.php'; ?>

<?php
get_footer();
?>