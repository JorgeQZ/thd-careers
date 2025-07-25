<?php
function registrar_postulaciones_post_type() {
    $labels = array(
        'name'               => 'Postulaciones',
        'singular_name'      => 'Postulación',
        'menu_name'          => 'Postulaciones',
        'name_admin_bar'     => 'Postulación',
        'add_new'            => 'Añadir nueva',
        'add_new_item'       => 'Añadir nueva Postulación',
        'new_item'           => 'Nueva Postulación',
        'edit_item'          => 'Editar Postulación',
        'view_item'          => 'Ver Postulación',
        'all_items'          => 'Todas las Postulaciones',
        'search_items'       => 'Buscar Postulación',
        'parent_item_colon'  => 'Postulación padre:',
        'not_found'          => 'No se encontraron Postulaciones.',
        'not_found_in_trash' => 'No se encontraron Postulaciones en la papelera.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'postulaciones' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true,
    );

    register_post_type( 'postulaciones', $args );
}
add_action( 'init', 'registrar_postulaciones_post_type' );

add_filter('acf/load_field', 'disable_message_load_fields');

function agregar_capacidades_postulaciones() {
    // Obtén el rol RH General
    $rol_rh_general = get_role('rh_general');

    if ($rol_rh_general) {
        // Agrega capacidades para editar, leer y publicar postulaciones
        $rol_rh_general->add_cap('edit_postulaciones');         // Permite editar postulaciones propias
        $rol_rh_general->add_cap('edit_others_postulaciones');  // Permite editar postulaciones de otros
        $rol_rh_general->add_cap('publish_postulaciones');      // Permite publicar postulaciones
        $rol_rh_general->add_cap('read_postulacion');           // Permite leer postulaciones
        $rol_rh_general->add_cap('read_private_postulaciones'); // Permite leer postulaciones privadas
        $rol_rh_general->add_cap('delete_postulaciones');       // Permite borrar postulaciones propias
    }
}
add_action('init', 'agregar_capacidades_postulaciones');

function ajustar_capacidades_postulaciones($caps, $cap, $user_id, $args) {
    // Asegúrate de que el post type es "postulaciones"
    if (!empty($args[0]) && get_post_type($args[0]) === 'postulaciones') {
        if ($cap === 'edit_post') {
            $caps = ['edit_posts'];
        }
        if ($cap === 'edit_others_posts') {
            $caps = ['edit_others_posts'];
        }
    }
    return $caps;
}
add_filter('map_meta_cap', 'ajustar_capacidades_postulaciones', 10, 4);

function filtrar_postulaciones_por_tienda($query) {
    if ($query->is_main_query() && $query->get('post_type') === 'postulaciones') {
        $current_user = wp_get_current_user();

        // Verificar si el usuario tiene el rol de RH General
        if (in_array('rh_general', $current_user->roles)) {
            $tienda = get_user_meta($current_user->ID, 'tienda', true);

            if ($tienda) {
                $query->set('meta_query', array(
                    array(
                        'key' => 'numero_de_tienda_vacante',
                        'value' => $tienda,
                        'compare' => '='
                    )
                ));
            }
        }
    }
}
add_action('pre_get_posts', 'filtrar_postulaciones_por_tienda');

function filtrar_postulaciones_por_distrito($query) {
    if ($query->is_main_query() && $query->get('post_type') === 'postulaciones') {
        $current_user = wp_get_current_user();

        // Verificar si el usuario tiene el rol de RH Distrital
        if (in_array('rh_distrito', $current_user->roles)) {
            $distrito = get_user_meta($current_user->ID, 'distrito', true);

            if ($distrito) {
                $query->set('meta_query', array(
                    array(
                        'key' => 'distrito_vacante',
                        'value' => $distrito,
                        'compare' => '='
                    )
                ));
            }
        }
    }
}
add_action('pre_get_posts', 'filtrar_postulaciones_por_distrito');

