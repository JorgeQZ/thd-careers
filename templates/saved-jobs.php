<?php
/*
Template Name: Vacantes de intere´s
*/

get_header();


$tax_name = "categorias_vacantes";
$ubicaciones = get_unique_locations_with_values('centros-logisticos');
$unique_titles = get_unique_vacantes_titles_by_taxonomy('centros-logisticos');
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
    <?php
    if(is_user_logged_in(  )){
    ?>
    <div class="vacantes-int-cont">
        <div class="container">
            <div class="title">
                Tus vacantes
                <span>de interés</span>
            </div>
            <div class="display-cont">
                <div class="text" id="count-jobs">Visualizando 0 de 0 vacantes</div>
                <div class="line"></div>
            </div>
            <ul class="list job-list" id="favorites-list"></ul>
             <?php

             ?>
            <button id="load-more" style="display: block;">Cargar más</button>
        </div>
    </div><!-- Contenido de la página -->
    <?php
    }else{
        ?>
        <div class="vacantes-int-cont">
            <div class="container">
                <div class="title">
                    Tus vacantes
                    <span>de interés</span>
                </div>
                <div class="display-cont">
                   <div class="title">Para ver tus vacantes de interés debes  <a href="<?php echo home_url( ).'/login/?redirect_to='.urlencode( get_permalink() ); ?>"><span>INICIAR SESIÓN</span> </a></div>
                </div>
            </div>

        </div><!-- Contenido de la página -->
        <?php
        }?>
</main>

<?php get_footer(); ?>
