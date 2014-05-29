<?php

    namespace Meerkat\Core;

    use Meerkat\Core\Page;
    use Meerkat\Event\Event;
    use Meerkat\Core\Page_Layout;
    use Meerkat\Core\Page_TplVar;
    use Meerkat\Core\Theme;
    use Meerkat\Core\Seo;

    class Controller extends \Controller {
        /**
         * @var \ORM
         */
        protected $model;
        protected $model_id;
        /**
         * @var \Meerkat\Form\Form
         */
        protected $form;
        protected $model_name;
        protected $base_url;
        protected $per_page = 10;
        protected $filters = null;

        function after() {
            Theme::instance()
                ->apply();
            if (Page::instance()
                ->template()
            ) {
                Event::dispatcher()
                    ->notify(new \sfEvent(null, 'MEERKAT_LAYOUT'));
            }

            $this->response->body(Page::instance()
                ->render());
            parent::after();
        }

        function before() {
            parent::before();
            \Meerkat\StaticFiles\File::need_lib('jquery');
            \Meerkat\Core\Page_TplVar::instance()
                ->set('base_url', $this->base_url);
            \Meerkat\Core\Page_TplVar::instance()
                ->set('per_page', $this->per_page);

            //установим лэйаут по-умолчанию
            Page_Layout::instance()
                ->template('!/layouts/default');
            //создали событие
            $event = new \sfEvent(null, 'Controller::before');
            //оповестили приложение
            \Meerkat\Event\Event::dispatcher()
                ->notify($event);
        }

        function redirect_msg($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirect_msg_success($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_success()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirect_msg_error($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_error()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirect_msg_info($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_info()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function crud_action_edit() {
            $this->crud_model();
            $this->crud_form();
            $this->form
                ->init_values($this->model->as_array());
            $this->form
                ->add_actions_group()
                ->add_submit('Сохранить')
                ->add_class('btn btn-lg btn-primary');
            if ($this->form
                ->get_form()
                ->validate()
            ) {
                $reason = $this->crud_save_edit();
                if (!$reason) {
                    $this->redirect_msg_success('Редактирование прошло успешно!', $this->base_url);
                }
            }
            \Meerkat\Core\Page_TplVar::instance()
                ->set_body($this->form);

            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Редактировать', $this->base_url . $this->model->pk() . '/edit');
            \Meerkat\Core\Page_TplVar::instance()
                ->set('item', $this->model);
        }

        protected function crud_model() {
            $this->model = \ORM::factory($this->model_name, \Request::current()
                ->param('id'));
            if (\Request::current()
                ->param('id')
            ) {
                \Meerkat\Core\Seo::instance()
                    ->add_breadcrumb($this->model->__toString(), $this->base_url . $this->model->pk());
            }
        }

        protected function crud_form() {
            $this->form = \Meerkat\Form\Form::factory($this->model_name);
            /*$this->form
                ->add_date('to')
                ->set_label('Конец')
                ->rule_required();*/
        }

        protected function crud_save_add() {
            try {
                $values = $this->form
                    ->get_form()
                    ->getValue();
                $this->model->values($values);
                $this->model->save();
                return false;
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        }

        protected function crud_save_edit() {
            try {
                $values = $this->form
                    ->get_form()
                    ->getValue();
                $this->model->values($values);
                $this->model->save();
                return false;
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        }

        function crud_action_add() {
            $this->crud_model();
            $this->crud_form();
            $this->form
                ->init_values($this->model->as_array());
            $this->form
                ->add_actions_group()
                ->add_submit('Добавить')
                ->add_class('btn btn-lg btn-primary');
            if ($this->form
                ->get_form()
                ->validate()
            ) {
                $reason = $this->crud_save_add();
                if (!$reason) {
                    $this->redirect_msg_success('Добавление прошло успешно!', $this->base_url);
                }
            }
            \Meerkat\Core\Page_TplVar::instance()
                ->set_body($this->form);
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Добавить', $this->base_url . 'add');
            \Meerkat\Core\Page_TplVar::instance()
                ->set('item', $this->model);
        }

        protected function crud_action_item() {
            \Meerkat\Core\Page_Layout::instance()
                ->template(true);
            $this->crud_model();
            \Meerkat\Core\Page_TplVar::instance()
                ->set('item', $this->model);
        }

        protected function crud_action_index() {
            \Meerkat\StaticFiles\Js::instance()
                ->add_onload('
                $("[data-action=show_sql]").click(function(){
                    $(this).next().toggleClass("hide");
                });
            ');
            \Meerkat\StaticFiles\Css::instance()
                ->add_inline('.meerkat_filter .form-control{
                width: 83.3333%;
                float: left;
            }');
            \Meerkat\Core\Page_Layout::instance()
                ->template(true);
            $this->crud_filter();
            $items = $this->model
                ->find_all($this->per_page)
                ->as_array();
            //\Database::debug();
            \Meerkat\Core\Page_TplVar::instance()
                ->set('last_query', \Database::instance()->last_query);
            \Meerkat\Core\Page_TplVar::instance()
                ->set('current_page', (max((int)\Arr::get($_GET, 'page', 1), 1) - 1));
            $cnt_all = \Database::instance()
                ->count_last_query();
            \Meerkat\Core\Page_TplVar::instance()
                ->set('cnt_all', $cnt_all);
            \Meerkat\Core\Page_TplVar::instance()
                ->set('items', $items);
        }

        protected function crud_filter() {
            $this->crud_model();
            if ($this->filters) {
                $filter = \Arr::get($_GET, 'filter');
                \Meerkat\Core\Page_TplVar::instance()
                    ->set('filter', $filter);
                $this->model->where_open();
                foreach ($this->filters as $f) {
                    $this->model->or_where($f, 'LIKE', '%' . $filter . '%');
                }
                $this->model->where_close();
            }

        }

    }