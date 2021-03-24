var myMap, multiRoute;

$(document).ready(function () {
    if(route.length > 0){
        $('#map-area').css({
            'height': '250px',
            'margin-bottom': '20px'
        });
        ymaps.ready(function () {
            myMap = new ymaps.Map("map-area", {
                center: [55.752554272581875, 37.61941593484034],
                zoom: 12,
                controls: ['zoomControl', 'fullscreenControl']
            });

            multiRoute = new ymaps.multiRouter.MultiRoute({
                referencePoints: route
            }, {
                boundsAutoApply: true
            });

            myMap.geoObjects.add(multiRoute);
        });
    }

    $.mask.definitions['h'] = '[0-2]';
    $.mask.definitions['m'] = '[0-5]';
    $('.adding-job  .time').mask('h9:m9');
    $('.adding-job  .time').bind("keyup", function(e) {
        var time = $(this).val().split( ':' );
        if ( +time[0] > 23 || +time[1] > 60 ) {
            $(this).val('')
        }
    });
    $.datepicker.regional['ru'] = {
        closeText: datepicerCloseTxt,
        prevText: datepicerPrevTxt,
        nextText: datepicerNextTxt,
        currentText: datepicerCurrentTxt,
        monthNames: datepicerMonthName,
        monthNamesShort: datepicerMonthNameShort,
        dayNames: datepicerDaysName,
        dayNamesShort: datepicerDaysNameShort,
        dayNamesMin: datepicerDaysNameMin,
        weekHeader: datepicerWeekTxt,
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    $('.ui-datepicker').datepicker({
        dateFormat: "dd.mm.yy",
        minDate: new Date()
    })
        .on("change", function () {
            $(this).closest('.form-group').find('.time').focus();
            if ($('#date-end').val() != '' && $('#date-start').val() != '') {
                validateDate();
            }
        });
    $('.adding-job .time').blur(function () {
        validateDate();
    });
    function validateDate() {
        var dateStart = $('#date-start').val().split('.'),
            dateEnd = $('#date-end').val().split('.'),
            timeStart = $('[name="timeStart"]').val().split(':'),
            hoursStart = +timeStart[0],
            minutesStart = (isNaN(timeStart[1])) ? 0: +timeStart[1],
            dStart = new Date(dateStart[2], dateStart[1] - 1, dateStart[0], hoursStart, minutesStart),
            timeEnd = $('[name="timeEnd"]').val().split(':'),
            hoursEnd = +timeEnd[0],
            minutesEnd = (isNaN(timeEnd[1])) ? 0: +timeEnd[1],
            dEnd = new Date(dateEnd[2], dateEnd[1] - 1, dateEnd[0], hoursEnd, minutesEnd);
        if (dStart > dEnd) {
            $('.ui-datepicker,  .adding-job  .time').val('');
            $('#date-start').focus();
        }
    }

    $('.adding-job  .presently,.adding-job .tomorrow').click(function (e) {
        e.preventDefault();
        var date = new Date(),
            date = ($(this).hasClass('tomorrow')) ? new Date(date.getTime() + (24 * 60 * 60 * 1000)) : date
        day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate(),
            month = (date.getMonth() < 9) ? '0' + (date.getMonth() + 1) : date.date.getMonth();

        $(this).closest('.form-group').find('.ui-datepicker').val(day + '.' + month + '.' + date.getFullYear()).siblings('.time').focus();
        if ($('#date-end').val() != '' && $('#date-start').val() != '') {
            validateDate();
        }
    });

    $(document).on('click', '.adding-job .contract-inp', function (e) {
        if ($(this).is(':checked')) {
            $(this).closest('.row').find('input[type="text"]').attr('disabled', 'disabled').val('');
            $('#checkbox-security').prop("checked", false);

        } else {
            $(this).closest('.row').find('input[type="text"]').removeAttr('disabled');
        }
    });

    $('.btn-file input').change(function (e) {
        var filesL = $(this).closest('.form-group').find('.files');
        if (filesL.find('.itm:not(.deleted)').length < maxFiles) {
            var file = $(this).prop('files')[0],
                clone = $(this).clone(),
                itm = '<div class="itm"><span>' + file.name + '</span><a class="remove" href="#"><svg class="icon icon_close"> <use xlink:href="' + templatePath + '/images/sprite-svg.svg#close"></use></svg></a></div>';
            $(this).val('');
            filesL.append(itm);
            filesL.find('.itm:last-child').prepend(clone);
            if (filesL.find('.itm:not(.deleted)').length === maxFiles) {
                $(this).closest('.btn-file').addClass('btn-disabled')
            }
        }
    });

    $(document).on('click', '.adding-job .files .itm .remove', function (e) {
        e.preventDefault();
        var parent = $(this).closest('.form-group'),
            parItm = $(this).closest('.itm');
        if (parItm.hasClass('existing')) {
            if (parent.find('.files .itm').length < maxFiles) {
                parItm.toggleClass('deleted');
                if (!parItm.hasClass('deleted')) {
                    parItm.find('input').remove();
                } else {
                    parItm.append('<input type="hidden" name="__removeFiles[]" value="' + $(this).data('id') + '">');
                }
            } else {
                parItm.addClass('deleted');
                parItm.append('<input type="hidden" name="__removeFiles[]" value="' + $(this).data('id') + '">');
            }
        } else {
            $(this).closest('.itm').remove();
        }
        if (parent.find('.files .itm:not(.deleted)').length < maxFiles) {
            parent.find('.btn-file').removeClass('btn-disabled')
        }
    });

    $(document).on('keyup change', 'input.number-float', function (e) {
        var $this = $(this);
        $this.val(replaceStr($this.val()));
    });

    $('#stages .btn').click(function (e) {
        e.preventDefault();
        var i = $('#stages .stages-list .itm').length + 1;
        $('#stages .stages-list').append(addStage(i));
        if (i > 1) {
            $('#stages .remove').show();
        }
    });

    $(document).on('focus change', '.contract-inp, .budget-inp', function (e) {
        $(this).closest('.row').find('.error').removeClass('error');
    });

    $(document).on('click', '.adding-job .stages .stages-list .remove', function (e) {
        e.preventDefault();
        var list = $(this).closest('.stages-list'),
            i = 0,
            itm = $(this).closest('.itm'),
            id = itm.data('id'),
            remove = '<a href="#" class="remove">';
        remove += '<svg class="icon icon_close">';
        remove += '<use xlink:href="' + siteTemplatePach + '/images/sprite-svg.svg#close"></use>';
        remove += '</svg>';
        remove += '</a>';
        
        if (itm.hasClass('existing')) {
            list.prepend('<input type="hidden" name="deleteStages[]" value="' + id + '">');
            itm.remove();
            list.find('.itm').each(function () {
                i++;
                var itmId = $(this).data('id');
                $(this).find('.ttl').html(stageStepTTl + ' # ' + i + remove);

                $(this).find('[for^="input-name"]').attr('for', 'input-name' + i);
                $(this).find('[id^="input-name"]').attr('id', 'input-name' + i);
                $(this).find('[id^="input-name"]').attr('name', 'stages[' + itmId + '][name]');

                $(this).find('[for^="stage-price"]').attr('for', 'stage-price' + i);
                $(this).find('[id^="stage-price"]').attr('id', 'stage-price' + i);
                $(this).find('[id^="stage-price"]').attr('name', 'stages[' + itmId + '][price]');

                $(this).find('[for^="stage-contract"]').attr('for', 'stage-contract' + i);
                $(this).find('[id^="stage-contract"]').attr('id', 'stage-contract' + i);
                $(this).find('[id^="stage-contract"]').attr('name', 'stages[' + itmId + '][contractPrice]');
            })
        } else {
            itm.remove();
            list.find('.itm').each(function () {
                i++;
                $(this).find('.ttl').html(stageStepTTl + ' # ' + i + remove);

                $(this).find('[for^="newStage-name"]').attr('for', 'newStage-name' + i);
                $(this).find('[id^="newStage-name"]').attr('id', 'newStage-name' + i);
                $(this).find('[id^="newStage-name"]').attr('name', 'newStages[' + i + '][name]');

                $(this).find('[for^="newStage-price"]').attr('for', 'newStage-price' + i);
                $(this).find('[id^="newStage-price"]').attr('id', 'newStage-price' + i);
                $(this).find('[id^="newStage-price"]').attr('name', 'newStages[' + i + '][price]');

                $(this).find('[for^="newStage-contract"]').attr('for', 'newStage-contract' + i);
                $(this).find('[id^="newStage-contract"]').attr('id', 'newStage-contract' + i);
                $(this).find('[id^="newStage-contract"]').attr('name', 'newStages[' + i + '][contractPrice]');
            })
        }
        if (list.find('.itm').length == 1) {
            list.find('.itm .remove').hide();
        }
    });

    $('.adding-job .btn-send').click(function (e) {
        e.preventDefault();
        checkInput($(this).closest('form'));
        if ($(this).closest('form').find('.error:visible').length === 0) {
            $(this).closest('form').submit();
        }
    });

    $('#checkbox-security').change(function () {
        if ($(this).is(':checked')) {
            $('.contract-inp').prop("checked", false);
            $('.budget-inp').prop("disabled", false);
        }
    });
});

function replaceStr(str) {
    var reg = /\d+([\.,]\d+)?/g,
        regP = /[\.,]/g,
        regN = /\d+/;
    if (str.match(reg)) {
        if (str.match(regP) && str.split(regP)[1].length < 2) {
            str = (str.split(regP)[1].length == 0) ? str.split(regP)[0] + '.' : (regN.test(str.split(regP)[1])) ? str : str.slice(0, -1);
        } else {
            str = str.match(reg);
            str = (str) ? str[0] : '';
        }
    } else {
        str = ''
    }
    return str;
}

function changeStage() {
    var stage = parseInt($('.adding-job input[name="radioStage"]:checked').val());
    if (stage) {
        $('#total-budget').hide();
        $('#stages').show();
    } else {
        $('#total-budget').show();
        $('#stages').hide();
    }
}

function addStage(i) {
    var stage = '<div class="itm">';
    stage += '<div class="ttl bold">' + stageStepTTl + ' # ' + i + '<a href="#" class="remove">';
    stage += '<svg class="icon icon_close">';
    stage += '<use xlink:href="' + siteTemplatePach + '/images/sprite-svg.svg#close"></use>';
    stage += '</svg>';
    stage += '</a></div>';
    stage += '<div class="form-group">';
    stage += '<label for="newStage-name' + i + '">' + stageNameLbl + '</label>';
    stage += '<input required="" id="newStage-name' + i + '" class="form-control required" type="text" name="newStages[' + i + '][name]">';
    stage += '</div>';
    stage += '<div class="row">';
    stage += '<div class="col-sm-6 col-xxs-12">';
    stage += '<div class="form-group">';
    stage += '<label for="newStage-price' + i + '">' + stagePriceLbl + '</label>';
    stage += '<input id="newStage-price' + i + '" class="budget-inp form-control number-float" type="text" name="newStages[' + i + '][price]">';
    stage += '</div>';
    stage += '</div>';
    stage += '<div class="col-sm-6 col-xxs-12">';
    stage += '<div class="form-group">';
    stage += '<input class="contract-inp checkbox" type="checkbox" name="newStages[' + i + '][contractPrice]" value="0" id="newStage-contract' + i + '">';
    stage += '<label class="form-checkbox" for="newStage-contract' + i + '">';
    stage += '<span class="icon-wrap">';
    stage += '<svg class="icon icon_checkmark">';
    stage += '<use xlink:href="' + siteTemplatePach + '/images/sprite-svg.svg#checkmark"></use>';
    stage += '</svg>';
    stage += '</span>' + contractor;
    stage += '</label>';
    stage += '</div>';
    stage += '</div>';
    stage += '</div>';
    stage += '</div>';
    return stage;
}

function checkInput(form) {
    form.find('select.required:visible').each(function () {
        if ($(this).val() !== '') {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        } else {
            $(this).addClass('error').closest('.form-group').addClass('error').find('.error-message').show();
        }
    });
    form.find('input[type=text].required:visible').each(function () {
        if ($(this).val() !== '') {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        } else {
            $(this).addClass('error').closest('.form-group').addClass('error');
        }
    });
    form.find('textarea.required:visible').each(function () {
        if ($(this).val() !== '') {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        } else {
            $(this).addClass('error').closest('.form-group').addClass('error');
        }
    });
    form.find('input[type=checkbox].required:visible').each(function () {
        if ($(this).is(':checked')) {
            $(this).removeClass('error').closest('.form-group').removeClass('error');
        } else {
            $(this).addClass('error').closest('.form-group').addClass('error');
        }
    });
    form.find('.budget-inp:visible').each(function () {
        var parent = $(this).closest('.row'),
            contr = parent.find('.contract-inp');
        if ($(this).val() === '' && !contr.is(':checked')) {
            $(this).addClass('error').closest('.form-group').addClass('error');
            contr.addClass('error').closest('.form-group').addClass('error');
        }
    })
}