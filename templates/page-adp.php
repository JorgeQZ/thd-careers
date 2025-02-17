<?php
/*
Template Name: ADP
*/

get_header();
?>

<body>

    <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    ?>

    <?php  get_footer(); ?>
</body>