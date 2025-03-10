<?php
/**
 * Template Name: Test
 */


 get_header(); // Incluir el encabezado de WordPress

 // Verificar si se envió un archivo
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
     $file = $_FILES['file'];

     // Subir el archivo a GCP usando tu función existente
     $upload_result = upload_to_gcp($file);
     echo '<pre>';
        print_r($upload_result);
        echo '</pre>';

     // Comprobar si la subida fue exitosa
     if (strpos($upload_result, '"name"') !== false) {
         // Extraer el nombre del archivo subido
         $response = json_decode($upload_result, true);
         $file_name = $response['name'];

         // Generar un nuevo token de acceso para visualizar el archivo
         try {
             $access_token = generar_token_acceso();
             $bucket_name = 'thdmx-bucket-test-careers_docs';
             $file_url = "https://storage.googleapis.com/download/storage/v1/b/{$bucket_name}/o/{$file_name}?alt=media&access_token={$access_token}";

             // Mostrar la URL del archivo subido
             echo "<h2>Archivo subido exitosamente</h2>";
             echo "<p>URL para ver el archivo: <a href='{$file_url}' target='_blank'>{$file_url}</a></p>";
         } catch (Exception $e) {
             echo "<p style='color: red;'>Error al generar la URL del archivo: " . $e->getMessage() . "</p>";
         }
     } else {
         echo "<p style='color: red;'>Error al subir el archivo a GCP.</p>";
     }
 } else {
     ?>

     <h2>Subir un archivo a Google Cloud Storage</h2>
     <form action="" method="post" enctype="multipart/form-data">
         <label for="file">Selecciona un archivo:</label>
         <input type="file" name="file" id="file" required>
         <br><br>
         <input type="submit" value="Subir archivo">
     </form>

     <?php
 }

 get_footer(); // Incluir el pie de página de WordPress
