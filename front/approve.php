<?php
include '../../../inc/includes.php';
Session::checkLoginUser();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $user_id = Session::getLoginUserID();

    // Aqui você faria a lógica real de gravação no banco (tabela de log de aprovação ou alteração de status)

    echo json_encode([
        'status' => 'success',
        'message' => "Chamado #$ticket_id aprovado com sucesso pelo usuário $user_id"
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Requisição inválida']);
