<?php

    use Meerkat\User\Me;
    use Meerkat\Widget\Widget_Navbar_Top;

    if (Me::id()) {
        Route::set('account_logout', trim(Kohana::$config->load('meerkat/user.url.account_logout'), '/'))
            ->defaults(
                array(
                    'directory'  => 'Account',
                    'controller' => 'Logout',
                    'action'     => 'index',
                )
            );
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('user.exit', \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_DOOR_OUT)->setAttribute('title','Выход с проекта').' Выход', Kohana::$config->load('meerkat/user.url.account_logout'));
    }
