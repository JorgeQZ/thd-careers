<?php
/**
 * Template Name: Corrección correos en vacantes (v5 - correo only)
 * Description: Corrige extra_data[data_correo] a partir de extra_data[data_tienda] guardando SOLO el subcampo de correo (sin tocar el grupo).
 */

if (!defined('ABSPATH')) exit;

// —— Opcional: activar logs en UI con ?debug=1
$THD_DEBUG = isset($_GET['debug']) ? true : false;

// ——— Seguridad
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die(esc_html__('No tienes permisos para acceder a esta herramienta.', 'thd'));
}

/** Utils */

function thd_get_admin_user_id(): ?int {
    $u = get_user_by('login', 'admin');
    return $u ? (int)$u->ID : null;
}

function thd_parse_num_tienda(?string $raw): ?string {
    if (!$raw) return null;
    if (preg_match('/(\d{3,5})/', $raw, $m)) return $m[1]; // ajusta rango si aplica
    $digits = preg_replace('/\D+/', '', $raw);
    return $digits !== '' ? $digits : null;
}

function thd_build_correo_from_tienda(string $num): string {
    $num = preg_replace('/\D+/', '', $num);
    return strtolower($num . '-humrec1@homedepot.com.mx');
}

/**
 * Devuelve arreglo extra_data; intenta ACF y luego metas extra_data_*.
 * (Sólo lectura; NO escribe el grupo.)
 */
function thd_get_extra_data(int $post_id): array {
    if (function_exists('get_field')) {
        $group = get_field('extra_data', $post_id);
        if (is_array($group) && !empty($group)) return $group;
    }
    $out = [];
    foreach (['data_tienda','data_distrito','data_correo'] as $k) {
        $v = get_post_meta($post_id, 'extra_data_'.$k, true);
        if ($v !== '' && $v !== null) $out[$k] = (string)$v;
    }
    return $out;
}

/**
 * Guarda SOLO el subcampo `data_correo`.
 * - Intenta update_field con la field key real via get_field_reference().
 * - Si no hay reference, intenta por nombre.
 * - Siempre sincroniza meta extra_data_data_correo.
 * Retorna true si el meta quedó con el valor deseado.
 */
function thd_update_correo_only(int $post_id, string $correo, bool $debug=false): bool {
    $logs = [];

    $ok_field = false;

    // ACF: por reference key si es posible
    if (function_exists('update_field')) {
        $ref_key = function_exists('get_field_reference') ? get_field_reference('data_correo', $post_id) : null;
        if ($ref_key) {
            $ok_field = (bool) update_field($ref_key, $correo, $post_id);
            if ($debug) $logs[] = 'update_field(sub key '.$ref_key.' => data_correo) => '.($ok_field?'OK':'FAIL');
        } else {
            // Intento por nombre (puede fallar si hay duplicados de nombre en otros grupos)
            $ok_field = (bool) update_field('data_correo', $correo, $post_id);
            if ($debug) $logs[] = 'update_field(sub name=data_correo) => '.($ok_field?'OK':'FAIL (sin reference key)');
        }
    } else {
        if ($debug) $logs[] = 'ACF no disponible: update_field() no existe';
    }

    // Refuerzo: meta directo
    update_post_meta($post_id, 'extra_data_data_correo', $correo);
    if ($debug) $logs[] = 'update_post_meta(extra_data_data_correo='.$correo.')';

    // Verificación por meta
    $saved = get_post_meta($post_id, 'extra_data_data_correo', true);
    $persisted = ((string)$saved === (string)$correo);

    if ($debug) {
        $logs[] = 'verify(meta extra_data_data_correo) => '.($persisted?'MATCH':'MISMATCH').' (saved="'.$saved.'" target="'.$correo.'")';
        set_query_var('thd_debug_logs', $logs);
    }

    // Consideramos éxito si el meta quedó bien (aunque update_field por nombre/key falle,
    // el valor práctico para WP/consultas ya queda disponible en meta).
    return $persisted;
}

/**
 * Candidata:
 * - post_type = vacantes
 * - autor ≠ admin
 * - extra_data_data_correo vacío/inexistente
 * - no _correo_skip
 */
function thd_is_candidate(int $post_id, ?int $admin_id): bool {
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'vacantes') return false;
    if ($admin_id && (int)$post->post_author === $admin_id) return false;
    if (get_post_meta($post_id, '_correo_skip', true)) return false;

    $meta_correo = get_post_meta($post_id, 'extra_data_data_correo', true);
    if (is_string($meta_correo) && trim($meta_correo) !== '') return false;

    $extra  = thd_get_extra_data($post_id);
    $correo = isset($extra['data_correo']) ? trim((string)$extra['data_correo']) : '';
    if ($correo !== '') return false;

    return true;
}

