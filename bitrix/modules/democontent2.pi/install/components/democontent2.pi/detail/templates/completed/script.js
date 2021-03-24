$(document).ready(function(){
    $('.comments-container .item .btn-edit').click(function (e) {
        e.preventDefault();
        $(this).addClass('hidden').siblings('.btn-save').removeClass('hidden');
        $($(this).attr('href')).removeClass('hidden').siblings('.descript').addClass('hidden');
        if (!$($(this).attr('href')).closest('.comment-block').is(':visible')) {
            $($(this).attr('href')).closest('.comment-block').show();
        }
    });
    $('.comments-container .item .btn-save').click(function (e) {
        e.preventDefault();
        if ($($(this).attr('href')).find('textarea').val() != '') {
            $($(this).attr('href')).submit();
        } else {
            $($(this).attr('href')).find('textarea').addClass('error')
        }
    });
});