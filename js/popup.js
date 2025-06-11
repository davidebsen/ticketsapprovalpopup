// popup.js — só estrutura e comportamento, sem estilização inline

document.addEventListener('DOMContentLoaded', () => {
  // Injeta o CSS se o hook falhar
  if (!document.querySelector('link[href*="ticketsapprovalpopup/css/popup.css"]')) {
    const link = document.createElement('link');
    link.rel  = 'stylesheet';
    link.href = GLPI_URL + '/plugins/ticketsapprovalpopup/css/popup.css';
    document.head.appendChild(link);
  }

  fetch(GLPI_URL + '/plugins/ticketsapprovalpopup/front/popup.php')
    .then(response => {
      if (!response.ok) {
        throw new Error('Falha ao carregar dados do popup: ' + response.status);
      }
      return response.json();
    })
    .then(data => {
      if (!Array.isArray(data) || data.length === 0) return;

      // Cria container principal
      const popup = document.createElement('div');
      popup.classList.add('ticketsapproval-popup');

      const groups = {
        validacao:   'Chamados aguardando sua aprovação',
        planejado:   'Chamados planejados',
        solucionado: 'Chamados solucionados'
      };

      // Monta conteúdo
      Object.entries(groups).forEach(([key, title]) => {
        const items = data.filter(i => i.consulta === key);
        if (!items.length) return;

        const h2 = document.createElement('h2');
        h2.textContent = title;
        popup.appendChild(h2);

        items.forEach(item => {
          const entry = document.createElement('div');
          entry.classList.add('ticketsapproval-entry');
          entry.innerHTML = `
            <strong>ID:</strong> ${item.id}
            <a href="${item.link}" target="_blank">Abrir</a><br>
            <strong>Título:</strong> ${item.title}<br>
            ${item.opened_date ? `<strong>Abertura:</strong> ${item.opened_date}<br>` : ''}
            ${item.solved_date ? `<strong>Solucionado em:</strong> ${item.solved_date}<br>` : ''}
            ${item.request_date ? `<strong>Solicitado em:</strong> ${item.request_date}<br>` : ''}
            ${item.comment ? `<strong>Comentário:</strong> ${decodeHTMLEntities(item.comment)}<br>` : ''}
          `;
          popup.appendChild(entry);
        });
      });

      // Botão de fechar
      const btn = document.createElement('button');
      btn.classList.add('ticketsapproval-close-btn');
      btn.textContent = 'Ver depois';
      btn.addEventListener('click', () => popup.remove());
      popup.appendChild(btn);

      document.body.appendChild(popup);
    })
    .catch(err => console.error(err));

  function decodeHTMLEntities(text) {
    const txt = document.createElement('textarea');
    txt.innerHTML = text;
    return txt.value;
  }
});
