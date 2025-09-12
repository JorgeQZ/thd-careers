<?php

/**
 * Template Name: Test
 */


get_header(); // Incluir el encabezado de WordPress

echo 'test';
$q = new WP_Query(array(
    'post_type'      => 'vacantes',
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
));

$unique = array();

if ($q->have_posts()) {
    foreach ($q->posts as $post_id) {
        $u = get_field('ubicacion', $post_id);

        // Normaliza a una lista de pares ['label'=>..., 'value'=>...]
        $entries = array();

        if (is_array($u)) {
            // ¿Lista (multiple) o asociativo (single)?
            $is_list = array_keys($u) === range(0, count($u) - 1);

            if ($is_list) {
                foreach ($u as $item) {
                    if (is_array($item)) {
                        $label = isset($item['label']) ? (string)$item['label'] : (isset($item['value']) ? (string)$item['value'] : '');
                        $value = isset($item['value']) ? (string)$item['value'] : $label;
                        if ($label !== '' || $value !== '') {
                            $entries[] = array('label' => $label, 'value' => $value);
                        }
                    } elseif (is_string($item) || is_numeric($item)) {
                        $entries[] = array('label' => (string)$item, 'value' => (string)$item);
                    }
                }
            } else {
                // Asociativo: esperamos keys 'label'/'value'
                $label = isset($u['label']) ? (string)$u['label'] : (isset($u['value']) ? (string)$u['value'] : '');
                $value = isset($u['value']) ? (string)$u['value'] : $label;
                if ($label !== '' || $value !== '') {
                    $entries[] = array('label' => $label, 'value' => $value);
                }
            }
        } elseif (is_string($u) || is_numeric($u)) {
            $entries[] = array('label' => (string)$u, 'value' => (string)$u);
        } else {
            // Nada usable
            continue;
        }

        // Por cada entrada, extrae código y arma label humano
        foreach ($entries as $e) {
            $raw_label = trim((string)$e['label']);
            $raw_value = trim((string)$e['value']);

            // Para obtener el código (1234 o 1234-56) buscamos al inicio
            $source_for_code = $raw_value !== '' ? $raw_value : $raw_label;
            if (!preg_match('/^\s*(\d+(?:-\d+)?)/', $source_for_code, $m)) {
                continue; // si no empieza con código, descartamos
            }
            $code = $m[1];

            // Label humano: si label es solo el código, intentamos quitarlo del value
            $human = $raw_label;
            if ($human === '' || preg_match('/^\d+(?:-\d+)?$/', $human)) {
                $human = preg_replace(
                    '/^\s*' . preg_quote($code, '/') . '\s*([:\-\|\x{2013}\x{2014}])?\s*/u',
                    '',
                    $raw_value
                );
                $human = trim((string)$human);
            }
            if ($human === '') {
                $human = $code;
            }

            // Dedup por código (último gana)
            $unique[$code] = array(
                'label' => $human,
                'value' => $code,
            );
        }
    }
}
wp_reset_postdata();

if (!empty($unique)) {
    uasort($unique, function ($a, $b) {
        return strcasecmp($a['label'], $b['label']);
    });
}

// Devuelve lista de pares (no dependemos de índices externos)
return array_values($unique);


get_footer(); // Incluir el pie de página de WordPress
