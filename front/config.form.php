<?php
// Arquivo: config.php (ou outro nome que você esteja usando para a página de configuração do plugin)
// Neste exemplo, vamos assumir que o nome do arquivo é config.php dentro de
//   /plugins/ticketsapprovalpopup/front/config.php
// Ajuste o include do caminho conforme a sua estrutura real.

include('../../inc/includes.php'); // Caminho relativo até o includes do GLPI

// Verifica permissão de administrador ou perfil adequado, se necessário
Session::checkRight('config', READ); // Ajuste se você tiver outra verificação de permissão

if (isset($_POST['update'])) {
    // Apenas a chave 'enable_popup'
    $config = [
        'enable_popup' => isset($_POST['enable_popup']) ? 1 : 0
    ];
    Config::setConfigurationValues('ticketsapprovalpopup', $config);
    Html::displayMessageAfterRedirect(__('Configurações salvas com sucesso', 'ticketsapprovalpopup'), true);
}

// Carrega o valor atual da configuração
$config = Config::getConfigurationValues('ticketsapprovalpopup');
$enable_popup = (isset($config['enable_popup']) && $config['enable_popup'] == 1) ? 1 : 0;

// Cabeçalho GLPI: altera o título conforme a necessidade
Html::header(
    __('Configuração do Tickets Approval Popup', 'ticketsapprovalpopup'),
    $_SERVER['PHP_SELF'],
    "plugins",
    "ticketsapprovalpopup"
);

// Início do formulário
echo "<form method='post' action=''>";

echo "<table class='tab_cadre'>";

// Título de seção
echo "<tr><th colspan='2'>" . __('Preferências', 'ticketsapprovalpopup') . "</th></tr>";

// Linha única: Checkbox para habilitar/desabilitar o popup
echo "<tr class='tab_bg_1'>";
echo "  <td width='50%'>" . __('Habilitar popup', 'ticketsapprovalpopup') . "</td>";
echo "  <td>";
Dropdown::showYesNo("enable_popup", $enable_popup);
echo "  </td>";
echo "</tr>";

// Botão de salvar
echo "<tr class='tab_bg_1'><td class='center' colspan='2'>";
echo "  <input type='submit' name='update' class='submit' value='" . _sx('button', 'Salvar') . "'>";
echo "</td></tr>";

echo "</table>";
echo "</form>";

// Rodapé GLPI
Html::footer();
?>
