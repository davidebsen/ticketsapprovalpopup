<?php
require_once dirname(__DIR__, 3) . '/inc/includes.php';
Session::checkLoginUser();
echo '<link rel="stylesheet" type="text/css" href="'.$CFG_GLPI['root_doc'].'/plugins/ticketsapprovalpopup/css/popup.css">';

global $DB, $CFG_GLPI;

$user_id = Session::getLoginUserID();
$tickets_approval = [];
$tickets_planned = [];

// Chamados aguardando aprovação
$query_approval = "
    SELECT t.id, t.name, t.date, t.entities_id, t.type,
           e.name AS entity_name,
           c.completename AS category_name,
           l.name AS location_name
    FROM glpi_tickets t
    LEFT JOIN glpi_entities e ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
    LEFT JOIN glpi_locations l ON l.id = t.locations_id
    INNER JOIN glpi_tickets_users tu ON tu.tickets_id = t.id
    WHERE t.status = 5
      AND tu.users_id = $user_id
      AND tu.type = 1
    ORDER BY t.date DESC
";

foreach ($DB->request($query_approval) as $row) {
    $tickets_approval[] = $row;
}

// Chamados planejados
$query_planned = "
    SELECT t.id, t.name, t.date, t.entities_id, t.type,
           e.name AS entity_name,
           c.completename AS category_name,
           l.name AS location_name,
           task.content AS task_description,
           task.begin AS task_begin,
           task.end AS task_end
    FROM glpi_tickets t
    LEFT JOIN glpi_tickettasks task ON task.tickets_id = t.id
    LEFT JOIN glpi_entities e ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
    LEFT JOIN glpi_locations l ON l.id = t.locations_id
    WHERE t.status = 3 AND t.users_id_recipient = $user_id
    ORDER BY t.date DESC
";
// Chamados pendentes de validação
$query_validation = "
    SELECT t.id, t.name, t.date, t.entities_id, t.type,
           e.name AS entity_name,
           c.completename AS category_name,
           l.name AS location_name,
           v.comment_submission AS validation_comment,
           v.submission_date AS validation_date
    FROM glpi_ticketvalidations v
    INNER JOIN glpi_tickets t ON t.id = v.tickets_id
    LEFT JOIN glpi_entities e ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
    LEFT JOIN glpi_locations l ON l.id = t.locations_id
    WHERE v.users_id_validate = $user_id AND v.status = 2
    ORDER BY v.submission_date DESC
";
foreach ($DB->request($query_planned) as $row) {
    $tickets_planned[] = $row;
}

if (empty($tickets_approval) && empty($tickets_planned)) {
    return;
}
// Chamados pendentes de validação
$query_validation = "
    SELECT t.id, t.name, t.date, t.entities_id, t.type,
           e.name AS entity_name,
           c.completename AS category_name,
           l.name AS location_name,
           v.comment_submission AS validation_comment,
           v.submission_date AS validation_date
    FROM glpi_ticketvalidations v
    INNER JOIN glpi_tickets t ON t.id = v.tickets_id
    LEFT JOIN glpi_entities e ON e.id = t.entities_id
    LEFT JOIN glpi_itilcategories c ON c.id = t.itilcategories_id
    LEFT JOIN glpi_locations l ON l.id = t.locations_id
    WHERE v.users_id_validate = $user_id AND v.status = 2
    ORDER BY v.submission_date DESC
";
foreach ($DB->request($query_validation) as $row) {
    $tickets_validation[] = $row;
}

function traduzirTipo($tipo) {
    $tipos = [
        1 => 'Incidente',
        2 => 'Requisição'
    ];
    return $tipos[$tipo] ?? 'Desconhecido';
}
?>



<script>
function esconderAvisoERedirecionar() {
    localStorage.setItem("avisosVistos", "1");
    const aviso = document.getElementById("block-screen");
    if (aviso) aviso.style.display = "none";
}
if (localStorage.getItem("avisosVistos") === "1") {
    document.addEventListener("DOMContentLoaded", () => {
        const aviso = document.getElementById("block-screen");
        if (aviso) aviso.remove();
    });
}
</script>

