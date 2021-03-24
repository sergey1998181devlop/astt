var myMap, multiRoute;

$(document).ready(function () {
    let route = BX.message('route');
    if (route.length > 0) {
        $('#map-area').css({
            'height': '250px',
            'margin-bottom': '20px'
        });
        ymaps.ready(function () {

            if(route.length == 1){
                var titleTask = $(document).find('.title').children('h1').text();

                myMap = new ymaps.Map("map-area", {
                    center: [route[0][0], route[0][1]],
                    zoom: 11,
                    controls: ['zoomControl', 'fullscreenControl']
                });
                myMap.geoObjects
                    .add(new ymaps.Placemark([route[0][0], route[0][1]], {
                            balloonContent: titleTask
                        }
                    ));
            }

            if(route.length > 1){
                myMap = new ymaps.Map("map-area", {
                    center: [55.752554272581875, 37.61941593484034],
                    zoom: 12,
                    controls: ['zoomControl', 'fullscreenControl']
                });

                multiRoute = new ymaps.multiRouter.MultiRoute({
                    referencePoints: route
                }, {
                    boundsAutoApply: true,
                });
                myMap.geoObjects.add(multiRoute);

            }


        });
    }

    $('#btn-file input').change(function (e) {
        if ($('#files-list .itm').length < BX.message('maxFiles')) {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + BX.message('templatePath') + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            $('#files-list').append(itm);
            $('#files-list .itm:last-child').prepend(clone);
            if ($('#files-list .itm').length === BX.message('maxFiles')) {
                $(this).closest('.btn-file').addClass('btn-disabled')
            }
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
    $('#deposit-amount').bind("keydown", function (e) {
        var ths = $(this);
        var keyKode = e.keyCode;
        if (keyKode > 95 && keyKode < 106 || keyKode > 47 && keyKode < 58 || keyKode == 8 || keyKode == 46 || keyKode == 9) {
        } else return false;
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
    $('.complain-item').click(function () {
        let $this = this;

        $.ajax({
            type: 'POST',
            url: $(this).data('path'),
            dataType: "json",
            data: 'id=' + $($this).data('id'),
            success: function (data) {
                showPopup('Задание успешно добавлено в избранное ' , 'alert alert-success');
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
});
