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

public static function install() {
    global $DB;
    $migration = new \Migration(130);

    if (!$DB->tableExists('glpi_plugin_ticketsapprovalpopup_config')) {
        $migration->addTable('glpi_plugin_ticketsapprovalpopup_config', [
            'id' => ['type'=>'int', 'primary'=>true, 'auto_increment'=>true, 'unsigned'=>true],
            'enable_popup' => ['type'=>'bool', 'default'=>1]
        ]);
    }
    $migration->executeMigration();
    if (!$DB->request('glpi_plugin_ticketsapprovalpopup_config')->numrows()) {
        $DB->insert('glpi_plugin_ticketsapprovalpopup_config', ['id'=>1,'enable_popup'=>1]);
    }
    return true;
}

