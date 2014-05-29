<?php

namespace Meerkat\Core;

use Meerkat\Twig\Twig;
use Meerkat\Core\Page_TplVar;

class Page_Layout {

    protected $layout = 'default';
    //protected $vars = array();
    static protected $instance;

    /**
     * 
     * @return Page_Layout
     */
    static function instance() {
        $uri = \Request::current()->uri();
        if (!isset(self::$instance[$uri])) {
            self::$instance[$uri] = new Page_Layout();
        }
        return self::$instance[$uri];
    }

    function template($tpl) {
        if ($tpl === true) {
            $tpl = '';
            if (\Request::current()->directory()) {
                $tpl .=\Request::current()->directory() . '/';
            }
            $tpl .=\Request::current()->controller() . '/' . \Request::current()->action();
        }
        $this->layout = mb_strtolower($tpl);
        //\Debug::info($this->layout);
        return $this;
    }

    /* function get($name) {
      return \Arr::get($this->vars, $name);
      } */

    function render() {
        //\Debug::info(TplVar::instance()->vars());
        //\Debug::info($this->layout);
        return Twig::from_template($this->layout)
                        ->set(Page_TplVar::instance()->vars())
                        ->render();
    }

    function __torString() {
        return $this->render();
    }

}