<div id="block-screen">
    <?php if (!empty($tickets_approval)) { ?>
        <h2>Chamados aguardando sua aprovação</h2>
        <div class="ticket-list">
            <?php foreach ($tickets_approval as $ticket) { ?>
                <div class="ticket-item">
                    <strong>🆔: <?php echo $ticket['id']; ?> - 
                        <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" >Abrir</a>
                    </strong><br>
                    📝 Título: <?php echo htmlspecialchars($ticket['name']); ?><br>
                    🕓 Abertura: <?php echo date('d/m/Y H:i', strtotime($ticket['date'])); ?><br>
                    🏢 Setor: <?php echo $ticket['entity_name'] ?: 'Não informada'; ?><br>
                    📋 Tipo: <?php echo traduzirTipo($ticket['type']); ?><br>
                    🗂️ Categoria: <?php echo $ticket['category_name'] ?: 'Não informada'; ?><br>
                    📌 Localização: <?php echo $ticket['location_name'] ?: 'Não informada'; ?><br>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($tickets_planned)) { ?>
        <h2>Chamados planejados</h2>
        <div class="ticket-list">
            <?php foreach ($tickets_planned as $ticket) { ?>
                <div class="ticket-item">
                    <strong>🆔: <?php echo $ticket['id']; ?> —
                        <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" >Abrir</a>
                    </strong><br>
                    📝 Título: <?php echo htmlspecialchars($ticket['name']); ?><br>
                    🕓 Abertura: <?php echo date('d/m/Y H:i', strtotime($ticket['date'])); ?><br>
                    🏢 Setor: <?php echo $ticket['entity_name'] ?: 'Não informada'; ?><br>
                    📋 Tipo: <?php echo traduzirTipo($ticket['type']); ?><br>
                    🗂️ Categoria: <?php echo $ticket['category_name'] ?: 'Não informada'; ?><br>
                    📌 Localização: <?php echo $ticket['location_name'] ?: 'Não informada'; ?><br>
                    <?php if (!empty($ticket['task_description'])) { ?>
                        📝 Tarefa: <?php echo strip_tags(html_entity_decode($ticket['task_description'])); ?><br>
                    <?php } ?>
                    <?php if (!empty($ticket['task_begin']) && !empty($ticket['task_end'])) { ?>
                        📅 Início: <?php echo date('d/m/Y H:i', strtotime($ticket['task_begin'])); ?> —
                        Fim: <?php echo date('d/m/Y H:i', strtotime($ticket['task_end'])); ?><br>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if (!empty($tickets_validation)) { ?>
        <h2>Chamados pendentes de validação</h2>
        <div class="ticket-list">
            <?php foreach ($tickets_validation as $ticket) { ?>
                <div class="ticket-item">
                    <strong>🆔: <?php echo $ticket['id']; ?> —
                        <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" >Abrir</a>
                    </strong><br>
                    📝 Título: <?php echo htmlspecialchars($ticket['name']); ?><br>
                    🕓 Abertura: <?php echo date('d/m/Y H:i', strtotime($ticket['date'])); ?><br>
                    🏢 Setor: <?php echo $ticket['entity_name'] ?: 'Não informada'; ?><br>
                    📋 Tipo: <?php echo traduzirTipo($ticket['type']); ?><br>
                    🗂️ Categoria: <?php echo $ticket['category_name'] ?: 'Não informada'; ?><br>
                    📌 Localização: <?php echo $ticket['location_name'] ?: 'Não informada'; ?><br>
                    📅 Data da Validação: <?php echo date('d/m/Y H:i', strtotime($ticket['validation_date'])); ?><br>
                    💬 Comentário: <?php echo strip_tags(html_entity_decode($ticket['validation_comment'])); ?><br>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <button id="ok-button" onclick="esconderAvisoERedirecionar()">Ver depois</button>
</div>