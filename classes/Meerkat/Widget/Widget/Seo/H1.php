<?php

namespace Meerkat\Widget;

class Widget_Seo_H1 extends Widget {

    /**
     * @param type $name
     * @return Widget_Breadcrumbs
     */
    static function instance() {
        return parent::_instance();
    }

    static function to_html($params = null) {
        return static::get_template()->render();
    }

}
