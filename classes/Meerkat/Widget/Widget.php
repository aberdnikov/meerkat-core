<?php

namespace Meerkat\Widget;

class Widget {

    static public $instances = array();

    protected static function _instance($name = 'default') {
        $class = get_called_class();
        if (!isset(self::$instances[$class][$name])) {
            self::$instances[$class][$name] = new $class;
        }
        return self::$instances[$class][$name];
    }

    /**
     * Объект шаблона
     * @return \Meerkat\Twig\Tpl
     */
    static function get_template() {
        return \Meerkat\Twig\Twig::from_template(self::get_template_name());
    }

    /**
     * Путь к шаблону (автовычисляемый по имени виджета)
     * @return type
     */
    static function get_template_name() {
        $class = str_replace('meerkat\widget\widget_', '', mb_strtolower(get_called_class()));
        return '!/widgets/' . $class;
    }

    static function to_html($params = null) {
        return self::get_template()->render();
    }

}
