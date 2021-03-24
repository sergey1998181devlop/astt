$(document).ready(function () {
    $('.popup.popup-location  .back').click(function (e) {
        e.preventDefault();
        $(this).closest('.tab').removeClass('active').siblings().addClass('active');
    });
    $('#regions a').click(function (e) {
        e.preventDefault();
        var list = $('#cities-regions'),
            regionId = $(this).attr('href');
        $.ajax({
            type: 'POST',
            url: __popupLocationAjaxPath,
            dataType: "json",
            data: 'regionId=' + regionId,
            success: function (data) {
                if (!data.error) {
                    list.html('');
                    $.each(data.result, function (ind, city) {
                        list.append('<li><a href="' + __popupLocationCityPath + city.id + '">' + city.name + '</a></li>')
                    });
                    list.closest('.tab').addClass('active').siblings().removeClass('active');
                }
            },
        });
    });
});