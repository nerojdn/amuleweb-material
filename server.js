document.addEventListener('DOMContentLoaded', function () {

    var selectedServer = null;

    var cards = document.querySelectorAll('.server-card');
    var actions = document.getElementById('mobileServerActions');

    var connectButton =
        document.getElementById('mobileServerConnectButton');

    var removeButton =
        document.getElementById('mobileServerRemoveButton');


    function deselectAll() {

        cards.forEach(function (card) {
            card.classList.remove('selected');
        });

        selectedServer = null;

        actions.classList.remove('visible');
    }


    cards.forEach(function (card) {

        card.addEventListener('click', function () {

            // Si pulsamos la tarjeta ya seleccionada,
            // la deseleccionamos.
            if (card.classList.contains('selected')) {
                deselectAll();
                return;
            }

            // Solo puede existir una selección.
            cards.forEach(function (otherCard) {
                otherCard.classList.remove('selected');
            });

            card.classList.add('selected');

            selectedServer = {
                ip: card.dataset.ip,
                port: card.dataset.port
            };

            actions.classList.add('visible');

        });

    });


    connectButton.addEventListener('click', function () {

        if (!selectedServer) {
            return;
        }

        window.location.href =
            'amuleweb-main-servers.php' +
            '?cmd=connect' +
            '&ip=' + encodeURIComponent(selectedServer.ip) +
            '&port=' + encodeURIComponent(selectedServer.port);

    });


    removeButton.addEventListener('click', function () {

        if (!selectedServer) {
            return;
        }

        if (!confirm('¿Eliminar este servidor?')) {
            return;
        }

        window.location.href =
            'amuleweb-main-servers.php' +
            '?cmd=remove' +
            '&ip=' + encodeURIComponent(selectedServer.ip) +
            '&port=' + encodeURIComponent(selectedServer.port);

    });

});