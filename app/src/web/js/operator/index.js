function updateTerminalSelectOptions() {
    var participantId = $('#operator-member_id').val();
    var terminals = getTerminalsByParticipant(participantId);

    var $select = $('#operator-terminal_id');
    $select.find('option').remove();
    addOption($select, null, '-');
    for (var i = 0; i < terminals.length; i++) {
        var terminal = terminals[i];
        addOption($select, terminal.id, terminal.name);
    }
}

function getTerminalsByParticipant(participantId) {
    if (!participantId) {
        return [];
    }
    var terminalsByParticipants = window.gon.terminalsByParticipants;
    if (!terminalsByParticipants[participantId]) {
        return [];
    }
    return terminalsByParticipants[participantId];
}

function addOption($select, value, text) {
    $('<option></option>')
        .val(value)
        .text(text)
        .appendTo($select);
}
