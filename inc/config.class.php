<?php
class PluginTicketsapprovalpopupConfig extends CommonDBTM {

    static $rightname = 'config';

    static function getTypeName($nb = 0) {
        return _n('Tickets approval popup configuration', 'Tickets approval popup configurations', $nb);
    }

    public static function isPopupEnabled() {
        global $DB;
        $table = 'glpi_plugin_ticketsapprovalpopup_config';

        // Create table if missing
        if (!$DB->tableExists($table)) {
            $DB->query("CREATE TABLE `$table` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `enable_popup` TINYINT(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            $DB->insert($table, ['id' => 1, 'enable_popup' => 1]);
        } elseif (!$DB->request($table, ['id' => 1])->numrows()) {
            $DB->insert($table, ['id' => 1, 'enable_popup' => 1]);
        }

        $row = $DB->request([
            'SELECT' => 'enable_popup',
            'FROM'   => $table,
            'WHERE'  => ['id' => 1]
        ])->current();

        return isset($row['enable_popup']) && (int)$row['enable_popup'] === 0;
    }
}