function thd_find_next_candidate(?int $admin_id): ?WP_Post {
    $mq = [
        'relation' => 'OR',
        ['key' => 'extra_data_data_correo', 'compare' => 'NOT EXISTS'],
        ['key' => 'extra_data_data_correo', 'value' => '', 'compare' => '='],
    ];
    $exclude_authors = [];
    if ($admin_id) $exclude_authors[] = $admin_id;

    $q = new WP_Query([
        'post_type'      => 'vacantes',
        'post_status'    => ['publish','pending','draft','future','private'],
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'meta_query'     => $mq,
        'author__not_in' => $exclude_authors,
        'no_found_rows'  => true,
    ]);

    if ($q->have_posts()) {
        foreach ($q->posts as $p) {
            if (thd_is_candidate($p->ID, $admin_id)) return $p;
        }
    }
    return null;
}

function thd_count_candidates(?int $admin_id): int {
    $mq = [
        'relation' => 'OR',
        ['key' => 'extra_data_data_correo', 'compare' => 'NOT EXISTS'],
        ['key' => 'extra_data_data_correo', 'value' => '', 'compare' => '='],
    ];
    $exclude_authors = [];
    if ($admin_id) $exclude_authors[] = $admin_id;

    $q = new WP_Query([
        'post_type'      => 'vacantes',
        'post_status'    => ['publish','pending','draft','future','private'],
        'posts_per_page' => 200,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'meta_query'     => $mq,
        'author__not_in' => $exclude_authors,
        'fields'         => 'ids',
        'no_found_rows'  => false,
    ]);

    $count = 0;
    if ($q->have_posts()) {
        foreach ($q->posts as $pid) if (thd_is_candidate($pid, $admin_id)) $count++;
        if (!empty($q->found_posts)) $count = max($count, (int)$q->found_posts);
    }
    return $count;
}

/**
 * Corrige una vacante y devuelve info rica para mensajes (sin tocar el grupo).
 */
function thd_fix_post_correo(int $post_id, bool $debug=false): array {
    $extra      = thd_get_extra_data($post_id);
    $tienda_raw = isset($extra['data_tienda']) ? (string)$extra['data_tienda'] : '';
    $title      = get_the_title($post_id) ?: ('#'.$post_id);

    $num = thd_parse_num_tienda($tienda_raw);
    if (!$num) {
        $msg = 'No se pudo extraer número de tienda desde <code>data_tienda</code> para <strong>'.esc_html($title).'</strong>'
             . ' (valor: <em>'.esc_html($tienda_raw ?: 'vacío').'</em>).';
        return [
            'ok'    => false,
            'msg'   => $msg,
            'id'    => $post_id,
            'title' => $title,
            'num'   => null,
            'correo'=> null,
            'reason'=> 'no_tienda',
        ];
    }

    $correo = thd_build_correo_from_tienda($num);

    // Guardar SOLO el subcampo + meta
    $ok = thd_update_correo_only($post_id, $correo, $debug);
    if ($ok) {
        $msg = 'Actualizado: <strong>' . esc_html($title) . '</strong> (Tienda <strong>' . esc_html($num) . '</strong>) → ' . esc_html($correo);
        return [
            'ok'    => true,
            'msg'   => $msg,
            'id'    => $post_id,
            'title' => $title,
            'num'   => $num,
            'correo'=> $correo,
        ];
    }

    $msg = 'No se pudo guardar <code>data_correo</code> (subcampo) para <strong>'.esc_html($title).'</strong>.';
    return [
        'ok'    => false,
        'msg'   => $msg,
        'id'    => $post_id,
        'title' => $title,
        'num'   => $num,
        'correo'=> $correo,
        'reason'=> 'save_fail',
    ];
}

function thd_skip_post(int $post_id): void {
    update_post_meta($post_id, '_correo_skip', 1);
}

/** UI */
get_header();

