<?php

    namespace Meerkat\Html;

    use Meerkat\Html\KHtml;
    use Meerkat\Html\Icon;

    /**
     *
     * @author aberdnikov
     */
    class Button extends KHtml {

        protected $_type = 'button';
        protected $_has_closed = true;
        protected $_icon = null;

        /**
         * @return \Meerkat\Html\Button
         */
        static function factory() {
            return new Button();
        }

        /**
         *
         * @return KHtml_Icon
         */
        function &add_ico() {
            $this->_icon = new Icon();
            return $this->_icon;
        }

        function get_content() {
            $content = parent::get_content();
            if ($this->_icon) {
                $content = $this->_icon . ' ' . $content;
            }
            return $content;
        }

        /**
         * @return $this
         */
        function size_large() {
            $this
                ->size_clear()
                ->addClass('btn-lg');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function size_clear() {
            return $this
                ->addClass('btn')
                ->removeClass('btn-lg btn-sm btn-xs');
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function size_small() {
            $this
                ->size_clear()
                ->addClass('btn-sm');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function size_extra_small() {
            $this
                ->size_clear()
                ->addClass('btn-xs');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_success() {
            $this
                ->style_clear()
                ->addClass('btn-success');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_warning() {
            $this
                ->style_clear()
                ->addClass('btn-warning');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_danger() {
            $this->style_clear()->addClass('btn-danger');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_inverse() {
            $this->style_clear()->addClass('btn-inverse');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_link() {
            $this->style_clear()->addClass('btn-link');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_default() {
            $this->style_clear()->addClass('btn-default');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_primary() {
            $this->style_clear()->addClass('btn-primary');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function disabled_on() {
            $this->addClass('disabled');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function disabled_off() {
            $this->removeClass('disabled');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function active_on() {
            $this->addClass('active');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function active_off() {
            $this->removeClass('active');
            return $this;
        }

        /**
         * @return \Meerkat\Html\Button
         */
        function style_clear() {
            return $this
                ->addClass('btn')
                ->removeClass('btn-default btn-primary btn-link btn-default btn-inverse btn-danger btn-warning btn-success');
        }

    }
