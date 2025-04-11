<?php
include '../../../inc/includes.php';

Session::checkLoginUser();
header('Content-Type: application/json');
global $DB, $CFG_GLPI;

$user_id = Session::getLoginUserID();
$tickets = [];

// Buscar tickets solucionados onde o usuário logado é o requerente
$query = "
    SELECT t.id, t.name, t.date, t.closedate, u.name AS assigned_to
    FROM glpi_tickets t
    LEFT JOIN glpi_users u ON t.users_id_assign = u.id
    WHERE t.status = 5
      AND t.users_id_recipient = $user_id
";

foreach ($DB->request($query) as $row) {
    $tickets[] = [
        'id'          => $row['id'],
        'title'       => $row['name'],
        'open_date'   => date('d/m/Y H:i', strtotime($row['date'])),
        'solve_date'  => $row['closedate'] ? date('d/m/Y H:i', strtotime($row['closedate'])) : 'Não solucionado',
        'assigned_to' => $row['assigned_to'] ?? 'Não atribuído',
        'link'        => $CFG_GLPI['root_doc'] . "/front/ticket.form.php?id=" . $row['id']
    ];
}

echo json_encode($tickets);
