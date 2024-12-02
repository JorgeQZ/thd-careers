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
    if ($_FILES['csv_file']['type'] !== 'text/csv') {
        echo '<div class="error"><p>Por favor, sube un archivo CSV válido.</p></div>';
        return;
    }

    // Leer el archivo CSV
    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if ($file) {
        // Eliminar BOM si existe (caracter invisible al principio)
        $csv_data = file_get_contents($_FILES['csv_file']['tmp_name']);
        $csv_data = preg_replace('/^\xEF\xBB\xBF/', '', $csv_data); // Elimina BOM
        file_put_contents($_FILES['csv_file']['tmp_name'], $csv_data); // Reemplazar archivo original sin BOM

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
                echo $entry['tipo_de_negocio'];
                echo '<br>';
                $repeater_data[] = [
                    'numero_de_tienda' => $entry['numero_de_tienda'] ?? '-',
                    'nombre_de_tienda' => $entry['nombre_de_tienda'] ?? '-',
                    'ubicacion' => $entry['ubicacion'] ?? '-',
                    'coordenadas' => (isset($entry['latitud']) && isset($entry['longitud']))
                    ? $entry['latitud'] . ', ' . $entry['longitud']
                    : '',
                    'distrito' => $entry['id_distrito'] ?? '-',
                    'tipo_de_negocio' => $entry['tipo_de_negocio'] ?? 'Tienda',
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