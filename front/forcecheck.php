<?php
include '../../../inc/includes.php';
Session::checkLoginUser();
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

foreach ($DB->request($query_planned) as $row) {
    $tickets_planned[] = $row;
}

if (empty($tickets_approval) && empty($tickets_planned)) {
    return;
}

function traduzirTipo($tipo) {
    $tipos = [
        1 => 'Incidente',
        2 => 'Requisição'
    ];
    return $tipos[$tipo] ?? 'Desconhecido';
}
?>

<style>
#block-screen {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background-color: rgba(245, 250, 255, 0.85);
    color: #003366;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    font-family: "Segoe UI", sans-serif;
    padding: 40px 20px;
    overflow-y: auto;
}
#block-screen h2 {
    color: #206bc4;
    font-size: 24px;
    margin-bottom: 10px;
    text-align: center;
}
.ticket-list {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    padding: 20px;
    width: 90%;
    max-width: 700px;
    border-left: 5px solid #206bc4;
    text-align: left;
}
.ticket-item {
    margin-bottom: 20px;
}
.ticket-item a {
    font-size: 12px;
    color: white;
    text-decoration: none;
    background-color: #007bff;
    padding: 4px 10px;
    border-radius: 4px;
    display: inline-block;
    margin-left: 6px;
}
.ticket-item a:hover {
    background-color: #0056b3;
}
#ok-button {
    margin-top: 20px;
    padding: 10px 30px;
    background-color: #206bc4;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
}
#ok-button:hover {
    background-color: #165a9c;
}
</style>

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
                    <strong>🆔: <?php echo $ticket['id']; ?> — 
                        <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" target="_blank">Abrir</a>
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
                    <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" target="_blank">Abrir</a>
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

    <button id="ok-button" onclick="esconderAvisoERedirecionar()">Ver depois</button>
</div>
