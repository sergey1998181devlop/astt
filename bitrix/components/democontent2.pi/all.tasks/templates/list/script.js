
var myMap, multiRoute ,  collection;

$(document).ready(function () {

    var cityReadyOneLoadId = $(document).find('.idCityTopPL').attr('data-target-idcity');
    var cityReadyOneLoadname = $(document).find('.idCityTopPL').attr('data-target-namecity');
    $(document).find('input[name="nameCity"]').val(cityReadyOneLoadId);
    $(document).find('.head').text(cityReadyOneLoadname);

    function showPopup(messege, classNa) {
        var popup = $(document).find('#popup-notification-account')
        $(popup).find('.classNotific').addClass(classNa);
        $(document).find('#popup-notification-account').find('.classNotific').children('.textStrond').html(messege);
        $.fancybox.open([
            {
                src: '#popup-notification-account'
            }]);

        function funcs() {
            $(document).find('#popup-notification-account').find('strong').text('');
            $(popup).find('.classNotific').removeClass(classNa);
            $.fancybox.close([
                {
                    src: '#popup-notification-account'
                }]);
        }
        // setTimeout(funcs, 2000);
    }
    $(document).find('#popup-notification-account').on('click' , '.textStrond a' , function () {

        window.open("/user/settings/", '_blank').focus();
    })
    $(document).on('click', '.see-phone-manager-notPhone', function () {

        showPopup(
            "Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>",
            "alert alert-success"
        );
    })


    function loadMapYa(data){


        groupsJs = data;
        var itemsMess = [];
        groupsJs.items.forEach( // перебираем все элементы массива array
            function sumNumber(currentValue) {
                var centerMessSplit = currentValue.center.split(",");

                var arCur = {
                    center: [centerMessSplit[0], centerMessSplit[1]],
                    name: currentValue.name,
                    description: currentValue.description,
                    phone: currentValue.phone,
                    start_date: currentValue.start_date,
                    count_day: currentValue.count_day,
                    beznal: currentValue.beznal,
                    nall: currentValue.nall,
                    nds: currentValue.nds,
                    nameCreatedAuthor: currentValue.nameCreatedAuthor,
                    managerTask: currentValue.managerTask,
                    countDayWord: currentValue.countDayWord,
                    nameCompany: currentValue.nameCompany,
                    classButton: currentValue.classButton,
                    dopClassButton: currentValue.dopClassButton,
                    nameCreatedAuthorClassDop: currentValue.nameCreatedAuthorClassDop,


                    subTypeEl: currentValue.subTypeEl,
                    typeEl: currentValue.typeEl,
                    item_code: currentValue.item_code,
                }

                itemsMess.push(arCur)
            }
        );
        var groups = [
            {
                name: groupsJs.name,
                style: groupsJs.style,
                items: itemsMess
            }
        ];

        $('#map-area').css({
            'height': '875px',
            'width': '875px',
            'margin-bottom': '20px'
        });

        ymaps.ready(function (ya) {
            // Создание экземпляра карты.
            myMap = new ymaps.Map('map-area', {
                center: [50.443705, 30.530946],
                zoom: 14
            }, {
                searchControlProvider: 'yandex#search'
            }),
                // Контейнер для меню.
                menu = $('<ul class="menu" id="menu-map-listItems"/>');

            for (var i = 0, l = groups.length; i < l; i++) {
                createMenuGroup(groups[i]);
            }

            function createMenuGroup(group) {
                // Пункт меню.
                var menuItem = $('<li><a href="#">' + group.name + '</a></li>'),
                    // Коллекция для геообъектов группы.
                    collection = new ymaps.GeoObjectCollection(null, {preset: group.style, geodesic: true }),
                    // Контейнер для подменю.
                    submenu = $('<ul class="submenu"/>');

                // Добавляем коллекцию на карту.

                myMap.geoObjects.add(collection);
                // Добавляем подменю.
                menuItem
                    .append(submenu)
                    // Добавляем пункт в меню.
                    .appendTo(menu)
                    // По клику удаляем/добавляем коллекцию на карту и скрываем/отображаем подменю.
                    .find('a')
                    .bind('click', function () {

                        if (collection.getParent()) {
                            myMap.geoObjects.remove(collection);
                            submenu.hide();
                        } else {
                            myMap.geoObjects.add(collection);
                            submenu.show();
                        }
                    });
                for (var j = 0, m = group.items.length; j < m; j++) {
                    createSubMenu(group.items[j], collection, submenu);
                }
            }

            function createSubMenu(item, collection, submenu) {
                // Пункт подменю.
                if (item.nameCompany !== "") {
                    nameCompany = '<br><div class="menedged-is  " >Компания:<p class="' + item.nameCreatedAuthorClassDop + '"> ' + item.nameCompany + ' </p></div></div>';
                    manager = '';
                } else {
                    manager = '<div class="menedged-is">Менеджер:<p> ' + item.managerTask + ' </p></div></div>';
                }


                if (item.classButton == "without-auth") {
                    // nameCompany = onclick=showPopup("Для просмотра содержимого подтвердите аккаунт на странице <br><a >Настройки профиля</a>" , "alert alert-success");';
                    //
                    $(document).on('click', '.without-auth', function () {
                        $.fancybox.open([
                            {
                                src: '#popup-registration'
                            }]);
                    })
                }
                var submenuItem = $('<li><a href="/' + item.typeEl + '/' + item.subTypeEl + '/' + item.item_code + '/">' + item.name + '</a></li>'),
                    // Создаем метку.
                    // placemark = new ymaps.Placemark(item.center, {balloonContent: item.name});

                    placemark = new ymaps.Placemark(item.center, {
                        // Зададим содержимое заголовка балуна.
                        balloonContentHeader: '<a href="/' + item.typeEl + '/' + item.subTypeEl + '/' + item.item_code + '/" >' + item.name + '</a><br><br>' +
                            '<div class="company-phone-detail">' +
                            '<div class="btn btn-green ' + item.dopClassButton + ' ' + item.classButton + '"   >Показать телефон</div>' +
                            '<div class="phone-non" style="display:none">' +
                            'Телефон:<p>' + item.phone + '</p>' +
                            '</div>' + nameCompany +
                            manager +
                            '<span class="countday">C ' + item.start_date + ' на ' + item.count_day + ' ' + item.countDayWord + '</span>',
                        // Зададим содержимое основной части балуна.
                        balloonContentBody: '<span class="desc-in-map">Описание проекта:<br>' + item.description + '</span>',

                        // Зададим содержимое нижней части балуна.
                        balloonContentFooter: '',
                        // Зададим содержимое всплывающей подсказки.
                        hintContent: item.name
                    });

                // Добавляем метку в коллекцию.
                collection.add(placemark);
                // Добавляем пункт в подменю.
                submenuItem
                    .appendTo(submenu)
                    // При клике по пункту подменю открываем/закрываем баллун у метки.
                    .find('a')
                    .bind('click', function () {
                        if (!placemark.balloon.isOpen()) {
                            placemark.balloon.open();
                        } else {
                            placemark.balloon.close();
                        }
                        return false;
                    });
            }

            // Добавляем меню в .block-map-input-item .
            menu.appendTo($('.block-map-input-item'));
            // Выставляем масштаб карты чтобы были видны все группы.так как влкадка закрытая ) вешаю байнд клик
            $('.sorty-panel-maplist p:eq(1)').bind('click', function () {
                myMap.container.fitToViewport();
                myMap.setBounds(myMap.geoObjects.getBounds());
            })

        });
    }
    groupsJs = BX.message('group');

    if(groupsJs["items"]){
        if(groupsJs["items"].length > 0){

            loadMapYa(groupsJs);

        }
    }

    $('.CategoryTitle').change(function () {
        if($(this).hasClass('checkedAllThisGroup')){
            $(this).removeClass('checkedAllThisGroup');
            $(this).closest('li').find('.subCategoryFilter').prop('checked' , false);
        }else {
            $(this).addClass('checkedAllThisGroup');
            $(this).closest('li').find('.subCategoryFilter').prop('checked' , true);
        }
    });

    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });

    $('.check-all').click(function () {
        $(document).find('.CategoryTitle').prop('checked' , true) , $(document).find('.subCategoryFilter').prop('checked' , true) ;
    });
    $('.reset-all').click(function () {
        $(document).find('.CategoryTitle').prop('checked' , false) , $(document).find('.subCategoryFilter').prop('checked' , false) ;
    });
    $('.subCategoryFilter').change(function (){
       var TitleHObj = $(this).closest('.check-list-tasks').find('.CategoryTitle') ,
           SubCount  = $(this).closest('.check-list-tasks').find('.subCategoryFilter').length ,
           SubCountChecked  = $(this).closest('.check-list-tasks').find('.subCategoryFilter:checked').length;
       //если кол-во чекнутых равно общему кол-во то ставлю галочку на разделе выше / если кол-во не равно кол-во чекнутых , но чекнутых больше одного  - ставлю бекграунд
       if(SubCount == SubCountChecked){
           $(TitleHObj).prop('checked' , true);
           $(TitleHObj).next('label').find('span').css('background' , '');
           $(TitleHObj).addClass('checkedAllThisGroup');
       }else {
           $(TitleHObj).removeClass('checkedAllThisGroup');
           $(TitleHObj).prop('checked' , false);
           if(SubCountChecked > 0){
               // $(this).closest('.open').find('.icon-wrap_subIn').css('background' , '#2b8bf0');
               $(TitleHObj).next('label').find('span').css('background' , '#2b8bf0');
           }else {
               //иначе убираю бекграунд
               $(TitleHObj).next('label').find('span').css('background' , '');
           }
       }
    });
    //filter-open-tabs
    function modalFilterTabs() {
        if( $('body').find('.preloaderForPlacehopldBl').length === 0 ){
            $(document).find('.preloaderForPlacehopld').after('<div class="preloaderForPlacehopldBl" ></div>')
        }
        $('.filter-sorty-to-items').find('.row-modal').toggle();
        $(document).find('.preloaderForPlacehopldBl').toggle();
    }
    function cursorBlockStatusOpend() {
        if($(document).find('.lighten-3-opened').css('display') == 'none'){
          //закрыт -> значит открываем
            $(document).find('.lighten-3-opened').show();
            $(document).find('.lighten-3-closed').hide();
        }else {
            //открыт -> значит закрываем
            $(document).find('.lighten-3-opened').hide();
            $(document).find('.lighten-3-closed').show();
        }
    }


    // block-sections
    $('.filter-sorty-to-items ').find('.input-group-append-modal').click(function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
    });
    $(document).find('.win-to-amber').click(function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
    });



    function updateListTasks(clickMoreLoad = false ) {


       currentlimit = "all";
        // город
        CityId =  $(document).find('input[name="nameCity"]').val();
        //новые старые дешевле дороже / по умолчанию новые
        sortRanj = "new";
            if($(document).find('.sorty-panel-alltasks li').find('ativeSrtAlltasks').attr('targetCode') !== "new"){
                 sortRanj =   $(document).find('.sorty-panel-alltasks ').find('.ativeSrtAlltasks').attr('targetCode');
            }
        if(clickMoreLoad == true){
            var ofset = $(document).find('.load-more-tasks').attr('data-ofset');
        }
        if($(document).find('.sorty-panel-maplist p:eq(0)').hasClass('activeInp')){
            limitMap = "LIST"
            $(document).find('.sorty-limit').show();
            var num = $(document).find('.active-li-dop').text();
            currentlimit = Number.parseInt(num);
        }
        if($(document).find('.sorty-panel-maplist p:eq(1)').hasClass('activeInp')){
            limitMap = "MAP"
            $(document).find('.sorty-limit').hide();
            currentlimit = "all";
        }





        //левый фильтор
        var popsDropDownData = $("#date-start-work").val(),
        popsDropDownCity = $(".change-update-prop-city option:selected").val(),
        propsDropDownCityCountID = $(".change-update-prop-city option:selected").attr("targ-code-citiId"),
        popsDropDownYearDay = $(".change-update-prop-day-year option:selected").val();
        var objParamsDop = {
            Date : popsDropDownData,
            CountDayOrder : popsDropDownYearDay,
            City : popsDropDownCity,
            CityId : propsDropDownCityCountID
        };

        var propsNavFilterCheckbox = $("input[name='propsLeftFilter']:checked").map(function(){return $(this).val();}).get();
        var propsNavFilter = $(document).find('.props-nav-filter').serializeArray();
        //правый фильтер
        var propsFilterSpecialization = $("input[name='SubCategories']:checked").map(function(){return $(this).val();}).get();


        $.ajax({
            type: "POST",
            url: "/ajax/updateListAllTasks.php",
            data: ({
                propLeftFilterDopParams : objParamsDop,
                propsLeftFilterCheckbox: propsNavFilterCheckbox,
                popsRightSpecialization: propsFilterSpecialization,
                propsRnj : sortRanj,
                offset : ofset,
                Ajax : "isset",
                limitMap : limitMap,
                currentlimit : currentlimit,
                CityId:CityId
            }),
            success: function(msg ){

                const array = JSON.parse(msg);

                if(array['NOTEMPTY'] == "Y"){
                    if(clickMoreLoad == false){
                        $(document).find(".tasks-list").empty();
                    }
                    var object = array['MESSITEMS'];

                    itemsNewAjax = [];
                    nameCompany = '';
                    manager = ''
                    $.each(object, function(index, value) {
                        // console.log(value);
                        if($(document).find('.sorty-panel-maplist p:eq(1)').hasClass('activeInp')) {
                            if (value.nameCompany !== "") {
                                nameCompany = '<br><div class="menedged-is  " >Компания:<p class="' + value.nameCreatedAuthorClassDop + '"> ' + value.nameCompany + ' </p></div></div>';
                                managerTask = '<div class="menedged-is">Менеджер:<p> ' + value.managerTask + ' </p></div></div>';
                                manager = '';
                            } else {
                                manager = '<div class="menedged-is">Менеджер:<p> ' + value.managerTask + ' </p></div></div>';
                            }



                            var newMessCord = {
                                center : value.MAP_COORD,
                                nameItem : value.NAME,

                                name:  value.NAME,
                                description: value.description,
                                phone: value.phone,
                                start_date: value.start_date,
                                count_day: value.count_day,
                                beznal: value.BEZNALL,
                                nall: value.NALL,
                                nds: value.NDS,
                                nameCreatedAuthor: value.nameCreatedAuthor,
                                managerTask: value.managerTask,
                                countDayWord: value.countDayWord,
                                nameCompany: value.nameCompany,
                                classButton: value.classButton,
                                dopClassButton: value.dopClassButton,
                                nameCreatedAuthorClassDop: value.nameCreatedAuthorClassDop,


                                subTypeEl: value.subTypeEl,
                                typeEl: value.typeEl,
                                item_code: value.item_code,
                            };

                            itemsNewAjax[index] = newMessCord;
                        }

                        if(value.CUR_FAVOURITE == "Y"){
                            var star =                             "                                                        <span class=\"span-star twe-te start-first active \" style=\"display: block;\">\n" +
                                "<svg version=\"1.1\" id=\"Capa_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" style=\"enable-background:new 0 0 512 512;\" xml:space=\"preserve\">\n" +
                                "<path style=\"fill:#FFDC64;\" d=\"M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0\n" +
                                "\tl-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174\n" +
                                "\tc-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705\n" +
                                "\tl121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z\"></path>\n" +
                                "<path style=\"fill:#FFC850;\" d=\"M267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0l-63.941,148.478L19.243,190.24\n" +
                                "\tc-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174c-2.489,11.042,9.436,19.707,19.169,13.927l31.024-18.422\n" +
                                "\tc4.294-176.754,86.42-301.225,151.441-372.431L267.847,26.833z\"></path>\n" +
                                "<path d=\"M510.967,196.781c-2.56-7.875-9.271-13.243-17.518-14.008l-156.535-14.518l-31.029-72.054\n" +
                                "\tc-1.639-3.804-6.049-5.562-9.854-3.922c-3.804,1.638-5.56,6.05-3.922,9.853l32.791,76.144c1.086,2.521,3.463,4.248,6.196,4.501\n" +
                                "\tl160.969,14.929c3.194,0.296,4.307,2.692,4.638,3.708c0.33,1.016,0.838,3.608-1.572,5.725L373.678,313.835\n" +
                                "\tc-2.063,1.812-2.97,4.605-2.366,7.283l35.545,157.703c0.705,3.13-1.229,4.929-2.095,5.557c-0.864,0.628-3.17,1.915-5.931,0.274\n" +
                                "\tl-139.003-82.537c-2.359-1.4-5.299-1.4-7.657,0l-139.003,82.537c-2.76,1.642-5.066,0.354-5.931-0.274\n" +
                                "\tc-0.865-0.628-2.8-2.427-2.095-5.556l18.348-81.406c0.911-4.041-1.627-8.055-5.667-8.965c-4.047-0.91-8.054,1.627-8.965,5.667\n" +
                                "\tl-18.348,81.407c-1.82,8.078,1.211,16.12,7.91,20.988c6.699,4.866,15.285,5.265,22.403,1.037l135.174-80.264l135.174,80.264\n" +
                                "\tc3.28,1.947,6.87,2.913,10.443,2.913c4.185,0,8.347-1.325,11.96-3.95c6.7-4.868,9.73-12.909,7.91-20.989l-34.565-153.36\n" +
                                "\tL505.029,218.41C511.251,212.944,513.525,204.657,510.967,196.781z\"></path>\n" +
                                "<path d=\"M116.085,362.057c-0.911,4.041,1.627,8.055,5.667,8.965c0.556,0.125,1.11,0.186,1.656,0.186c3.43,0,6.524-2.367,7.309-5.853\n" +
                                "\tl9.97-44.237c0.604-2.679-0.304-5.473-2.366-7.283L16.87,207.141c-2.41-2.117-1.902-4.709-1.571-5.725\n" +
                                "\tc0.33-1.016,1.442-3.412,4.637-3.708l160.968-14.929c2.733-0.253,5.11-1.98,6.196-4.501L251.04,29.801\n" +
                                "\tc1.269-2.946,3.891-3.265,4.959-3.265c1.069,0,3.691,0.318,4.96,3.264l17.367,40.327c1.64,3.804,6.05,5.561,9.854,3.922\n" +
                                "\tc3.804-1.638,5.56-6.05,3.922-9.853l-17.367-40.328c-3.276-7.605-10.454-12.33-18.736-12.33c-8.28,0-15.459,4.725-18.735,12.331\n" +
                                "\tl-62.18,144.388L18.551,182.773c-8.245,0.765-14.958,6.132-17.518,14.008c-2.559,7.875-0.284,16.163,5.938,21.629l118.106,103.755\n" +
                                "\tL116.085,362.057z\"></path>                            </svg></span>\n" ;
                            var starTwe =                          "                                                        <span class=\"span-star twe  \" style=\"display: none;\" \"=\"\">\n" +
                                "                                                        <svg version=\"1.1\" id=\"Capa_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" style=\"enable-background:new 0 0 512 512;\" xml:space=\"preserve\">\n" +
                                "<path style=\"fill:rgba(100,60,37,0.39);\" d=\"M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0\n" +
                                "\tl-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174\n" +
                                "\tc-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705\n" +
                                "\tl121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z\"></path></svg>\n" +
                                "\n" +
                                "\n" +
                                "                                                        </span>\n" ;
                        }else {
                            var star =                          "                                                        <span class=\"span-star twe start-first active \" style=\"display: block;\" \"=\"\">\n" +
                                "                                                        <svg version=\"1.1\" id=\"Capa_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" style=\"enable-background:new 0 0 512 512;\" xml:space=\"preserve\">\n" +
                                "<path style=\"fill:rgba(100,60,37,0.39);\" d=\"M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0\n" +
                                "\tl-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174\n" +
                                "\tc-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705\n" +
                                "\tl121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z\"></path></svg>\n" +
                                "\n" +
                                "\n" +
                                "                                                        </span>\n" ;
                            var starTwe =                              "                                                        <span class=\"span-star twe-te \" style=\"display: none;\">\n" +
                                "<svg version=\"1.1\" id=\"Capa_1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" viewBox=\"0 0 512 512\" style=\"enable-background:new 0 0 512 512;\" xml:space=\"preserve\">\n" +
                                "<path style=\"fill:#FFDC64;\" d=\"M492.757,190.241l-160.969-14.929L267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0\n" +
                                "\tl-63.941,148.478L19.243,190.24c-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174\n" +
                                "\tc-2.489,11.042,9.436,19.707,19.169,13.927l139.001-82.537l139.002,82.537c9.732,5.78,21.659-2.885,19.17-13.927l-35.544-157.705\n" +
                                "\tl121.452-106.694C508.583,205.305,504.029,191.286,492.757,190.241z\"></path>\n" +
                                "<path style=\"fill:#FFC850;\" d=\"M267.847,26.833c-4.477-10.396-19.218-10.396-23.694,0l-63.941,148.478L19.243,190.24\n" +
                                "\tc-11.271,1.045-15.826,15.064-7.322,22.534l121.452,106.694L97.828,477.174c-2.489,11.042,9.436,19.707,19.169,13.927l31.024-18.422\n" +
                                "\tc4.294-176.754,86.42-301.225,151.441-372.431L267.847,26.833z\"></path>\n" +
                                "<path d=\"M510.967,196.781c-2.56-7.875-9.271-13.243-17.518-14.008l-156.535-14.518l-31.029-72.054\n" +
                                "\tc-1.639-3.804-6.049-5.562-9.854-3.922c-3.804,1.638-5.56,6.05-3.922,9.853l32.791,76.144c1.086,2.521,3.463,4.248,6.196,4.501\n" +
                                "\tl160.969,14.929c3.194,0.296,4.307,2.692,4.638,3.708c0.33,1.016,0.838,3.608-1.572,5.725L373.678,313.835\n" +
                                "\tc-2.063,1.812-2.97,4.605-2.366,7.283l35.545,157.703c0.705,3.13-1.229,4.929-2.095,5.557c-0.864,0.628-3.17,1.915-5.931,0.274\n" +
                                "\tl-139.003-82.537c-2.359-1.4-5.299-1.4-7.657,0l-139.003,82.537c-2.76,1.642-5.066,0.354-5.931-0.274\n" +
                                "\tc-0.865-0.628-2.8-2.427-2.095-5.556l18.348-81.406c0.911-4.041-1.627-8.055-5.667-8.965c-4.047-0.91-8.054,1.627-8.965,5.667\n" +
                                "\tl-18.348,81.407c-1.82,8.078,1.211,16.12,7.91,20.988c6.699,4.866,15.285,5.265,22.403,1.037l135.174-80.264l135.174,80.264\n" +
                                "\tc3.28,1.947,6.87,2.913,10.443,2.913c4.185,0,8.347-1.325,11.96-3.95c6.7-4.868,9.73-12.909,7.91-20.989l-34.565-153.36\n" +
                                "\tL505.029,218.41C511.251,212.944,513.525,204.657,510.967,196.781z\"></path>\n" +
                                "<path d=\"M116.085,362.057c-0.911,4.041,1.627,8.055,5.667,8.965c0.556,0.125,1.11,0.186,1.656,0.186c3.43,0,6.524-2.367,7.309-5.853\n" +
                                "\tl9.97-44.237c0.604-2.679-0.304-5.473-2.366-7.283L16.87,207.141c-2.41-2.117-1.902-4.709-1.571-5.725\n" +
                                "\tc0.33-1.016,1.442-3.412,4.637-3.708l160.968-14.929c2.733-0.253,5.11-1.98,6.196-4.501L251.04,29.801\n" +
                                "\tc1.269-2.946,3.891-3.265,4.959-3.265c1.069,0,3.691,0.318,4.96,3.264l17.367,40.327c1.64,3.804,6.05,5.561,9.854,3.922\n" +
                                "\tc3.804-1.638,5.56-6.05,3.922-9.853l-17.367-40.328c-3.276-7.605-10.454-12.33-18.736-12.33c-8.28,0-15.459,4.725-18.735,12.331\n" +
                                "\tl-62.18,144.388L18.551,182.773c-8.245,0.765-14.958,6.132-17.518,14.008c-2.559,7.875-0.284,16.163,5.938,21.629l118.106,103.755\n" +
                                "\tL116.085,362.057z\"></path>                            </svg></span>\n" ;
                        }
                        $(document).find(".tasks-list").append("<div class='task-preview task-preview-newDez'>" +
                            "  <div class=\"image-for-task\">\n" +
                            "                                            <img src=\"/images/itemsImages/"+value.UF_IBLOCK_TYPE+".png\">\n" +
                            "                                        </div>" +
                            "<div class='tbl tbl-fixed'>" +
                            "<div class=\"tbc\">\n" +
                            "                                                                                            <div class=\"complain\">\n" +
                            "                                                    <div class=\"complain-item\" data-id="+value.UF_ITEM_ID+" data-path=\"/bitrix/components/democontent2.pi/all.tasks/templates/list/ajaxFavorites.php\" data-category=\"60\">\n" +
                            "\n" +

                            "\n" + star +"\n" +
                            "\n" + starTwe +"\n" +
                            "                                                    </div>\n" +
                            "                                                </div>\n" +
                            "                                                                                        <div class=\"ttl medium\"> "+value.NAME+"</div>\n" +
                            "                                            <div class=\"desc\">\n" +
                            "                                                " +value.DESC+"                                                                                                \n" +
                            "                                                                                            </div>\n" +
                            "                                            <div class=\"btm clearfix\">\n" +
                            "                                                <div class=\"left\">\n" +
                            "\n" +
                            "                                                    <div class=\"date-box\">\n" +
                            "\n" +
                            "                                                                                                                    <svg class=\"icon icon_time\">\n" +
                            "                                                                <use xlink:href=\"/bitrix/templates/democontent2.pi/images/sprite-svg.svg#time\"></use>\n" +
                            "                                                            </svg>\n" +
                            "                                                            <span title=\"C 12.02.2021 на 8 смен\" class=\"timestamp\">C  " +value.START_DATA+"  на    " +value.COUNT_DAY+"&nbsp" +value.WORD_COUNT_DAY+"</span>\n" +
                            "                                                                                                            </div>\n" +
                            "                                                    <div class=\"location-box\">\n" +
                            "                                                        <svg class=\"icon icon_location\">\n" +
                            "                                                            <use xlink:href=\"/bitrix/templates/democontent2.pi/images/sprite-svg.svg#location\"></use>\n" +
                            "                                                        </svg>\n" +
                            "                                                        " +value.CITY_NAME+"                                                 </div>\n" +
                            "\n" +
                            "                                                </div>\n" +
                            "                                                <div class=\"right\" style=\"display: none\">\n" +
                            "                                                    <div class=\"responses-box left\">\n" +
                            "                                                        <svg class=\"icon icon_comment\">\n" +
                            "                                                            <use xlink:href=\"/bitrix/templates/democontent2.pi/images/sprite-svg.svg#comment\"></use>\n" +
                            "                                                        </svg>\n" +
                            "                                                        нет откликов                                                    </div>\n" +
                            "                                                    <div class=\"views-box left\">\n" +
                            "                                                        <svg class=\"icon icon_eye\">\n" +
                            "                                                            <use xlink:href=\"/bitrix/templates/democontent2.pi/images/sprite-svg.svg#eye\"></use>\n" +
                            "                                                        </svg>\n" +
                            "                                                        0                                                    </div>\n" +
                            "                                                </div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>" +
                            value.RIGHT_BLOCK +
                            "</div> " +
                            "<a class=\"lnk-abs\" href=\"/"+value.UF_IBLOCK_TYPE+"/"+value.UF_IBLOCK_CODE+"/"+value.UF_CODE+"/\"></a></div>");
                    });


                    if($(document).find('.sorty-panel-maplist p:eq(1)').hasClass('activeInp')){
                        //вообщем тут я знаю что нажата карта и нужно обновить именно ее
                        myMap.geoObjects.removeAll();
//удаляю все метки старые с карты и наполяю новый массив данными из фильтра
                        var messFeatures = [];
                        $.each(itemsNewAjax , function(index,item) {
                            var centerMessSplit = item.center.split(",");
                            var newMessAfterAjax = {
                                type: "Feature",
                                id: index,
                                geometry: {
                                    type: "Point",
                                    coordinates: [centerMessSplit[0] , centerMessSplit[1]]
                                },
                                properties: {
                                    // Зададим содержимое заголовка балуна.
                                    balloonContentHeader: '<a href="/' + item.typeEl + '/' + item.subTypeEl + '/' + item.item_code + '/" >' + item.name + '</a><br><br>' +
                                        '<div class="company-phone-detail">' +
                                        '<div class="btn btn-green ' + item.dopClassButton + ' ' + item.classButton + '"   >Показать телефон</div>' +
                                        '<div class="phone-non" style="display:none">' +
                                        'Телефон:<p>' + item.phone + '</p>' +
                                        '</div>' + nameCompany +
                                        manager +
                                        managerTask +
                                        '<span class="countday">C ' + item.start_date + ' на ' + item.count_day + ' ' + item.countDayWord + '</span>',
                                    // Зададим содержимое основной части балуна.
                                    balloonContentBody: '<span class="desc-in-map">Описание проекта:<br>' + item.description + '</span>',

                                    // Зададим содержимое нижней части балуна.
                                    balloonContentFooter: '',
                                    // Зададим содержимое всплывающей подсказки.
                                    hintContent: item.name
                                }
                            }
                            messFeatures.push(newMessAfterAjax);
                        });
                        var collection = {
                            type: "FeatureCollection",
                            features: messFeatures
                        };
                        //вызываю обьект менеджер и наполняю его новыми обьектами
                        objectManager = new ymaps.ObjectManager({ clusterize: true });


//добавляю обьекты на карту
                        objectManager.add(collection);
//получаю все обьекты и проставляю на основе их увеличение карты  / чтобы видно было все
                        myMap.geoObjects.add(objectManager);
                        myMap.container.fitToViewport();
                        myMap.setBounds(myMap.geoObjects.getBounds() , { checkZoomRange: true, duration : 10});



                     

                    }

                    var offset = $(document).find('.load-more-tasks').attr('data-ofset');
                    var countElementsal = $(document).find('.tasks-list').find('.task-preview ').length;
                    if($(document).find('.block-btn-load-more').length == 0){
                        $(document).find('.tasks-list').after('<div class=" block-btn-load-more">\n' +
                            '\n' +
                            '                        <div class="load-more-tasks btn btn-green" data-ofset="">\n' +
                            '                            Показать еще\n' +
                            '                        </div>\n' +
                            '\n' +
                            '                    </div>');
                    }
                    $(document).find('.load-more-tasks').attr('data-ofset' , countElementsal + 1 );
                }else {
                    if(clickMoreLoad == false){
                        $(document).find(".tasks-list").empty();
                        $(document).find(".tasks-list").append('<div class="alert alert-info alert-info-allList" role="alert">\n' +
                            'К сожалению,заявки по данному поисковому запросу не найдены \n' +
                            '</div>');
                        $(document).find('.block-btn-load-more').remove();
                    }else {
                        $(document).find('.block-btn-load-more').remove();
                    }
                    if($(document).find('.sorty-panel-maplist p:eq(1)').hasClass('activeInp')){

                        myMap.geoObjects.removeAll();
                        showPopup(
                            "К сожалению,заявки по данному поисковому запросу не найдены",
                            "alert alert-success"
                        );

                    };

                }
            }
        });
    }
    $(document).find('.sorty-limit-ul-dop').find('.list-li').on('click' , function () {

        if($(document).find('.sorty-panel-maplist p:eq(0)').hasClass('activeInp')){
            $(document).find('.sorty-limit-ul-dop').find('.list-li').removeClass('active-li-dop');
            $(this).addClass('active-li-dop');
            updateListTasks(false , true);
        }


    });
    $('.check-all').click(function () {
        $(this).addClass('active');
        $(document).find('.reset-all').removeClass('active');
        $(document).find('.CategoryTitle').prop('checked' , true) , $(document).find('.subCategoryFilter').prop('checked' , true) ;
    });
    $('.reset-all').click(function () {
        $(this).addClass('active');
        $(document).find('.check-all').removeClass('active');
        $(document).find('.CategoryTitle').prop('checked' , false) , $(document).find('.subCategoryFilter').prop('checked' , false) ;
    });



    $(document).find('.popup-location-users-filter').find('.putcityow').on('click' , function () {
        var codeCity = $(this).attr('target-city-code');
        var nameCity = $(this).text();
        $(document).find('.location-users-filter').find('.head').text(nameCity);
        $(document).find('input[name="nameCity"]').val(codeCity);
        $.fancybox.close([
            {
                src: '#popup-location-users'
            }]);
        updateListTasks();
    })
    $(document).find('.sorty-panel-maplist').children('p').click(function () {
        if($(this).hasClass('activeInp')){
        }else {
            $(document).find('.sorty-panel-maplist').children('p').removeClass('activeInp');
            $(this).addClass('activeInp');
            var elemBlockview =  $(this).attr('data-targ');
            $(document).find('.block-short-menu').hide();
            $(document).find('.'+elemBlockview).show();
            updateListTasks();
        }
    });
    $.datepicker.regional['ru'] = {
        closeText: datepicerCloseTxt,
        prevText: datepicerPrevTxt,
        nextText: datepicerNextTxt,
        currentText: datepicerCurrentTxt,
        monthNames: datepicerMonthName,
        monthNamesShort: datepicerMonthNameShort,
        dayNames: datepicerDaysName,
        dayNamesShort: datepicerDaysNameShort,
        dayNamesMin: datepicerDaysNameMin,
        weekHeader: datepicerWeekTxt,
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    datapickerFilter = $('.ui-datepicker').datepicker({
        dateFormat: "dd.mm.yy",
        minDate: new Date()
    })
        .on("change", function () {
            // $(this).closest('.form-group').find('.time').focus();
            if ( $('#date-start-task').val() !== '') {

                // updateListTasks();
            }
        });
    // $('#date-start-task').datepicker({
    //     dateFormat: "dd.mm.yy"
    // });
    $(document).find('.filterright-r p , .filterright-r .close-all-check ').click(function (){
        //cбросить все
        $(document).find('select[name="state"]').val('').trigger('change');
        datapickerFilter.datepicker('setDate', null);
        $("input[name='propsLeftFilter']").prop('checked', false);
        updateListTasks();
    });
    $(document).find('.del-count-day-tasks-list').click(function () {
        datapickerFilter.datepicker('setDate', null);
        updateListTasks();
    })
    $(document).find('.block-money-ch').click(function (){
        if($(this).hasClass('checkedEllem')){
            $(document).find('.block-money-ch').prop('checked' , false);
            $(this).prop('checked' , false);
            $(this).removeClass('checkedEllem');
        }else {
            $(document).find('.block-money-ch').prop('checked' , false);
            $(document).find('.block-money-ch').removeClass('checkedEllem');
            $(this).prop('checked' , true);
            $(this).addClass('checkedEllem');
        }
    });
    $(document).find('.sorty-panel-alltasks li').click(function () {
        $(document).find('.sorty-panel-alltasks li').removeClass('ativeSrtAlltasks');
        $(this).addClass('ativeSrtAlltasks');
        updateListTasks();
    });
    //при проставлении параметров в левом фильтре  - собираю с двух форм параметры  и обновляю
    $(document).find('.change-update-prop').on('change' , function (){
        updateListTasks();
    });
    $(document).on('click' , '.load-more-tasks', function (){
        updateListTasks(true);
    });

    $(document).find('.props-nav-filter-item').on('change' , function (){
        updateListTasks();
    });
    //при нажатии на кнопку собираю с двух форма параметры и обновляю
    $(document).find('.block-button-set-options').on('click' , '.btn' , function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
        updateListTasks();
    });
    //при нажатии на подложку  - собираю с двух форм и обновляю
    $(document).on('click' , '.preloaderForPlacehopldBl', function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
        updateListTasks();
    });

    $(document).on('click' , '.complain-item' , function () {
        let $this = this;
        var el = this;
        $.ajax({
            type: 'POST',
            url: $(this).data('path'),
            context: this,
            dataType: "json",
            data: 'id=' + $($this).data('id'),
            success: function (data ) {
                if($(this).find('.twe').hasClass('active')){
                    $(this).find('.twe').hide();
                    $(this).find('.twe').removeClass('active');
                    $(this).find('.twe-te').show();
                    $(this).find('.twe-te').addClass('active');
                }else{
                    console.log('addActive');
                    $(this).find('.twe').show();
                    $(this).find('.twe').addClass('active');

                    $(this).find('.twe-te').hide();
                    $(this).find('.twe-te').removeClass('active');
                }
            },
            error: function (data) {
            }
        });
    });
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('#scroller').fadeIn();
        } else {
            $('#scroller').fadeOut();
        }
    });
    $('#scroller').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });
});
