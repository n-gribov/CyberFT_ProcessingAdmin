var dataModalSelector = '#data-modal';
var dataModalTitleSelector = '#data-modal .modal-title';
var dataModalBodySelector = '#data-modal .modal-body';
var dataFormSelector = '#data-form';
var $body = $('body');

//Массив интервалов и стоимостей документов для создания тарифных планов
let registry_tariffication_array = [];

//Для форматирования чисел в рубли
let rubFormat = new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' });

function viewData(route) {
    if (window.getSelection().toString().length === 0) {
        $.get(route).done(function(response) {
            renderResponse(response);
            $(dataModalSelector).modal();
        });
    }
}

function showCreateModal(parentId, isParticipant) {
    var path = '/' + gon.controllerName + '/create';
    if (parentId && !isParticipant) {
        path += '?parentId=' + parentId;
    } else if (parentId && isParticipant) {
        path += '?participantId=' + parentId;
    }

    $.get(path).done(function(response) {
        renderResponse(response);
        $(dataModalSelector).modal();
    });
}

function renderResponse(response) {
    $(dataModalTitleSelector).html(response.title);
    $(dataModalBodySelector).html(response.content);
}

function removeHashFromLocation() {
    var newUrl = window.location.href.split("#")[0];
    window.history.replaceState({}, document.title, newUrl);
}

$('#btn-create-data').on('click', function(e) {
    e.preventDefault();
    var parentId = $('#btn-create-data').data('parent-id');
    if (parentId) {
        showCreateModal($('#btn-create-data').data('parent-id'), 0);
    } else {
        showCreateModal($('#btn-create-data').data('participant-id'), 1);
    }
});

$body.on('beforeSubmit', dataFormSelector, function(e) {
    var formData = new FormData($(dataFormSelector).get(0));
    var action = $(this).attr('action');

    $.ajax({
        url: action,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            renderResponse(response);
        }
    });

    return false;
});

$body.on('click', '#update-data', function(e) {
    e.preventDefault();

    var path = '/' + gon.controllerName + '/update';
    var dataId = $(this).data('id');

    $.get(path, {'id': dataId}).done(function(response) {
        renderResponse(response);
    });
});

$body.on('click', '#block-data', function(e) {
    e.preventDefault();

    var path = '/' + gon.controllerName + '/block';
    var dataId = $(this).data('id');

    $.get(path, {'id': dataId}).done(function(response) {
        renderResponse(response);
    });
});

$body.on('click', '#unblock-data', function(e) {
    e.preventDefault();

    var path = '/' + gon.controllerName + '/unblock';
    var dataId = $(this).data('id');

    $.get(path, {'id': dataId}).done(function(response) {
        renderResponse(response);
    });
});

$body.on('click', '#btn-update-data-cancel', function (e) {
    e.preventDefault();

    var route = $(this).data('route');

    $.get(route).done(function(response) {
        renderResponse(response);
    });
});

$body.on('click', 'a.modal-action-button', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    $.post(url).done(function(response) {
        renderResponse(response);
    });
});

$(document).ready(function () {
    var hash = window.location.hash;
    var matches = hash.match(/^#?create\((\d+)\)$/);
    if (matches !== null) {
        showCreateModal(matches[1]);
        removeHashFromLocation();
    }

    // Fix for Select2 widget in modal
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
});
