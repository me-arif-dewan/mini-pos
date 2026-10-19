import $ from 'jquery';

/** Mirrors App\Services\OrderCalculator so the preview matches what the server saves. */
const round2 = (n) => Math.round((Number(n) + Number.EPSILON) * 100) / 100;

function discountAmount(base, type, value) {
    value = parseFloat(value) || 0;
    if (value <= 0 || base <= 0 || !type) return 0;
    const amount = type === 'percent' ? round2((base * Math.min(value, 100)) / 100) : value;
    return round2(Math.min(amount, base));
}

export function initOrderForm() {
    const $form = $('#order-form');
    if (!$form.length) return;

    const taxRate = parseFloat($form.data('tax-rate')) || 0;
    const currency = String($form.data('currency') ?? '');
    const template = $('#item-row-template').html();
    const $rows = $('#item-rows');
    const errors = $form.data('errors') || {};
    let nextIndex = 0;

    const money = (n) =>
        currency + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    function addRow(values = {}) {
        const index = nextIndex++;
        const $row = $(template.replace(/__INDEX__/g, index));

        if (values.product_id) $row.find('.js-product').val(String(values.product_id));
        if (values.quantity) $row.find('.js-qty').val(values.quantity);
        if (values.discount_type) $row.find('.js-dtype').val(values.discount_type);
        if (values.discount_value) $row.find('.js-dvalue').val(values.discount_value);

        const rowErrors = Object.entries(errors)
            .filter(([key]) => key.startsWith(`items.${values.__old ?? index}.`))
            .flatMap(([, messages]) => messages);
        if (rowErrors.length) {
            $row.find('.js-row-errors').html(rowErrors.map((m) => `<p class="field-error">${$('<i>').text(m).html()}</p>`).join(''));
            $row.find('.js-qty').addClass('input-error');
        }

        $rows.append($row);
        refreshRow($row);
        syncProductOptions();
        recalc();
    }

    function refreshRow($row) {
        const $option = $row.find('.js-product option:selected');
        const price = parseFloat($option.data('price'));
        const stock = $option.data('stock');
        const hasProduct = $option.val() !== '';

        $row.find('.js-price').val(hasProduct ? price.toFixed(2) : '');

        const qty = parseInt($row.find('.js-qty').val(), 10) || 0;
        const $stock = $row.find('.js-stock');

        if (!hasProduct) {
            $stock.text('Pick a product').removeClass('text-rose-600 text-emerald-600').addClass('text-slate-400');
        } else if (qty > stock) {
            $stock.text(`Only ${stock} in stock`).removeClass('text-slate-400 text-emerald-600').addClass('text-rose-600');
            $row.find('.js-qty').addClass('input-error');
        } else {
            $stock.text(`${stock} in stock`).removeClass('text-slate-400 text-rose-600').addClass('text-emerald-600');
            $row.find('.js-qty').removeClass('input-error');
        }

        const gross = hasProduct ? round2(price * qty) : 0;
        const discount = discountAmount(gross, $row.find('.js-dtype').val(), $row.find('.js-dvalue').val());
        $row.data({ gross, discount, over: hasProduct && qty > stock, ready: hasProduct && qty > 0 });
        $row.find('.js-line-total').text(money(gross - discount));
    }

    /** A product can only be on one line, so grey it out in the other selects. */
    function syncProductOptions() {
        const chosen = $rows.find('.js-product').map((_, el) => el.value).get().filter(Boolean);
        $rows.find('.js-product').each(function () {
            const own = this.value;
            $(this).find('option').each(function () {
                this.disabled = this.value !== '' && this.value !== own && chosen.includes(this.value);
            });
        });
    }

    function recalc() {
        let subtotal = 0;
        let lineDiscounts = 0;
        let valid = $rows.children().length > 0;

        $rows.children().each(function () {
            const $row = $(this);
            subtotal += $row.data('gross') || 0;
            lineDiscounts += $row.data('discount') || 0;
            if (!$row.data('ready') || $row.data('over')) valid = false;
        });

        subtotal = round2(subtotal);
        lineDiscounts = round2(lineDiscounts);
        const linesNet = round2(subtotal - lineDiscounts);
        const orderDiscount = discountAmount(linesNet, $('#order-discount-type').val(), $('#order-discount-value').val());
        const taxable = round2(linesNet - orderDiscount);
        const tax = round2((taxable * taxRate) / 100);
        const grand = round2(taxable + tax);

        $('#sum-subtotal').text(money(subtotal));
        $('#sum-line-discounts').text('-' + money(lineDiscounts));
        $('#sum-order-discount').text('-' + money(orderDiscount));
        $('#sum-taxable').text(money(taxable));
        $('#sum-tax').text(money(tax));
        $('#sum-grand').text(money(grand));
        $('#empty-items').toggleClass('hidden', $rows.children().length > 0);
        $('#submit-order').prop('disabled', !valid);
    }

    // Events
    $('#add-item').on('click', () => addRow());

    $rows.on('change input', '.js-product, .js-qty, .js-dtype, .js-dvalue', function () {
        refreshRow($(this).closest('.js-row'));
        syncProductOptions();
        recalc();
    });

    $rows.on('click', '.js-remove', function () {
        $(this).closest('.js-row').remove();
        syncProductOptions();
        recalc();
    });

    $('#order-discount-type, #order-discount-value').on('change input', recalc);

    $('#customer_id').on('change', function () {
        const $option = $(this).find('option:selected');
        const $info = $('#customer-info');
        if (!$option.val()) return $info.addClass('hidden');
        $info.removeClass('hidden');
        $('#ci-email').text($option.data('email') || 'No email');
        $('#ci-phone').text($option.data('phone') || 'No phone');
        $('#ci-address').text($option.data('address') || 'No address');
    }).trigger('change');

    // Restore rows after a failed submit, otherwise start with one empty line.
    const old = $form.data('old-items') || [];
    if (old.length) {
        Object.entries(old).forEach(([oldIndex, values]) => addRow({ ...values, __old: oldIndex }));
    } else {
        addRow();
    }
}
