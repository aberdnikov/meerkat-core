<?php

    use Meerkat\User\Me;
    use \Meerkat\Widget\Widget_NavList;

    if ('id' . Me::id() == Me::login()) {
        Route::set('account_login', trim(Kohana::$config->load('meerkat/user.url.account_login'), '/'))
            ->defaults(
                array(
                    'directory'  => 'Account',
                    'controller' => 'Login',
                    'action'     => 'index',
                )
            );
        Widget_NavList::instance()
            ->item(
                __('meerkat-user.menu.account'),
                'account_profile.account_login',
                __('Изменить логин'),
                Kohana::$config->load('meerkat/user.url.account_login')
            );
    }