$admin_id = thd_get_admin_user_id();
$notice   = '';
$notice_t = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thd_correccion_action'])) {
    check_admin_referer('thd_correccion_correo');
    $action  = sanitize_text_field((string)($_POST['thd_correccion_action'] ?? ''));
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

    if ($action === 'fix_one' && $post_id > 0) {
        if (thd_is_candidate($post_id, $admin_id)) {
            $res = thd_fix_post_correo($post_id, $THD_DEBUG);
            $notice   = $res['msg'];                           // incluye título y motivo si falla
            $notice_t = $res['ok'] ? 'success' : 'error';
        } else {
            $notice   = 'El post ya no es candidato (quizá ya se actualizó o no cumple filtros).';
            $notice_t = 'warning';
        }
    } elseif ($action === 'skip_one' && $post_id > 0) {
        thd_skip_post($post_id);
        $notice   = 'Vacante marcada como “omitida”.';
        $notice_t = 'success';
    } elseif ($action === 'fix_batch') {
        $limit = isset($_POST['batch_limit']) ? max(1, (int)$_POST['batch_limit']) : 25;
        $done  = 0; $errs = 0;
        $updated = []; // elementos OK
        $errors  = []; // elementos con error (detalle)

        while ($done + $errs < $limit) {
            $next = thd_find_next_candidate($admin_id);
            if (!$next) break;

            $res = thd_fix_post_correo($next->ID, $THD_DEBUG);
            if ($res['ok']) {
                $done++;
                $updated[] = [
                    'id'    => $res['id'],
                    'title' => $res['title'],
                    'num'   => $res['num'],
                    'correo'=> $res['correo'],
                ];
            } else {
                $errs++;
                $skipped = false;
                if (isset($res['reason']) && $res['reason'] === 'no_tienda') {
                    thd_skip_post($next->ID);
                    $skipped = true;
                }
                $errors[] = [
                    'id'      => $res['id'],
                    'title'   => $res['title'],
                    'reason'  => $res['reason'] ?? 'unknown',
                    'msg'     => $res['msg'],
                    'skipped' => $skipped,
                ];
            }
        }

        // Lista de OK
        $ok_html = '';
        if (!empty($updated)) {
            $ok_html .= '<ul>';
            foreach ($updated as $u) {
                $edit_url = get_edit_post_link($u['id']);
                $title    = esc_html($u['title']);
                $num      = esc_html((string)$u['num']);
                $correo   = esc_html((string)$u['correo']);
                $link     = $edit_url ? '<a href="'.esc_url($edit_url).'" target="_blank" rel="noopener">'.$title.'</a>' : $title;
                $ok_html .= '<li>'.$link.' — Tienda <strong>'.$num.'</strong> → '.$correo.'</li>';
            }
            $ok_html .= '</ul>';
        }

        // Lista de errores
        $err_html = '';
        if (!empty($errors)) {
            $err_html .= '<ul>';
            foreach ($errors as $e) {
                $edit_url = get_edit_post_link($e['id']);
                $title    = esc_html($e['title']);
                $link     = $edit_url ? '<a href="'.esc_url($edit_url).'" target="_blank" rel="noopener">'.$title.'</a>' : $title;
                $tag      = ($e['skipped'] ? ' <em style="opacity:.8">[omitida]</em>' : '');
                $err_html .= '<li>'.$link.' — '.wp_kses_post($e['msg']).$tag.'</li>';
            }
            $err_html .= '</ul>';
        }

        // Mensaje final
        $notice   = 'Lote finalizado: <strong>' . $done . '</strong> actualizadas, <strong>' . $errs . '</strong> con error.';
        if ($ok_html)  $notice .= '<br><br><strong>Vacantes actualizadas:</strong>'.$ok_html;
        if ($err_html) $notice .= '<br><strong>Con errores:</strong>'.$err_html;

        $notice_t = ($errs === 0) ? 'success' : 'warning';
    }
}

// Datos UI
$total = thd_count_candidates($admin_id);
$next  = thd_find_next_candidate($admin_id);
$debug_logs = get_query_var('thd_debug_logs');

