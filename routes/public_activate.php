<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\User\Me;

    Route::set('public_activate', trim(Kohana::$config->load('meerkat/user.url.public_activate'), '/'))
        ->defaults(
            array(
                'directory'  => 'Public',
                'controller' => 'Activate',
                'action'     => 'index',
            )
        );

    //где-то в приложении делаем множество обработчиков события, реагирующих на него
    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller::before', function (sfEvent $event) {
        if (Me::id() && Me::activate_code()) {
            $disabled = true;
            foreach (Kohana::$config->load('meerkat/user.enable_no_confirm') as $url) {
                if (Arr::get($_SERVER, 'PHP_SELF') == Kohana::$config->load($url)) {
                    $disabled = false;
                    break;
                }
            }
            if ($disabled) {
                \Meerkat\Widget\Widget_Alert::factory('Требуется активация аккаунта')
                    ->as_info()
                    ->put();
                HTTP::redirect(URL::site(Kohana::$config->load('meerkat/user.url.public_activate').'?return='.Arr::get($_SERVER, 'REQUEST_URI'), 'http'), 302);
            }

        }
    });
