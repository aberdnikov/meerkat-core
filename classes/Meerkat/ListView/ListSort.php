<?php
    namespace Meerkat\ListView;
    use Meerkat\Twig\Twig;

    class ListSort {
        protected $fields = array();
        protected $model_name;

        /**
         * @param $model ListSort
         */
        function __construct($model_name) {
            $this->model_name = $model_name;
        }

        /**
         * @param $model
         * @return ListSort
         */
        static function factory($model_name) {
            $class = '\Meerkat\ListView\ListSort_' . $model_name;
            if (!class_exists($class)) {
                $class = __CLASS__;
            }
            return new $class($model_name);
        }

        /**
         * @return ListSort
         */
        function addSort($field, $sql, $get = null) {
            if (!$get) {
                $get = $field;
            }
            $this->fields[$field]        = array();
            $this->fields[$field]['get'] = $get;
            $this->fields[$field]['sql'] = $sql;
            $value                       = \Arr::get($_GET, '_s');
            $direction                   = \Arr::get($_GET, '_d');
            if ($value != $get) {
                $direction = '';
            }
            $this->fields[$field]['current'] = $direction;
            switch ($direction) {
                case 'asc':
                    $this->fields[$field]['next'] = 'desc';
                    break;
                case 'desc':
                    $this->fields[$field]['next'] = '';
                    break;
                default:
                    $this->fields[$field]['current'] = '';
                    $this->fields[$field]['next']    = 'asc';
                    break;
            }
            return $this;
        }

        /**
         * @return \ORM
         */
        function apply(\ORM $model) {
            foreach ($this->fields as $field => $data) {
                $get       = \Arr::get($data, 'get');
                $sql       = \Arr::get($data, 'sql');
                $value     = \Arr::path($_GET, '_s');
                $direction = \Arr::path($_GET, '_d');
                if ($value == $get) {
                    $model->order_by($sql, $direction);
                }
            }
            return $model;
        }

        function getSortLink($label, $field) {
            $data = \Arr::get($this->fields, $field);
            if ($data) {
                $uri = \URL::query(array(
                    '_s' => \Arr::get($data, 'get'),
                    '_d' => \Arr::get($data, 'next'),
                ));
                switch (\Arr::get($data, 'current')) {
                    case 'asc':
                        $label .= ' <i class="glyphicon glyphicon-chevron-down"></i>';
                        $title = 'Отсортировать по убыванию';
                        break;
                    case 'desc':
                        $label .= ' <i class="glyphicon glyphicon-chevron-up"></i>';
                        $title = 'Убрать сортировку по полю';
                        break;
                    default:
                        $title = 'Отсортировать по возрастанию';
                        break;
                }
                return \HTML::anchor($_SERVER['PHP_SELF'] . $uri, $label, array('title' => $title));
            }
            return $label;
        }

        function getSort() {
            return $this->fields;
        }


    }