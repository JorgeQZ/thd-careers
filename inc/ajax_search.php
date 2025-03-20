<?php
/*
==================
    Ajax Search
======================
*/


// add the ajax fetch js
add_action( 'wp_footer', 'ajax_fetch' );
function ajax_fetch() {
?>
<script src="<?php echo get_template_directory_uri(  ).'/js/purify.js'?>"></script>

<script type="text/javascript">
function fetch(){
    jQuery.ajax({
        url: '<?php echo admin_url('admin-ajax.php'); ?>',
        type: 'post',
        data: {
            action: 'data_fetch',
            keyword: jQuery('#inputSearch').val()
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                // Usar DOMPurify para evitar XSS antes de insertar
                let sanitizedHtml = DOMPurify.sanitize(response.data.html);
                jQuery("#contenedor-resultados").html(sanitizedHtml);
            } else {
                jQuery("#contenedor-resultados").text("No se encontraron resultados.");
            }
        },
        error: function () {
            jQuery("#contenedor-resultados").text("Error en la búsqueda.");
        },
    });

}
</script>

<?php
}


// the ajax function
add_action('wp_ajax_data_fetch' , 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch','data_fetch');

function data_fetch() {
    // Asegurar que WordPress ha sido cargado
    if (!defined('ABSPATH')) {
        exit;
    }

    // Verificar si el input está presente
    if (!isset($_POST['keyword'])) {
        wp_send_json_error(['message' => 'No keyword provided']);
        exit;
    }

    // Sanitizar el input
    $keyword = sanitize_text_field($_POST['keyword']);

    // Evitar consultas si el keyword está vacío
    if (empty($keyword)) {
        wp_send_json_error(['message' => 'Empty keyword']);
        exit;
    }

    ob_start(); // Iniciar buffer de salida para evitar problemas de eco accidental
    echo '<div class="cont-result">';

    // Consultas seguras a la base de datos
    $args = array(
        'posts_per_page' => -1,
        'post_type'      => 'vacantes',
        'post_status'    => 'publish',
        's'             => esc_sql($keyword),
    );

    $titulo_query = new WP_Query($args);

    // Buscar en los campos personalizados
    $customfields_query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type'      => 'vacantes',
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'codigo_de_vacante',
                'value'   => esc_sql($keyword),
                'compare' => 'LIKE'
            ),
        ),
    ));

    // Buscar en las taxonomías
    $terms = get_terms(array(
        'taxonomy'   => 'categorias_vacantes',
        'hide_empty' => false,
        'search'     => esc_sql($keyword),
    ));

    $term_slugs = wp_list_pluck($terms, 'slug');

    $taxonomies_query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type'      => 'vacantes',
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'categorias_vacantes',
                'field'    => 'slug',
                'terms'    => $term_slugs,
                'operator' => 'IN',
            ),
        ),
    ));

    // Si hay resultados en alguna consulta
    if ($titulo_query->have_posts() || $customfields_query->have_posts() || $taxonomies_query->have_posts()) {
        $merged_posts = array_merge($titulo_query->posts, $customfields_query->posts, $taxonomies_query->posts);

        foreach ($merged_posts as $post) {
            setup_postdata($post);

            // Obtener y sanitizar la ubicación
            $ubicacion = get_field("ubicacion", $post);
            $ubicacion_label = isset($ubicacion['label']) ? esc_html($ubicacion['label']) : '';

            ?>
            <div class="resultado">
                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                    <h2><?php echo esc_html(get_the_title($post)); ?></h2>
                    <span><?php echo esc_html($ubicacion_label); ?></span>
                </a>
            </div>
            <?php
        }

        wp_reset_postdata();
    } else {
        ?>
        <div class="resultado">
            <p>No se encontraron resultados.</p>
        </div>
        <?php
    }

    echo '</div>';

    $output = ob_get_clean(); // Capturar la salida y almacenarla en una variable
    wp_send_json_success(['html' => $output]); // Enviar respuesta en JSON

    exit;
}
