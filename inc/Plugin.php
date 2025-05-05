<?php
namespace PluginTicketsapprovalpopup;

class Plugin {
    public static function install() {
        return true;
    }

    public static function uninstall() {
        file_put_contents(GLPI_LOG_DIR . "/plugin_ticketsapprovalpopup_uninstall.log", "Uninstall executed\n", FILE_APPEND);

        global $DB;

        $tables = [
            "glpi_plugin_ticketsapprovalpopup_config",
            "glpi_plugin_ticketsapprovalpopup_seen"
        ];

        foreach ($tables as $table) {
            if ($DB->tableExists($table)) {
                $DB->query("DROP TABLE `$table`");
            }
        }

        return true;
    }
}
