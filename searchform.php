<form id="search-form" action="get" role="search" autocomplete="off">
    <input type="text" id="inputSearch" class="search-input" name="s" placeholder="Buscar vacantes" onkeyup="fetch()">
    <div id="cont-icon-lupa" class="cont-icon">
        <img src="<?php echo get_template_directory_uri(  ).'/imgs/icon-search.svg' ?>" alt="" class="icon">
    </div>
</form>

<div class="resultados">
    <div id="contenedor-resultados">
    </div>
</div>