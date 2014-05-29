<?php

use Meerkat\Core\Page_TplVar;
use Meerkat\Widget\Widget_Alert;
use Meerkat\Widget\Widget_Breadcrumbs;
use Meerkat\Core\Page_Layout;
use Meerkat\Core\Map;
use Meerkat\Form\Form;
use Meerkat\Html\Fieldset;
use Meerkat\Twig\Twig;

class Controller_Account_Logout extends Controller_Account {

    public function action_index() {
        Page_Layout::instance()->template(true);
        \Meerkat\Core\Seo::instance()->add_breadcrumb('Выход', '/logout');
        $form = Form::factory('logout');
        $form->add_hidden('s');
        $form->add_static('<div class="alert alert-warning">Для исключения автоматического разлогинивания выход возможен только после нажатия на кнопку</div>');
        $form->add_actions_group()->add_submit('s')->add_class('btn btn-primary btn-lg btn-lg btn-block')->set_label('Вы действительно хотите выйти?');
        if ($form->get_element()->validate()) {
            Auth::instance()->logout(true);
            Widget_Alert::factory('Вы вышли с проекта!')
                    ->as_info()
                    ->put();
            $this->redirect('/');
        }
        Page_TplVar::instance()->set_body($form->render());
    }

}