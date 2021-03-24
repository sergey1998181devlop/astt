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
                    parItm.append('<input type="hidden" name="__removeFiles[]" value="'+$(this).data('id')+'">');
                }
            } else {
                parItm.addClass('deleted');
                parItm.append('<input type="hidden" name="__removeFiles[]" value="'+$(this).data('id')+'">');
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
    });

    $(document).on('focus change', '.contract-inp, .budget-inp', function (e) {
        $(this).closest('.row').find('.error').removeClass('error');
    });

    $(document).on('click', '.adding-job .stages .stages-list .remove', function (e) {
        e.preventDefault();
        $(this).closest('.itm').remove();
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


    prop($('#create-inp-subcategory').val());
});

function prop(id) {
    $.ajax({
        type: 'POST',
        url: ajaxPath,
        dataType: "json",
        data: 'iblock=' + id + '&type=properties',
        success: function (data) {
            if (!data.error) {
                properties(data['result'])
            }
        },
        error: function (data) {
        }
    });
};

function propSelectCreate(data) {
    var prop = $('#properties');
    var select = '';
    if (!data.isRequired) {
        select += '<select   style="width: 100%" name="prop[' + data.id + ']">';
        select += '<option value="0">' + selectVariant + '</option>';
    } else {
        select += '<select class="required" style="width: 100%"  required  name="prop[' + data.id + ']">';
    }
    $.each(data.values, function (index, item) {
        var selected = (item.isDefault) ? 'selected' : '';
        select += '<option value="' + item['id'] + '" ' + selected + '>' + item['name'] + '</option>';
    });
    select += '</select>';
    prop.append('<div class="col-md-4 col-sm-6 col-xs-12"><div class="form-group"><label>' + data.name + '</label> ' + select + '</div></div>');
    if (data.isRequired) {
        prop.find('>div:last-child').find('label').append('<span class="required-field">*</span>');
        prop.find('>div:last-child').find('select').attr('required', 'required').addClass('required');
        if (!prop.find('>div:last-child').find('option[selected]').length > 0) {
            prop.find('>div:last-child').find('select option:first-child').attr('selected', 'selected')
        }
    }
    prop.find('select:not(.select2-hidden-accessible)').select2({
        language: 'ru',
        minimumResultsForSearch: Infinity
    });
}

function properties(data) {
    var prop = $('#properties');
    prop.html('');
    $.each(data, function (index, value) {
        switch (value['type']) {
            case 'integer':
                prop.append('<div class="col-md-4 col-sm-6 col-xs-12"><div class="form-group"><label>' + value.name + '</label><input class="form-control number-float" type="text" name="prop[' + value.id + ']" ></div></div>');
                if (value.required) {
                    prop.find('>div:last-child').find('label').append('<span class="required-field">*</span>');
                    prop.find('>div:last-child').find('input').attr('required', 'required').addClass('required');
                }
                break;
            case 'string':
                prop.append('<div class="col-md-4 col-sm-6 col-xs-12"><div class="form-group"><label>' + value.name + '</label><input class="form-control" type="text" name="prop[' + value.id + ']" ></div></div>');
                if (value.required) {
                    prop.find('>div:last-child').find('label').append('<span class="required-field">*</span>');
                    prop.find('>div:last-child').find('input').attr('required', 'required').addClass('required');
                }
                break;
            case 'list':
                propSelectCreate(value)
                break;
        }
    });
}


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

function addStage(i) {
    var stage = '<div class="itm">';
    stage += '<div class="ttl bold">' + stageStepTTl + ' # ' + i + '<a href="#" class="remove">';
    stage += '<svg class="icon icon_close">';
    stage += '<use xlink:href="' + siteTemplatePach + '/images/sprite-svg.svg#close"></use>';
    stage += '</svg>';
    stage += '</a></div>';
    stage += '<div class="form-group">';
    stage += '<label for="stage-name' + i + '">' + stageNameLbl + '</label>';
    stage += '<input required="" id="stage-name' + i + '" class="form-control required" type="text" name="stages[' + i + '][name]">';
    stage += '</div>';
    stage += '<div class="row">';
    stage += '<div class="col-sm-6 col-xxs-12">';
    stage += '<div class="form-group">';
    stage += '<label for="stage-price' + i + '">' + stagePriceLbl + '</label>';
    stage += '<input id="stage-price' + i + '" class="budget-inp form-control number-float" type="text" name="stages[' + i + '][price]">';
    stage += '</div>';
    stage += '</div>';
    stage += '<div class="col-sm-6 col-xxs-12">';
    stage += '<div class="form-group">';
    stage += '<input class="contract-inp checkbox" type="checkbox" name="stages[' + i + '][contractPrice]" value="0" id="stage-contract' + i + '">';
    stage += '<label class="form-checkbox" for="stage-contract' + i + '">';
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