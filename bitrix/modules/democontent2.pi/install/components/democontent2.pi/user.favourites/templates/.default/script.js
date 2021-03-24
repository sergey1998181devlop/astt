$(document).ready(function () {
    $('.remove-item').click(function () {
        let $this = this;
        $.ajax({
            type: 'POST',
            url: BX.message('userFavouritesPath'),
            dataType: "json",
            data: 'id=' + $($this).data('id'),
            success: function (data) {
                if (!data.error) {
                    $('#executor-' + $($this).data('id')).remove();
                }
            },
            error: function (data) {
            }
        });
    });
});