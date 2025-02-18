<?php


function restringir_acceso_rueda_de_valores() {
    // Verificar si el usuario tiene uno de los roles permitidos
    if (!current_user_can('administrator') && !current_user_can('admin_ti_careers')) {
        // Remover la opción del menú
        remove_menu_page('rueda-de-valores-acf');
        remove_menu_page('rueda-de-valores');
    }
}
add_action('admin_menu', 'restringir_acceso_rueda_de_valores', 99);

function display_rueda()
{

    $item_1 = get_field('item_1', 'option');
    $item_2 = get_field('item_2', 'option');
    $item_3 = get_field('item_3', 'option');
    $item_4 = get_field('item_4', 'option');
    $item_5 = get_field('item_5', 'option');
    $item_6 = get_field('item_6', 'option');
    $item_7 = get_field('item_7', 'option');
    $item_8 = get_field('item_8', 'option');

    $output = '<div class="rueda-cont">';
    $output .= '<img class="logo" src="'.get_template_directory_uri(  ).'/imgs/logo-thd.jpg" />';
    $output .=  file_get_contents(get_template_directory_uri(  ).'/img/Rueda.svg');
    $output .= '
        <div class="item-desc rueda-desc" data-id-item="_1">
            <img src="" alt="" class="icon">
            <div class="title">'.$item_1['texto'].'</div>
            <div class="desc">'.$item_1['descripcion'].'</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_2">
            <img src="" alt="" class="icon">
            <div class="title">'.$item_2['texto'].'</div>
            <div class="desc">'.$item_2['descripcion'].'</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_3">
            <img src="" alt="" class="icon">
            <div class="title">'.$item_3['texto'].'</div>
            <div class="desc">'.$item_3['descripcion'].'</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_4">
            <img src="" alt="" class="icon">
            <div class="title">'.$item_4['texto'].'</div>
            <div class="desc">'.$item_4['descripcion'].'</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_5">
            <img src="" alt="" class="icon">
            <div class="title">'.$item_5['texto'].'</div>
            <div class="desc">'.$item_5['descripcion'].'</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_6">
            <img src="" alt="" class="icon">
            <div class="title">crear valor para el accionista</div>
            <div class="desc">Hacemos lo necesario para que el negocio crezca de manera sustentable. </div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_7">
            <img src="" alt="" class="icon">
            <div class="title">respeto por todos y todas</div>
            <div class="desc">Reconocemos el valor de toda persona, en un ambiente inclusivo y de respeto mutuo.</div>
        </div>
        <div class="item-desc rueda-desc" data-id-item="_8">
            <img src="" alt="" class="icon">
            <div class="title">espiritu empresarial</div>
            <div class="desc">Motivamos a nuestros(as) asociados(as) a que sean dueños(as) de lo que hacen, para lograr su crecimiento y marcar la diferencia.</div>
        </div>
    </div>';


    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const items = [
                { id: "#texto_item_1", text: "'.$item_1['texto'].'" },
                { id: "#texto_item_2", text: "'.$item_2['texto'].'" },
                { id: "#texto_item_3", text: "'.$item_3['texto'].'" },
                { id: "#texto_item_4", text: "'.$item_4['texto'].'" },
                { id: "#texto_item_5", text: "'.$item_5['texto'].'" },
                { id: "#texto_item_6", text: "'.$item_6['texto'].'" },
                { id: "#texto_item_7", text: "'.$item_7['texto'].'" },
                { id: "#texto_item_8", text: "'.$item_8['texto'].'" }
            ];

            items.forEach(item => {
                const element = document.querySelector(item.id);
                if (element) {
                    element.textContent = item.text;
                }
            });
        });
    </script>';
    return $output; // Devuelve el contenido
}

add_shortcode('rueda_thd', 'display_rueda');


