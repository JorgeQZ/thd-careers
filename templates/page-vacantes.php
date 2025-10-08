<?php
/**
 * Template Name: Vacantes
 */

get_header();

$tax_name = "categorias_vacantes";
$term = get_field($tax_name);
$term_name = '';
if ($term != '') {
    $term_name     = $term->name;
    $ubicaciones   = get_unique_locations_with_values($term->slug);
    $unique_titles = get_unique_vacantes_titles_by_taxonomy($term->slug);
} else {
    $unique_titles = get_unique_vacantes_titles();
    $ubicaciones   = get_unique_locations();
}

if (!function_exists('norm')) {
    function norm($s)
    {
        return strtolower(trim((string)$s));
    }
}

$loc_map = array();
foreach ((array) $ubicaciones as $u) {
    if (is_array($u) && isset($u['value'], $u['label'])) {
        $v = (string)$u['value'];
        if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', $v, $mm)) {
            $v = $mm[1];
        }
        $k = norm($v);
        if ($k !== '') {
            $loc_map[$k] = trim((string)$u['label']);
        }
    }
}

$global_map = array();
$all_locations = get_unique_locations();
foreach ((array) $all_locations as $g) {
    if (is_array($g) && isset($g['value'], $g['label'])) {
        $v = (string)$g['value'];
        if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', $v, $mm)) {
            $v = $mm[1];
        }
        $k = norm($v);
        if ($k !== '') {
            $global_map[$k] = trim((string)$g['label']);
        }
    } elseif (is_string($g) || is_numeric($g)) {
        $v = (string)$g;
        if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', $v, $mm)) {
            $k = norm($mm[1]);
            if ($k !== '') {
                $global_map[$k] = $v;
            }
        }
    }
}

$is_logged_in = is_user_logged_in();
?>
<div class="popup-cont" id="popup-login">
    <div class="container">
        <div class="close" id="close-login">+</div>
        <div class="title">Inicia sesión o <span>Regístrate</span></div>
        <div class="desc">Para poder postularte, es necesario que inicies sesión. Si aún no cuentas con una cuenta, puedes registrarte de manera rápida y sencilla. <br><br></div>
        <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
        <div class="error-message" style="color: red; margin-bottom: 10px;">Usuario o contraseña incorrectos. Intenta de nuevo.</div>
        <?php endif; ?>
        <div class="login-form">
            <form action="<?php echo esc_url(site_url('wp-login.php')); ?>" method="post">
                <input type="text" name="log" placeholder="Nombre de usuario o correo" required autocomplete="off">
                <input type="password" name="pwd" autocomplete="off" placeholder="Contraseña" required>
                <?php $redir = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : home_url('/'); ?>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($redir); ?>" />
                <br>
                <button type="submit" class="button_sub">Iniciar sesión</button>
            </form>
        </div>
        <hr>
        <div class="register-link">¿No tienes cuenta? <a href="<?php echo site_url('/login/'); ?>" class="button_sub">Regístrate aquí</a></div>
    </div>
</div>

<div class="header" style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container"><?php the_title();?></div>
</div>

<main>
    <div class="vacantes-cont">
        <div class="container">
            <?php the_content(); ?>

            <?php $slug = get_post_field('post_name', get_post());
