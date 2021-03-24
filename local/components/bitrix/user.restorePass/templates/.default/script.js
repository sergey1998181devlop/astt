$(document).ready(function () {
    function showPopup(messege , classNa){
        var popup = $(document).find('#popup-notification-account');
        $(popup).find('.classNotific').addClass(classNa);
        $(document).find('#popup-notification-account').find('.classNotific').children('.textStrond').text(messege);
        $.fancybox.open([
            {
                src: '#popup-notification-account'
            }]);
        function funcs(){
            $(document).find('#popup-notification-account').find('strong').text('');
            $(popup).find('.classNotific').removeClass(classNa);
            $.fancybox.close([
                {
                    src: '#popup-notification-account'
                }]);
        }
        setTimeout(funcs, 2000);
    }
    $('#active-employees').find('.active-account').click(function (evt) {
        var pass1 =  $('#active-employees').find('input[name="NEWPASS_USER"]').val();
        var pass2 =  $('#active-employees').find('input[name="NEWPASS_USER_REPEAT"]').val();

        if(pass1 == pass2){
            $('#active-employees').submit();

        }else {
            showPopup("Пароли не совпадают" , 'alert alert-danger');
            evt.preventDefault();
            return false;
        }
    });
    // showPopup("Пароли не совпадают" , 'alert alert-danger');
});