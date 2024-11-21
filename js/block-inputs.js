(function($) {
    $(document).ready(function() {
       if (!$('body').hasClass('role-administrator')) {
        $('.input-search').prop('disabled', true)

        $('input#title').prop('disabled', true);

        $('.edit-slug, .page-title-action, .categorias_vacantes-tabs .tabs, #categorias_vacantesdiv .hide-if-no-js').remove();

        $('.deshabilitado-checkbox input[type="checkbox"]').each(function() {
            $(this).prop('disabled', true);
        });
        }
    });
})(jQuery);
