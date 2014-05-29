<?php

    namespace Meerkat\Widget;

    class Widget_Navbar_Top extends Widget {

        protected static $instance;
        /**
         * Карта элементов верхнего меню
         * @var \Meerkat\Core\Map
         */
        public $_map_left;
        public $_map_right;
        public $right_after = '';
        public $right_before = '';
        /**
         * Заголовок в шапке/бренд
         * @var string
         */
        protected $brand = 'Meerkat';

        function __construct() {
            $this->_map_rigth = \Meerkat\Core\Map::instance(get_called_class() . '|right');
            $this->_map_left  = \Meerkat\Core\Map::instance(get_called_class() . '|left');
        }

        static function to_html($params = null) {
            $tpl = static::get_template();
            $tpl->set('right_before', self::instance()->right_before);
            $tpl->set('right_after', self::instance()->right_after);
            $tpl->set('map_right', get_called_class() . '|right');
            $tpl->set('map_left', get_called_class() . '|left');
            $tpl->set('brand', Widget_Navbar_Top::instance()
                ->get_brand());
            return $tpl->render();
        }

        /**
         * @param type $name
         * @return Widget_Navbar_Top
         */
        static function instance($name = 'default') {
            return parent::_instance($name);
        }

        function get_brand() {
            return $this->brand;
        }

        function set_brand($brand) {
            $this->brand = $brand;
        }

        function &map_right() {
            return $this->_map_rigth;
        }

        function &map_left() {
            return $this->_map_left;
        }

    }