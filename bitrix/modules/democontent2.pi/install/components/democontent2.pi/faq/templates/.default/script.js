$(document).ready(function(){
    $('.list-questions .head').click(function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.item');
        $parent.find('.desc').stop().slideToggle(function () {
            $parent.toggleClass('open');
        })
    });
});