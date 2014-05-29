<?php

    namespace Meerkat\ViewItem;
    use Meerkat\Twig\Twig;

    class ViewItem {

        protected $model;
        protected $model_id;
        protected $model_name;
        protected $fields;
        protected $template = null;

        function __construct($model) {
            $this->model      = $model;
            $this->model_name = $model->object_name();
            $this->model_id   = $model->pk();
            $this->initFields();
        }

        function getModel() {
            return $this->model;
        }

        function initFields() {
            $columns = $this->model->list_columns();
            unset($columns[$this->model->primary_key()]);
            $this->fields = array_keys($columns);
            foreach ($this->model->belongs_to() as $belongs => $val) {
                //$this->fields[] = $belongs;
            }
        }

        function setFields($fields) {
            $this->fields = $fields;
            return $this;
        }

        function as_array() {
            $model             = $this->model;
            $ret               = array();
            $ret['__toString'] = $this->__toString();
            foreach ($model->object() as $k => $v) {
                $ret[$k] = $this->get($k) . '';
            }
            foreach ($model->belongs_to() as $belongs => $val) {
                $ret[$belongs] = ViewItem::factory($model->get($belongs))
                    ->as_array();
            }
            return $ret;
        }

        function __toString() {
            return $this->model->__toString();
        }

        function get($field) {
            $callback = array($this,
                'field__' . $field);
            if (is_callable($callback)) {
                return call_user_func($callback);
            }
            foreach ($this->model->belongs_to() as $belongs => $val) {
                if(\Arr::get($val, 'foreign_key')==$field){
                    return ViewItem::factory($this->model->get($belongs));
                }
            }
            return $this->model->$field;
        }

        /**
         * @param $model
         * @return \Meerkat\ViewItem\ViewItem
         */
        static function factory($model) {
            if (!is_object($model)) {
                \Debug::stop($model);
            }
            $class = '\Meerkat\ViewItem\\ViewItem_' . \Text::ucfirst($model->object_name(), '_');
            //print ($class).'<br />';
            if (!class_exists($class)) {
                $class = __CLASS__;
            }
            return new $class($model);
        }

        function card() {
            try {
                if (!$this->template) {
                    $this->template = '!/components/view_item';
                }
                $tpl = Twig::from_template($this->template);
                $tpl->set('fields', $this->fields);
                $tpl->set('labels', $this->labels());
                $tpl->set('model', $this->model);
                $tpl->set('view_item', $this);
                $tpl->set('model_name', $this->model_name);
                $tpl->set('primary_key', $this->model->primary_key());
                return $tpl->render();
            }
            catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        function labels() {
            return $this->model->labels();
        }

        function label($field) {
            return $this->model->label($field);
        }

        function field__thumb_small() {
            return $this->thumb('small');
        }

        function field__thumb_medium() {
            return $this->thumb('medium');
        }

        function field__thumb_big() {
            return $this->thumb('big');
        }

        function thumb($size, $attrs = array(), $prop = null) {
            return \Meerkat\Twig\Twig_Extension::f_thumb_img(
                $this->model_name,
                $this->model_id,
                $size,
                $prop,
                $attrs
            );
        }

    }