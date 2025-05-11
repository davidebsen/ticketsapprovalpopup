<?php
class PluginTicketsapprovalpopupConfig {
    static function getConfig() {
        global $DB;
        if (!$DB->tableExists('glpi_plugin_ticketsapprovalpopup_config')) {
            return ['enable_popup' => 0];
        }
        $result = $DB->request(['SELECT' => 'enable_popup', 'FROM' => 'glpi_plugin_ticketsapprovalpopup_config', 'WHERE' => ['id' => 1]]);
        return $result->current() ?: ['enable_popup' => 0];
    }

    static function isPopupEnabled() {
        $conf = self::getConfig();
        return isset($conf['enable_popup']) && (int)$conf['enable_popup'] === 1;
    }
}