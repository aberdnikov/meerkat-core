<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\Widget\Widget_NavList;
    use Meerkat\User\Me;

    Widget_NavList::instance()
        ->item(
            __('meerkat-user.menu.account'),
            'account_profile',
            __('Управление профилем'),
            Kohana::$config->load('meerkat/user.url.account')
        );

    if (Me::id()) {
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('user', \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER) . ' ' . Me::username(), Kohana::$config->load('meerkat/user.url.account'));
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('user.cabinet', \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER_HOME) . ' Кабинет ', Kohana::$config->load('meerkat/user.url.account'));
    }


    Route::set('account', trim(Kohana::$config->load('meerkat/user.url.account'), '/'))
        ->defaults(
            array(
                'controller' => 'Account',
                'action'     => 'index',
            )
        );
