function getSelectedDownloadCards() {
    return Array.from(
        document.querySelectorAll('.download-card')
    ).filter(function(card) {
        var checkbox = card.querySelector('input[type="checkbox"]');
        return checkbox && checkbox.checked;
    });
}


function getDownloadActions(cards) {

    if (cards.length === 0) {
        return [];
    }

    var hasDownloading = false;
    var hasPaused = false;
    var hasWaiting = false;

    cards.forEach(function(card) {

        var status = card.dataset.status;

        if (status === 'Downloading') {
            hasDownloading = true;
        }

        if (status === 'Paused') {
            hasPaused = true;
        }

        if (status === 'Waiting') {
            hasWaiting = true;
        }
    });

    var actions = [];

    /*
     * Reanudar solamente cuando TODAS las seleccionadas
     * están en un estado que admite reanudación.
     */
    if (!hasDownloading && !hasWaiting) {
        actions.push('resume');
    }

    /*
     * Pausar solamente cuando NINGUNA de las seleccionadas
     * está pausada.
     */
    if (!hasPaused) {
        actions.push('pause');
    }

    /*
     * Cancelar siempre es una acción común.
     */
    actions.push('cancel');

    return actions;
}


function updateDownloadSelection() {

    var selectedCards = getSelectedDownloadCards();
    var selectedCount = selectedCards.length;

    var actions = getDownloadActions(selectedCards);
    var actionsBar = document.getElementById('mobileDownloadActions');

    var countElement = document.getElementById('downloadSelectedCount');

    if (!actionsBar) {
        return;
    }

    /*
     * Actualizar contador.
     */
    if (countElement) {
        countElement.textContent = selectedCount;
    }

    /*
     * Mostrar/ocultar cada acción.
     */
    actionsBar.querySelectorAll('.mobile-download-action').forEach(function(button) {

        var action = button.dataset.action;

        button.style.display =
            actions.indexOf(action) !== -1
                ? 'flex'
                : 'none';
    });
    

    /*
     * Mostrar la barra solamente cuando hay selección.
     */
    if (selectedCards.length > 0) {
        actionsBar.classList.add('visible');
    } else {
        actionsBar.classList.remove('visible');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateDownloadSelection();
});