function disable_message_load_fields( $field ) {
    $fields_to_disable = ['Nombre', 'Select1-postulacion', 'Correo', 'Apellidopaterno', 'Apellidomaterno', 'Telefono', 'vacante_vacante', 'ubicacion_vacante', 'numero_de_tienda_vacante', 'distrito_vacante', 'correo_vacante', 'id_vacante', 'id_postulante', 'puesto-de-trabajo-1', 'compania-1', 'ubicacion-1', 'desde-1', 'hasta-1', 'descripcion-del-rol-1', 'actualmente-trabajo-aqui-1', 'puesto-de-trabajo-2', 'compania-2', 'ubicacion-2', 'desde-2', 'hasta-2', 'descripcion-del-rol-2', 'actualmente-trabajo-aqui-2', 'has-trabajado', 'escuela-o-universidad', 'titulo-1', 'ultimo-grado-de-estudios', 'tipo-de-apoyo', 'que-tipo', 'especifique-otro', 'acepto-voluntariamente', 'CV']; // Lista de campos que deseas hacer readonly

    if (in_array($field['name'], $fields_to_disable)) {
        $field['readonly'] = 1;
    }

    return $field;
}

add_action('acf/input/admin_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Selecciona el contenedor del campo CV
            const cvField = document.querySelector('.acf-field[data-name="CV"]');

            if (cvField) {
                // Oculta el botón de editar
                const editButton = cvField.querySelector('.acf-icon.-pencil[data-name="edit"]');
                if (editButton) {
                    editButton.style.display = 'none';
                }

                // Oculta el botón de quitar
                const removeButton = cvField.querySelector('.acf-icon.-cancel[data-name="remove"]');
                if (removeButton) {
                    removeButton.style.display = 'none';
                }
            }
        });
    </script>
    <?php
});

// Validación antes de guardar el estado
function validar_estado_postulacion( $post_id ) {
    if ( get_post_type( $post_id ) == 'postulaciones' ) {
        $estado = get_field('Estado', $post_id);

        // Si el estado es "Seleccione un estado", no permitir guardar
        if ($estado == 'Seleccione un estado') {
            // Aquí puedes mandar un mensaje de error o hacer que no se guarde el estado
            update_field('Estado', '', $post_id); // No guardar el estado
        }
    }
}
add_action('acf/save_post', 'validar_estado_postulacion', 20);

// Agregar nuevas columnas para mostrar los campos 'Estado' y 'Vacante' en la tabla del post-type 'postulaciones'
function agregar_columnas_postulaciones($columns) {
    $columns['acf_estado'] = __('Estado'); // Título de la columna 'Estado'
    $columns['acf_vacante'] = __('Vacante'); // Título de la columna 'Vacante'
    $columns['acf_n_de_tienda'] = __('N. de Tienda'); // Título de la columna 'N. de Tienda'
    $columns['acf_n_de_distrito'] = __('N. de Distrito'); // Título de la columna 'N. de Distrito'
    $columns['acf_ubicacion'] = __('Ubicación'); // Título de la columna 'Ubicación'
    return $columns;
}
add_filter('manage_postulaciones_posts_columns', 'agregar_columnas_postulaciones');

// Mostrar los valores de los campos 'Estado' y 'Vacante' en sus respectivas columnas
function mostrar_valores_columnas_postulaciones($column, $post_id) {
    if ($column == 'acf_estado') {
        $estado = get_field('Estado', $post_id); // Obtener el valor del campo 'Estado'
        echo esc_html($estado); // Mostrar el valor en la tabla
    }

    if ($column == 'acf_vacante') {
        $vacante = get_field('vacante_vacante', $post_id); // Obtener el valor del campo 'Vacante'
        echo wp_strip_all_tags($vacante); // Mostrar el valor en la tabla
    }

    if ($column == 'acf_ubicacion') {
        $ubicacion = get_field('ubicacion_vacante', $post_id); // Obtener el valor del campo 'Ubicacion'
        echo wp_strip_all_tags($ubicacion); // Mostrar el valor en la tabla
    }

    if ($column == 'acf_n_de_tienda') {
        $n_de_tienda = get_field('numero_de_tienda_vacante', $post_id); // Obtener el valor del campo 'N. de Tienda'
        echo wp_strip_all_tags($n_de_tienda); // Mostrar el valor en la tabla
    }

    if ($column == 'acf_n_de_distrito') {
        $n_de_distrito = get_field('distrito_vacante', $post_id); // Obtener el valor del campo 'N. de Distrito'
        echo wp_strip_all_tags($n_de_distrito); // Mostrar el valor en la tabla
    }
}
add_action('manage_postulaciones_posts_custom_column', 'mostrar_valores_columnas_postulaciones', 10, 2);

