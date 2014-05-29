<?php
    namespace Meerkat\Core;
    class Seo {
        static protected $_instance;
        protected $_props;
        protected $_breadcrumbs;

        static function instance() {
            if (!isset(self::$_instance)) {
                self::$_instance = new Seo();
                foreach(\Kohana::$config->load('meerkat/seo') as $k=>$v){
                    self::$_instance->set($k, $v);
                }
            }
            return self::$_instance;
        }

        /**
         * @param $name
         * @return string
         */
        function get($name) {
            return \Arr::get($this->_props, $name);
        }

        /**
         * @param      $name
         * @param null $value
         * @return \Meerkat\Core\Seo
         */
        function _($name, $value = null) {
            return $this->set(str_replace('set_', '', $name), $value);
        }

        /**
         * @param $name
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set($name, $value) {
            $this->_props[$name] = $value;
            return $this;
        }

        /**
         * @param $text
         * @param $url
         * @return \Meerkat\Core\Seo
         */
        function add_breadcrumb($text, $url) {
            $text = strip_tags($text);
            $this
                ->set_title($text)
                ->set_h1($text);
            $this->_props['breadcrumbs'][] = array($text,
                $url);
            return $this;

        }

        /**
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set_h1($value) {
            return $this->_(__FUNCTION__, $value);
        }
        /**
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set_h1_ext($value) {
            return $this->_(__FUNCTION__, $value);
        }

        /**
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set_title($value) {
            return $this->_(__FUNCTION__, $value);
        }

        /**
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set_meta_keywords($value) {
            return $this->_(__FUNCTION__, $value);
        }

        /**
         * @param $value
         * @return \Meerkat\Core\Seo
         */
        function set_meta_description($value) {
            return $this->_(__FUNCTION__, $value);
        }
    }