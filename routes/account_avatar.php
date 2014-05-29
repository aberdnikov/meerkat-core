<?php

    use Meerkat\User\Me;
    use Meerkat\Widget\Widget_Navbar_Top;
    use \Meerkat\Widget\Widget_NavList;

    if (Me::id()) {
        Route::set('account_avatar', trim(Kohana::$config->load('meerkat/user.url.account_avatar'), '/'))
            ->defaults(
                array(
                    'directory'  => 'Account',
                    'controller' => 'Avatar',
                    'action'     => 'index',
                )
            );
        Widget_NavList::instance()
            ->item(
                __('meerkat-user.menu.account'),
                'account_profile.account_avatar',
                __('Изменить аватар'),
                Kohana::$config->load('meerkat/user.url.account_avatar')
            );

    }
