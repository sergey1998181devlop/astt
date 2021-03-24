$(document).ready(function () {
    var cookieOptions = {expires: 365, path: '/'};
    if ($.cookie('coockieConfirmation')) {
        $('#message-cookie').remove();
    }  else {
        $('#message-cookie').css('display', 'block');
    }
    $('#message-cookie .close').click(function (e) {
        e.preventDefault();
        $.cookie('coockieConfirmation', true, cookieOptions);
        $(this).closest('#message-cookie').remove();
        $('header').removeAttr('style');
    });
    headerPadTop();
});
$(window).resize(function () {
    headerPadTop();
}); // end window.resize
function headerPadTop() {
    if ($('#message-cookie').length > 0 && $('#message-cookie').is(':visible')) {
        $('header').css({'padding-top': $('#message-cookie').outerHeight() + 36});
    }
}