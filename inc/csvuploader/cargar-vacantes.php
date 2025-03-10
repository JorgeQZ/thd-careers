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
        echo '<div class="notice notice-error"><p>' . esc_html($e->getMessage()) . '</p></div>';
    }
}
function process_csv_to_vacantes()
{
    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if ($file === false) {
        throw new Exception('No se pudo abrir el archivo CSV.');
    }

    $csv_data = file_get_contents($_FILES['csv_file']['tmp_name']);
    $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data); // Elimina BOM

    $headers = fgetcsv($file);
    if ($headers === false) {
        fclose($file);
        throw new Exception('El archivo CSV no tiene encabezados válidos.');
    }

    $data = [];
    while (($row = fgetcsv($file)) !== false) {
        $data[] = array_combine($headers, $row);
    }

    fclose($file);

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
            'numberposts' => 1, // Solo recuperar 1 resultado
            'meta_query' => [
                [
                    'key'   => 'codigo_de_vacante',
                    'value' => $entry['codigo_de_vacante'],
                ]
            ]
        ]);

        if (!empty($existing_post)) {
            continue; // Si ya existe, saltar a la siguiente
        }

         $featured_image_url = !empty($entry['imagen_destacada']) ? trim($entry['imagen_destacada']) : 'https://homedepotmexico-develop.go-vip.net/carreras/wp-content/uploads/sites/9/2025/01/fondo-footer.png';

        $vacante_data = [
            'post_title'  => $entry['titulo'],
            'post_type'   => 'vacantes',
            'post_status' => 'draft',
            'meta_input'  => [
                'codigo_de_vacante' => $entry['codigo_de_vacante'],
                'descripcion'       => $entry['descripcion'],
                'video'             => $entry['video'],
                'ubicacion'         => $entry['ubicacion'],
                'beneficios'        => $beneficios,
                'emi' => (!empty($entry['emi']) && in_array(strtolower($entry['emi']), ['true', 'verdadero', '1', 'sí', 'si', 'SÍ', 'SI'])) ? 1 : 0,
                'imagen_qr'         => $entry['imagen_qr'],
                'url_de_la_vacante' => $entry['url_de_la_vacante']
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

