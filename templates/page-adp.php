<?php
/*
Template Name: ADP
*/

get_header();
?>

<body>

    <div class="contenedor-adp">

        <?php
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
        ?>

    </div>

    <?php  get_footer(); ?>
</body>