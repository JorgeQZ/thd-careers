<?php
get_header();
?>
<style>
header {
    display: none;
}
</style>
<?php
the_title();
// the_content();


// echo get_the_title().'</br>';
// echo get_the_id().'</br>';
// print_r(get_field('ubicacion'));
echo'<br>';
// echo get_field('tipo_de_jornada');

// echo $tienda;
// var_dump(get_field('extra_data'));
// $extra_data = get_field('extra_data_data_tienda');

// print_r($extra_data);
// if(have_rows('extra_data')){
//     echo 'h';
//     $tienda_ = get_sub_field('data_tienda');
// }

// echo $tienda_;

$video = get_field('video');
 // Si el valor es un iframe, extraer la URL
 if (strpos($video, '<iframe') !== false) {
    preg_match('/src="([^"]+)"/', $video, $matches);
    if (!empty($matches[1])) {
        echo $matches[1]; // Devolver solo la URL
    }
}
?>



<?php
get_footer();
?>