var ui = {

    isDesktop: function () {
        return ($(window).width() > 991) ? true : false;
    },
    isTabletWide: function () {
        return ($(window).width() > 767 && $(window).width() < 992) ? true : false;
    },
    isTablet: function () {
        return ($(window).width() < 768 && $(window).width() > 479) ? true : false;
    },

    isPhoneWide: function () {
        return ($(window).width() < 480) ? true : false;
    },

    isTouch: function () {
        try {
            document.createEvent("TouchEvent");
            return true;
        }
        catch (e) {
            return false;
        }
    },

    svg: function () {
        svg4everybody({});
    },

    timeActive: function () {
        $(".timestamp").timeago();
    },

    footerEmpty: function () {
        if ($(window).width() > 767) {
            $('.footer-empty').css({'min-height': $('footer').outerHeight()})
        } else {
            $('.footer-empty').css({'min-height': '0px'})
        }
    },
    inpMask: function () {
        if ($('[data-mask]').length > 0) {
            $('[data-mask]').each(function () {
                $(this).mask($(this).data('mask'));
            })
        }
    },

    menu: function () {
        $('header .burger').click(function (e) {
            e.preventDefault();
            $(this).toggleClass('open').closest('header').find('.menu').toggleClass('open');
            $('#category-nav.open').removeClass('open');
        });
    },
    headerFixed: function () {
        if ($('#bx-panel').length > 0 && $(window).scrollTop() < $('#bx-panel').outerHeight()) {
            $('header .header-top').addClass('header-abs');
        } else {
            $('header .header-top').removeClass('header-abs');
        }
    },

    select: function () {
        $('.js-select').each(function () {
            var pl = $(this).attr('placeholder');
            var search = ($(this).hasClass('has-search')) ? '' : Infinity;
            $(this).select2({
                language: 'ru',
                placeholder: pl,
                minimumResultsForSearch: search
            });
        });
    },

    btnUp: function () {
        if ($(window).scrollTop()>40) {
            $('#btn-up').show();
        } else {
            $('#btn-up').hide();
        };
        $('.btn-up').click(function (e) {
            $('html, body').stop().animate({ scrollTop: 0 }, 400);
            e.preventDefault();
        });
    },

    mainSearch: function () {
        $('.main-search .exemple .js-lnk').click(function (e) {
            e.preventDefault();
            $(this).closest('.tbc').find('input').val($(this).text());
        });
    },
    tabs: function () {
        $('.tab-lnk').click(function (e) {
            if ($($(this).attr('href')).length > 0) {
                e.preventDefault();
                $('.tabs-head a[href="'+$(this).attr('href')+'"]').closest('li').addClass('active').siblings().removeClass('active');
                $($(this).attr('href')).addClass('active').siblings().removeClass('active');
            }
        });
        $('.tabs-head a, .tab-lnk').click(function (e) {

            if ($($(this).attr('href')).length > 0 ) {
                e.preventDefault();
                $(this).closest('li').addClass('active').siblings().removeClass('active');
                $($(this).attr('href')).addClass('active').siblings().removeClass('active');
            }
        });
    },
    validation: function () {

        $('.form-control').focus(function () {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        });
        $('select').change(function () {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        });
        $('input[type="checkbox"]').change(function () {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        });
        $('form').not('.ajax').each(function () {
            var item = $(this),
                btn = item.find('.btn-submit');

            function checkInput() {
                item.find('select.required').each(function () {
                    if ($(this).val() != '') {
                        $(this).removeClass('error').closest('.form-group').removeClass('error');
                    } else {
                        $(this).addClass('error').closest('.form-group').addClass('error').find('.error-message').show();
                    }
                });
                item.find('input[type=text].required, input[type=phone].required').each(function () {
                    if ($(this).val() != '') {
                        $(this).removeClass('error').closest('.form-group').removeClass('error');
                    } else {
                        $(this).addClass('error').closest('.form-group').addClass('error');
                    }
                });
                item.find('input[type=password].required').each(function () {
                    if ($(this).val() != '') {
                        $(this).removeClass('error').closest('.form-group').removeClass('error');
                    } else {
                        $(this).addClass('error').closest('.form-group').addClass('error');

                    }
                });
                if ($('.pass1', item).length != 0) {
                    var pass01 = item.find('.pass1').val();
                    var pass02 = item.find('.pass2').val();
                    if (pass01 != pass02) {
                        $('.pass1, .pass2', item).addClass('error').closest('.form-group').addClass('error');
                    }
                }
                item.find('textarea.required').each(function () {
                    if ($(this).val() != '') {
                        $(this).removeClass('error').closest('.form-group').removeClass('error');
                    } else {
                        $(this).addClass('error').closest('.form-group').addClass('error');
                    }
                });
                item.find('input[type=email]').each(function () {
                    var regexp = /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i;
                    var $this = $(this);
                    if ($this.hasClass('required')) {
                        if (regexp.test($this.val())) {
                            $this.removeClass('error').closest('.form-group').removeClass('error');
                        } else {
                            $this.addClass('error').closest('.form-group').addClass('error');
                        }
                    } else {
                        if ($this.val() != '') {
                            if (regexp.test($this.val())) {
                                $this.removeClass('error').closest('.form-group').removeClass('error');
                            } else {
                                $this.addClass('error').closest('.form-group').addClass('error');
                            }
                        } else {
                            $this.removeClass('error').closest('.form-group').removeClass('error');
                        }
                    }
                });

                item.find('input[type=checkbox].required').each(function () {
                    if ($(this).is(':checked')) {
                        $(this).removeClass('error').closest('.form-group').removeClass('error');
                    } else {
                        $(this).addClass('error').closest('.form-group').addClass('error');
                    }
                });
            }

            btn.click(function () {
                checkInput();
                var sizeEmpty = item.find('.error:visible').size();
                if (sizeEmpty > 0) {
                    return false;
                } else {
                    if (!item.hasClass('ajax-form') && !btn.hasClass('form-sending')) {
                        btn.addClass('form-sending');
                        item.submit();
                    }
                }
            });
        });
    },
    tooltips: function () {
        $('[data-tooltip]').each(function () {
            var trigger = (typeof $(this).data('trigger') !== typeof undefined && $(this).data('trigger') !== false) ? $(this).data('trigger') : 'hover';
            $(this).tooltip({
                trigger: trigger
            })
        })
        $(document).on('click', '.tooltip-close', function (e) {
            e.preventDefault();
            $('[aria-describedby="' + $(this).closest('.tooltip').attr('id') + '"]').click();
        });
    },
    footerDropdown: function () {
        $('.footer-top .item .title').click(function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest('.item').find('.mobile-dropdown').stop().slideToggle(function () {
                $this.toggleClass('active');
            });
        });
    },
    profileTabsHead: function () {
        $('.profile-tabs-head .head').click(function (e) {
            e.preventDefault();
            $(this).closest('.profile-tabs-head').toggleClass('open').find('ul').slideToggle();
        })
    },
    userAva: function () {
        $('.ava-inp-file input').change(function () {
            var size = (this.files[0].size / (1024 * 1024)).toFixed(2);
            if (size > 10) {
                $(this).addClass('error').closest('.form-group').addClass('error')
            } else {
                $(this).removeClass('error').closest('.form-group').removeClass('error');
                var img = $(this).closest('.ava-inp-file').find('img');
                readURL(this, img)
                console.log()
            }
        });
    },
    comments: function () {
        $('.comments-container .comment-btm .js-lnk').click(function (e) {
            e.preventDefault();
            var $this = $(this),
                txt = !$this.closest('.comment-block').siblings('.answer').is(':visible') ? 'Скрыть ответ' : 'Ответный отзыв';
            $(this).closest('.comment-block').siblings('.answer').stop().slideToggle(function () {
                $this.text(txt);
            });


        });
    },
    blockStiky: function () {
        if ($('.sticky-block').length > 0) {
            setTimeout(function () {
                var top = $('.header-top').outerHeight() +5;
                $('.sticky-block').each(function (e) {
                    var container = $(this).data('container');
                    $(this).hcSticky({
                        stickTo: container,
                        top: top,
                        responsive: true,
                        responsive: {
                            991: {
                                disable: true
                            }
                        }
                    });
                });
            }, 300)

        }
    },
    assessment: function () {
        $('.assessment-box label').hover(
            function(){ //over
                $(this).addClass('active').prevAll().addClass('active');
                $(this).nextAll().removeClass('active');
            },
            function(){ //out
                checkAssement()
            }
        );
        $('.assessment-box input[type="radio"]').each(function () {
            checkAssement();
        });
        function checkAssement() {
            if ($('.assessment-box').find('input:checked').length>0) {
                $('[for="'+$('.assessment-box').find('input:checked').attr('id')+'"]').addClass('active').prevAll().addClass('active');
                $('[for="'+$('.assessment-box').find('input:checked').attr('id')+'"]').nextAll().removeClass('active');
            } else {
                $('.assessment-box label').removeClass('active')
            }

        }
    },

    mainInit: function () {
        this.assessment();
        this.blockStiky();
        this.comments();
        this.userAva();
        this.profileTabsHead();
        this.footerDropdown();
        this.tooltips();
        this.validation();
        this.tabs();
        this.footerEmpty();
        this.mainSearch();
        this.select();
        this.menu();
        this.isDesktop();
        this.isTabletWide();
        this.isTablet();
        this.isPhoneWide();
        this.isTouch();
        this.svg();
        this.inpMask();
        this.timeActive();
        this.headerFixed();
        this.btnUp();
    }
};
$(document).ready(function () {
    ui.mainInit();
    $('header .header-top .right-nav  .login>a[data-fancybox]').fancybox({
        touch: false
    });
}); // end document.ready


$(window).load(function () {


}); // end window.load


$(window).resize(function () {
    ui.isDesktop();
    ui.isTabletWide();
    ui.isTablet();
    ui.isPhoneWide();
    ui.footerEmpty();
}); // end window.resize

$(window).scroll(function () {
    ui.headerFixed();
    ui.btnUp();
}); // end window.scroll
function readURL(input, img) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(img).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}