<?php
include '../../../inc/includes.php';
Session::checkLoginUser();
global $DB, $CFG_GLPI;

$user_id = Session::getLoginUserID();
$tickets = [];

$query = "
    SELECT t.id, t.name, t.date
    FROM glpi_tickets t
    INNER JOIN glpi_tickets_users tu ON tu.tickets_id = t.id
    WHERE t.status = 5
      AND tu.users_id = $user_id
      AND tu.type = 1
    ORDER BY t.solvedate DESC
";

foreach ($DB->request($query) as $row) {
    $tickets[] = $row;
}

if (empty($tickets)) {
    return;
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
    justify-content: center;
    font-family: "Segoe UI", sans-serif;
    padding: 40px 20px;
}
#block-screen h2 {
    color: #206bc4;
    font-size: 26px;
    margin-bottom: 15px;
    text-align: center;
}
.alert-icon {
    margin-bottom: 25px;
}
.alert-icon svg {
    width: 48px;
    height: 48px;
    fill: #f0ad4e;
}
.ticket-list {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
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
    color: white;
    text-decoration: none;
    background-color: #007bff;
    padding: 6px 12px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 5px;
}
.ticket-item a:hover {
    background-color: #0056b3;
}
#ok-button {
    margin-top: 10px;
    padding: 12px 30px;
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
if (localStorage.getItem("avisosVistos") === "1") {
    // Aviso já visto, não exibe novamente
    document.addEventListener("DOMContentLoaded", () => {
        const aviso = document.getElementById("block-screen");
        if (aviso) aviso.remove();
    });
}

function esconderAviso() {
    localStorage.setItem("avisosVistos", "1");
    document.getElementById("block-screen").style.display = "none";
}
</script>

<div id="block-screen">
    <h2>Chamados solucionados aguardando sua aprovação</h2>


    <div class="ticket-list">
        <?php foreach ($tickets as $ticket) { ?>
            <div class="ticket-item">
                <strong>#<?php echo $ticket['id']; ?> - <?php echo htmlspecialchars($ticket['name']); ?></strong><br>
                🕓 Abertura: <?php echo date('d/m/Y H:i', strtotime($ticket['date'])); ?><br>
                <a href="<?php echo $CFG_GLPI['root_doc']; ?>/front/ticket.form.php?id=<?php echo $ticket['id']; ?>" target="_blank">Ver chamado</a>
            </div>
        <?php } ?>
    </div>

    <button id="ok-button" onclick="esconderAviso()">OK, entendi</button>
</div>
