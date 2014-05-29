<?php

    use Meerkat\User\Me;
    use Meerkat\Widget\Widget_Navbar_Top;
    use \Meerkat\Widget\Widget_NavList;

    if (Me::id()) {
        \Meerkat\Route\Route::factory(Kohana::$config->load('meerkat/user.url.account_email'))
            ->controller('Email')
            ->directory('Account')
            ->with_item(false)
            ->put();
        Widget_NavList::instance()
            ->item(
                __('meerkat-user.menu.account'),
                'account_profile.account_email',
                __('Изменить E-mail'),
                Kohana::$config->load('meerkat/user.url.account_email')
            );

    }
