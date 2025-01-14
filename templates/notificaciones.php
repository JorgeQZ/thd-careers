<?php
/*
Template Name: Notificaciones
*/

get_header();

$estado = 'Aceptado';
$mensaje = ' Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum ';
?>

<!-- Banner con el titulo de la página -->
<div class="banner">
    <div class="container">
        <div class="title-cont">
           Vacantes de interés
        </div>
    </div>
</div><!-- Banner con el titulo de la página -->


<!-- Contenido de la página -->
<main>
    <div class="not-cont">
        <div class="container">
            <ul class="list">
                <li class="item">
                    <div class="img">
                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                    </div>
                    <div class="desc">
                        <a href="<?php the_permalink();?>">Titulo de la vacante postulada</a>
                        <div class="icon-cont">
                            <div class="text">Estado: <span><?php echo $estado; ?></span></div>
                        </div>

                        <div class="text mensaje">
                            <br>
                            Mensaje:
                            <?php echo $mensaje;?>
                        </div>
                    </div>
                    <div class="plus">+</div>
                </li>
                <li class="item">
                    <div class="img">
                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                    </div>
                    <div class="desc">
                        <a href="<?php the_permalink();?>">Titulo de la vacante postulada</a>
                        <div class="icon-cont">
                            <div class="text">Estado: <span><?php echo $estado; ?></span></div>
                        </div>

                        <div class="text mensaje">
                            <br>
                            Mensaje:
                            <?php echo $mensaje;?>
                        </div>
                    </div>
                    <div class="plus">+</div>
                </li>
                <li class="item">
                    <div class="img">
                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                    </div>
                    <div class="desc">
                        <a href="<?php the_permalink();?>">Titulo de la vacante postulada</a>
                        <div class="icon-cont">
                            <div class="text">Estado: <span><?php echo $estado; ?></span></div>
                        </div>

                        <div class="text mensaje">
                            <br>
                            Mensaje:
                            <?php echo $mensaje;?>
                        </div>
                    </div>
                    <div class="plus">+</div>
                </li>
                <li class="item">
                    <div class="img">
                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                    </div>
                    <div class="desc">
                        <a href="<?php the_permalink();?>">Titulo de la vacante postulada</a>
                        <div class="icon-cont">
                            <div class="text">Estado: <span><?php echo $estado; ?></span></div>
                        </div>

                        <div class="text mensaje">
                            <br>
                            Mensaje:
                            <?php echo $mensaje;?>
                        </div>
                    </div>
                    <div class="plus">+</div>
                </li>
            </ul>
        </div>
    </div><!-- Contenido de la página -->
</main>

<?php  get_footer(); ?>