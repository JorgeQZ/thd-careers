<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leer Excel y Convertir a JSON</title>
    <script src="<?php echo get_template_directory(  ).'/js/xlsx.full.min.js'?>"></script>
</head>

<body>

    <h1>Convertir Excel a JSON</h1>
    <p>Carga un archivo Excel para convertirlo a un formato JSON personalizado.</p>
    <input type="file" id="fileInput" accept=".xlsx, .xls" />
    <pre id="jsonOutput"></pre> <!-- Aquí se mostrará el JSON generado -->

    <script>
    document.getElementById('fileInput').addEventListener('change', function(e) {
        var file = e.target.files[0];
        var reader = new FileReader();

        reader.onload = function(e) {
            var data = e.target.result;
            var workbook = XLSX.read(data, {
                type: 'binary'
            });

            // Objeto que almacenará los resultados finales
            var jsonResult = {};

            // Iteramos sobre todas las hojas del archivo
            workbook.SheetNames.forEach(function(sheetName) {
                var sheet = workbook.Sheets[sheetName];
                var json = XLSX.utils.sheet_to_json(sheet, {
                    header: 1
                }); // Convertir a JSON sin procesar (todas las filas)

                json.forEach(function(row, index) {
                    // Omitir la primera fila (encabezados)
                    if (index === 0) return;

                    var postalCode = row[0]; // Código postal
                    var settlement = row[1]; // Nombre del asentamiento
                    var type = row[2]; // Tipo de asentamiento
                    var municipality = row[3]; // Municipio
                    var state = row[4]; // Estado
                    var city = row[5]; // Ciudad

                    // Si el código postal no está en el objeto, lo inicializamos
                    if (!jsonResult[postalCode]) {
                        jsonResult[postalCode] = {
                            "d_estado": state || "",
                            "data": []
                        };
                    }

                    // Agregamos la información del asentamiento al array correspondiente
                    jsonResult[postalCode].data.push({
                        "d_asenta": settlement,
                        "d_tipo_asenta": type,
                        "d_mnpio": municipality,
                        "d_ciudad": city
                    });
                });
            });

            // Eliminar claves "undefined" y sus contenidos si existen
            if (jsonResult["undefined"]) {
                delete jsonResult["undefined"];
            }

            // Mostrar el JSON resultante en el elemento <pre> con formato legible
            document.getElementById('jsonOutput').textContent = JSON.stringify(jsonResult, null, 4);
        };

        reader.readAsBinaryString(file); // Leer el archivo Excel como binario
    });
    </script>

</body>

</html>
