$(document).ready(function(){
    $('header .header-top .right-nav nav ul li.itm-notification').hover(
        function(){ //over
            $(this).find('.dropdown').stop().slideDown();
        },
        function(){ //out
            $(this).find('.dropdown').stop().slideUp();
        }
    );
});