// Cambiar el estado a "Visto" al hacer clic en "Editar" una postulación
function cambiar_estado_al_abrir_edicion() {
    global $pagenow;

    // Verificar si estamos en la página de edición y si el post type es 'postulaciones'
    if ($pagenow == 'post.php' && isset($_GET['post'])) {
        $post_id = $_GET['post'];

        // Verificar si es una postulación
        if (get_post_type($post_id) == 'postulaciones') {
            // Obtener el estado actual
            $estado_actual = get_field('Estado', $post_id);

            // Solo cambiar a 'Visto' si el estado actual no es 'Aceptado' o 'Rechazado'
            if ($estado_actual !== 'Aceptado' && $estado_actual !== 'Rechazado') {
                update_field('Estado', 'Visto', $post_id); // Cambiar el estado a 'Visto'
                // // Llamar a la función de correo inmediatamente después de cambiar el estado
                // enviar_correo_cambio_estado($post_id);
            }
        }
    }
}
add_action('admin_init', 'cambiar_estado_al_abrir_edicion');

// CORREOS

// function custom_phpmailer_smtp( $phpmailer ) {
//     // Configuración del remitente


//     require_once get_template_directory() . '/config-sendgrid.php';

//     $phpmailer->Password = SENDGRID_PASSWORD;
//     $phpmailer->isSMTP();
//     $phpmailer->Host       = 'smtp.sendgrid.net'; // Servidor SMTP de tu dominio
//     $phpmailer->SMTPAuth   = true; // Activar autenticación SMTP
//     $phpmailer->Username   = 'apikey'; // Correo
//     $phpmailer->Password = SENDGRID_PASSWORD;
//     $phpmailer->SMTPSecure = 'tls';
//     $phpmailer->Port       = 587;
//     $phpmailer->From       = "noreply@wordpressvip.com";
//     $phpmailer->FromName   = "Construyendo Carreras - The Home Depot";
// }

// add_action( 'phpmailer_init', 'custom_phpmailer_smtp' );

