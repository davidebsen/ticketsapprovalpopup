<?php

function plugin_init_ticketsapprovalpopup() {
    Plugin::registerClass('PluginTicketsapprovalpopupConfig', [
        'addtabon' => ['Entity']
    ]);
    $PLUGIN_HOOKS['config_page']['ticketsapprovalpopup'] = 'front/config.form.php';
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['add_javascript']['ticketsapprovalpopup'] = ['js/popup.js'];
$PLUGIN_HOOKS['add_javascript']['ticketsapprovalpopup']['central'] = ['js/popup.js'];
    $PLUGIN_HOOKS['menu_toadd']['ticketsapprovalpopup'] = ['menu' => 'front/config.form.php'];
}

function plugin_version_ticketsapprovalpopup() {

    return [
        'name'           => __('Tickets aprovação', 'ticketsapprovalpopup'),
        'version'        => '1.0.3',
        'license'        => 'GPLv3+',
        'author'         => 'David Ebsen',
        'homepage'       => 'https://github.com/davidebsen/ticketsapprovalpopup',
        'minGlpiVersion' => '10.0.0',
        'requirements'   => ['glpi' => ['min' => '10.0.0', 'max' => '11.0.0']]
    ];
}

function plugin_ticketsapprovalpopup_check_prerequisites() {
    return true;
}

function plugin_ticketsapprovalpopup_check_config() {
    return true;
}

function plugin_ticketsapprovalpopup_install() {
    return true;
}

function plugin_ticketsapprovalpopup_uninstall() {
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


global $PLUGIN_HOOKS;
$PLUGIN_HOOKS['csrf_compliant']['ticketsapprovalpopup'] = true;


$PLUGIN_HOOKS['display_central']['ticketsapprovalpopup'] = 'plugin_ticketsapprovalpopup_displaycentral';

function plugin_ticketsapprovalpopup_displaycentral() {
    echo "<script src='plugins/ticketsapprovalpopup/js/popup.js'></script>";
}


$PLUGIN_HOOKS['display_central']['ticketsapprovalpopup'] = 'plugin_ticketsapprovalpopup_displaycentral_force';

function plugin_ticketsapprovalpopup_displaycentral_force() {
    global $CFG_GLPI;
    include(GLPI_ROOT . "/plugins/ticketsapprovalpopup/front/forcecheck.php");
}
