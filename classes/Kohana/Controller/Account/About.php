<?php

    class Kohana_Controller_Account_About extends Controller_Account {

        protected $fields = array(
            'username',
            'login',
            'is_man',
            'about',
        );
        function action_index() {
            $model = ORM::factory('User', Meerkat\User\Me::id());
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Изменить информацию о себе', Kohana::$config->load('meerkat/user.url.account_about'));
            $form = \Meerkat\ORMForm\ORMForm::factory($model)
                ->set_structure($this->fields)
                ->build();
            //$form->add_meta();
            $form->init_values();
            $form->add_submit('Сохранить');
            $f = $form->get_quickform();
            if ($f->validate()) {
                $values = $form->get_values();
                $model->values($values);
                $model->save();
                Auth::instance()
                    ->force_login($model->login);
                $this->redirect_msg_info('Информация о себе изменена');
            }
            Meerkat\Core\Page_TplVar::instance()
                ->set_body($form);
        }

    }