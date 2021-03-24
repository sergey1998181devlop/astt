var myMap, multiRoute;

$(document).ready(function () {
    if(route.length > 0){
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
        if ($('#files-list .itm').length < maxFiles) {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + templatePath + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            $('#files-list').append(itm);
            $('#files-list .itm:last-child').prepend(clone);
            if ($('#files-list .itm').length == maxFiles) {
                $(this).closest('.btn-file').addClass('btn-disabled')
            }
        }
    });
    $('.btn-complain, .btn-completed').click(function (e) {
        e.preventDefault();
        var popup = $(this).attr('href'),
            stageId = $(this).data('id');
        $.fancybox.open([
            {
                src :popup
            }
        ], {
            padding: 0,
            openEffect  : 'fade',
            closeEffect : 'fade',
            nextEffect  : 'none',
            prevEffect  : 'none',
            afterShow : function( instance, current ) {
                $(popup).find('[name="stageId"]').val(stageId);
            }
        });
    })
});
