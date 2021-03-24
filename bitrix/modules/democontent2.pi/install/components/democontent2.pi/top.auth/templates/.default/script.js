$(document).ready(function () {
    $('.forget-password-lnk').click(function (e) {
        e.preventDefault();
        $(this).closest('.tab').removeClass('active').siblings().addClass('active');
    });
    if (typeof __authErrorReason !== 'undefined' && __authErrorReason.length > 0) {
        $.fancybox.open([
            {
                src: '#popup-registration'
            }
        ], {
            padding: 0,
            openEffect: 'fade',
            closeEffect: 'fade',
            nextEffect: 'none',
            prevEffect: 'none',
            beforeShow: function (instance, current) {
                $('#popup-registration .tabs-head a[href="#tab2"]').click();
            }
        });

        $('#tab2').prepend('<div class="alert alert-danger text-center">' + __authErrorReason + '</div>');
        setTimeout(function () {
            $('#tab2 .alert.alert-danger').remove();
        }, 10000)
    }
    $('#popup-registration #tab1 input').change(function () {
        var alert = $(this).closest('form').find('.alert');
        alert.stop().slideUp(function () {
            alert.remove();
        });
    });
    $('#registration .btn').click(function (e) {
        e.preventDefault();
        var $this = $(this),
            $form = $this.closest('form');
        var name = $form.find('input[name="name"]').val(),
            email = $form.find('input[name="email"]').val();

        let phone = '0';
        if (__phoneRequired) {
            phone = $form.find('input[name="phone"]').val();
        }

        if (typeof __reCaptchaInit !== 'undefined' && __reCaptchaInit > 0) {
            var response = grecaptcha.getResponse(registerReCaptcha);
            if (response.length === 0) {
                //TODO reCaptcha Error Message
            } else {
                if ($this.closest('form').find('.error').length === 0) {
                    $.ajax({
                        type: 'POST',
                        url: __authAjaxPath,
                        dataType: "json",
                        data: 'name=' + encodeURIComponent(name) + '&email=' + email + '&phone=' + phone + '&reCaptchaCode=' + response,
                        success: function (data) {
                            if (!data.error) {
                                $form[0].reset();
                                $('#popup-registration a[href="#tab2"]').closest('li').addClass('active').siblings().removeClass('active');
                                $('#popup-registration #tab2').addClass('active').siblings().removeClass('active');
                                if ($('#tab2').find('.alert.alert-success').length > 0) {
                                    $('#tab2').find('.alert.alert-success').remove();
                                }
                                $('#tab2').prepend('<div class="alert alert-success text-center">' + __registerSuccessMessage + '</div>');
                            } else {
                                $form.prepend('<div class="alert alert-danger text-center">' + __registerErrorMessage + '</div>');
                                grecaptcha.reset(registerReCaptcha);
                            }
                        },
                        error: function (data) {
                            grecaptcha.reset(registerReCaptcha);
                        }
                    });
                }
            }
        } else {
            if ($this.closest('form').find('.error').length === 0) {
                $.ajax({
                    type: 'POST',
                    url: __authAjaxPath,
                    dataType: "json",
                    data: 'name=' + encodeURIComponent(name) + '&email=' + email + '&phone=' + phone,
                    success: function (data) {
                        if (!data.error) {
                            $form[0].reset();
                            $('#popup-registration a[href="#tab2"]').closest('li').addClass('active').siblings().removeClass('active');
                            $('#popup-registration #tab2').addClass('active').siblings().removeClass('active');
                            if ($('#tab2').find('.alert.alert-success').length > 0) {
                                $('#tab2').find('.alert.alert-success').remove();
                            }
                            $('#tab2').prepend('<div class="alert alert-success text-center">' + __registerSuccessMessage + '</div>');
                        } else {
                            $form.prepend('<div class="alert alert-danger text-center">' + __registerErrorMessage + '</div>');
                        }
                    },
                    error: function (data) {
                    }
                });
            }
        }
    });
    $('.restore-password .btn').click(function (e) {
        e.preventDefault();
        var $this = $(this),
            $form = $this.closest('form');
        if ($this.closest('form').find('.error').length === 0) {
            var email = $form.find('input[name="restorePasswordEmail"]').val(),
                phone = $form.find('input[name="restorePasswordPhone"]').val();
            $.ajax({
                type: 'POST',
                url: __authAjaxPath,
                dataType: "json",
                data: 'restorePasswordEmail=' + email + '&restorePasswordPhone=' + phone,
                success: function (data) {
                    if (!data.error) {
                        $form[0].reset();
                        $('#tab2').prepend('<div class="alert alert-success text-center">' + __restorePasswordSuccessMessage + '</div>');
                    } else {
                        $form.prepend('<div class="alert alert-danger text-center">' + __restorePasswordErrorMessage + '</div>');
                    }
                },
                error: function (data) {
                }
            });
        }
    });

    if (typeof __needAuth !== 'undefined' && __needAuth) {
        $('#popup-registration a[href="#tab1"]').click();
        $.fancybox.open([
            {
                src: '#popup-registration'
            }
        ], {
            padding: 0,
            openEffect: 'fade',
            closeEffect: 'fade',
            nextEffect: 'none',
            prevEffect: 'none',
        });
    }
});