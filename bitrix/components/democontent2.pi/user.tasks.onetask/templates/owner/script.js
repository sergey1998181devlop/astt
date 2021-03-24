var myMap, multiRoute;
$(document).ready(function () {
 
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

    $('.user-card-block .anchore').bind('click', function (event) {
        var $anchor = $(this).attr('href');
        $('.tabs-head a[href="#responses-1"]').click();
        $('html, body').stop().animate({
            scrollTop: $($anchor).offset().top - $('.header-top').outerHeight()
        }, 600);
        event.preventDefault();
    });
    $('.btn-response').click(function (e) {
        e.preventDefault();
        var iblockId = $('#task').data('iblockid'),
            taskId = $('#task').data('taskid'),
            offerId = $(this).closest('.responses-preview').data('offerid'),
            action = $(this).data('action');
        $.ajax({
            type: 'POST',
            url: ajaxPath,
            dataType: "json",
            data: 'iBlockId=' + iblockId + '&taskId=' + taskId + '&offerId=' + offerId + '&action=' + action,
            success: function (data) {
                if (!data.error) {
                    location.reload();
                }
            },
            error: function (data) {
            }
        });
    })
    $('.btn-complain, .btn-completed').click(function (e) {
        e.preventDefault();
        var popup = $(this).attr('href'),
            stageId = $(this).data('id');
        $.fancybox.open([
            {
                src: popup
            }
        ], {
            padding: 0,
            openEffect: 'fade',
            closeEffect: 'fade',
            nextEffect: 'none',
            prevEffect: 'none',
            afterShow: function (instance, current) {
                $(popup).find('[name="stageId"]').val(stageId);
            }
        });
    });
    $('.btn-completed-all').click(function (e) {
        e.preventDefault();
        var popup = $(this).attr('href'),
            stageId = $(this).data('id');
        $.fancybox.open([
            {
                src: popup
            }
        ], {
            padding: 0,
            openEffect: 'fade',
            closeEffect: 'fade',
            nextEffect: 'none',
            prevEffect: 'none',
            afterShow: function (instance, current) {
                $(popup).find('[name="stageId"]').val(stageId);
            }
        });
    });
    $('.checklist-tag').click(function () {
        $(this).toggleClass('active');

        let checked = [];
        $('.checklist-tag').each(function () {
            if ($(this).hasClass('active')) {
                checked.push($(this).data('id'));
            }
        });

        if (!checked.length) {
            $('.responses-preview').removeClass('hidden');
        } else {
            $('.responses-preview').each(function () {
                let show = false;
                let _this = this;

                for (let i in checked) {
                    if ($(_this).hasClass('checked' + checked[i])) {
                        show = true;
                        break;
                    }
                }

                if (show) {
                    $(_this).removeClass('hidden');
                } else {
                    $(_this).addClass('hidden');
                }
            });
        }
    });
});