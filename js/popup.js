
(function () {
    function loadScript(url, callback) {
        var script = document.createElement('script');
        script.src = url;
        script.onload = callback;
        document.head.appendChild(script);
    }

    function loadCSS(url) {
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = url;
        document.head.appendChild(link);
    }

    function showLoadingSpinner() {
        const loading = document.createElement('div');
        loading.id = 'popup-loading';
        loading.innerHTML = '<div style="position:fixed;top:10%;left:50%;transform:translateX(-50%);padding:1em;background:#fff;border:1px solid #ccc;border-radius:5px;z-index:10000;">Carregando tickets...</div>';
        document.body.appendChild(loading);
    }

    function hideLoadingSpinner() {
        const loading = document.getElementById('popup-loading');
        if (loading) loading.remove();
    }

    function alreadyShownThisSession() {
        return sessionStorage.getItem("popupShown") === "true";
    }

    function markAsShown() {
        sessionStorage.setItem("popupShown", "true");
    }

    function renderPopup(tickets) {
        if (!tickets || tickets.length === 0) return;

        var popupContent = '<div><h3>Tickets pendentes de aprovação:</h3>';

        tickets.forEach(function (ticket) {
            popupContent += `
                <div style="border:1px solid #007bff; border-radius:10px; padding:1em; margin-bottom:1em;">
                    <strong>#${ticket.id} - ${ticket.title}</strong><br>
                    🕒 Abertura: ${ticket.open_date}<br>
                    👤 Atribuído a: ${ticket.assigned_to || "Não atribuído"}<br>
                    ✅ Solucionado em: ${ticket.solve_date || "Não informado"}<br>
                    <button onclick="window.open('ticket.php?id=${ticket.id}')" style="margin-right: 8px">
        Ver chamado
    </button>
    <button onclick="aprovarChamado(${ticket.id})" style="background-color:green;color:white;border:none;padding:0.5em 1em;border-radius:5px;">
        Aprovar
    </button>
                            style="margin-top:0.5em;background:#007bff;color:white;border:none;padding:0.5em 1em;border-radius:5px;">
                        Ver chamado
                    </button>
                </div>`;
        });

        popupContent += '</div>';

        $("<div></div>").html(popupContent).dialog({
            modal: true,
            title: "Aprovação de Tickets",
            width: 500,
            close: function () {
                markAsShown();
            }
        });
    }

    function initPopup() {
        if (alreadyShownThisSession()) return;

        showLoadingSpinner();

        $.ajax({
            url: "plugins/ticketsapprovalpopup/front/popup.php",
            method: "GET",
            dataType: "json",
            success: function (tickets) {
                hideLoadingSpinner();
                renderPopup(tickets);
            },
            error: function () {
                hideLoadingSpinner();
                console.error("Erro ao buscar tickets");
            }
        });
    }

    if (typeof $().dialog !== "function") {
        loadCSS("https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css");
        loadScript("https://code.jquery.com/ui/1.13.2/jquery-ui.min.js", initPopup);
    } else {
        initPopup();
    }
})();
