$(document).ready(function () {
    $('#sorty-portflolio').on('change', function (e) {
        $('#' + $(this).val()).addClass('active').siblings().removeClass('active');

        if (parseInt(this.value) > 0) {
            window.location.href = BX.message('curPage') + '?id=' + parseInt(this.value);
        }
    });
});