<?php
/**
 * Template Name: GCP File
 */
get_header();

// Código del formulario para subir el archivo
if (isset($_POST['submit']) && isset($_FILES['file_to_upload'])) {
    $file = $_FILES['file_to_upload'];
    $result = upload_to_gcp($file); // Llamar a la función que sube el archivo
    echo 'File uploaded successfully! Response: ' . $result;
}
?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file_to_upload" />
    <input type="submit" name="submit" value="Upload File" />
</form>


<?php get_footer(); ?>