// Función para enviar correos cuando se cree un nuevo post con estado "Postulado"
function enviar_correo_cambio_estado( $post_id ) {
    // Verifica si el post type es 'postulaciones' y no es un autosave
    if ( get_post_type($post_id) != 'postulaciones' || wp_is_post_autosave($post_id) ) {
        return;
    }

    // Obtener el nuevo estado del campo ACF
    $nuevo_estado = get_field('Estado', $post_id);
    $nuevo_estado = trim($nuevo_estado); // Eliminar espacios en blanco

    // Verificar que el estado sea 'Postulado'
    if ($nuevo_estado !== 'Postulado') {
        return;
    }

    // Verifica si es un nuevo post (estado anterior no existe)
    $estado_anterior = get_post_meta($post_id, '_estado_anterior', true);
    if (!empty($estado_anterior)) {
        return; // Si hay estado anterior, no enviar el correo (no es un nuevo post)
    }

    // Obtener los datos del postulante
    $nombre_postulante = get_field('Nombre', $post_id);
    $apellido_paterno = get_field('Apellidopaterno', $post_id);
    $apellido_materno = get_field('Apellidomaterno', $post_id);
    $correo_postulante = get_field('Correo', $post_id);
    $telefono_postulante = get_field('Telefono', $post_id);

    // Obtener los datos de la vacante
    $vacante = get_field('vacante_vacante', $post_id);
    $correo_vacante = get_field('correo_vacante', $post_id);

    // Verificar que el correo de la vacante esté disponible
    if (empty($correo_vacante)) {
        error_log('No se encontró un correo para la vacante en el post ID: ' . $post_id);
        return;
    }

    // Configurar encabezados para SMTP
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Carreras Home Depot <noreply@homedepot.com.mx>'
    );

    // Configurar el correo al encargado de la vacante
    $to_vacante = $correo_vacante;
    $subject_vacante = 'Nueva postulación para la vacante: ' . esc_html($vacante);

    $link_edit_post = get_edit_post_link($post_id) ?: admin_url('post.php?post=' . $post_id . '&action=edit');

    $message_vacante = '<p>Un candidato se ha postulado para la vacante: <strong>' . esc_html($vacante) . '</strong>.</p>';
    $message_vacante .= '<p><strong>Información del postulante:</strong></p>';
    $message_vacante .= '<ul>';
    $message_vacante .= '<li><strong>Nombre:</strong> ' . esc_html($nombre_postulante) . '</li>';
    $message_vacante .= '<li><strong>Apellido Paterno:</strong> ' . esc_html($apellido_paterno) . '</li>';
    $message_vacante .= '<li><strong>Apellido Materno:</strong> ' . esc_html($apellido_materno) . '</li>';
    $message_vacante .= '<li><strong>Correo:</strong> ' . esc_html($correo_postulante) . '</li>';
    $message_vacante .= '<li><strong>Teléfono:</strong> ' . esc_html($telefono_postulante) . '</li>';
    $message_vacante .= '</ul>';
    $message_vacante .= '<p><strong>Enlace de la postulación:</strong> <a href="' . esc_url($link_edit_post) . '" target="_blank" rel="noopener noreferrer">' . esc_url($link_edit_post) . '</a></p>';

    // Enviar el correo al encargado de la vacante
    if (!wp_mail($to_vacante, $subject_vacante, $message_vacante, $headers)) {
        error_log('Error al enviar correo a la vacante: ' . $to_vacante);
    }

    // Configurar el correo al postulante
    $to_postulante = $correo_postulante;
    $subject_postulante = '¡Postulación exitosa para la vacante: ' . esc_html($vacante) . '!';
    $message_postulante = '<p>Hola <strong>' . esc_html($nombre_postulante) . '</strong>,</p>';
    $message_postulante .= '<p>Tu postulación para la vacante <strong>' . esc_html($vacante) . '</strong> ha sido exitosa.</p>';
    $message_postulante .= '<p>Por favor, mantente atento a tus medios de contacto para el seguimiento de tu postulación.</p>';
    $message_postulante .= '<p>¡Gracias por tu interés!</p>';

    // Enviar el correo al postulante
    if (!wp_mail($to_postulante, $subject_postulante, $message_postulante, $headers)) {
        error_log('Error al enviar correo al postulante: ' . $to_postulante);
    }

    // Actualiza el estado anterior para marcar que ya se envió la notificación
    update_post_meta($post_id, '_estado_anterior', $nuevo_estado);
}

// Hook para ejecutar la función al guardar el post manualmente desde el backend
add_action('acf/save_post', 'enviar_correo_cambio_estado', 20);



add_filter('acf/load_value/name=CV', 'mostrar_cv_url_actualizado_en_admin', 10, 3);
function mostrar_cv_url_actualizado_en_admin($value, $post_id, $field) {
    // Obtener el ID del postulante desde ACF
    $user_id = get_field('id_postulante', $post_id);

    $nombre_de_cv = get_field('nombre_de_cv', $post_id);

    if (!empty($nombre_de_cv)) {
        // Se usó un archivo nuevo en la postulación
        $cv_url = obtener_url_archivo($nombre_de_cv);
        return esc_url_raw($cv_url);
    }

    if ($user_id) {
        // Obtener el nombre del archivo guardado en user_meta con la clave 'CV'
        $file_name = get_user_meta($user_id, 'gcs_url_name', true);

        if ($file_name) {
            // Generar la URL temporal con access token
            $cv_url = obtener_url_archivo($file_name);

            return esc_url_raw($cv_url); // Mostrar la URL generada dinámicamente
        }
    }

    return $value; // Si no hay nada, mostrar lo que había
}

add_action('acf/render_field/name=CV', 'renderizar_solo_boton_cv_url');
function renderizar_solo_boton_cv_url($field) {
    $cv_url = $field['value'];
    echo '<style>
    .acf-field[data-name="CV"] .link-wrap {
        display: none !important;
    }
</style>';
    echo '<div style="margin-top: 5px;">';

    if ($cv_url) {
        echo '<a href="' . esc_url($cv_url) . '" target="_blank" class="button button-primary" rel="noopener noreferrer">Ver CV</a>';
    } else {
        echo '<em>CV no disponible</em>';
    }

    echo '</div>';

}