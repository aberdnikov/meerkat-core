<?php

namespace Meerkat\Core;

/**
 * Управление темами сайта
 */
class Theme {

    static protected $instance;
    //тема по умолчанию
    static protected $default = 'default';
    //текущая тема
    protected $theme;

    /**
     * 
     * @return \Meerkat\Core\Theme
     */
    static function instance() {
        if (!self::$instance) {
            self::$instance = new Theme();
        }
        return self::$instance;
    }

    /**
     * Сменить тему
     * @param type $theme
     * @return \Meerkat\Core\Theme
     */
    function set($theme) {
        $this->theme = $theme;
        return $this;
    }

    function get() {
        return $this->theme;
    }

    /**
     * Применить темы модуля
     */
    function apply($module = null) {
        if ($module) {
            $path = \Arr::get(\Kohana::modules(), $module);
        } else {
            $path = APPPATH;
        }
        if (!$path) {
            return false;
        }
        $f = $path . 'themes/' . $this->theme . '.php';
        if (file_exists($f)) {
            \Kohana::load($f);
        } else {
            $f = $path . 'themes/' . self::$default . '.php';
            if (file_exists($f)) {
                \Kohana::load($f);
            }
        }
    }

}