if ($slug !== 'nuestras-vacantes' && $slug !== 'ver-todo') : ?>
            <div class="title">Nuestras <span>vacantes</span></div>
            <?php endif; ?>
            <p>Comienza realizando una búsqueda mediante palabras clave o ubicación para mostrar resultados</p>

            <div class="row <?php if (!is_user_logged_in()) {
                echo 'wide-column';
            }?>">

                <div class="input-search wide">
                    <input type="text" placeholder="Ingresa palabras clave del puesto" class="search-input">
                    <ul class="suggestions-list hidden">
                        <?php foreach ($unique_titles as $title) {
                            $tkey = norm($title);
                            echo '<li><label>';
                            echo '<input type="checkbox" name="title[]" id="'. esc_attr(sanitize_title($title)).'" value="'. esc_attr($tkey).'">';
                            echo '<span class="checkbox"></span>';
                            echo '<span class="text">' . esc_html($title) . '</span>';
                            echo '</label></li>';
                        } ?>
                    </ul>
                </div>

                <div class="input-search wide">
                    <input type="text" placeholder="Ingresa tu ubicación" class="search-input">
                    <?php if ($term != ''): ?>
                    <ul class="suggestions-list hidden">
                        <?php
                        $processed_values = array();
                        foreach ((array)$ubicaciones as $ubicacion) {
                            if (!is_array($ubicacion) || !isset($ubicacion['value'])) {
                                continue;
                            }
                            $value = (string)$ubicacion['value'];
                            $label = isset($ubicacion['label']) ? (string)$ubicacion['label'] : '';
                            if (preg_match('/^\d{3,5}-\d{1,3}$/', $value)) {
                                $k = norm($value);
                            } else {
                                $k = norm($label !== '' ? $label : $value);
                            }
                            if ($k === '' || in_array($k, $processed_values, true)) {
                                continue;
                            }
                            if ($label === '' || preg_match('/^\d{3,5}-\d{1,3}$/', $label)) {
                                if (!empty($loc_map[$k])) {
                                    $label = $loc_map[$k];
                                } elseif (!empty($global_map[$k])) {
                                    $label = $global_map[$k];
                                }
                            }
                            if ($label === '') {
                                $label = $value;
                            }
                            $label = function_exists('mb_convert_case') ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8') : ucwords(strtolower($label));
                            echo '<li><label>';
                            echo '<input type="checkbox" name="ubicacion[]" value="' . esc_attr($k) . '">';
                            echo '<span class="checkbox"></span>';
                            echo '<span class="text">' . esc_html($label) . '</span>';
                            echo '</label></li>';
                            $processed_values[] = $k;
                        }
?>
                    </ul>
                    <?php else: ?>
                    <ul class="suggestions-list hidden">
                        <?php
$processed_values = array();
                        foreach ((array)$ubicaciones as $ubicacion) {
                            if (is_array($ubicacion)) {
                                $label_raw = isset($ubicacion['label']) ? (string)$ubicacion['label'] : '';
                                $value_raw = isset($ubicacion['value']) ? (string)$ubicacion['value'] : '';
                            } elseif (is_string($ubicacion) || is_numeric($ubicacion)) {
                                $label_raw = (string)$ubicacion;
                                $value_raw = (string)$ubicacion;
                            } else {
                                continue;
                            }
                            $label_raw = trim(wp_strip_all_tags($label_raw));
                            $value_raw = trim((string)$value_raw);
                            if (preg_match('/^\d{3,5}-\d{1,3}$/', $value_raw)) {
                                $k = norm($value_raw);
                            } else {
                                $k = norm($label_raw !== '' ? $label_raw : $value_raw);
                            }
                            if ($k === '' || in_array($k, $processed_values, true)) {
                                continue;
                            }
                            $human = $label_raw !== '' ? $label_raw : $value_raw;
                            if (preg_match('/^\d{3,5}-\d{1,3}$/', $value_raw)) {
                                if (!empty($loc_map[$k])) {
                                    $human = $loc_map[$k];
                                } elseif (!empty($global_map[$k])) {
                                    $human = $global_map[$k];
                                }
                            }
                            $ubicacion_label = function_exists('mb_convert_case') ? mb_convert_case($human, MB_CASE_TITLE, 'UTF-8') : ucwords(strtolower($human));
                            echo '<li><label>';
                            echo '<input type="checkbox" name="ubicacion[]" value="' . esc_attr($k) . '">';
                            echo '<span class="checkbox"></span>';
                            echo '<span class="text">' . esc_html($ubicacion_label) . '</span>';
                            echo '</label></li>';
                            $processed_values[] = $k;
                        }
                        ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="columns">
                <div class="column <?php if (!is_user_logged_in()) {
                    echo 'wide-column';
                }?>">
                    <?php
                    $args = array(
                        'post_type'   => 'vacantes',
                        'post_status' => 'publish',
                        'order'       => 'ASC',
                        'orderby'     => 'title',
                    );
if (!empty($term) && !empty($term->slug)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => $tax_name,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ),
    );
}
$query = new WP_Query($args);

