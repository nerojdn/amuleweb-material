function getSelectedDownloadCards() {
    return Array.from(
        document.querySelectorAll('.download-card')
    ).filter(function(card) {
        var checkbox = card.querySelector('input[type="checkbox"]');
        return checkbox && checkbox.checked;
    });
}

function getSelectedDownloadRows() {

    return Array.from(
        document.querySelectorAll('.download-row')
    ).filter(function(row) {

        var checkbox = row.querySelector(
            'input[type="checkbox"]'
        );

        return checkbox && checkbox.checked;
    });
}


function getDownloadMobileActions(cards) {

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

function getDesktopDownloadActions(rows) {

    if (rows.length === 0) {
        return [];
    }

    var hasDownloading = false;
    var hasPaused = false;
    var hasWaiting = false;

    rows.forEach(function(row) {

        var status = row.dataset.status;

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


function updateMobileDownloadSelection() {

    var selectedCards = getSelectedDownloadCards();
    var selectedCount = selectedCards.length;

    var actions = getDownloadMobileActions(selectedCards);
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

function updateDesktopDownloadSelection() {

    var selectedRows = getSelectedDownloadRows();
    var selectedCount = selectedRows.length;

    var allCheckboxes = document.querySelectorAll(
        '.downloads-desktop .download-row input[type="checkbox"]'
    );

    var selectAllCheckbox = document.querySelector(
        '.downloads-header input[name="selectAllFiles"]'
    );

    var totalCount = allCheckboxes.length;

    if (selectAllCheckbox) {

        selectAllCheckbox.checked =
            totalCount > 0 &&
            selectedCount === totalCount;

        selectAllCheckbox.indeterminate =
            selectedCount > 0 &&
            selectedCount < totalCount;
    }

    var actions = getDesktopDownloadActions(selectedRows);

    document.querySelectorAll('.download-row').forEach(function(row) {

        var checkbox = row.querySelector(
            'input[type="checkbox"]'
        );

        if (!checkbox) {
            return;
        }

        row.classList.toggle(
            'selected',
            checkbox.checked
        );

    });

    var actionsBar =
        document.getElementById('desktopDownloadActions');

    var countElement =
        document.getElementById('desktopDownloadSelectedCount');

    /*
     * Actualizar contador.
     */
    if (countElement) {
        countElement.textContent = selectedCount;
    }

    /*
     * Mostrar/ocultar acciones según el estado
     * de las descargas seleccionadas.
     */
    if (actionsBar) {

        actionsBar
            .querySelectorAll('.desktop-download-action')
            .forEach(function(button) {

                var action = button.dataset.action;

                button.style.display =
                    actions.indexOf(action) !== -1
                        ? 'inline-flex'
                        : 'none';
            });

        /*
         * Mostrar la barra solamente cuando hay selección.
         */
        if (selectedCount > 0) {
            actionsBar.classList.add('visible');
        } else {
            actionsBar.classList.remove('visible');
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    updateMobileDownloadSelection();
    updateDesktopDownloadSelection();
});