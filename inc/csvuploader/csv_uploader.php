<?php

add_action(
    'admin_menu',
    function () {
        add_menu_page(
            'Subir CSV a ACF', // Título de la página
            'Subir CSV', // Título del menú
            'manage_options', // Capacidad necesaria
            'subir-csv', // Slug de la página
            'render_csv_upload_page', // Función que renderiza la página
            'dashicons-upload', // Icono del menú
            20
        );
    }
);

// Renderizar la página de subida
function render_csv_upload_page()
{
    ?>
<div class="wrap">
    <h1>Subir CSV para Opciones de Página ACF</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="csv_file">Selecciona un archivo CSV:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv">
        <?php submit_button('Subir y Procesar');?>
    </form>
</div>
<?php

    // Procesar el archivo CSV al enviar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
        process_csv_to_repeater();
    }
}

// Procesar el archivo CSV y guardar los datos en el repeater de ACF
function process_csv_to_repeater()
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
    }
    if ($file) {
        // Eliminar BOM si existe (caracter invisible al principio)
        if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
            $file_tmp_name = $_FILES['csv_file']['tmp_name'];
            $file_mime_type = mime_content_type($file_tmp_name);
            $allowed_mime_types = ['text/csv', 'text/plain'];

            // Validar el tipo MIME
            if (in_array($file_mime_type, $allowed_mime_types)) {
                $csv_data = file_get_contents($file_tmp_name);
            } else {
                echo 'Error: Tipo de archivo no permitido.';
                $csv_data = null; // Manejo seguro en caso de error
            }
        } else {
            echo 'Error: No se cargó el archivo o ocurrió un problema.';
            $csv_data = null; // Manejo seguro en caso de error
        }
        $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data); // Elimina BOM
        // Verificar si el token CSRF es válido
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Verificar si csv_data tiene contenido válido
            if (!empty($csv_data) && is_string($csv_data)) {
                file_put_contents($_FILES['csv_file']['tmp_name'], $csv_data); // Reemplazar archivo original sin BOM
            } else {
                echo 'Error: Los datos del archivo no son válidos.';
            }
        } else {
            echo 'Error: Token CSRF inválido o ausente.';
        }


        // Leer encabezados del archivo
        $headers = fgetcsv($file);

        // Normalizar encabezados (convertir a minúsculas y eliminar espacios)
        $headers = array_map(function ($header) {
            return strtolower(str_replace(' ', '_', trim($header)));
        }, $headers);

        // Arreglo para almacenar los datos
        $data = [];
        while (($row = fgetcsv($file)) !== false) {
            $data[] = array_combine($headers, $row);
        }
        fclose($file);

        // Preparar los datos para el repeater
        if (function_exists('update_field')) {
            $repeater_data = [];
            foreach ($data as $entry) {
                echo json_encode($entry);
                echo htmlspecialchars($entry['tipo_de_negocio'], ENT_QUOTES, 'UTF-8');
                echo '<br>';
                $repeater_data[] = [
                    'numero_de_tienda' => $entry['numero_de_tienda'] ?? '-',
                    'nombre_de_tienda' => $entry['nombre_de_tienda'] ?? '-',
                    'ubicacion' => '------',
                    'coordenadas' => (isset($entry['latitud']) && isset($entry['longitud']))
                    ? $entry['latitud'] . ', ' . $entry['longitud']
                    : '',
                    'distrito' => $entry['id_distrito'] ?? '-',
                    'tipo_de_negocio' => $entry['tipo_de_negocio'] ?? 'Tienda',
                    'correo' => $entry['correo'] ?? '-',
                ];
            }

            // Actualizar el repeater en ACF
            $result = update_field('catalogo_de_tiendas', $repeater_data, 'option');

            if ($result) {
                echo '<div class="updated"><p>Datos importados correctamente en el repeater de ACF.</p></div>';
            } else {
                echo '<div class="error"><p>No se pudieron guardar los datos en el repeater de ACF.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Error: ACF no está disponible.</p></div>';
        }
    } else {
        echo '<div class="error"><p>No se pudo leer el archivo.</p></div>';
    }
}