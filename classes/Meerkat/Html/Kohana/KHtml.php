<?php

    namespace Meerkat\Html;

    require_once 'HTML/Common2.php';

    /**
     * Fabric Class & Base Class
     *
     * @author aberdnikov
     */
    class Kohana_KHtml extends \HTML_Common2 {

        protected $_content = '';
        protected $_type = '';
        protected $_has_closed = true;

        function set_value($value) {
            $this->setAttribute('value', $value);
            return $this;
        }

        function set_title($value) {
            $this->setAttribute('title', __($value));
            return $this;
        }

        function set_content($content) {
            $this->_content = $content;
            return $this;
        }

        function get_type() {
            return $this->_type;
        }

        function set_attr($name, $value) {
            $this->setAttribute($name, $value);
            return $this;
        }

        function add_class($name) {
            $this->addClass($name);
            return $this;
        }

        function remove_class($name) {
            $this->removeClass($name);
            return $this;
        }

        function has_class($name) {
            return $this->hasClass($name);
        }

        function set_type($type) {
            $this->_type = $type;
            return $this;
        }

        function set_has_closed($val) {
            $this->_has_closed = (bool)$val;
            return $this;
        }

        function __toString() {
            if ($this->_has_closed) {
                return sprintf('<%s%s>%s</%s>', $this->_type, $this->getAttributes(true), $this->get_content(), $this->_type);
            }
            else {
                return sprintf('<%s%s />', $this->_type, $this->getAttributes(true));
            }
        }

        function get_content() {
            return $this->_content;
        }

    }

?>
