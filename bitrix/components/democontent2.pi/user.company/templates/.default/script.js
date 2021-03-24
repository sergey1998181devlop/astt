$(document).ready(function () {

    $('.specialization-list > ul > div > li >input[type=checkbox]').change(function () {
        if ($(this).is(':checked')) {
            $(this).closest('li').find('ul input[type=checkbox]').prop('checked', true);
            // $(this).next('label').children('.icon-wrap_subIn').css('background' , 'red');
            // $(this).next('label').find('.icon-wrap_subIn').css('background' , '');
            // if( $(this).closest('open').find('input:checkbox:checked').length > 0){
            //      $(this).closest('open').children('input').prop('checked', true);
            //      alert();
            // }else {
            //     $(this).closest('open').children('input').prop('checked', false);
            // }

        } else {

            $(this).closest('li').find('ul input[type=checkbox]').prop('checked', false);

            $(this).next('label').children('.icon-wrap_subIn').css('background' , '');

        }
    });
    $('.specialization-list > ul > div  > li ul input[type=checkbox]').change(function () {


        if (!$(this).is(':checked')) {

            $(this).closest('ul').siblings('input[type=checkbox]').prop('checked', false);
        }
        var
            elLenght = $(this).closest('ul').find('input[type=checkbox]').length,
            elLenghtChecked = $(this).closest('ul').find('input[type=checkbox]:checked').length;
        if(elLenghtChecked > 0){

            // $(this).closest('ul').siblings('input[type=checkbox]').prop('checked', true);
            $(this).closest('.open').find('.icon-wrap_subIn').css('background' , '#2b8bf0');


        }
        else {
            $(this).closest('.open').find('.icon-wrap_subIn').css('background' , '');
        }
        if (elLenght == elLenghtChecked) {

            $(this).closest('ul').siblings('input[type=checkbox]').prop('checked', true);
            $(this).closest('.open').find('.icon-wrap_subIn').css('background' , '');
        }
    });

    $('.specialization-list .icon-angle-wrap').click(function () {
        $(this).closest('li').toggleClass('open').find('ul').stop().slideToggle();
    });

    $('.specialization-list ul ul ').each(function () {
        var $list = $(this),
            elLenght = $list.find('li').length,
            elLenghtChecked = $list.find('input[type=checkbox]:checked').length;
        if (elLenght == elLenghtChecked) {
            $list.siblings('input[type=checkbox]').prop('checked', true);
        }

    });

    $('.ava-inp-file input').change(function () {
        var size = parseFloat((this.files[0].size / (1024 * 1024)).toFixed(2));
        if (size > maxSizeAva) {
            $(this).addClass('error').closest('.form-group').addClass('error');
        } else {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
            readURL(this);
        }
    });
    $('.btn-transform-pas').click(function (e) {
        e.preventDefault();



        $('.new_pass').stop().slideDown({
            duration: 700,
            // easing: "linear",
            // queue: false,
            // complete: function(){ // callback
            //     $('.pers_data').stop().slideUp(800);
            // },
        });
    });
    $('.new_pass .js-lnk').click(function (e) {
        e.preventDefault();
        // $('.new_pass').slideUp("slow");
        $('.new_pass').stop().slideUp({
            duration: 700,
            // easing: "linear",
            // queue: false,
            // complete: function(){ // callback
            //     $('.pers_data').stop().slideDown(800);
            // },
        });

    });
    $('.btn-verifications').click(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        if (form.find('.files .itm').length > 0) {

        } else {
            form.find('.error-message').show();
        }
    });
    $('.btn-file input').change(function (e) {
        var filesL = $(this).closest('.form-group').find('.files');
        var prewie = $(this).closest('.form-group').find('.prewPhotoL');
        $(this).closest('form').find('.error-message').hide();
        if (filesL.find('.itm').length < maxFiles) {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = ' <div class="attachment-picts attachment-picts-user-company row ">' +
                    '<div class="item">' +
                    '<a href="">' +
                    '<div class="in">' +
                    '<div class="object-fit-container">' +
                    '<img src="" alt="" data-object-fit="cover" data-object-position="50% 50%" >' +
                    '</div>' +
                    '</div>' +
                    '</a>' +
                    '</div>' +
                    '</div>';
                // itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + templatePath + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            // itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + templatePath + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            filesL.append(itm);
            filesL.find('.itm:last-child').prepend(clone);
            var el = ' <div class="attachment-picts attachment-picts-user-company row ">' +
                                                                         '<div class="item">' +
                                                                                 '<a href="">' +
                                                                                    '<div class="in">' +
                                                                                        '<div class="object-fit-container">' +
                                                                                            '<img src="" alt="" data-object-fit="cover" data-object-position="50% 50%" >' +
                                                                                        '</div>' +
                                                                                     '</div>' +
                                                                                '</a>' +
                                                                        '</div>' +
                    '</div>';
            console.log(el);

            if (filesL.find('.itm').length === maxFiles) {
                $(this).closest('.btn-file').addClass('btn-disabled')
            }
        }
    });
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#ava-image').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
