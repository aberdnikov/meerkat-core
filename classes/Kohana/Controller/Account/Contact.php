<?php

    class Kohana_Controller_Account_Contact extends Controller_Account {
        protected $fields = array(
            'site',
            'phone',
        );

        function action_index() {
            $model = ORM::factory('User', Meerkat\User\Me::id());
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Изменить информацию о себе', Kohana::$config->load('meerkat/user.url.account_about'));
            $form = \Meerkat\ORMForm\ORMForm::factory($model)
                ->set_structure($this->fields)
                ->build();
            $form->init_values();
            $form->add_submit('Сохранить');
            $f = $form->get_quickform();
            if ($f->validate()) {
                $values       = $form->get_values();
                $model->site  = Arr::get($values, 'site');
                $model->phone = Arr::get($values, 'phone');
                $model->save();
                $this->redirect_msg_info('Контактные данные изменены');
            }
            Meerkat\Core\Page_TplVar::instance()
                ->set_body($form);
        }

    }