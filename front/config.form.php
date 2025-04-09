<?php
include '../../../inc/includes.php';
Session::checkRight("config", READ);

$configfile = GLPI_PLUGIN_DOC_DIR . '/ticketsapprovalpopup/config.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents($configfile, json_encode($_POST['statuses']));
    Html::displayMessageAfterRedirect('Configurações salvas com sucesso');
}

$statuses = file_exists($configfile) ? json_decode(file_get_contents($configfile), true) : [];

$status_options = [
    'not_solved' => 'Não solucionado',
    'planned'    => 'Planejado',
    'updated'    => 'Atualização de comentário'
];

echo '<form method="post">';
echo '<h2>Quais status devem gerar popups?</h2>';
foreach ($status_options as $key => $label) {
    $checked = in_array($key, $statuses) ? 'checked' : '';
    echo "<label><input type='checkbox' name='statuses[]' value='$key' $checked> $label</label><br>";
}
echo '<br><button type="submit" class="btn btn-primary">Salvar</button>';
echo '</form>';
