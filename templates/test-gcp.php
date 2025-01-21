<?php
/**
 * Template Name: GCP File
 */
get_header();

// Código del formulario para subir el archivo
if (isset($_POST['submit']) && isset($_FILES['file_to_upload'])) {
    $file = $_FILES['file_to_upload'];
    $gcs_response = upload_to_gcp($file); // Llamar a la función que sube el archivo
    echo 'File uploaded successfully! Response: ' . $gcs_response;

    if ($gcs_response) {
        // Asumimos que la respuesta contiene una URL al archivo en GCS
        $gcs_url = json_decode($gcs_response)->mediaLink;

        // Obtener el ID del usuario actual
        $user_id = get_current_user_id();

        // Guardar la URL del archivo de GCS como metadato del usuario
        update_user_meta($user_id, 'cv_gcs_url', $gcs_url);

        // Mostrar mensaje de éxito
        echo 'seeee';
    } else {
        echo '<p>Hubo un error al subir el archivo a GCS.</p>';
    }
}
?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file_to_upload" />
    <input type="submit" name="submit" value="Upload File" />
</form>


<?php get_footer(); ?>