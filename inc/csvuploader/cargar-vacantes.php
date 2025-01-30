<?php
session_start();

// Generar y guardar el token CSRF en la sesión al cargar la página
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<div class="wrap">
    <h1>Subir CSV para Vacantes</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="csv_file">Selecciona un archivo CSV:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv">

        <!-- Campo oculto con el token CSRF -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <?php submit_button('Subir y Procesar'); ?>
    </form>
</div>

<?php

// Procesar el archivo CSV al enviar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    // Verificar si el token CSRF es válido
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        process_csv_to_vacantes();
    } else {
        echo 'Error: Token CSRF inválido o ausente.';
    }
}

// Función para procesar el archivo CSV y guardar los datos en el CPT de Vacantes
function process_csv_to_vacantes()
{
    // Verificar si el archivo subido es un CSV
    if ($_FILES['csv_file']['type'] !== 'text/csv' && $_FILES['csv_file']['type'] !== 'application/vnd.ms-excel') {
        die('El archivo no es un CSV válido.');
    }

    // Leer el archivo CSV
    if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    } else {
        echo 'Error: El archivo no es válido o no se cargó correctamente.';
        return; // Detener ejecución si el archivo no es válido
    }

    // Eliminar BOM si existe (caracter invisible al principio)
    $csv_data = file_get_contents($_FILES['csv_file']['tmp_name']);
    $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data);

    // Leer encabezados del archivo
    $headers = fgetcsv($file);
    $headers = array_map(function ($header) {
        return trim(str_replace("\xEF\xBB\xBF", '', $header));
    }, $headers);

    // Arreglo para almacenar los datos
    $data = [];
    while (($row = fgetcsv($file)) !== false) {
        // Eliminar BOM en cada campo de la fila
        $row = array_map(function ($value) {
            return trim(str_replace("\xEF\xBB\xBF", '', $value));
        }, $row);
        $data[] = array_combine($headers, $row);
    }
    fclose($file);

    // Preparar los datos para el CPT Vacantes
    foreach ($data as $entry) {
        $vacante_data = [
            'post_title'   => $entry['codigo_de_vacante'],  // El código de la vacante se usará como título
            'post_type'    => 'vacantes',  // El tipo de post CPT
            'post_status'  => 'publish',  // Publicar la vacante inmediatamente
            'meta_input'   => [
                'codigo_de_vacante' => $entry['codigo_de_vacante'] ?? '',
                'descripcion'       => $entry['descripcion'] ?? '',
                'video'             => $entry['video'] ?? '',
                'ubicacion'         => $entry['ubicacion'] ?? '',
                'beneficios'        => !empty($entry['beneficios']) ? explode(',', $entry['beneficios']) : [],
                'emi'               => $entry['emi'] ?? '',
                'imagen_qr'         => $entry['imagen_qr'] ?? '',
                'url_de_la_vacante' => $entry['url_de_la_vacante'] ?? '',
            ]
        ];

        // Insertar la vacante como un nuevo CPT
        $post_id = wp_insert_post($vacante_data);

        // Asignar la taxonomía 'categorias_vacantes'
        if (!empty($entry['categorias_vacantes'])) {
            $categorias = explode(',', $entry['categorias_vacantes']);
            wp_set_object_terms($post_id, $categorias, 'categorias_vacantes');
        }

        // Verificar si la vacante fue creada correctamente
        if ($post_id) {
            echo '<div class="updated"><p>Vacante ' . esc_html($entry['codigo_de_vacante']) . ' importada correctamente.</p></div>';
        } else {
            echo '<div class="error"><p>Error al importar la vacante ' . esc_html($entry['codigo_de_vacante']) . '.</p></div>';
        }
    }
}
?>