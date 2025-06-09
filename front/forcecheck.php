<?php

require_once dirname(__DIR__, 3) . '/inc/includes.php';
Session::checkLoginUser();

global $DB, $CFG_GLPI;
$user_id = Session::getLoginUserID();
echo '<link rel="stylesheet" type="text/css" href="'
     . $CFG_GLPI['root_doc']
     . '/plugins/ticketsapprovalpopup/css/popup.css">';
function traduzirTipo(int $tipo): string {
    return [
        1 => 'Incidente',
        2 => 'Requisição'
    ][$tipo] ?? 'Desconhecido';
}
$tickets_approval = [];
$sql1 = "
    SELECT 
        t.id,
        t.name,
        t.date,
        f.content,
        e.name AS entity_name,
        t.type,
        tc.name AS category_name,
        l.name AS location_name
    FROM glpi_tickets t
    JOIN glpi_itilfollowups f 
      ON f.items_id = t.id
     AND f.itemtype = 'Ticket'
    JOIN glpi_tickets_users tech 
      ON tech.tickets_id = t.id
    JOIN glpi_tickets_users req  
      ON req.tickets_id = t.id 
     AND req.type = 1
   LEFT JOIN glpi_entities e 
      ON e.id = t.entities_id
   LEFT JOIN glpi_itilcategories tc 
      ON tc.id = t.itilcategories_id
   LEFT JOIN glpi_locations l 
      ON l.id = t.locations_id
   WHERE tech.users_id = $user_id
     AND tech.type IN (2, 6)
     AND f.users_id = req.users_id
     AND f.users_id != $user_id
     AND t.is_deleted = 0
     AND f.date >= SUBTIME(NOW(), '15:15:00')
   ORDER BY f.date DESC
";
foreach ($DB->request($sql1) as $row) {
    $tickets_approval[] = [
        'id'            => $row['id'],
        'name'          => $row['name'],
        'date'          => $row['date'],
        'content'       => $row['content'],
        'entity_name'   => $row['entity_name'],
        'type'          => $row['type'],
        'category_name' => $row['category_name'],
        'location_name' => $row['location_name']
    ];
}
$tickets_pending = [];
$sql2 = "
    SELECT 
        t.id,
        t.name,
        t.date,
        e.name AS entity_name,
        t.type,
        tc.name AS category_name,
        l.name AS location_name
    FROM glpi_tickets t
    LEFT JOIN glpi_entities e 
      ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories tc 
      ON tc.id = t.itilcategories_id
    LEFT JOIN glpi_locations l 
      ON l.id = t.locations_id
    INNER JOIN glpi_tickets_users tu 
      ON tu.tickets_id = t.id
   WHERE t.status = 5
     AND tu.users_id = $user_id
     AND tu.type = 1
     AND t.is_deleted = 0
   ORDER BY t.date DESC
";
foreach ($DB->request($sql2) as $row) {
    $tickets_pending[] = $row;
}
$tickets_planned = [];
$sql3 = "
    SELECT 
        t.id,
        t.name,
        t.date,
        e.name AS entity_name,
        t.type,
        tc.name AS category_name,
        l.name AS location_name,
        task.content AS task_description,
        task.begin   AS task_begin,
        task.end     AS task_end
    FROM glpi_tickets t
    LEFT JOIN glpi_tickettasks task 
      ON task.tickets_id = t.id
    LEFT JOIN glpi_entities e 
      ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories tc 
      ON tc.id = t.itilcategories_id
    LEFT JOIN glpi_locations l 
      ON l.id = t.locations_id
   WHERE t.status = 3
     AND t.users_id_recipient = $user_id
     AND t.is_deleted = 0
   ORDER BY t.date DESC
";
foreach ($DB->request($sql3) as $row) {
    $tickets_planned[] = $row;
}
$tickets_validation = [];
$sql4 = "
    SELECT 
        t.id,
        t.name,
        t.date,
        e.name AS entity_name,
        t.type,
        tc.name AS category_name,
        l.name AS location_name,
        v.comment_submission AS validation_comment,
        v.submission_date   AS validation_date
    FROM glpi_ticketvalidations v
    INNER JOIN glpi_tickets t 
      ON t.id = v.tickets_id
    LEFT JOIN glpi_entities e 
      ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories tc 
      ON tc.id = t.itilcategories_id
    LEFT JOIN glpi_locations l 
      ON l.id = t.locations_id
   WHERE v.users_id_validate = $user_id
     AND v.status = 2
     AND t.is_deleted = 0
   ORDER BY v.submission_date DESC
