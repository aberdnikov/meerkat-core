<?php

namespace Meerkat\Core;

use \Arr as Arr;

class Map {

    protected $items = array();
    protected static $instances;

    const DIVIDER = '--divider--';

    /**
     * 
     * @param type $name
     * @return Map
     */
    static function instance($name) {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new Map();
        }
        return self::$instances[$name];
    }

    function add_divider($access_name) {
        $r = Arr::path($this->items, $access_name);
        $this->add($access_name . '.div' . (count($r) + 1), self::DIVIDER, '#');
    }

    function add($access_name, $text, $url) {
        $path = str_replace('.', '._items.', $access_name);
        Arr::set_path($this->items, $path . '.item.text', $text);
        Arr::set_path($this->items, $path . '.item.url', $url);
        Arr::set_path($this->items, $path . '.access_name', $access_name);
        $parents = explode('.', $access_name);
        while (array_pop($parents) != null) {
            if (count($parents)) {
                $this->auto_create_parent(implode('.', $parents));
            }
        }
    }

    /**
     * $access_name = 'admin.plugins.module.config_module';
     * $path = 'admin._items.plugins._items.module._items.config';
     * $default_title = 'Config Module';
     * @param type $access_name
     */
    function auto_create_parent($access_name) {
        $path = str_replace('.', '._items.', $access_name);
        $arr = explode('.', $access_name);
        $tmp = array_pop($arr);
        $default_title = ucwords(str_replace('_', ' ', $tmp));
        if (!Arr::path($this->items, $path . '.item')) {
            Arr::set_path($this->items, $path . '.item.text', $default_title);
            Arr::set_path($this->items, $path . '.item.url', '#');
        }
        if (!Arr::path($this->items, $path . '.access_name')) {
            Arr::set_path($this->items, $path . '.access_name', $access_name);
        }
    }
    
    function get_items(){
        return $this->items;
    }

}

