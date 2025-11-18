<?php
/*
==================
    Ajax Search (Corregido)
==================
*/

// Inyectar JS
add_action('wp_footer', 'ajax_fetch');
function ajax_fetch() {
?>
<script src="<?php echo get_template_directory_uri() . '/js/purify.js' ?>"></script>

<script type="text/javascript">
function fetch(){
    jQuery.ajax({
        url: "<?php echo admin_url('admin-ajax.php'); ?>",
        type: "post",
        data: {
            action: "data_fetch",
            keyword: jQuery("#inputSearch").val()
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                let sanitized = DOMPurify.sanitize(response.data.html);
                jQuery("#contenedor-resultados").html(sanitized);
            } else {
                jQuery("#contenedor-resultados").text("No se encontraron resultados.");
            }
        },
        error: function() {
            jQuery("#contenedor-resultados").text("Error en la búsqueda.");
        }
    });
}
</script>
<?php
}


// AJAX
add_action('wp_ajax_data_fetch', 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch', 'data_fetch');

function data_fetch() {

    if (!defined('ABSPATH')) exit;

    if (!isset($_POST['keyword'])) {
        wp_send_json_error(['message' => 'No keyword provided']);
        exit;
    }

    $keyword = sanitize_text_field($_POST['keyword']);

    if (empty($keyword)) {
        wp_send_json_error(['message' => 'Empty keyword']);
        exit;
    }

    /* ------------------------------
        Normalización y Global Map
    ------------------------------ */

    if (!function_exists('norm')) {
        function norm($s) {
            return strtolower(trim((string)$s));
        }
    }

    // Construir global_map basado en TODOS los choices de ubicaciones
    $global_map = array();
    $all_locations = get_unique_locations();

    foreach ((array)$all_locations as $g) {
        if (is_array($g) && isset($g['value'], $g['label'])) {
            $v = (string)$g['value'];

            // Si viene código NNNN-N, extraerlo
            if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', $v, $mm)) {
                $v = $mm[1];
            }

            $k = norm($v);
            if ($k !== '') {
                $global_map[$k] = trim((string)$g['label']);
            }
        }
    }


    ob_start();
    echo '<div class="cont-result">';


    /* ------------------------------
        Consultas
    ------------------------------ */

    // Búsqueda general por título
    $titulo_query = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type'      => 'vacantes',
        'post_status'    => 'publish',
        's'              => esc_sql($keyword),
    ));

    // Búsqueda por campo personalizado
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

    // Búsqueda por taxonomía
    $terms = get_terms(array(
        'taxonomy'   => 'categorias_vacantes',
        'hide_empty' => false,
        'search'     => esc_sql($keyword),
    ));

    $term_slugs = wp_list_pluck($terms, 'slug');

    $taxonomies_query = (!empty($term_slugs)) ? new WP_Query(array(
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
    )) : null;


    /* ------------------------------
        Merge de resultados
    ------------------------------ */

    $merged_posts = array();

    if ($titulo_query->have_posts())        $merged_posts = array_merge($merged_posts, $titulo_query->posts);
    if ($customfields_query->have_posts()) $merged_posts = array_merge($merged_posts, $customfields_query->posts);
    if ($taxonomies_query && $taxonomies_query->have_posts())
        $merged_posts = array_merge($merged_posts, $taxonomies_query->posts);


    if (!empty($merged_posts)) {

        foreach ($merged_posts as $post) {
            setup_postdata($post);

            /* ------------------------------------------
                Resolución CORRECTA del label de ubicación
            -------------------------------------------- */

            $ubicacion = get_field("ubicacion", $post->ID);
            $label = "";
            $value = "";

            // ACF retorna array
            if (is_array($ubicacion)) {
                $label = (string)($ubicacion['label'] ?? '');
                $value = (string)($ubicacion['value'] ?? '');
            } elseif (is_string($ubicacion) || is_numeric($ubicacion)) {
                $value = (string)$ubicacion;
            }

            // Detectar código tipo NNNN-N
            $source = $value !== '' ? $value : $label;

            if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', (string)$source, $m)) {
                $ck = norm($m[1]);
                if (!empty($global_map[$ck])) {
                    $label = $global_map[$ck];
                }
            }

            // Fallback usando choices del ACF
            $fobj = get_field_object("ubicacion", $post->ID);
            $choices = (is_array($fobj) && isset($fobj['choices'])) ? $fobj['choices'] : array();

            if (($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) && $value !== '') {
                if (isset($choices[$value])) {
                    $label = trim((string)$choices[$value]);
                }
            }

            // Último fallback seguro
            if ($label === '') {
                $label = $value;
            }

            // Normalizar visual
            $ubicacion_label = function_exists('mb_convert_case')
                ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8')
                : ucwords(strtolower($label));

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
        <div class="resultado"><p>No se encontraron resultados.</p></div>
        <?php
    }

    echo '</div>';

    $output = ob_get_clean();
    wp_send_json_success(['html' => $output]);
    exit;
}
