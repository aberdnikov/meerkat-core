<?php
    namespace Meerkat\ListView;
    use Meerkat\Html\Icon_Famfamfam;
    use Meerkat\Twig\Twig;

    class ListView {
        public $base_url;
        /**
         * @var \ORM
         */
        protected $model;
        protected $model_name;
        protected $per_page = 10;
        protected $template = null;
        protected $fields = array();
        protected $aliases = array();
        protected $actions = array();
        protected $labels = array();
        /**
         * @var ListSort
         */
        protected $list_sort = null;
        /**
         * @var ListFilter
         */
        protected $list_filter = null;
        protected $callbacks = array();
        protected $action_callbacks = array();

        /**
         * @param $model ListView
         */
        function __construct(\ORM $model) {
            $this->model      = $model;
            $this->model_name = $model->object_name();
            if(!count($this->fields)){
                $this->fields     = array_keys($this->model->list_columns());
            }
            $class            = new \ReflectionClass(get_called_class());
            $methods          = $class->getMethods();
            foreach ($methods as $method) {
                if (mb_strpos($method->getName(), 'callbackAction__') === 0) {
                    $action          = str_replace('callbackAction__', '', $method->getName());
                    $this->actions[] = $action;
                }
            }
            $this->labels                 = $this->model->labels();
            $this->labels['thumb_small']  = '&nbsp;';
            $this->labels['thumb_medium'] = '&nbsp;';
            $this->labels['thumb_large']  = '&nbsp;';
        }

        /**
         * @param $model
         * @return ListView
         */
        static function factory($model) {
            $class = '\Meerkat\ListView\ListView_' . \Text::ucfirst($model->object_name(), '_');
            if (!class_exists($class)) {
                $class = __CLASS__;
            }
            return new $class($model);
        }

        function setLabels($labels) {
            $this->labels = \Arr::merge($this->labels, $labels);
        }

        /**
         * @param $value ListSort
         * @return ListView
         */
        function setListSort($list_sort) {
            $list_sort->apply($this->model);
            $this->list_sort = $list_sort;
            return $this;
        }

        /**
         * @param $value ListSort
         * @return ListView
         */
        function setModelName($model_name) {
            $this->model_name = $model_name;
            return $this;
        }

        /**
         * @param $value ListSort
         * @return ListFilter
         */
        function setListFilter($list_filter) {
            $list_filter->apply($this->model);
            $this->list_filter = $list_filter;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setFieldCallback($field, $callback) {
            $this->callbacks[$field][] = $callback;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setActionCallback($action, $callback) {
            $this->action_callbacks[$action] = $callback;
            return $this;
        }

        function getField($item, $field) {
            //return $field.var_dump($item,1);
            $callbacks = \Arr::get($this->callbacks, $field, array());
            $value     = $item->get($field);
            if (count($callbacks)) {
                foreach ($callbacks as $callback) {
                    $value = call_user_func($callback, $value, $item);
                }
            }
            return $value;
        }

        function setPerPage($value) {
            $this->per_page = abs((int)$value);
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setFields($fields) {
            $this->fields = $fields;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setAliases($aliases) {
            $this->aliases = $aliases;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setActions($actions) {
            $this->actions = $actions;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setBaseUrl($value) {
            $this->base_url = $value;
            return $this;
        }

        /**
         * @param $value
         * @return ListView
         */
        function setTemplate($value) {
            $this->template = $value;
            return $this;
        }

        function getActionLink($item, $action) {
            $callback = array($this,
                'callbackAction__' . $action);
            if (is_callable($callback)) {
                return call_user_func($callback, $item);
            }
            $callback = \Arr::get($this->action_callbacks, $action);
            if ($callback && is_callable($callback)) {
                return call_user_func($callback, $item, $this);
            }
            return '[BAD]';
        }

        function callbackAction__item($item) {
            return '<a href="' . $this->base_url . $item->pk() . '"><i class="iconfam_zoom" title="Подробнее"></i></a>';
        }

        function callbackAction__edit($item) {
            return '<a href="' . $this->base_url . $item->pk() . '/edit"><i class=" iconfam_pencil" title="Редактировать"></i></a>';
        }

        function callbackAction__delete($item) {
            return '<a href="' . $this->base_url . $item->pk() . '/delete"><i class="iconfam_cancel" title="Удалить"></i></a>';
        }

        function __toString() {
            try {

                if (!$this->template) {
                    $this->template = '!/components/view_list';
                }
                $tpl   = Twig::from_template($this->template);
                $items = $this->model
                    ->find_all($this->per_page)
                    ->as_array();
                $tpl->set('last_query', \Database::instance()->last_query);
                $tpl->set('current_page', (max((int)\Arr::get($_GET, 'page', 1), 1) - 1));
                $cnt_all = \Database::instance()
                    ->count_last_query();
                $tpl->set('use_filter', count(\Arr::get($_GET, 'filter')));
                $tpl->set('cnt_all', $cnt_all);
                $tpl->set('per_page', $this->per_page);
                $tpl->set('fields', $this->fields);
                $tpl->set('labels', $this->labels);
                $tpl->set('aliases', $this->aliases);
                $tpl->set('base_url', $this->base_url);
                $tpl->set('actions', $this->actions);
                $tpl->set('model', $this->model);
                $tpl->set('model_name', $this->model_name);
                $tpl->set('primary_key', $this->model->primary_key());
                $tpl->set('items', $items);
                $tpl->set('list_sort', $this->list_sort);
                $tpl->set('list_view', $this);
                return $tpl->render();
            }
            catch (\Exception $e) {
                return $e->getMessage();
            }
        }

    }