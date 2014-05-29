<?php
    namespace Meerkat\CRUD;
    use Meerkat\Core\Page_TplVar;
    use Meerkat\ViewItem\ViewItem;
    use ORM;
    use Meerkat\Html\Div;

    class CRUD {
        protected $model_name;
        protected $model_id;
        protected $base_url;

        function __construct($model_name) {
            $this->model_id = \Request::current()
                ->param('id');
            $this->setModelName($model_name);
        }

        function setModelName($model_name) {
            $this->model_name = $model_name;
            return $this;
        }

        /**
         * @return CRUD
         */
        static function factory($model_name) {
            return new CRUD($model_name);
        }

        function getBaseUrl() {
            return $this->base_url;
        }

        function setBaseUrl($base_url) {
            $this->base_url = $base_url;
            return $this;
        }

        /**
         * @param null $model
         * @return \Meerkat\ListView\ListView
         */
        function actionIndex($model = null) {
            if (!$model) {
                $this->item();
            }
            else {
                $this->model = $model;
            }
            $list = \Meerkat\ListView\ListView::factory($this->model);
            $list->setBaseUrl($this->base_url);
            return $list;
        }

        function item() {
            $this->model = ORM::factory($this->model_name, $this->model_id);
            if ($this->model->loaded()) {
                \Meerkat\Core\Seo::instance()
                    ->add_breadcrumb($this->model, $this->base_url . $this->model_id);
            }
            else {
                if ($this->model_id) {
                    throw new HTTP_Exception_500('Not found!');
                }
            }
        }

        function actionAdd($structure = null) {
            $this->item();
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Добавить', $this->base_url . 'add');
            $form = \Meerkat\ORMForm\ORMForm::factory($this->model);
            if ($structure) {
                $form->set_structure($structure);
            }
            $form = $form->build();
            $form
                ->get_form()
                ->add_actions_group()
                ->add_submit('Добавить')
                ->add_class('btn-primary btn btn-lg');
            if ($form
                ->get_quickform()
                ->validate()
            ) {
                $params = array(
                    'model_name' => $this->model_name,
                    'model'      => $this->model,
                    'form'       => $form,
                );

                $event  = new \sfEvent(null, 'CRUD::add:before', $params);
                \Meerkat\Event\Event::dispatcher()
                    ->notify($event);

                $values = $form
                    ->get_quickform()
                    ->getValue();
                $this->model->values($values);
                $this->model->save();
                $params = array(
                    'model_name' => $this->model_name,
                    'model'      => $this->model,
                    'form'       => $form,
                );
                $event  = new \sfEvent(null, 'CRUD::add:after', $params);
                \Meerkat\Event\Event::dispatcher()
                    ->notify($event);

                $this->redirectMsgSuccess('Операция прошла успешно!', $this->base_url);

            }
            Page_TplVar::instance()
                ->set_body($form);
        }

        function actionEdit($structure = null) {
            $this->item();
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Редактировать', $this->base_url . $this->model_id . '/edit');
            $form = \Meerkat\ORMForm\ORMForm::factory($this->model);
            if ($structure) {
                $form->set_structure($structure);
            }
            $form = $form->build();
            $form->init_values($this->model->as_array());
            $form
                ->get_form()
                ->add_actions_group()
                ->add_submit('Сохранить')
                ->add_class('btn-primary btn btn-lg');
            if ($form
                ->get_quickform()
                ->validate()
            ) {
                $params = array(
                    'model_name' => $this->model_name,
                    'model'      => $this->model,
                    'form'       => $form,
                );
                $event  = new \sfEvent(null, 'CRUD::edit:before', $params);
                \Meerkat\Event\Event::dispatcher()
                    ->notify($event);

                $values = $form
                    ->get_quickform()
                    ->getValue();
                $this->model->values($values);
                $this->model->save();
                $params = array(
                    'model_name' => $this->model_name,
                    'model'      => $this->model,
                    'form'       => $form,
                );
                $event  = new \sfEvent(null, 'CRUD::edit:after', $params);
                \Meerkat\Event\Event::dispatcher()
                    ->notify($event);

                $this->redirectMsgSuccess('Операция прошла успешно!', $this->base_url);

            }
            Page_TplVar::instance()
                ->set_body($form);
        }

        function actionDelete($fields = null) {
            $this->item();
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Удалить', $this->base_url . $this->model_id . '/delete');
            $form = $this->formDelete($fields);
            if ($form
                ->get_form()
                ->validate()
            ) {
                try {
                    $this->model->delete();
                }
                catch (Exception $e) {
                    $this->redirectMsgError($e->getMessage());
                }
                $params = array(
                    'model_name' => $this->model_name,
                    'model_id'   => $this->model_id,
                );
                $event  = new \sfEvent(null, 'CRUD::delete:after', $params);
                \Meerkat\Event\Event::dispatcher()
                    ->notify($event);

                $this->redirectMsgSuccess('Операция прошла успешно!', $this->base_url);
            }
            Page_TplVar::instance()
                ->set_body($form);
        }

        function getModel() {
            return $this->model;
        }

        /**
         * @return \Meerkat\ViewItem\ViewItem
         *
         */
        function actionItem() {
            $this->item();
            return \Meerkat\ViewItem\ViewItem::factory($this->model);
        }

        function redirectMsg($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirectMsgSuccess($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_success()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirectMsgError($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_error()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function redirectMsgInfo($msg, $url = null) {
            \Meerkat\Widget\Widget_Alert::factory($msg)
                ->as_info()
                ->put();
            \HTTP::redirect($url ? $url : $_SERVER['PHP_SELF']);
        }

        function formDelete($fields = null) {
            $form     = \Meerkat\Form\Form::factory($this->model_name);
            $viewitem = ViewItem::factory($this->model);
            if ($fields) {
                $viewitem->setFields($fields);
            }
            $form->add_static($viewitem->card());
            $form
                ->add_actions_group()
                ->add_submit('Удалить?')
                ->add_class('btn btn-danger btn-lg');
            return $form;
        }

    }