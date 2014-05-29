<?php

namespace Meerkat\Widget;

class Widget_Alert extends Widget{

    const SESSION_KEY = 'widget_alert';
    const AS_DEFAULT = 'alert';
    const AS_SUCCESS = 'alert alert-success';
    const AS_ERROR = 'alert alert-danger';
    const AS_INFO = 'alert alert-info';

    protected $as_block = false;
    protected $css = self::AS_DEFAULT;
    protected $text;
    protected $title = null;

    /**
     * @return Widget_Map
     */
    static function instance() {
        return parent::_instance();
    }
    
    /**
     * @param type $name
     * @return \Meerkat\Widget\Widget_Alert
     */
    static function factory($text) {
        return new Widget_Alert($text);
    }

    function __construct($text=null) {
        $this->text = $text;
    }

    /**
     * @param type $text
     * @return \Meerkat\Widget\Widget_Alert
     */
    function title($text) {
        $this->title = $text;
        return $this;
    }
    function css($css) {
        $this->css = $css;
        return $this;
    }

    function as_error() {
        return $this->css(self::AS_ERROR);
    }

    function as_info() {
        return $this->css(self::AS_INFO);
    }

    function as_success() {
        return $this->css(self::AS_SUCCESS);
    }

    function as_default() {
        return $this->css(self::AS_DEFAULT);
    }

    function as_block($val = true) {
        $this->as_block = (bool) $val;
        return $this;
    }

    static function get_items() {
        return (array) \Session::instance()->get_once(self::SESSION_KEY, array());
    }

    static function to_html($params=null) {
        return static::get_template()->set('alerts', self::get_items())->render();
        //\Debug::stop(self::get_template());
    }

    function put() {
        $messages = self::get_items();
        $messages[] = array(
            'css' => $this->css . ($this->as_block ? ' alert-block' : ''),
            'text' => $this->text,
            'title' => $this->title,
        );
        \Session::instance()->set(self::SESSION_KEY, $messages);
    }

}