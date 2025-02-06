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
function process_csv_to_repeater()
{
    // Verificar si el archivo subido es un CSV
    if ($_FILES['csv_file']['type'] !== 'text/csv' && $_FILES['csv_file']['type'] !== 'application/vnd.ms-excel') {
        die('<div class="error"><p>El archivo no es un CSV válido.</p></div>');
    }

    // Leer el archivo CSV
    if (isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    } else {
        echo '<div class="error"><p>Error: El archivo no es válido o no se cargó correctamente.</p></div>';
        return;
    }

    // Eliminar BOM si existe (caracter invisible al principio)
    $csv_data = file_get_contents($_FILES['csv_file']['tmp_name']);
    $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data);

    // Leer encabezados del archivo
    $headers = fgetcsv($file);
    $headers = array_map('trim', $headers);

    // Arreglo para almacenar los datos
    $data = [];
    while (($row = fgetcsv($file)) !== false) {
        if (count($row) === count($headers)) {
            $row = array_map('trim', $row);
            $data[] = array_combine($headers, $row);
        }
    }
    fclose($file);

    if (function_exists('update_field')) {
        $repeater_data = [];
        foreach ($data as $entry) {
            $repeater_data[] = [
                'numero_de_tienda' => $entry['numero_de_tienda'] ?? '-',
                'nombre_de_tienda' => $entry['nombre_de_tienda'] ?? 'Desconocido',
                'ubicacion' => $entry['ubicacion'] ?? '-',
                'coordenadas' => (!empty($entry['latitud']) && !empty($entry['longitud']))
                    ? $entry['latitud'] . ', ' . $entry['longitud']
                    : 'Coordenadas no disponibles',
                'distrito' => $entry['id_distrito'] ?? '-',
                'tipo_de_negocio' => $entry['tipo_de_negocio'] ?? 'Tienda',
                'correo' => $entry['correo'] ?? '-',
            ];
        }

        // Obtener los datos actuales en ACF sin sobrescribir datos manuales
        $existing_data = get_field('catalogo_de_tiendas', 'option') ?: [];

        // Crear un array asociativo para facilitar la actualización
        $indexed_existing_data = [];
        foreach ($existing_data as $existing_entry) {
            $indexed_existing_data[$existing_entry['numero_de_tienda']] = $existing_entry;
        }

        // Agregar nuevos datos sin sobrescribir manuales
        foreach ($repeater_data as $entry) {
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
            echo '<div class="updated"><p>' . count($final_data) . ' tiendas importadas correctamente en el repeater de ACF.</p></div>';
            delete_transient('cached_stores'); // Eliminar caché cuando se actualiza el catálogo
        } else {
            echo '<div class="error"><p>No se pudieron guardar los datos en el repeater de ACF.</p></div>';
        }
    } else {
        echo '<div class="error"><p>Error: ACF no está disponible.</p></div>';
    }
}
?>
