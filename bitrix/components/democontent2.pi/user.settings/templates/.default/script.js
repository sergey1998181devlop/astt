$(document).ready(function () {

    $('.specialization-list > ul > li >input[type=checkbox]').change(function () {
        if ($(this).is(':checked')) {
            $(this).closest('li').find('ul input[type=checkbox]').prop('checked', true);
        } else {
            $(this).closest('li').find('ul input[type=checkbox]').prop('checked', false);
        }
    });
    $('.specialization-list > ul > li ul input[type=checkbox]').change(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('ul').siblings('input[type=checkbox]').prop('checked', false);
        }
        var
            elLenght = $(this).closest('ul').find('input[type=checkbox]').length,
            elLenghtChecked = $(this).closest('ul').find('input[type=checkbox]:checked').length;
        if (elLenght == elLenghtChecked) {
            $(this).closest('ul').siblings('input[type=checkbox]').prop('checked', true);
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
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#ava-image').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('.ava-inp-file input').change(function () {
        readURL(this);
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
        $(this).hide();
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
        $('.btn-transform-pas').show();
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
        $(this).closest('form').find('.error-message').hide();
        if (filesL.find('.itm').length < maxFiles) {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + templatePath + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            filesL.append(itm);
            filesL.find('.itm:last-child').prepend(clone);
            if (filesL.find('.itm').length === maxFiles) {
                $(this).closest('.btn-file').addClass('btn-disabled')
            }
        }
    });
    jQuery(function($){
        $(".emailUpAc-phone").mask("+7 (999) 999-99-99",{
            completed:function(){

                // alert("You typed the following: "+this.val());
                // alert(this.val());
                $('.phoneBtnApprove').show();
                if($(this).val() == $(document).find('input[name="OldNumber"]').val()  ){
                    $(document).find('.pers_dataBtnSaveData').removeAttr('disabled');
                    $(document).find('.phoneBtnApprove').hide();

                }
            }

        }
        );
    });
    $('.emailUpAc-phone').on('keydown', function(e){

        e.stopPropagation();
        if(e.keyCode == 8) {

            $('.phoneBtnApprove').hide();
            $(document).find('.pers_dataBtnSaveData').attr('disabled' , true);
        }

    });

    $('input[name="name"]').on('keyup', function(e){

        if($(this).val().length == 0) {
            $(document).find('.pers_dataBtnSaveData').attr('disabled' , true);
        }else {
            if( $(document).find('input[name="name"]').val() !== '' &&
                $(document).find('input[name="phone"]').val() !== '' &&
                $(document).find('input[name="authEmail"]').val() !== ''){
                $(document).find('.pers_dataBtnSaveData').removeAttr('disabled');
            }
        }
    })
    //
    $('input[name="authEmail"]').on('keyup', function(e){

        if($(this).val().length == 0) {
            $(document).find('.pers_dataBtnSaveData').attr('disabled' , true);
            $(document).find('.emailCheck').hide();
        }

        if($(this).val() == $(document).find('input[name="EmailOld"]').val() ){
            $(document).find('.pers_dataBtnSaveData').removeAttr('disabled');
            $(document).find('.emailCheck').hide();
        }else {
            $(document).find('.pers_dataBtnSaveData').attr('disabled' , true);
            $(document).find('.emailCheck').show();
        }

    })


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
