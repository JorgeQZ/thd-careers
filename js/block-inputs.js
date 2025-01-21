(function($) {
    $(document).ready(function() {
        if(block_inputs_query_vars.user_role != 'administrator' &&block_inputs_query_vars.user_role != 'rh_admin') {

                $('.input-search').prop('disabled', true)

                $('input#title').prop('disabled', true);

                $('.edit-slug, .page-title-action, .categorias_vacantes-tabs .tabs, #categorias_vacantesdiv .hide-if-no-js').remove();

                $('.deshabilitado-checkbox input[type="checkbox"], .deshabilitado-checkbox input[type="radio"], .deshabilitado-checkbox input[type="image"]').each(function() {
                    $(this).prop('disabled', true);
                });

                $('.deshabilitado-checkbox .acf-actions').remove();
            }
    });
})(jQuery);
