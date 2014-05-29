<?php

namespace Meerkat\Core;

use Meerkat\Twig\Twig;

class Page_TplVar {

    protected $vars = array();
    //сюда будем складывать имена полей, которые были назначены пользователями, а не сгенерированы автоматически
    protected $changed_manual = array();
    static protected $instance;

    /**
     * 
     * @return Page_TplVar
     */
    static function instance() {
        if (!\Request::$current) {
            $uri = \Request::$initial->uri();
        } else {
            $uri = \Request::$current->uri();
        }
        //\Debug::stop($uri);
        if (!isset(self::$instance[$uri])) {
            self::$instance[$uri] = new Page_TplVar();
            //\Debug::info(self::$instance);
        }
        return self::$instance[$uri];
    }

    function set($name, $val, $is_changed_manual = false) {
        if ($is_changed_manual) {
            $this->changed_manual[$name] = $name;
        }
        $this->vars[$name] = $val;
        return $this;
    }

    function get($name) {
        return \Arr::get($this->vars, $name);
    }

    function vars() {
        return $this->vars;
    }

    function render() {
        return Twig::from_template($this->layout)->set($this->vars)->render();
    }

    function __toString() {
        return $this->render();
    }

    protected function _set($name, $val, $is_changed_manual = false) {
        $name = str_replace('set_', '', $name);
        return $this->set($name, $val, $is_changed_manual);
    }

    protected function _add($name, $val) {
        $name = str_replace('add_', '', $name);
        if (!isset($this->vars[$name])) {
            $this->vars[$name] = array();
        }
        return $this->vars[$name][] = $val;
    }

    function is_changed_manual($name) {
        return isset($this->changed_manual[$name]);
    }

    function set_body_attr($val, $is_changed_manual = false) {
        $this->_set(__FUNCTION__, $val, $is_changed_manual);
    }
    function set_body($val, $is_changed_manual = false) {
        $this->_set(__FUNCTION__, $val, $is_changed_manual);
    }

    function set_page_title($val, $is_changed_manual = false) {
        $this->_set(__FUNCTION__, $val, $is_changed_manual);
    }

}