?>
<style>
.thd-wrap { max-width: 980px; margin: 24px auto; background:#fff; padding:24px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,.06); }
.thd-h1 { margin:0 0 16px; }
.thd-note { padding:12px 16px; border-radius:8px; margin-bottom:16px; }
.thd-note.success{ background:#e7f7ee; border:1px solid #b6ebcd; }
.thd-note.error  { background:#fdecea; border:1px solid #f5c6cb; }
.thd-note.warning{ background:#fff8e1; border:1px solid #ffe08a; }
.thd-note.info   { background:#eef5ff; border:1px solid #cfe0ff; }
.thd-card { border:1px solid #eee; border-radius:10px; padding:16px; margin-top:10px; }
.thd-row { display:flex; gap:16px; align-items:center; }
.thd-row>div { flex:1; }
.thd-actions { display:flex; gap:10px; margin-top:12px; }
.button-primary { background:#0073aa; border-color:#0073aa; color:#fff; padding:8px 14px; border-radius:6px; cursor:pointer; }
.button-secondary { background:#f3f4f6; border:1px solid #d1d5db; color:#111827; padding:8px 14px; border-radius:6px; cursor:pointer; }
.thd-small { font-size:12px; color:#666; }
.thd-mt { margin-top:12px; }
.thd-batch { margin-top:24px; padding:16px; border:1px dashed #ddd; border-radius:10px; }
.thd-pre { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size:12px; white-space:pre-wrap; background:#f8fafc; border:1px solid #e5e7eb; padding:10px; border-radius:8px; }
</style>

<div class="thd-wrap">
  <h1 class="thd-h1">Corrección de correos en Vacantes</h1>
  <p class="thd-small">Criterios: <strong>solo vacantes</strong> con meta <code>extra_data_data_correo</code> vacío o inexistente y cuyo autor <strong>no</strong> sea el usuario <code>admin</code>.</p>

  <?php if ($notice): ?>
    <div class="thd-note <?php echo esc_attr($notice_t); ?>">
      <?php echo wp_kses_post($notice); ?>
    </div>
  <?php endif; ?>

  <?php if ($THD_DEBUG && !empty($debug_logs) && is_array($debug_logs)): ?>
    <div class="thd-note info">
      <strong>Debug:</strong>
      <div class="thd-pre"><?php echo esc_html(implode("\n", $debug_logs)); ?></div>
    </div>
  <?php endif; ?>

  <p><strong>Pendientes aproximadas:</strong> <?php echo esc_html((string)$total); ?></p>

  <?php if ($next):
    $pid      = $next->ID;
    $extra    = thd_get_extra_data($pid);
    $tienda   = isset($extra['data_tienda']) ? (string)$extra['data_tienda'] : '';
    $num      = thd_parse_num_tienda($tienda);
    $proposed = $num ? thd_build_correo_from_tienda($num) : '(no se pudo inferir)';
    $author   = get_userdata($next->post_author);
  ?>
    <div class="thd-card">
      <div class="thd-row">
        <div><strong>ID:</strong> <?php echo esc_html((string)$pid); ?></div>
        <div><strong>Título:</strong> <a href="<?php echo esc_url(get_edit_post_link($pid)); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html(get_the_title($pid)); ?></a></div>
      </div>
      <div class="thd-row thd-mt">
        <div><strong>Autor:</strong> <?php echo esc_html($author ? $author->user_login : '—'); ?></div>
        <div><strong>data_tienda:</strong> <?php echo esc_html($tienda ?: '—'); ?></div>
      </div>
      <div class="thd-row thd-mt">
        <div><strong>Correo propuesto:</strong> <?php echo esc_html($proposed); ?></div>
        <div><span class="thd-small">Calculado a partir del número detectado en <code>data_tienda</code>.</span></div>
      </div>
      <form method="post" class="thd-actions">
        <?php wp_nonce_field('thd_correccion_correo'); ?>
        <input type="hidden" name="post_id" value="<?php echo esc_attr((string)$pid); ?>">
        <button class="button-primary" name="thd_correccion_action" value="fix_one" type="submit">Corregir esta vacante</button>
        <button class="button-secondary" name="thd_correccion_action" value="skip_one" type="submit">Omitir</button>
      </form>
    </div>
  <?php else: ?>
    <div class="thd-card">
      <strong>No hay más vacantes candidatas.</strong>
      <p class="thd-small">Si esperabas más resultados, verifica que falte <code>extra_data_data_correo</code> y que el autor no sea <code>admin</code>.</p>
    </div>
  <?php endif; ?>

  <div class="thd-batch">
    <h3>Procesamiento en lote</h3>
    <p class="thd-small">Actualiza varias de una sola vez (procesado en serie en servidor, <em>máximo recomendado: 50</em> por clic).</p>
    <form method="post">
      <?php wp_nonce_field('thd_correccion_correo'); ?>
      <label for="batch_limit">Cantidad:</label>
      <input type="number" min="1" max="200" step="1" id="batch_limit" name="batch_limit" value="25" style="width:80px;">
      <button class="button-primary" name="thd_correccion_action" value="fix_batch" type="submit">Corregir en lote</button>
    </form>
  </div>

  <p class="thd-small thd-mt">Notas:
    <br>• Si no se puede extraer un número desde <code>data_tienda</code>, se marcará como <em>omitida</em> (meta <code>_correo_skip</code>).
    <br>• Esta versión guarda únicamente el subcampo <code>data_correo</code> y el meta <code>extra_data_data_correo</code>; no modifica el grupo <code>extra_data</code>.
  </p>
</div>

<?php get_footer();
