$(document).ready(function () {
    $('.package-root-checkbox, .package-item-checkbox').change(function () {
        calculateCost();
    });
});

function calculateCost() {
    let sum = 0;
    $('.package-root-checkbox').each(function () {
        let root = this;
        let p = $(root).data('package');

        if ($(root).is(':checked')) {
            sum += $(root).data('price');
            $('.package-item-' + p).prop('checked', false);
        } else {
            let itemsSum = 0;
            let checkedCount = 0;
            let itemsCount = 0;
            $('.package-item-' + p).each(function () {
                itemsCount++;
                let item = this;
                if ($(item).is(':checked')) {
                    checkedCount++;
                    itemsSum += $(item).data('price');
                }
            });

            if (checkedCount === itemsCount) {
                itemsSum = $(root).data('price');
                $(root).prop('checked', true);
                $('.package-item-' + p).prop('checked', false);
            } else {
                $(root).prop('checked', false);
            }
            sum += itemsSum;
        }
    });

    $('#tariff-sum').text(sum);
    if (sum > 0) {
        $('.tariff-plan button').prop('disabled', false);
    } else {
        $('.tariff-plan button').prop('disabled', true);
    }
}

