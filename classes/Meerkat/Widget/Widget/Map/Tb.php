<?php

namespace Meerkat\Widget;

class Widget_Map_Tb extends Widget {

    /**
     * @return Widget_Map_Tb
     */
    static function instance() {
        return parent::_instance();
    }

    static function to_html($params = null) {
        $tpl = static::get_template();
        $map = \Meerkat\Core\Map::instance($params);
        //\Debug::stop($map->get_items());
        $tpl->set('map', $map->get_items());
        return $tpl->render();
    }

}