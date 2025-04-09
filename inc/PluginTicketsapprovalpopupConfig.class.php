<?php
class PluginTicketsapprovalpopupConfig extends CommonDBTM {

    public static function getTypeName($nb = 0) {
        return __('Tickets aprovação', 'ticketsapprovalpopup');
    }

    public function showForm($ID, array $options = []) {
        include_once(GLPI_ROOT . "/plugins/ticketsapprovalpopup/front/config.form.php");
        return true;
    }
}
