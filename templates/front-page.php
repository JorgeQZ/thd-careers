<?php
/**
 * Template Name: Frontpage
 */
get_header();
?>
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