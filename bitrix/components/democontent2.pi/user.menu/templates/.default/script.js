$(document).ready(function () {
    $('.status-control').click(function () {
        let type = $(this).data('type');

        $('.status-control').addClass('btn-secondary').removeClass('btn-green');
        $(this).addClass('btn-green').removeClass('btn-secondary');

        switch (type) {
            case 'free':
            case 'busy':
                $.ajax({type: 'POST', url: BX.message('userMenuPath'), dataType: "json", data: 'type=' + type});
                break;
        }
    });
});