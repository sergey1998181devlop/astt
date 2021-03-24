$(document).ready(function () {
    $('.remove-item').click(function () {
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
});