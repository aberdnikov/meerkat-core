<?php

class Kohana extends Kohana_Core {

    public static function modules(array $modules = NULL) {
        $ret = parent::modules($modules);
        $paths = Kohana::$_paths;
        if (defined('DOMAINPATH')) {
            array_unshift($paths, DOMAINPATH);
        }
        Kohana::$_paths = $paths;
        if (file_exists('install' . EXT)) {
            include 'install' . EXT;
            exit;
        }
        return $ret;
    }

}