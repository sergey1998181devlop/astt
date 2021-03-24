$(document).ready(function () {
    $('.sorty-panel ul li:eq(0)').click(function () {
        $('.updateCompList').hide();
    });
    $('.sorty-panel ul li:eq(1)').click(function () {
        $('.updateCompList').show();
    });
    //функция для переключения кнопки обновить
    function funcRePastBut() {
        $(document).find('.updateCompList').removeClass("updateCompListrotated");
        $(document).find('.block-but-show').hide();
    }
    $('.updateCompList').click(function () {
        $(this).addClass("updateCompListrotated");
        $(document).find('.block-but-show').show();
            event.preventDefault();
            var maxData = $('input[name="maxData"]').val();
            $.ajax({
                type: "POST",
                url: "/ajax/getListCompany.php",
                data: ({
                    updateCompany : "Y",
                    maxData : maxData
                }),
                success: function(msg ){
                    var json = JSON.parse(msg);
                    var  array = json;
                    if(array.status == 'updateSuccess'){

                        $.each(array.COMPANY ,function(index,value){
                            // действия, которые будут выполняться для каждого элемента массива
                            // index - это текущий индекс элемента массива (число)
                            // value - это значение текущего элемента массива
                            //выведем индекс и значение массива в консоль
                            // Добавляю на страницу новые компании и обновляю дату добавления последней компании
                            $(document).find('.tasks-list-company').prepend('\n' +
                                '<div class="task-preview ">\n' +
                                '                                            <div class="tbl tbl-fixed">\n' +
                                '                                                <div class="tbc">\n' +
                                '                                                    <div class="ttl medium">\n' +
                                '                                                      '+value.NameMinCompany+'                                                    </div>\n' +
                                '                                                    <div class="desc">\n' +
                                '                                                       '+value.DescriptionCompany+'                                                                                          ...\n' +
                                '                                                    </div>\n' +
                                '\n' +
                                '                                                </div>\n' +
                                '                                                <div class="tbc tbc-info">\n' +
                                '\n' +
                                '                                                    <div class="user-box">\n' +
                                '                                                        <div class="tbl tbl-fixed">\n' +
                                '                                                            <div class="tbc">\n' +
                                '                                                                <div class="ava">\n' +
                                '                                                                                                                                        <div class="object-fit-container">\n' +
                                '                                                                        <img src="'+value.UserAvatar+'" alt="" data-object-fit="cover" data-object-position="50% 50%">\n' +
                                '                                                                    </div>\n' +
                                '                                                                </div>\n' +
                                '                                                            </div>\n' +
                                '                                                            <div class="tbc">\n' +
                                '                                                                <a class="" href="/user/'+value.UserId+'/">\n' +
                                '                                                                    <div class="name medium" style="color:#222">\n' +
                                '                                                                        '+value.UserName+'&nbsp;'+value.UserFemale+'                                                                    </div>\n' +
                                '                                                                </a>                                                                                                                                                                            <div class="feedback">\n' +
                                '\n' +
                                '\n' +
                                '                                                                </div>\n' +
                                '                                                            </div>\n' +
                                '                                                        </div>\n' +
                                '                                                        <a class="lnk-abs" href="/user/'+value.UserId+'/"></a>\n' +
                                '\n' +
                                '                                                    </div>\n' +
                                '                                                </div>\n' +
                                '\n' +
                                '                                            </div>\n' +
                                '                                            <a class="lnk-abs" href="/user/moderataion/company/'+index+'/?us='+index+'"></a>\n' +
                                '                                        </div>\n' );

                        });
                        //устанавливаю новую максимальную дату и полученных новых компаний
                        $('input[name="maxData"]').val(array.MAXDATA);
                        setTimeout(funcRePastBut , 1000);
                    }
                    setTimeout(funcRePastBut , 1000);

                }
            });




    });
    //также функция на таймер  - на каждую минуту  - обновляю компании
    var reloadDataCompany = function(){
            $(this).addClass("updateCompListrotated");
            $(document).find('.block-but-show').show();


            $.ajax({
                type: "POST",
                url: "/ajax/getListCompanyAutoUpdate.php",
                data: ({
                    updateCompany : "Y",

                }),
                success: function(msg ){
                    var json = JSON.parse(msg);
                    var  array = json;
                    if(array.status == 'updateSuccess'){
                        $(document).find('.tasks-list-company').empty();

                        $.each(array.COMPANY ,function(index,value){
                            console.log(value);
                            // действия, которые будут выполняться для каждого элемента массива
                            // index - это текущий индекс элемента массива (число)
                            // value - это значение текущего элемента массива
                            //выведем индекс и значение массива в консоль
                            // Добавляю на страницу новые компании и обновляю дату добавления последней компании
                            $(document).find('.tasks-list-company').prepend('\n' +
                                '<div class="task-preview ">\n' +
                                '                                            <div class="tbl tbl-fixed">\n' +
                                '                                                <div class="tbc">\n' +
                                '                                                    <div class="ttl medium">\n' +
                                '                                                      '+value.NameMinCompany+'                                                    </div>\n' +
                                '                                                    <div class="desc">\n' +
                                '                                                       '+value.DescriptionCompany+'                                                                                          ...\n' +
                                '                                                    </div>\n' +
                                '\n' +
                                '                                                </div>\n' +
                                '                                                <div class="tbc tbc-info">\n' +
                                '\n' +
                                '                                                    <div class="user-box">\n' +
                                '                                                        <div class="tbl tbl-fixed">\n' +
                                '                                                            <div class="tbc">\n' +
                                '                                                                <div class="ava">\n' +
                                '                                                                                                                                        <div class="object-fit-container">\n' +
                                '                                                                        <img src="'+value.UserAvatar+'" alt="" data-object-fit="cover" data-object-position="50% 50%">\n' +
                                '                                                                    </div>\n' +
                                '                                                                </div>\n' +
                                '                                                            </div>\n' +
                                '                                                            <div class="tbc">\n' +
                                '                                                                <a class="" href="/user/'+value.UserId+'/">\n' +
                                '                                                                    <div class="name medium" style="color:#222">\n' +
                                '                                                                        '+value.UserName+'&nbsp;'+value.UserFemale+'                                                                    </div>\n' +
                                '                                                                </a>                                                                                                                                                                            <div class="feedback">\n' +
                                '\n' +
                                '\n' +
                                '                                                                </div>\n' +
                                '                                                            </div>\n' +
                                '                                                        </div>\n' +
                                '                                                        <a class="lnk-abs" href="/user/'+value.UserId+'/"></a>\n' +
                                '\n' +
                                '                                                    </div>\n' +
                                '                                                </div>\n' +
                                '\n' +
                                '                                            </div>\n' +
                                '                                            <a class="lnk-abs" href="/user/moderataion/company/'+index+'/?us='+index+'"></a>\n' +
                                '                                        </div>\n' );

                        });
                        //устанавливаю новую максимальную дату и полученных новых компаний
                        $('input[name="maxData"]').val(array.MAXDATA);
                        setTimeout(funcRePastBut , 1000);
                    }
                    setTimeout(funcRePastBut , 1000);

                }
            });
        setTimeout(arguments.callee,10000);
    };


    setTimeout( reloadDataCompany,10000 );

    // function reloadDataCompany (){
    //     alert();
    //     $(this).addClass("updateCompListrotated");
    //     $(document).find('.block-but-show').show();
    //     event.preventDefault();
    //
    //     $.ajax({
    //         type: "POST",
    //         url: "/ajax/getListCompanyAutoUpdate.php",
    //         data: ({
    //             updateCompany : "Y",
    //
    //         }),
    //         success: function(msg ){
    //             var json = JSON.parse(msg);
    //             var  array = json;
    //             if(array.status == 'updateSuccess'){
    //                 $(document).find('.tasks-list-company').empty();
    //
    //                 $.each(array.COMPANY ,function(index,value){
    //                     // действия, которые будут выполняться для каждого элемента массива
    //                     // index - это текущий индекс элемента массива (число)
    //                     // value - это значение текущего элемента массива
    //                     //выведем индекс и значение массива в консоль
    //                     // Добавляю на страницу новые компании и обновляю дату добавления последней компании
    //                     $(document).find('.tasks-list-company').prepend('\n' +
    //                         '<div class="task-preview ">\n' +
    //                         '                                            <div class="tbl tbl-fixed">\n' +
    //                         '                                                <div class="tbc">\n' +
    //                         '                                                    <div class="ttl medium">\n' +
    //                         '                                                      '+value.NameMinCompany+'                                                    </div>\n' +
    //                         '                                                    <div class="desc">\n' +
    //                         '                                                       '+value.DescriptionCompany+'                                                                                          ...\n' +
    //                         '                                                    </div>\n' +
    //                         '\n' +
    //                         '                                                </div>\n' +
    //                         '                                                <div class="tbc tbc-info">\n' +
    //                         '\n' +
    //                         '                                                    <div class="user-box">\n' +
    //                         '                                                        <div class="tbl tbl-fixed">\n' +
    //                         '                                                            <div class="tbc">\n' +
    //                         '                                                                <div class="ava">\n' +
    //                         '                                                                                                                                        <div class="object-fit-container">\n' +
    //                         '                                                                        <img src="'+value.UserAvatar+'" alt="" data-object-fit="cover" data-object-position="50% 50%">\n' +
    //                         '                                                                    </div>\n' +
    //                         '                                                                </div>\n' +
    //                         '                                                            </div>\n' +
    //                         '                                                            <div class="tbc">\n' +
    //                         '                                                                <a class="" href="/user/'+value.UserId+'/">\n' +
    //                         '                                                                    <div class="name medium" style="color:#222">\n' +
    //                         '                                                                        &nbsp;'+value.UserFemale+'                                                                    </div>\n' +
    //                         '                                                                </a>                                                                                                                                                                            <div class="feedback">\n' +
    //                         '\n' +
    //                         '\n' +
    //                         '                                                                </div>\n' +
    //                         '                                                            </div>\n' +
    //                         '                                                        </div>\n' +
    //                         '                                                        <a class="lnk-abs" href="/user/'+value.UserId+'/"></a>\n' +
    //                         '\n' +
    //                         '                                                    </div>\n' +
    //                         '                                                </div>\n' +
    //                         '\n' +
    //                         '                                            </div>\n' +
    //                         '                                            <a class="lnk-abs" href="/user/moderataion/company/'+index+'/?us='+index+'"></a>\n' +
    //                         '                                        </div>\n' );
    //
    //                 });
    //                 //устанавливаю новую максимальную дату и полученных новых компаний
    //                 $('input[name="maxData"]').val(array.MAXDATA);
    //                 setTimeout(funcRePastBut , 1000);
    //             }
    //             setTimeout(funcRePastBut , 1000);
    //
    //         }
    //     });
    // }



});