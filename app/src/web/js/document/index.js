function isSelectAllChecked() {
    return $('#documents-table .select-on-check-all').is(':checked');
}

function beforePageRender(items) {
    if (isSelectAllChecked()) {
        for (var i = 0; i < items.length; i++) {
            $(items[i]).find('[name="selection[]"]').prop('checked', true);
        }
    }
}

function getSelectedDocumentsIds() {
    return $('#documents-table').yiiGridView('getSelectedRows');
}

function resendByIds() {
    var ids = getSelectedDocumentsIds();

    if (ids.length === 0) {
        return;
    }
    if (!confirm(i18next.t('confirmDocumentsResending', {count: ids.length}))) {
        return;
    }

    var form = $('#resend-by-ids-form');
    form.find('input[name="ids[]"]').remove();
    for (var i = 0; i < ids.length; i++) {
        form.append($('<input type="hidden" name="ids[]" value="' + ids[i] + '"/>'));
    }

    form.trigger('submit');
}

function resendBySearchParams() {
    var confirmMessage = i18next.t('allDocumentsAreSelected') + ' ' + i18next.t('confirmDocumentsResending', {count: window.gon.count});

    if (!confirm(confirmMessage)) {
        return;
    }

    var form = $('#resend-by-search-params-form');
    form.find('input[name^="DocumentSearch"]').remove();
    for (var pair of new URLSearchParams(location.search)) {
        var name = pair[0];
        var value = pair[1];
        if (value !== '') {
            form.append($('<input type="hidden" name="' + name + '" value="' + value + '"/>'));
        }
    }

    form.trigger('submit');
}

$('body').on('click', '#documents-table tbody td:not(:first-child)', function () {
    var id = $(this).closest('tr').data('key');
    viewData('/document/view?id=' + id);
});

$('body').on('change', '#documents-table [name="selection[]"]', function () {
    var hasSelected = getSelectedDocumentsIds().length > 0;
    $('#resend-button').attr('disabled', !hasSelected);
});

$('#type-filter-modal .clear-button').on('click', function () {
    $('#type-select').val(null).trigger('change');
});

$('#type-filter-modal .apply-button').on('click', function () {
    var applyFilter = function (selectionId) {
        $('#documents-table')
            .find('[name="DocumentSearch[message_code_selection_id]"]')
            .val(selectionId)
            .trigger('change');
    };

    $(this).prop('disabled', true);
    var values = $('#type-select').val();
    if (values.length === 0) {
        applyFilter('');
        return;
    }
    var self = this;
    $.ajax({
        url: '/document/save-message-code-filter-selection',
        data: JSON.stringify(values),
        type: 'post',
        contentType: 'application/json',
        error: function () {
            $(self).prop('disabled', false);
        },
        success: function (response) {
            if (response.id) {
                applyFilter(response.id);
            }
        },
    });
});

$('#type-filter-modal').on('show.bs.modal', function () {
    // If values were edited and modal was closed without applying changes, on modal re-open actual values should be restored
    var state = $(this).find('input[name=state]').val();
    $('#type-select').val(JSON.parse(state)).trigger('change');
});

$('#resend-button').on('click', function () {
    if (isSelectAllChecked()) {
        resendBySearchParams();
    } else {
        resendByIds();
    }
});
