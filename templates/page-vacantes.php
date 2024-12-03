<?php
/**
 * Template Name: Vacantes
 */

get_header();
the_content();

echo get_the_post_thumbnail();
$tax_name = "categorias_vacantes";
$term = get_field($tax_name);

$args = array(
    'post_type' => 'vacantes',
    'order' => 'DESC',
    'orderby' => 'date',
    'tax_query' => array(
        array(
            'taxonomy' => $tax_name,
            'field'    => 'slug',
            'terms'    => $term->slug
        ),
    ),
);
$query = new WP_Query($args);

if($query->have_posts()):
?>

<div>
    <ul>
        <?php
            while($query->have_posts()):
                $query->the_post();
        ?>

        <li>
            <a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a>
        </li>

        <?php
            endwhile;
        ?>
    </ul>
</div>

<?php
else:
    echo 'No hay vacantes en esta categoría';
endif;
?>

<?php get_footer(); ?>