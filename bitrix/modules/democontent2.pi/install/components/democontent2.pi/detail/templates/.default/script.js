var myMap, multiRoute;

$(document).ready(function () {
    let route = BX.message('route');
    if (route.length > 0) {
        $('#map-area').css({
            'height': '250px',
            'margin-bottom': '20px'
        });
        ymaps.ready(function () {
            myMap = new ymaps.Map("map-area", {
                center: [55.752554272581875, 37.61941593484034],
                zoom: 12,
                controls: ['zoomControl', 'fullscreenControl']
            });

            multiRoute = new ymaps.multiRouter.MultiRoute({
                referencePoints: route
            }, {
                boundsAutoApply: true
            });

            myMap.geoObjects.add(multiRoute);
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
    $('.complain-item').click(function () {
        if (BX.message('isAuthorized')) {
            let $this = this;
            $.ajax({
                type: 'POST',
                url: BX.message('detailTemplateAjaxPath'),
                dataType: "json",
                data: 'id=' + $($this).data('id') + '&category=' + $($this).data('category'),
                success: function (data) {
                    if (!data.error) {
                        alert(BX.message('complainSuccess'));
                    }
                },
                error: function (data) {
                }
            });
        } else {
            alert(BX.message('pleaseAuth'));
        }
    });
    $('.contacts-open').click(function () {
        if (BX.message('isAuthorized')) {
            let $this = this;
            $.ajax({
                type: 'POST',
                url: BX.message('detailTemplateAjaxPath'),
                dataType: "json",
                data: 'contact-id=' + $($this).data('id') + '&hash=' + $($this).data('hash'),
                success: function (data) {
                    if (!data.error) {
                        $('.contacts-error').addClass('dn');
                        $('.contacts-view').html('<a class="btn btn-blue" href="tel:+' + data['result']['phone'] + '">' + data['result']['formattedPhone'] + '</a>');
                        $('.contacts-open').remove();
                        $('#top-balance').html(data['result']['balance']);
                    } else {
                        $('.contacts-error').removeClass('dn');
                    }
                },
                error: function (data) {
                }
            });
        } else {
            alert(BX.message('pleaseAuth'));
        }
    });
});
