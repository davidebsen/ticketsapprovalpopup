
(function () {
  if (window.tapPopupLoaded) return;
  window.tapPopupLoaded = true;

  const container = document.createElement('div');
  container.id = 'tap-block-screen-popup-container';
  document.body.appendChild(container);

  const shadow = container.attachShadow({ mode: 'open' });

  const style = document.createElement('style');
  style.textContent = `
    .tap-block-screen-backdrop {
      position: fixed;
      z-index: 99999;
      left: 0; top: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .tap-block-screen-popup {
      background: #fff;
      border-radius: 10px;
      max-width: 500px;
      width: 92vw;
      min-width: 240px;
      padding: 32px 24px 24px 24px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.25);
      position: relative;
      font-family: Arial, sans-serif;
      box-sizing: border-box;
    }
    .tap-block-screen-close {
      position: absolute;
      top: 12px;
      right: 12px;
      background: #eee;
      border: none;
      border-radius: 50%;
      width: 28px; height: 28px;
      cursor: pointer;
      font-size: 18px;
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }
    .tap-block-screen-close:hover {
      background: #ccc;
    }
    .tap-block-screen-title {
      font-size: 22px;
      margin-bottom: 16px;
      color: #1a1a1a;
      font-weight: bold;
    }
    .tap-block-screen-body {
      font-size: 16px;
      color: #333;
      word-break: break-word;
    }
    @media (max-width: 600px) {
      .tap-block-screen-popup {
        max-width: 98vw;
        width: 98vw;
        padding: 16px 4vw 16px 4vw;
        font-size: 15px;
      }
      .tap-block-screen-title {
        font-size: 17px;
        margin-bottom: 10px;
      }
      .tap-block-screen-body {
        font-size: 14px;
      }
      .tap-block-screen-close {
        top: 8px;
        right: 8px;
        width: 24px; height: 24px;
        font-size: 16px;
      }
    }
  `;
  shadow.appendChild(style);

  const popup = document.createElement('div');
  popup.className = 'tap-block-screen-backdrop';
  popup.innerHTML = `
    <div class="tap-block-screen-popup">
      <button class="tap-block-screen-close" title="Fechar">&times;</button>
      <div class="tap-block-screen-title">Atenção!</div>
      <div class="tap-block-screen-body">
        Seu conteúdo do popup vai aqui.
      </div>
    </div>
  `;
  shadow.appendChild(popup);

  shadow.querySelector('.tap-block-screen-close').onclick = closePopup;
  shadow.querySelector('.tap-block-screen-backdrop').onclick = function (e) {
    if (e.target === popup) closePopup();
  };

  function closePopup() {
    container.remove();
    window.tapPopupLoaded = false;
  }

  window.openTapBlockScreenPopup = function (title, body) {
    shadow.querySelector('.tap-block-screen-title').textContent = title || 'Atenção!';
    shadow.querySelector('.tap-block-screen-body').innerHTML = body || '';
    container.style.display = 'block';
  };

})();
