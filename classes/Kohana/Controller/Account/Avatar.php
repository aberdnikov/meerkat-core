<?php

class Kohana_Controller_Account_Avatar extends Controller_Account {

    function action_index() {
        Meerkat\Core\Seo::instance()->add_breadcrumb('Изменить аватарки', Kohana::$config->load('meerkat/user.url.account_avatar'));
        $model = ORM::factory('User', Meerkat\User\Me::id());
        $form = \Meerkat\Form\Form::factory('status');
        $form->add_thumb($model, 'Аватар', 'logo');
        $form->add_static('<hr />');
        $form->add_thumb($model, 'Фотография в профиле');
        $form->add_actions_group()->add_submit('Сменить аватарки')->add_class('btn btn-lg btn-success');
        if ($form->get_element()->validate()) {
            $form->upload_thumb($model);
            $form->upload_thumb($model, 'logo');
            $this->redirect_msg_success('Иллюстрации обновлены');
        }
        \Meerkat\Core\Page_TplVar::instance()->set_body($form);
    }

}