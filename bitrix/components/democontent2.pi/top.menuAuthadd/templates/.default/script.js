$(document).ready(function () {
    function showPopup(messege , classNa){
        var popup = $(document).find('#popup-notification-account')
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


    $(document).on('click' , '.header-search-company' , function (){
        var SearchNum = $(document).find("input[name='searchInpHead']").val();
        $.ajax({
            type: "POST",
            url: "/ajax/searchCompany.php",
            data: ({
                SearchNum : SearchNum
            }),
            success: function(msg){

                var  array = JSON.parse(msg);
                console.log(array);
                if(array.FINDER == "Y"){
                    window.location.replace("/user/"+array.UF_ADMIN_COMPANY_ID+"/");
                }else {
                    showPopup(
                        " К сожалению,компания по данному поисковому запросу не найдена ",
                        "alert alert-danger"
                    );
                }

            }
        });

    });

})