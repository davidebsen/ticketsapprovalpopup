<?php
/**
 * Config page to enable / disable the popup
 */
include '../../../inc/includes.php';

Session::checkRight('config', READ);

global $DB;
$table = 'glpi_plugin_ticketsapprovalpopup_config';

// ensure table/row exists
if (!$DB->tableExists($table)) {
    $DB->query("CREATE TABLE `$table` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `enable_popup` TINYINT(1) NOT NULL DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $DB->insert($table, ['id' => 1, 'enable_popup' => 1]);
} elseif (!$DB->request($table, ['id' => 1])->numrows()) {
    $DB->insert($table, ['id' => 1, 'enable_popup' => 1]);
}

// save action
if (isset($_POST['save'])) {
    Session::checkRight('config', UPDATE);
    $enable = isset($_POST['enable_popup']) ? intval($_POST['enable_popup']) : 0;
    $DB->update($table, ['enable_popup' => $enable], ['id' => 1]);
    Session::addMessageAfterRedirect(__('Configuration saved'), true, INFO);
    Html::redirect($_SERVER['REQUEST_URI']);
}

// current value
$row = $DB->request([
    'SELECT' => 'enable_popup',
    'FROM'   => $table,
    'WHERE'  => ['id' => 1]
])->current();
$enable_popup = $row ? (int)$row['enable_popup'] : 1;

Html::header(__('Tickets approval popup'), '', 'plugins', 'ticketsapprovalpopup', 'config');
echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
echo '<table class="tab_cadre_fixe">';
echo '<tr class="tab_bg_1"><th colspan="2">'.__('Preferences').'</th></tr>';
echo '<tr class="tab_bg_1"><td>'.__('Enable popup').'</td><td>';
Dropdown::showYesNo('enable_popup', $enable_popup);
echo '</td></tr>';
echo '<tr class="tab_bg_2"><td colspan="2" class="center">';
echo '<input type="submit" class="submit" name="save" value="'.__('Save').'">';
echo '</td></tr></table>';
Html::closeForm();
Html::footer();