";
foreach ($DB->request($sql4) as $row) {
    $tickets_validation[] = $row;
}
if (
    empty($tickets_approval)
    && empty($tickets_pending)
    && empty($tickets_planned)
    && empty($tickets_validation)
) {
    die();
}
?>
<div id="block-screen">
  <div class="container-popup">
    <?php if (!empty($tickets_approval)): ?>
      <h2 class="subtitulo-secao text-respostas" data-icon="💬">
        COMENTÁRIO CHAMADOS
        <span class="badge badge-pill badge-info"><?= count($tickets_approval) ?></span>
      </h2>
      <div class="row justify-content-center mb-xs-1px">
        <?php foreach ($tickets_approval as $ticket): ?>
          <div class="col-md-6 col-lg-4 mb-1px d-flex">
            <a href="<?= $CFG_GLPI['root_doc'] ?>/front/ticket.form.php?id=<?= $ticket['id'] ?>"
               class="card-chamado card-link card-chamado-info flex-fill"
               title="Clique para abrir">
              <div class="card-chamado-header">
                <span class="ticket-title">
                  #<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['name']) ?>
                </span>
                <span class="icon-open">🡪</span>
              </div>
              <div class="card-chamado-body">
                <ul class="list-unstyled">
                  <?php if (trim($ticket['date']) !== ''): ?>
                    <li>
                      <span class="icon">🕓</span>
                      <strong>Data:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['date'])) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['content']) !== ''): ?>
                    <li>
                      <span class="icon">💬</span>
                      <strong>Resposta:</strong>&nbsp;<?= nl2br(html_entity_decode(strip_tags($ticket['content']))) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['entity_name']) !== ''): ?>
                    <li>
                      <span class="icon">🏢</span>
                      <strong>Setor:</strong>&nbsp;<?= htmlspecialchars($ticket['entity_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['type'])): ?>
                    <li>
                      <span class="icon">📋</span>
                      <strong>Tipo:</strong>&nbsp;<?= traduzirTipo($ticket['type']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['category_name']) !== ''): ?>
                    <li>
                      <span class="icon">🗂️</span>
                      <strong>Categoria:</strong>&nbsp;<?= htmlspecialchars($ticket['category_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['location_name']) !== ''): ?>
                    <li>
                      <span class="icon">📌</span>
                      <strong>Localização:</strong>&nbsp;<?= htmlspecialchars($ticket['location_name']) ?>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($tickets_pending)): ?>
      <h2 class="subtitulo-secao text-aprovacao" data-icon="✔️">
        AGUARDANDO SUA APROVAÇÃO
        <span class="badge badge-pill badge-primary"><?= count($tickets_pending) ?></span>
      </h2>
      <div class="row justify-content-center mb-xs-1px">
        <?php foreach ($tickets_pending as $ticket): ?>
          <div class="col-md-6 col-lg-4 mb-1px d-flex">
            <a href="<?= $CFG_GLPI['root_doc'] ?>/front/ticket.form.php?id=<?= $ticket['id'] ?>"
               class="card-chamado card-link card-chamado-primary flex-fill"
               title="Clique para abrir">
              <div class="card-chamado-header">
                <span class="ticket-title">
                  #<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['name']) ?>
                </span>
                <span class="icon-open">🡪</span>
              </div>
              <div class="card-chamado-body">
                <ul class="list-unstyled">
                  <?php if (trim($ticket['date']) !== ''): ?>
                    <li>
                      <span class="icon">🕓</span>
                      <strong>Abertura:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['date'])) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['entity_name']) !== ''): ?>
                    <li>
                      <span class="icon">🏢</span>
                      <strong>Setor:</strong>&nbsp;<?= htmlspecialchars($ticket['entity_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['type'])): ?>
                    <li>
                      <span class="icon">📋</span>
                      <strong>Tipo:</strong>&nbsp;<?= traduzirTipo($ticket['type']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['category_name']) !== ''): ?>
                    <li>
                      <span class="icon">🗂️</span>
                      <strong>Categoria:</strong>&nbsp;<?= htmlspecialchars($ticket['category_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['location_name']) !== ''): ?>
                    <li>
                      <span class="icon">📌</span>
                      <strong>Localização:</strong>&nbsp;<?= htmlspecialchars($ticket['location_name']) ?>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($tickets_planned)): ?>
      <h2 class="subtitulo-secao text-planejado" data-icon="📅">
        EM PLANEJAMENTO
        <span class="badge badge-pill badge-success"><?= count($tickets_planned) ?></span>
      </h2>
      <div class="row justify-content-center mb-xs-1px">
        <?php foreach ($tickets_planned as $ticket): ?>
          <div class="col-md-6 col-lg-4 mb-1px d-flex">
            <a href="<?= $CFG_GLPI['root_doc'] ?>/front/ticket.form.php?id=<?= $ticket['id'] ?>"
               class="card-chamado card-link card-chamado-success flex-fill"
               title="Clique para abrir">
              <div class="card-chamado-header">
                <span class="ticket-title">
                  #<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['name']) ?>
                </span>
                <span class="icon-open">🡪</span>
              </div>
              <div class="card-chamado-body">
                <ul class="list-unstyled">
                  <?php if (trim($ticket['date']) !== ''): ?>
                    <li>
                      <span class="icon">🕓</span>
                      <strong>Abertura:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['date'])) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['entity_name']) !== ''): ?>
                    <li>
                      <span class="icon">🏢</span>
                      <strong>Setor:</strong>&nbsp;<?= htmlspecialchars($ticket['entity_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['type'])): ?>
                    <li>
                      <span class="icon">📋</span>
                      <strong>Tipo:</strong>&nbsp;<?= traduzirTipo($ticket['type']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['category_name']) !== ''): ?>
                    <li>
                      <span class="icon">🗂️</span>
                      <strong>Categoria:</strong>&nbsp;<?= htmlspecialchars($ticket['category_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['location_name']) !== ''): ?>
                    <li>
                      <span class="icon">📌</span>
                      <strong>Localização:</strong>&nbsp;<?= htmlspecialchars($ticket['location_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['task_description']) !== ''): ?>
                    <li>
                      <span class="icon">🖋</span>
                      <strong>Tarefa:</strong>&nbsp;<?= nl2br(html_entity_decode(strip_tags($ticket['task_description']))) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['task_begin']) && !empty($ticket['task_end'])): ?>
                    <li>
                      <span class="icon">🗓</span>
                      <strong>Intervalo:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['task_begin'])) ?> — <?= date('d/m/Y H:i', strtotime($ticket['task_end'])) ?>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($tickets_validation)): ?>
      <h2 class="subtitulo-secao text-validacao" data-icon="🛡️">
        AGUARDANDO VALIDAÇÃO
        <span class="badge badge-pill badge-warning"><?= count($tickets_validation) ?></span>
      </h2>
      <div class="row justify-content-center mb-xs-1px">
        <?php foreach ($tickets_validation as $ticket): ?>
          <div class="col-md-6 col-lg-4 mb-1px d-flex">
            <a href="<?= $CFG_GLPI['root_doc'] ?>/front/ticket.form.php?id=<?= $ticket['id'] ?>"
               class="card-chamado card-link card-chamado-warning flex-fill"
               title="Clique para abrir">
              <div class="card-chamado-header">
                <span class="ticket-title">
                  #<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['name']) ?>
                </span>
                <span class="icon-open">🡪</span>
              </div>
              <div class="card-chamado-body">
                <ul class="list-unstyled">
                  <?php if (trim($ticket['date']) !== ''): ?>
                    <li>
                      <span class="icon">🕓</span>
                      <strong>Abertura:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['date'])) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['entity_name']) !== ''): ?>
                    <li>
                      <span class="icon">🏢</span>
                      <strong>Setor:</strong>&nbsp;<?= htmlspecialchars($ticket['entity_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['type'])): ?>
                    <li>
                      <span class="icon">📋</span>
                      <strong>Tipo:</strong>&nbsp;<?= traduzirTipo($ticket['type']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['category_name']) !== ''): ?>
                    <li>
                      <span class="icon">🗂️</span>
                      <strong>Categoria:</strong>&nbsp;<?= htmlspecialchars($ticket['category_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['location_name']) !== ''): ?>
                    <li>
                      <span class="icon">📌</span>
                      <strong>Localização:</strong>&nbsp;<?= htmlspecialchars($ticket['location_name']) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (!empty($ticket['validation_date'])): ?>
                    <li>
                      <span class="icon">📅</span>
                      <strong>Validação:</strong>&nbsp;<?= date('d/m/Y H:i', strtotime($ticket['validation_date'])) ?>
                    </li>
                  <?php endif; ?>

                  <?php if (trim($ticket['validation_comment']) !== ''): ?>
                    <li>
                      <span class="icon">💬</span>
                      <strong>Comentário:</strong>&nbsp;<?= nl2br(html_entity_decode(strip_tags($ticket['validation_comment']))) ?>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="text-center mb-2">
      <button id="ok-button" class="btn-ver-depois" onclick="esconderAvisoERedirecionar()">
        Ver depois
      </button>
    </div>

  </div>
</div>

<script>
  if (localStorage.getItem("avisosVistos") === "1") {
    document.addEventListener("DOMContentLoaded", () => {
      const aviso = document.getElementById("block-screen");
      if (aviso) aviso.remove();
    });
  }
  document.addEventListener("DOMContentLoaded", function() {
    const block = document.getElementById("block-screen");
    if (block && block.parentNode !== document.body) {
      document.body.appendChild(block);
    }
  });
  function esconderAvisoERedirecionar() {
    localStorage.setItem("avisosVistos", "1");
    const aviso = document.getElementById("block-screen");
    if (aviso) aviso.remove();
  }
</script>
