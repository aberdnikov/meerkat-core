<?php

    namespace Meerkat\Widget;

    use \Arr as Arr;

    class Widget_Paginator extends Widget {

        /**
         * @return Widget_Paginator
         */
        static function instance() {
            return parent::_instance();
        }

        static function to_html($params = null) {
            \Meerkat\StaticFiles\Css::instance()->add_inline('
            .pager+.pagination{margin-top:0;}
            .pagination+.pager{margin:0;}
            ',__CLASS__);
            $per_page = Arr::get($params, 'per_page');
            $cnt_all  = Arr::get($params, 'cnt_all');
            $uri      = Arr::get($params, 'uri');
            $size     = Arr::get($params, 'size');
            $pager    = Arr::get($params, 'pager');
            if (!$per_page) {
                return '';
            }
            return \Pagination::factory(array(
                'uri'            => $uri,
                'size'           => $size,
                'pager'          => $pager,
                'total_items'    => $cnt_all,
                'items_per_page' => $per_page,
            ))
                ->render();
        }

    }