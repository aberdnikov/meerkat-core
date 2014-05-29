<?php

    namespace Meerkat\Widget;
    use Arr;
    use Meerkat\Core\Map;

    class Widget_NavList extends Widget {

        protected $items = array();
        protected $groups = array();

        static function to_html($params = null) {
            $prefix = \Arr::get($params, 'prefix');
            $prefix = $prefix ? $prefix = (array)explode(',', \Arr::get($params, 'prefix')) : array();
            /*return
            print_r($params,1).
            print_r(Widget_NavList::instance()->items(), 1)
                    ;*/
            $ret = array();
            foreach (Widget_NavList::instance()
                         ->items() as $access_name => $item
            ) {
                if (!count($prefix)) {
                    $ret[\Arr::get($item, 'group')][] = $item;
                }
                else {
                    foreach ($prefix as $p) {
                        if (mb_strpos($access_name, $p) === 0) {
                            $ret[\Arr::get($item, 'group')][] = $item;
                            break;
                        }
                    }
                }
            }
            $_ret = array();
            foreach ($ret as $group => $items) {
                $map_name = 'Widget_NavList|' . md5($group);
                $map      = \Meerkat\Core\Map::instance($map_name);
                foreach ($items as $item) {
                    $map->add(Arr::get($item, 'access_name'), Arr::get($item, 'text'), Arr::get($item, 'url'));
                }
                $_ret[$group] = $map->get_items();
            }
            return static::get_template()
                ->set('items', $_ret)
                ->render();
        }

        function items() {
            return $this->items;
        }

        /**
         * @return Widget_NavList
         */
        static function instance() {
            return parent::_instance();
        }

        function groups() {
            return $this->groups;
        }

        function item($group, $access_name, $text, $url) {
            $this->items[$access_name] = array(
                'url'         => trim($url, '/'),
                'text'        => $text,
                'access_name' => $access_name,
                'group'       => $group,
            );
        }

    }