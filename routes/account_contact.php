<?php

    use \Meerkat\Widget\Widget_NavList;

    Route::set('account_contact', trim(Kohana::$config->load('meerkat/user.url.account_contact'), '/'))
        ->defaults(
            array(
                'directory'  => 'Account',
                'controller' => 'Contact',
                'action'     => 'index',
            )
        );
    Widget_NavList::instance()
        ->item(
            __('meerkat-user.menu.account'),
            'account_profile.contact`',
            __('Изменить контактные данные'),
            Kohana::$config->load('meerkat/user.url.account_contact')
        );
