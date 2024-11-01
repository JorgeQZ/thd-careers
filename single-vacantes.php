<?php
get_header();
?>
<style>
    header{
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


// $ubicacion = get_field('ubicacion');
// $tienda = get_field('data_tienda');
$tienda_ = get_sub_field('data_tienda');
// $distrito = get_field('data_distrito');
// echo $ubicacion['value'];
// $ubicacion_exploded = explode('-', $ubicacion['value']);
// $data_tienda = $ubicacion_exploded[0];
// $data_distrito = $ubicacion_exploded[1];
// echo $data_tienda;
// echo $data_distrito;

// echo $tienda;
echo $tienda_;
// echo $distrito;

// echo $tienda;
// var_dump(get_field('extra_data'));
$extra_data = get_field('extra_data');

print_r($extra_data['data_tienda']);
// if(have_rows('extra_data')){
//     echo 'h';
//     $tienda_ = get_sub_field('data_tienda');
// }

// echo $tienda_;
?>



<?php
get_footer();
?>