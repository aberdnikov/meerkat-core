<?php

use Meerkat\Core\Page_TplVar;
use Meerkat\Widget\Widget_Alert;
use Meerkat\Core\Page_Layout;
use Meerkat\Core\Map;
use Meerkat\Form\Form;
use Meerkat\Html\Fieldset;
use Meerkat\Html\Icon_Famfamfam;
use Meerkat\Twig\Twig;


class Controller_Public_Login extends Controller_Index {

    public function action_index() {
        if (\Meerkat\User\Me::id()) {
            Meerkat\Widget\Widget_Alert::factory('Вы уже авторизованы')
                ->as_error()
                ->put();
            $this->redirect('/');
        }

        if ($ret = Arr::get($_GET, 'return')) {
            Session::instance()
                ->set('return', $ret);
        }
        Page_Layout::instance()
            ->template(true);
        \Meerkat\Core\Seo::instance()
            ->add_breadcrumb('Вход', Kohana::$config->load('meerkat/user.url.public_login'));
        $form         = Meerkat\Widget\Widget_Form_Login::form();
        if ($form
            ->get_element()
            ->validate()
        ) {
            if (Auth::instance()
                ->login(Arr::get($_POST, 'login'), Arr::get($_POST, 'pass'), 1)
            ) {
                $this->redirect(Session::instance()
                    ->get_once('return', '/'));
            }
            else {
                //$pass->set_error('Не верный пароль или email');
            }
        }
        Page_TplVar::instance()
            ->set_body($form->render());
    }

}