<?php
    namespace Meerkat\ListView;
    use Meerkat\Html\Div;
    use \Arr;

    class ListFilter {
        /**
         * @var array
         */
        protected $filters;
        /**
         * @var string
         */
        protected $template = null;
        /**
         * @var \Meerkat\Form\Form
         */
        protected $form;
        protected $model_name;

        function __construct($model_name) {
            $this->model_name = $model_name;
        }

        /**
         * @param \ORM $model
         * @return ListFilter
         */
        static function factory($model_name) {
            $class = '\Meerkat\ListView\ListFilter_' . $model_name;
            if (!class_exists($class)) {
                $class = __CLASS__;
            }
            return new $class($model_name);
        }

        function setTemplate($tpl) {
            $this->template = $tpl;
        }

        /**
         * @param      $key   string
         * @param null $value string
         * @return ListFilter
         */
        function addFilter($field = null) {
            $this->filters[$field] = $field;
            return $this;
        }

        /**
         * @return \ORM
         */
        function apply($model) {
            if (count($this->filters)) {
                $filter = trim(Arr::get($_GET, 'filter'));
                if ($filter) {
                    $model->where_open();
                    foreach ($this->filters as $field) {
                        $model->or_where($field, 'LIKE', '%' . $filter . '%');
                    }
                    $model->where_close();
                }
            }
            return $model;
        }

        function __toString() {
            $this->build_form();
            $this->form->set_layout(\Meerkat\Form\Form::LAYOUT_INLINE);
            $this->form
                ->add_submit('найти')
                ->add_class('btn btn-info ');
            if ($this->form
                ->get_form()
                ->getElements()
            ) {
                return Div::factory()
                    ->add_class('well')
                    ->set_content($this->form->render($this->template))
                    ->__toString();
            }
            return '123';
        }

        function build_form() {
            $this->form = \Meerkat\Form\Form::factory($this->model_name . '_filter', 'get', null, false);
            if (count($this->filters)) {
                $this->form
                    ->add_text('filter')
                    ->set_label('Фильтр')
                    ->add_class('form-control');
            }
        }

    }