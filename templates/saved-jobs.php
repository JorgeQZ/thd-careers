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
    <div class="vacantes-int-cont">
        <div class="container">
            <div class="title">
                Tus vacantes
                <span>de interés</span>
            </div>
            <div class="display-cont">
                <div class="text" id="count-jobs">Visualizando 4 de 6 vacantes</div>
                <div class="line"></div>
            </div>
            <ul class="list job-list" id="favorites-list"></ul>
            <button id="load-more" style="display: block;">Cargar más</button>
        </div>
    </div><!-- Contenido de la página -->
</main>

<?php get_footer(); ?>
