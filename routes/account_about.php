<?php

    use \Meerkat\Widget\Widget_NavList;

    Route::set('account_about', trim(Kohana::$config->load('meerkat/user.url.account_about'), '/'))
        ->defaults(
            array(
                'directory'  => 'Account',
                'controller' => 'About',
                'action'     => 'index',
            )
        );
    Widget_NavList::instance()
        ->item(
            __('meerkat-user.menu.account'),
            'account_profile.about`',
            __('Изменить подробности о себе'),
            Kohana::$config->load('meerkat/user.url.account_about')
        );
