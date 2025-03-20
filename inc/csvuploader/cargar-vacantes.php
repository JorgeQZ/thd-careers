<?php
session_start();

// Generar y guardar el token CSRF en la sesión al cargar la página
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<div class="wrap">
    <h1>Subir CSV para Vacantes</h1>
    <form id="csvUploadForm" action="" method="post" enctype="multipart/form-data">
        <label for="csv_file">Selecciona un archivo CSV:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" id="submitBtn">Subir y Procesar</button>
        <div id="progressContainer" style="display:none; margin-top:10px;">
            <progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
            <p id="progressText">Subiendo archivo...</p>
        </div>
    </form>
</div>

<script>
document.getElementById('csvUploadForm').addEventListener('submit', function(event) {
    event.preventDefault();
    let form = this;
    let formData = new FormData(form);
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('progressContainer').style.display = 'block';
    let xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.upload.onprogress = function(event) {
        if (event.lengthComputable) {
            let percentComplete = (event.loaded / event.total) * 100;
            document.getElementById('progressBar').value = percentComplete;
            document.getElementById('progressText').innerText = 'Progreso: ' + Math.round(percentComplete) + '%';
        }
    };
    xhr.onload = function() {
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('progressText').innerText = xhr.status === 200 ? 'Importación completada con éxito.' : 'Error al procesar el archivo.';
    };
    xhr.send(formData);
});
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    try {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception('Error: Token CSRF inválido o ausente.');
        }
        if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo.');
        }
        if ($_FILES['csv_file']['type'] !== 'text/csv' && $_FILES['csv_file']['type'] !== 'application/vnd.ms-excel') {
            throw new Exception('El archivo subido no es un CSV válido.');
        }

        process_csv_to_vacantes();

    } catch (Exception $e) {
        // Registrar error sin exponer detalles al usuario
        error_log('Error en cargar-vacantes.php: ' . $e->getMessage());

        // Mensaje genérico para evitar exposición de detalles técnicos
        echo '<div class="notice notice-error"><p>' . esc_html__('Ocurrió un error inesperado. Por favor, intenta de nuevo más tarde.', 'tu-text-domain') . '</p></div>';
    }
}



function process_csv_to_vacantes()
{
    // Asegurar que WordPress ha sido cargado
    if (!defined('ABSPATH')) {
        exit;
    }

    // Verificar si el archivo se ha subido correctamente
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        echo '<div class="error"><p>Error: No se ha subido un archivo válido.</p></div>';
        return;
    }

    $file = $_FILES['csv_file'];

    // Validar MIME type y extensión
    $allowed_exts = ['csv'];
    $allowed_types = ['text/csv', 'application/vnd.ms-excel'];
    $file_mime = mime_content_type($file['tmp_name']);
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Validar la extensión
    if (!in_array($file_mime, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
        echo '<div class="error"><p>Error: Solo se permiten archivos CSV.</p></div>';
        return;
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

    // Asegurar que el archivo es realmente local
    if (!is_uploaded_file($file['tmp_name'])) {
        echo '<div class="error"><p>Error: Archivo no válido o manipulado.</p></div>';
        return;
    }

    // Abrir archivo CSV de forma segura
    $max_rows = 5000; // Límite de filas procesadas
    $row_count = 0;

    $handle = fopen($file['tmp_name'], 'r');
    if ($handle === false) {
        echo '<div class="error"><p>Error: No se pudo abrir el archivo CSV.</p></div>';
        return;
    }

    // Eliminar BOM si existe
    $csv_data = stream_get_contents($handle);
    $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data);
    fseek($handle, 0); // Reiniciar la lectura del archivo después de eliminar BOM

    // Leer encabezados y sanitizar
    $headers = fgetcsv($handle);
    if ($headers === false) {
        fclose($handle);
        echo '<div class="error"><p>Error: El archivo CSV no tiene encabezados válidos.</p></div>';
        return;
    }

    $headers = array_map('trim', $headers);
    $data = [];

    while (($row = fgetcsv($handle)) !== false) {
        $line_number++;
        $row_count++;

        if ($row_count > $max_rows) {
            echo '<div class="error"><p>Error: El archivo CSV excede el límite de ' . $max_rows . ' filas.</p></div>';
            fclose($handle);
            return;
        }

        // Verificar que la fila tiene el mismo número de columnas que los encabezados
        if (count($row) !== count($headers)) {
            fclose($handle);
            echo '<div class="error"><p>Error en la línea ' . $line_number . ': Número incorrecto de columnas.</p></div>';
            return;
        }

        // Sanitizar cada campo antes de procesarlo
        $row = array_map('trim', $row);
        $entry = array_combine($headers, array_map('esc_html', $row));
        $data[] = $entry;
    }

    fclose($handle);
    // Validación de Beneficios
    $beneficios_permitidos = [
        'Prestaciones superiores a la ley',
        'Bono por objetivos',
        'Fondo de Ahorro',
        'Flexibilidad Laboral',
        'Caja de Ahorro',
        'Seguros y Apoyos económicos',
        'Compra de acciones',
        'Vales de despensa'
    ];

    foreach ($data as $entry) {
        $beneficios = isset($entry['beneficios']) ? explode(',', $entry['beneficios']) : [];
        $beneficios = array_map('trim', $beneficios);
        $beneficios = array_filter($beneficios, function ($beneficio) use ($beneficios_permitidos) {
            return in_array($beneficio, $beneficios_permitidos, true);
        });

        // VERIFICAR SI YA EXISTE LA VACANTE PARA EVITAR DUPLICADOS
        $existing_post = get_posts([
            'post_type'  => 'vacantes',
            'numberposts' => 1,
            'meta_query' => [
                [
                    'key'   => 'codigo_de_vacante',
                    'value' => $entry['codigo_de_vacante'],
                ]
            ]
        ]);

        if (!empty($existing_post)) {
            continue;
        }

        $featured_image_url = !empty($entry['imagen_destacada']) ? trim($entry['imagen_destacada']) : 'https://homedepotmexico-develop.go-vip.net/carreras/wp-content/uploads/sites/9/2025/01/fondo-footer.png';

        $vacante_data = [
            'post_title'  => esc_html($entry['titulo']),
            'post_type'   => 'vacantes',
            'post_status' => 'draft',
            'meta_input'  => [
                'codigo_de_vacante' => esc_html($entry['codigo_de_vacante']),
                'descripcion'       => esc_html($entry['descripcion']),
                'video'             => esc_url($entry['video']),
                'ubicacion'         => esc_html($entry['ubicacion']),
                'beneficios'        => $beneficios,
                'emi' => (!empty($entry['emi']) && in_array(strtolower($entry['emi']), ['true', 'verdadero', '1', 'sí', 'si', 'SÍ', 'SI'])) ? 1 : 0,
                'imagen_qr'         => esc_url($entry['imagen_qr']),
                'url_de_la_vacante' => esc_url($entry['url_de_la_vacante'])
            ]
        ];

        $post_id = wp_insert_post($vacante_data);

        if ($post_id) {
            if (!empty($entry['categorias_vacantes'])) {
                wp_set_object_terms($post_id, explode(',', $entry['categorias_vacantes']), 'categorias_vacantes');
            }
            update_field('beneficios', $beneficios, $post_id);
            set_featured_image($post_id, $featured_image_url);
        }
    }
}


function set_featured_image($post_id, $image_url) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $tmp = download_url($image_url);
    if (is_wp_error($tmp)) return;
    $file_array = ['name' => basename($image_url), 'tmp_name' => $tmp];
    $attachment_id = media_handle_sideload($file_array, $post_id);
    if (!is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, $attachment_id);
    }
}

