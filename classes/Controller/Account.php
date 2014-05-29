<?php

use Meerkat\Form\Form;

class Controller_Account extends Controller_Index {

    function before() {
        parent::before();
        if (!\Meerkat\User\Me::id()) {
            $this->redirect_msg_error('Требуется авторизация', Kohana::$config->load('meerkat/user.url.public_login').'?return=' . urldecode($_SERVER['PHP_SELF']));
        }
        Meerkat\Core\Seo::instance()->add_breadcrumb('Мой кабинет', Kohana::$config->load('meerkat/user.url.account'));
        Meerkat\Core\Page_Layout::instance()->template('!/layouts/account');
    }

    function action_index() {
        Meerkat\Core\Page_Layout::instance()->template(true);
    }


}
