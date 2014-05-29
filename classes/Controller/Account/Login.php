<?php

class Controller_Account_Login extends Controller_Account {

    function action_index() {
        //\Meerkat\Base\Page_Layout::instance()->template('true');
        Meerkat\Core\Seo::instance()->add_breadcrumb('Изменить логин', Kohana::$config->load('meerkat/user.url.account_login'));
        $model = ORM::factory('User',  Meerkat\User\Me::id());
        $form = \Meerkat\ORMForm\ORMForm::factory($model)->set_structure(array(
            'login',
        ))->build();
        $form->init_values();
        $form->add_submit('Сменить логин');
        $f = $form->get_quickform();
        if($f->validate()){
            $values = $form->get_values();
            $model->login = Arr::get($values,'login');
            $model->save();
            Auth::instance()->force_login($model->login);
            $this->redirect_msg_info('Логин изменен', Kohana::$config->load('meerkat/user.url.account'));
        }
        Meerkat\Core\Page_TplVar::instance()->set_body($form);
    }

}