if ($query->have_posts()):
    ?>
                    <ul class="list job-list">
                        <?php
        while ($query->have_posts()):
            $query->the_post();

            $raw   = get_field('ubicacion', get_the_ID());
            $label = '';
            $value = '';

            if (is_array($raw)) {
                $label = (string)($raw['label'] ?? '');
                $value = (string)($raw['value'] ?? '');
            } elseif (is_string($raw) || is_numeric($raw)) {
                $value = (string)$raw;
            }

            $source = $value !== '' ? $value : $label;
            if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', (string)$source, $m)) {
                $ck = norm($m[1]);
                if (!empty($loc_map[$ck])) {
                    $label = $loc_map[$ck];
                } elseif (!empty($global_map[$ck])) {
                    $label = $global_map[$ck];
                }
                $loc_key = $ck;
            } else {
                $loc_key = norm($label !== '' ? $label : $value);
            }

            $fobj    = function_exists('get_field_object') ? get_field_object('ubicacion', get_the_ID()) : null;
            $choices = (is_array($fobj) && isset($fobj['choices']) && is_array($fobj['choices'])) ? $fobj['choices'] : array();

            if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                if ($value !== '' && isset($choices[$value]) && is_string($choices[$value])) {
                    $label = trim((string)$choices[$value]);
                }
            }

            if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                $guess = preg_replace('/^\s*\d+(?:-\d+)?\s*(?:[:\-\|\x{2013}\x{2014}])?\s*/u', '', (string)$value);
                $guess = is_string($guess) ? trim($guess) : (string)$value;
                if ($guess !== '') {
                    $label = $guess;
                }
            }

            if ($label === '') {
                $label = (string)$value;
            }

            $display = function_exists('mb_convert_case') ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8') : ucwords(strtolower($label));
            $title_key = norm(get_the_title());
            ?>
                            <li class="item" data-id="<?php echo esc_attr(get_the_ID()); ?>"
                                data-tienda="<?php echo esc_attr(get_field('extra_data_data_tienda')); ?>"
                                data-title="<?php echo esc_attr(get_the_title()); ?>"
                                data-title-key="<?php echo esc_attr($title_key); ?>"
                                data-loc="<?php echo esc_attr($loc_key); ?>">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="img">
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/imgs/logo-thd.jpg'); ?>" alt="">
                                    </div>
                                    <div class="desc">
                                        <div class="job-title"><?php the_title(); ?></div>
                                        <div class="icon-cont">
                                            <img src="<?php echo esc_url(get_theme_file_uri('imgs/pin-de-ubicacion-2.png')); ?>" alt="Location">
                                            <div class="text"><?php echo esc_html($display); ?></div>
                                        </div>
                                    </div>
                                    <div class="fav">
                                        <div class="img">
                                            <img src="<?php echo esc_url(get_theme_file_uri('imgs/me-gusta-2.png')); ?>" alt="Like">
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php
                    endif;
wp_reset_postdata();
?>
                </div>

                <?php if (is_user_logged_in()): ?>
                <div class="column">
                    <div class="saved-jobs">
                        <div class="title">Vacantes de interés</div>
                        <div class="desc">Haz clic en el botón para ver las vacantes de tu interés guardadas.</div>
                        <a href="<?php echo home_url().'/vacantes-de-interes/' ?>" class="button">Ir a mis vacantes</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const list = document.querySelector(".list.job-list");

  if (list) {
    list.addEventListener("click", function(e) {
      const img = e.target.closest(".fav img");
      if (!img) return;
      e.preventDefault();
      e.stopPropagation();
      img.classList.toggle("active");
    });
  }

  function selected(name) {
    return new Set(Array.from(document.querySelectorAll('input[name="'+name+'"]:checked')).map(el => (el.value || '').toLowerCase()));
  }

  function applyFilter() {
    if (!list) return;
    const locs = selected('ubicacion[]');
    const titles = selected('title[]');
    const items = list.querySelectorAll("li.item");
    items.forEach(li => {
      const lk = (li.dataset.loc || '').toLowerCase();
      const tk = (li.dataset.titleKey || '').toLowerCase();
      const okLoc = locs.size === 0 || locs.has(lk);
      const okTit = titles.size === 0 || titles.has(tk);
      li.style.display = (okLoc && okTit) ? "" : "none";
    });
  }

  document.addEventListener("change", function(e){
    if (!e.target) return;
    if (e.target.name === "ubicacion[]" || e.target.name === "title[]") applyFilter();
  });

  applyFilter();
});
</script>

<?php get_footer(); ?>
