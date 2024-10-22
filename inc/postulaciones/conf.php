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

function disable_message_load_fields( $field ) {
    $fields_to_disable = ['Nombre', 'Correo', 'Apellidopaterno', 'Apellidomaterno']; // Lista de campos que deseas hacer readonly

    if (in_array($field['name'], $fields_to_disable)) {
        $field['readonly'] = 1;
    }

    return $field;
}

function custom_acf_admin_css() {
    $screen = get_current_screen(); // Obtener la pantalla actual

    // Verifica que solo aplique en el post-type 'postulaciones'
    if ($screen && $screen->post_type === 'postulaciones') {
        echo '<style>
            .acf-field[data-name="CV"] .acf-actions {
                display: none !important;
            }
        </style>';
    }
}
add_action('admin_head', 'custom_acf_admin_css');

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

// Agregar una nueva columna para mostrar el valor del campo Estado en la tabla del post-type 'postulaciones'
function agregar_columna_estado_postulaciones($columns) {
    $columns['acf_estado'] = __('Estado'); // Título de la columna
    return $columns;
}
add_filter('manage_postulaciones_posts_columns', 'agregar_columna_estado_postulaciones');

// Mostrar el valor del campo Estado en la nueva columna
function mostrar_valor_estado_postulaciones($column, $post_id) {
    if ($column == 'acf_estado') {
        $estado = get_field('Estado', $post_id); // Obtiene el valor del campo 'Estado'
        echo esc_html($estado); // Imprime el valor en la tabla
    }
}
add_action('manage_postulaciones_posts_custom_column', 'mostrar_valor_estado_postulaciones', 10, 2);

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
                // Llamar a la función de correo inmediatamente después de cambiar el estado
                enviar_correo_cambio_estado($post_id);
            }
        }
    }
}
add_action('admin_init', 'cambiar_estado_al_abrir_edicion');

// CORREOS

function custom_phpmailer_smtp( $phpmailer ) {
    // Configuración del remitente
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'mail.akevia.mx'; // Servidor SMTP de tu dominio
    $phpmailer->SMTPAuth   = true; // Activar autenticación SMTP
    $phpmailer->Username   = 'javiertrevino@akevia.mx'; // Tu correo de dominio propio
    $phpmailer->Password   = 'Akevia09'; // Contraseña de tu correo
    $phpmailer->SMTPSecure = 'ssl'; // Método de encriptación ('ssl' o 'tls')
    $phpmailer->Port       = 465; // Puerto SMTP (587 para TLS o 465 para SSL)
}

add_action( 'phpmailer_init', 'custom_phpmailer_smtp' );

// Función para enviar correo cuando el estado cambie
function enviar_correo_cambio_estado( $post_id ) {
    // Verifica si el post type es 'postulaciones' y no es un autosave
    if ( get_post_type($post_id) != 'postulaciones' || wp_is_post_autosave($post_id) ) {
        return;
    }

    // Obtener el nuevo estado del campo ACF
    $nuevo_estado = get_field('Estado', $post_id);

    // Asegurarse de que el estado no esté vacío y eliminar posibles espacios en blanco
    $nuevo_estado = trim( $nuevo_estado );

    // Verifica si el nuevo estado está correctamente
    if (!$nuevo_estado) {
        error_log('El nuevo estado no se ha establecido correctamente.');
        return;
    }

    // Obtener el estado anterior para compararlo
    $estado_anterior = get_post_meta( $post_id, '_estado_anterior', true );

    // Asegurarse de que el estado anterior también esté limpio de espacios en blanco
    $estado_anterior = trim( $estado_anterior );

    // Si el estado ha cambiado o el estado anterior está vacío (es la primera vez que se guarda)
    if ( $nuevo_estado && $nuevo_estado !== $estado_anterior ) {
        // Obtener el nombre de la persona
        $nombre_postulante = get_field('Nombre', $post_id);
        $correo = get_field('Correo', $post_id);

        // Configura los detalles del correo
        $to = 'javiertrevino@akevia.mx, ' . esc_html($correo); // Dirección de correo a la que se enviará la notificación
        $subject = 'Cambio de estado en la postulación de ' . esc_html($nombre_postulante); // Asunto del correo con el nombre de la persona
        $message = 'El estado de la postulación de ' . esc_html($nombre_postulante) . ' ha cambiado a: ' . esc_html($nuevo_estado); // Mensaje del correo
        $headers = array('Content-Type: text/html; charset=UTF-8'); // Cabeceras del correo

        // Enviar el correo
        wp_mail( $to, $subject, $message, $headers );

        // Actualiza el estado anterior en el meta para futuras comparaciones
        update_post_meta( $post_id, '_estado_anterior', $nuevo_estado );
    }
}

// Hook para ejecutar la función al guardar el post manualmente desde el backend
add_action('acf/save_post', 'enviar_correo_cambio_estado', 20);