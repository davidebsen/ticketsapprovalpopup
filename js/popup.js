
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

    if (typeof $().dialog !== "function") {
        console.log("jQuery UI não encontrado. Carregando...");
        loadCSS("https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css");
        loadScript("https://code.jquery.com/ui/1.13.2/jquery-ui.min.js", initPopup);
    } else {
        initPopup();
    }

    function initPopup() {
        console.log("Iniciando verificação de tickets...");

        $.ajax({
            url: "plugins/ticketsapprovalpopup/front/popup.php",
            method: "GET",
            dataType: "json",
            success: function (tickets) {
                console.log("Tickets recebidos:", tickets);

                if (tickets.length > 0) {
                    let message = "<strong>Chamados solucionados aguardando sua aprovação:</strong><ul style='list-style:none; padding:0;'>";
                    tickets.forEach(function (ticket) {
                        message += `
                            <li style="margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                                <div><strong>#${ticket.id} - ${ticket.name}</strong></div>
                                <div>🕓 Abertura: ${ticket.date}</div>
                                <div>✅ Conclusão: ${ticket.closedate}</div>
                                <div style="margin-top: 8px;">
                                    <a href="${ticket.link}" target="_blank" style="padding: 6px 12px; background-color: #007bff; color: white; border-radius: 4px; text-decoration: none;">
                                        Ver chamado
                                    </a>
                                </div>
                            </li>
                        `;
                    });
                    message += "</ul>";

                    $("<div>").html(message).dialog({
                        modal: true,
                        title: "Aviso de Aprovação",
                        width: 500,
                        buttons: {
                            "Fechar": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
        });
    }
})();
