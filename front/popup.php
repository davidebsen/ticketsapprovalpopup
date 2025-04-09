<?php
include '../../../inc/includes.php';

Session::checkLoginUser();
header('Content-Type: application/json');
global $DB;

$user_id = Session::getLoginUserID();
$tickets = [];

// Buscar tickets solucionados onde o usuário logado é o requerente
$query = "
    SELECT id, name, date, closedate
    FROM glpi_tickets
    WHERE status = 5
      AND users_id_recipient = $user_id
";

foreach ($DB->request($query) as $row) {
    $tickets[] = [
        'id'         => $row['id'],
        'name'       => $row['name'],
        'reason'     => 'Solucionado - Aguardando sua aprovação',
        'date'       => date('d/m/Y H:i', strtotime($row['date'])),
        'closedate'  => $row['closedate'] ? date('d/m/Y H:i', strtotime($row['closedate'])) : 'Não encerrado',
        'link'       => $CFG_GLPI['root_doc'] . "/front/ticket.form.php?id=" . $row['id']
    ];
}

echo json_encode($tickets);
