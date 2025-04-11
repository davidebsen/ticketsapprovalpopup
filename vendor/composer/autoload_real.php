<?php
class ComposerAutoloaderInit {
    public static function getLoader() {
        spl_autoload_register(function ($class) {
            if (strpos($class, 'PluginTicketsapprovalpopup\\') === 0) {
                $classPath = __DIR__ . '/../../inc/' . str_replace('\\', '/', substr($class, strlen('PluginTicketsapprovalpopup\\'))) . '.php';
                if (file_exists($classPath)) {
                    require_once $classPath;
                }
            }
        });
        return true;
    }
}
return ComposerAutoloaderInit::getLoader();
