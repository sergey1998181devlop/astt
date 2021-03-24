$(document).ready(function () {

    var cityReadyOneLoadId = $(document).find('.idCityTopPL').attr('data-target-idcity');
    var cityReadyOneLoadname = $(document).find('.idCityTopPL').attr('data-target-namecity');
    $(document).find('input[name="nameCity"]').val(cityReadyOneLoadId);
    $(document).find('.head').text(cityReadyOneLoadname);
    function loadUsers( clickMoreLoad = false ){
        offset = 0;
        if(clickMoreLoad == true){
             offset = $(document).find('.load-more-users').attr('data-ofset');
        }
        var city =  $(document).find('input[name="nameCity"]').val();
        var num = $(document).find('.active-li-dop').text();
        currentlimit = Number.parseInt(num);

            sortOne = $(document).find('.category-nav').find('li:eq(0)').attr('data-target-filter');


            sortTwe = $(document).find('.category-nav').find('li:eq(1)').attr('data-target-filter');

        var propsFilterSpecialization = $("input[name='SubCategories']:checked").map(function(){return $(this).val();}).get();
        $.ajax({
            type: "POST",
            url: "/users/",
            data: ({
                ratind : $(document).find('input[name="rating"]').val(),
                ajaxItem : "Y",
                subCodigories : propsFilterSpecialization,
                sortOne:sortOne,
                sortTwe:sortTwe,
                offset:offset,
                city:city,
                currentlimit:currentlimit
            }) ,
            success: function(msg){


                        if( $(msg).find('.profile-preview').length > 0) {
                            $(document).find('.load-more-users').show();
                            $(document).find('.alert-info-users-emp').remove();
                        }

                    if(clickMoreLoad == true){
                        if( $(msg).find('.profile-preview').length > 0) {

                            // $("msg#some_id").timeago("update", new Date());
                            // $('.timestamp', msg).timeago();
                                 // $(".timestamp").timeago();

                            var elems = $(msg).find('.profile-preview');
                            $(document).find('.masters-list').append(elems);

                            // $(".timestamp").timeago();
                            // $(document).find(".timestamp").live(function () {
                            //     $(this).timeago();
                            // });
                            // $('.timestamp').livequery(function() {
                            //     $(this).timeago();
                            // });

                            var offsetnew = $(document).find('.profile-preview').length;
                            console.log(offsetnew);
                            $(document).find('.load-more-users').attr('data-ofset', offsetnew)
                        }else{
                            $(document).find('.load-more-users').hide();
                        }

                    }else {
                        if( $(msg).find('.profile-preview').length > 0) {

                            $(document).find('.masters-list').empty();
                            var elems = $(msg).find('.profile-preview');
                            $(document).find('.masters-list').append(elems);

                            var offsetnew = $(document).find('.profile-preview').length;
                            $(document).find('.load-more-users').attr('data-ofset', offsetnew)
                        }else {
                            if( $(document).find('.alert-info-users-emp').length == 0){

                                $(document).find('.masters-list').append('   <div class="alert alert-info alert-info-users-emp">К сожалению,исполнители по данному поисковому запросу не найдены </div>');
                            }
                            $(document).find('.load-more-users').hide();
                            $(document).find('.profile-preview').remove();
                        }
                    }


            }
        });

    }
    $(document).find('.dop-list-users').find('.list-li').on('click' , function () {
            $(document).find('.dop-list-users').find('.list-li').removeClass('active-li-dop');
            $(this).addClass('active-li-dop');
            loadUsers();
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
        loadUsers();
    })
    $(document).on('click' , '.load-more-users', function (){
        loadUsers(true);
    });
    $(document).find('.category-nav').find('li:eq(0)').click(function () {
        if($(this).hasClass('active')){
            $(this).attr('data-target-filter' , 'po-date-down');
            $(this).removeClass('active');
        }else {
            $(this).attr('data-target-filter' , 'po-date-height');
            $(this).addClass('active');
        }
        loadUsers();
    });
    $(document).find('.category-nav').find('li:eq(1)').click(function () {
        if($(this).hasClass('active')){
            $(this).attr('data-target-filter' , 'po-rate-down');
            $(this).removeClass('active');
        }else {
            $(this).attr('data-target-filter' , 'po-rate-height');
            $(this).addClass('active');
        }
        loadUsers();
    });
    $(document).find('.filterright-r').on('click' , ' p , .close-all-check' , function () {
        $('input[name="setallstars"]').attr("disabled" , true);
        $('input[name="setallstars"]').prop('checked' , true);

        objOldElems = $(document).find('.assessment-box-users');
        len = $(document).find('.assessment-box-users').find('.sv-star-ev');
            $(document).find('.assessment-box-users').find('.sv-star-ev').remove();
            var emptuStar = $(document).find('.list-stars-preload').find('.icon_star-empty').html();
            $.each(len , function (key) {
                $(document).find('.assessment-box-users').prepend("<div class='star-elem empty-star-sv sv-star-ev'><svg class='icon icon_star-empty'>"+emptuStar+"</div></div>");
            });
            $(document).find('input[name="rating"]').val("emptyRating");
        loadUsers();
    });
    $(document).find('.assessment-box-users').on('click' , '.sv-star-ev' , function () {
                $('input[name="setallstars"]').prop('checked' , false);
                $('input[name="setallstars"]').removeAttr("disabled");
                len = $(document).find('.assessment-box-users').find('.sv-star-ev');
                currentEl = $(this).index();
                $.each(len, function (key) {
                    var objStar = $(document).find('.assessment-box-users').find('.sv-star-ev:eq('+key+')');
                    var objStarLastFull = $(document).find('.assessment-box-users').find('.full-star-sv:last').index();
                    console.log(key , currentEl);
                    if( key <= currentEl){
                            // $(objStar).toggleClass('empty-star-sv full-star-sv');
                            $(objStar).attr('input-rating-togle' , key);
                            $(objStar).addClass('full-star-sv');
                            $(objStar).removeClass('empty-star-sv');
                            $(objStar).empty();
                            $(document).find('.list-stars-preload').find('.icon_star-full').clone().prependTo($(objStar));
                    }else {
                        // alert();
                        // $(objStar).toggleClass('full-star-sv empty-star-sv');
                        $(objStar).attr('input-rating-togle' , key);
                        $(objStar).addClass('empty-star-sv');
                        $(objStar).removeClass('full-star-sv');
                        $(objStar).empty();
                        $(document).find('.list-stars-preload').find('.icon_star-empty').clone().prependTo($(objStar));
                    }
                });
                var lastEl =  $(document).find('.assessment-box-users').find('.full-star-sv:last').attr('input-rating-togle');
                var numTating = Number.parseInt(lastEl)  + 1;
                $(document).find('input[name="rating"]').val(numTating);
        loadUsers();
    })
    $('input[name="setallstars"]').change(function () {

        $('input[name="setallstars"]').attr("disabled" , true);

        objOldElems = $(document).find('.assessment-box-users');
        len = $(document).find('.assessment-box-users').find('.sv-star-ev');
        if($(this).prop('checked')){
            $(document).find('.assessment-box-users').find('.sv-star-ev').remove();
            var emptuStar = $(document).find('.list-stars-preload').find('.icon_star-empty').html();
            $.each(len , function (key) {
                $(document).find('.assessment-box-users').prepend("<div class='star-elem empty-star-sv sv-star-ev'><svg class='icon icon_star-empty'>"+emptuStar+"</div></div>");
            });
            $(document).find('input[name="rating"]').val("emptyRating");
        }else {

        }
        loadUsers();
    })

    $(document).on('click' , '.remove-item' , function () {
        let $this = this;
        $.ajax({
            type: 'POST',
            url: BX.message('usersPath'),
            dataType: "json",
            data: 'id=' + $($this).data('id'),
            success: function (data) {
            },
            error: function (data) {
            }
        });

        if ($($this).hasClass('active')) {
            $($this).html(BX.message('addToFavourites'));
        } else {
            $($this).html(BX.message('inFavourites'));
        }

        $($this).toggleClass('active');
    });
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

    function modalFilterTabs() {
        if( $('body').find('.preloaderForPlacehopldBl').length === 0 ){
            $(document).find('.preloaderForPlacehopld').after('<div class="preloaderForPlacehopldBl" ></div>')
        }
        $('.filter-sorty-to-items').find('.row-modal').toggle();
        $(document).find('.preloaderForPlacehopldBl').toggle();
    }
    $(document).find('.win-to-amber').click(function (){
        modalFilterTabs();
        cursorBlockStatusOpend();

    });
    //при нажатии на кнопку собираю с двух форма параметры и обновляю
    $(document).find('.block-button-set-options').on('click' , '.btn' , function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
        loadUsers();
    });
    $(document).on('click' , '.preloaderForPlacehopldBl', function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
        loadUsers();
    });

    $('.CategoryTitle').change(function () {
        if($(this).hasClass('checkedAllThisGroup')){
            $(this).removeClass('checkedAllThisGroup');
            $(this).closest('li').find('.subCategoryFilter').prop('checked' , false);
        }else {
            $(this).addClass('checkedAllThisGroup');
            $(this).closest('li').find('.subCategoryFilter').prop('checked' , true);
        }
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

    $('.filter-sorty-to-items ').find('.input-group-append-modal').click(function (){
        modalFilterTabs();
        cursorBlockStatusOpend();
    });



});