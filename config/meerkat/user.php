<?php
    return array(
        //ссылки на страницы модуля
        'url'               => array(
            'account'              => '/account/',
            'account_about'        => '/account/about/',
            'account_login'        => '/account/login/',
            'account_logout'       => '/account/logout/',
            'account_avatar'       => '/account/avatar/',
            'account_email'        => '/account/email/',
            'account_contact'      => '/account/contacts/',

            'public_login'         => '/account-login/',
            'public_register'      => '/account-register/',
            'public_lostpass'      => '/account-lostpass/',
            'public_lostpass_code' => '/account-lostpass-code/',
            'public_activate'      => '/account-activate/',

            'public_user'          => '/users/',

            'admin_user'           => Kohana::$config->load('meerkat/admin.url.admin') . 'users/',
        ),
        //показывать в верхнем меню
        'show'              => array(
            'public_login'    => true,
            'public_register' => true,
        ),
        //разрешить
        'can'               => array(
            //публичную регистрацию
            'public_register' => true,
            //смену логина
            'account_login'   => true,
        ),
        //страницы, разрешенные для посещения неподтвержденным пользователям
        'enable_no_confirm' => array(
            'meerkat/user.url.public_activate',
            'meerkat/user.url.account_logout',
        ),
        //требуется подтверждение email
        'require_confirm'   => 1,
        //заголовки писем
        'mail'              => array(
            'register'     => array(
                'user'  => Arr::get($_SERVER, 'HTTP_HOST') . ': регистрация на проекте',
                'admin' => Arr::get($_SERVER, 'HTTP_HOST') . ': новый пользователь',
            ),
            'email_change' => array(
                'user' => Arr::get($_SERVER, 'HTTP_HOST') . ': код активации для изменения e-mail',
            ),
            'lostpass'     => array(
                'user' => Arr::get($_SERVER, 'HTTP_HOST') . ': запрос на изменение пароля',
            ),
            'lostpass2'    => array(
                'user' => Arr::get($_SERVER, 'HTTP_HOST') . ': ваш новый пароль',
            ),
        ),
        'title'             => array(
            'account'         => 'Мой кабинет',
            'account_profile' => 'Управление профилем',
            'account_login'   => 'Изменить логин',
        )
    );