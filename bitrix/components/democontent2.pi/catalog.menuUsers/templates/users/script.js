$(document).ready(function(){
    $('.btn-filter').click(function (e) {
        e.preventDefault();
        $('#category-nav').toggleClass('open');
        $('header .burger.open').click();
    });
    $('#category-nav .close').click(function (e) {
        e.preventDefault();
        $('#category-nav.open').removeClass('open');
    });
});