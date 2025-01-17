(function ($) {

    $(".top__search-input-cont form .cont-icon .icon, .top__search-input-cont-sec form .cont-icon .icon").on('click', function (e) {
        $(".search-input").focus();
    });

    $(".search-input").focus(function (e) {
        e.preventDefault();
        $('.top__bar_cont').addClass('focus');
        //$('.overlay').show();
        $('.main__cont .cont-principal').css({ 'filter': 'blur(0.5px)' });
    });

    /*
        $(".search-input").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            console.log(value);
            if(value.length >= 3) {
                $(".overlay ul").addClass("active");
                $(".overlay ul li").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            }else{
                $(".overlay ul").removeClass("active");
            }
        });
    */

    $("#inputSearch").keyup(function () {
        if ($(this).val().length > 2) {
            $("#contenedor-resultados").show();
        } else {
            $("#contenedor-resultados").hide();
        }
    });
/*
    $(".overlay").on('click', function (e) {
        if ($(e.target).closest('#contenedor-resultados').length > 0) return;
        $('.top__bar_cont').removeClass('focus');
        $('.overlay').hide();
        $(".main__cont .cont-principal").css({ 'filter': '' });
        $('#inputSearch').val("");
        $('#contenedor-resultados').empty();

    });
*/
    $('#inputSearch').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            console.log("ahah");
            return false;
        }
    });

})(jQuery);