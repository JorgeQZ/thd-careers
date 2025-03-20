<?php
session_start();

// Generar y guardar el token CSRF en la sesión al cargar la página
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<div class="wrap">
    <h1>Subir CSV para Opciones de Página ACF</h1>
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
        process_csv_to_repeater();
    } else {
        echo '<div class="error"><p>Error: Token CSRF inválido o ausente.</p></div>';
    }
}

// Función para procesar el archivo CSV y guardar los datos en el repeater de ACF
function process_csv_to_repeater(){
    // Asegurar que WordPress ha sido cargado
    if (!defined('ABSPATH')) {
        exit;
    }

    // Verificar si el archivo fue subido correctamente
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="error"><p>Error: No se ha subido un archivo válido.</p></div>';
        return;
    }

    $file = $_FILES['csv_file'];

    // Validar el tipo MIME y extensión del archivo
    $allowed_exts = ['csv'];
    $allowed_types = ['text/csv', 'application/vnd.ms-excel'];
    $file_mime = mime_content_type($file['tmp_name']);
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);


    // Validar la extensión
    if (!in_array($file_mime, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
        echo '<div class="error"><p>Error: Solo se permiten archivos CSV.</p></div>';
        return;
    }

    // Validar el tipo MIME
    if (!in_array($file_mime, $allowed_types)) {
        die("Error: Tipo MIME no permitido.");
    }

    if (!in_array($file_mime, $allowed_types) || strtolower($file_ext) !== 'csv') {
        echo '<div class="error"><p>Error: Solo se permiten archivos CSV.</p></div>';
        return;
    }

    // Validar que el archivo no sea mayor a 5MB
    if ($file['size'] > 5 * 1024 * 1024) {
        echo '<div class="error"><p>Error: El archivo es demasiado grande.</p></div>';
        return;
    }

    // Leer el contenido del CSV
    if (!is_uploaded_file($file['tmp_name'])) {
        echo '<div class="error"><p>Error: Archivo no válido o manipulado.</p></div>';
        return;
    }

    // Eliminar BOM si existe
    $csv_data = file_get_contents($file['tmp_name']);
    $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data);

    // Abrir el archivo de manera segura
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        echo '<div class="error"><p>Error: No se pudo abrir el archivo CSV.</p></div>';
        return;
    }

    // Leer encabezados y sanitizar
    $headers = fgetcsv($handle);
    if (!$headers) {
        echo '<div class="error"><p>Error: El archivo CSV está vacío o tiene un formato incorrecto.</p></div>';
        fclose($handle);
        return;
    }

    $headers = array_map('trim', $headers);

    // Validar que los encabezados esperados estén en el CSV
    $required_headers = ['numero_de_tienda', 'nombre_de_tienda', 'ubicacion', 'latitud', 'longitud', 'id_distrito', 'tipo_de_negocio', 'correo'];
    foreach ($required_headers as $header) {
        if (!in_array($header, $headers)) {
            echo '<div class="error"><p>Error: El archivo CSV no tiene los encabezados correctos.</p></div>';
            fclose($handle);
            return;
        }
    }

    // Procesar filas del CSV
    $data = [];
    $max_rows = 5000; // Límite de filas procesadas
    $row_count = 0;
    while (($row = fgetcsv($handle)) !== false) {

        $row_count++;

        if ($row_count > $max_rows) {
            echo '<div class="error"><p>Error: El archivo CSV excede el límite de ' . $max_rows . ' filas.</p></div>';
            fclose($handle);
            return;
        }


        if (count($row) === count($headers)) {
            $row = array_map('trim', $row);
            $entry = array_combine($headers, $row);

            // Sanitizar valores del CSV antes de procesarlos
            $entry = array_map('esc_html', $entry);

            $data[] = [
                'numero_de_tienda' => $entry['numero_de_tienda'] ?? '-',
                'nombre_de_tienda' => $entry['nombre_de_tienda'] ?? 'Desconocido',
                'ubicacion' => $entry['ubicacion'] ?? '-',
                'coordenadas' => (!empty($entry['latitud']) && !empty($entry['longitud']))
                    ? esc_html($entry['latitud'] . ', ' . $entry['longitud'])
                    : 'Coordenadas no disponibles',
                'distrito' => $entry['id_distrito'] ?? '-',
                'tipo_de_negocio' => $entry['tipo_de_negocio'] ?? 'Tienda',
                'correo' => $entry['correo'] ?? '-',
            ];
        }
    }
    fclose($handle);

    // Validar si ACF está disponible antes de continuar
    if (!function_exists('update_field')) {
        echo '<div class="error"><p>Error: ACF no está disponible.</p></div>';
        return;
    }

    // Obtener los datos actuales en ACF sin sobrescribir datos manuales
    $existing_data = get_field('catalogo_de_tiendas', 'option') ?: [];

    // Crear un array asociativo para facilitar la actualización
    $indexed_existing_data = [];
    foreach ($existing_data as $existing_entry) {
        $indexed_existing_data[$existing_entry['numero_de_tienda']] = $existing_entry;
    }

    // Agregar nuevos datos sin sobrescribir manuales
    foreach ($data as $entry) {
        $indexed_existing_data[$entry['numero_de_tienda']] = array_merge(
            $indexed_existing_data[$entry['numero_de_tienda']] ?? [],
            $entry
        );
    }

    // Convertir de nuevo a un array numérico
    $final_data = array_values($indexed_existing_data);

    // Actualizar el campo en ACF
    $result = update_field('catalogo_de_tiendas', $final_data, 'option');

    if ($result) {
        echo '<div class="updated"><p>' . esc_html(count($final_data)) . ' tiendas importadas correctamente en el repeater de ACF.</p></div>';
        delete_transient('cached_stores'); // Eliminar caché cuando se actualiza el catálogo
    } else {
        echo '<div class="error"><p>No se pudieron guardar los datos en el repeater de ACF.</p></div>';
    }
}
?>
