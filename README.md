# TicketsApprovalPopup — Plugin GLPI

Plugin desenvolvido para exibir automaticamente uma **notificação pop-up após o login** com a lista de chamados solucionados aguardando aprovação, de forma personalizada por perfil, usuário e tipo de ticket.

---

## ✨ Funcionalidades

- 🛎️ Pop-up exibido automaticamente ao logar com tickets pendentes de aprovação.
- 👤 Filtro inteligente por usuário logado (requerente, técnico, observador etc.).
- 🎛️ Configuração avançada:
  - Por tipo de ticket: solucionado, rejeitado, atualizado, planejado, novo...
  - Por perfil, usuário, entidade ou grupo.
- 🔒 Acesso à configuração exclusivo para perfis com permissão de super-administrador.
- 📄 Tela de bloqueio para aprovação antes de acessar o sistema.
- 🧠 Todas as regras são salvas em **formato JSON**, sem usar tabelas extras no banco.

---

## 🧑‍💼 Público-alvo

Ambientes GLPI onde os usuários precisam aprovar chamados antes do encerramento formal, com regras específicas por departamento, grupo ou função.

---

## 🚀 Instalação

1. Acesse a pasta `plugins/` no diretório da sua instalação do GLPI.
2. Extraia o conteúdo deste plugin dentro da pasta `ticketsapprovalpopup/`.
3. Vá em **GLPI > Configurar > Plugins** e clique em **Instalar** e depois **Ativar**.
4. Acesse o menu de configuração do plugin para definir os filtros e perfis.

---


## 📇 Créditos

Desenvolvido por:

**David Ebsen**  
GitHub: [https://github.com/davidebsen](https://github.com/davidebsen)  
LinkedIn: [https://www.linkedin.com/in/david-ebsen/](https://www.linkedin.com/in/david-ebsen/)

---

Distribuído sob licença **GPLv3+**
