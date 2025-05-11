// Adicionado automaticamente
var link = document.createElement("link");
link.rel = "stylesheet";
link.href = "css/popup.css";
document.head.appendChild(link);


document.addEventListener('DOMContentLoaded', function() {
    fetch('/plugins/ticketsapprovalpopup/front/popup.php')
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const groups = {
                    'validacao': 'Chamados aguardando sua aprovação',
                    'planejado': 'Chamados planejados',
                    'solucionado': 'Chamados solucionados'
                };

                let popupContent = '';
                for (const [groupKey, groupTitle] of Object.entries(groups)) {
                    const filtered = data.filter(item => item.consulta === groupKey);
                    if (filtered.length > 0) {
                        popupContent += `<h2 style='margin-top:0; color:#007bff; font-size:18px;'>${groupTitle}</h2>`;
                        filtered.forEach(item => {
                            let commentDecoded = item.comment ? decodeHTMLEntities(item.comment) : '';
                            popupContent += `
                                <div style="border:1px solid #ddd; border-radius:8px; padding:10px; margin-bottom:10px; background:#f9f9f9;">
                                    <strong>ID:</strong> ${item.id} 
                                    <a href="${item.link}" target="_blank" 
                                    style="background:#007bff; color:#fff; padding:4px 8px; border-radius:4px; text-decoration:none; margin-left:5px;">Abrir</a><br>
                                    <strong>Título:</strong> ${item.title}<br>
                                    <strong>Abertura:</strong> ${item.opened_date || ''}<br>
                                    ${item.solved_date ? `<strong>Solucionado em:</strong> ${item.solved_date}<br>` : ''}
                                    ${item.request_date ? `<strong>Solicitado em:</strong> ${item.request_date}<br>` : ''}
                                    ${commentDecoded ? `<strong>Comentário:</strong> ${commentDecoded}<br>` : ''}
                                </div>`;
                        });
                    }
                }

                if (popupContent !== '') {
                    const popup = document.createElement('div');
                    popup.innerHTML = popupContent;

                    popup.style.position = 'fixed';
                    popup.style.top = '10%';
                    popup.style.left = '50%';
                    popup.style.transform = 'translateX(-50%)';
                    popup.style.background = '#fff';
                    popup.style.border = '1px solid #ccc';
                    popup.style.padding = '20px';
                    popup.style.boxShadow = '0 4px 16px rgba(0,0,0,0.2)';
                    popup.style.borderRadius = '10px';
                    popup.style.zIndex = '9999';
                    popup.style.maxWidth = '500px';
                    popup.style.maxHeight = '70%';
                    popup.style.overflowY = 'auto';
                    popup.style.fontFamily = 'Arial, sans-serif';

                    const closeButton = document.createElement('button');
                    closeButton.innerText = 'Ver depois';
                    closeButton.style.display = 'block';
                    closeButton.style.margin = '20px auto 0';
                    closeButton.style.padding = '10px 20px';
                    closeButton.style.background = '#007bff';
                    closeButton.style.color = '#fff';
                    closeButton.style.border = 'none';
                    closeButton.style.borderRadius = '4px';
                    closeButton.style.cursor = 'pointer';

                    closeButton.addEventListener('click', () => {
                        popup.remove();
                    });

                    popup.appendChild(closeButton);
                    document.body.appendChild(popup);
                }
            }
        });

    // Função para decodificar entidades HTML
    function decodeHTMLEntities(text) {
        const txt = document.createElement('textarea');
        txt.innerHTML = text;
        return txt.value;
    }
});
