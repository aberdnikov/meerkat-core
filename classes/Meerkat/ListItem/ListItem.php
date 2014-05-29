<?php
    namespace Meerkat\ListItem;
    class ListItem {
        /**
         * @var ORM
         */
        protected $model;
        protected $params;

        function __construct($model, $params = array()) {
            $this->model  = $model;
            $this->params = $params;
        }

        static function factory($model, $params = array()) {
            return new ListItem($model, $params);
        }

        function __toString() {
            return $this->render();
        }

        function render() {
            return \Meerkat\Twig\Twig::from_template('!/listitems/' . $this->model->object_name())
                ->set('model', $this->model)
                ->set('params', $this->params)
                ->render();
        }
    }