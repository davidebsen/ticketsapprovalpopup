<?php
define('PLUGIN_TICKETSAPPROVALPOPUP_VERSION', '1.0.4');
define('PLUGIN_TICKETSAPPROVALPOPUP_GLPIMIN', '10.0.0');
define('PLUGIN_TICKETSAPPROVALPOPUP_GLPIMAX', '11.0.0');

function plugin_version_ticketsapprovalpopup() {
    return [
        'name'           => "Tickets Approval Popup",
        'version'        => PLUGIN_TICKETSAPPROVALPOPUP_VERSION,
        'author'         => 'Your Name',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/yourrepo',
        'minGlpiVersion' => PLUGIN_TICKETSAPPROVALPOPUP_GLPIMIN,
        'maxGlpiVersion' => PLUGIN_TICKETSAPPROVALPOPUP_GLPIMAX
    ];
}

function plugin_init_ticketsapprovalpopup() {
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant']['ticketsapprovalpopup'] = true;
    $PLUGIN_HOOKS['addCss']['ticketsapprovalpopup'] = 'css/popup.css';
    $PLUGIN_HOOKS['addJs']['ticketsapprovalpopup'] = 'js/popup.js';
}
