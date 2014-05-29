<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\User\Me;
    use Meerkat\Twig\Twig;

    if (!Me::id()) {
        Widget_Navbar_Top::instance()
            ->map_right()
            ->add('register', __('Зарегистрироваться'), Kohana::$config->load('meerkat/user.url.public_register'));
        \Meerkat\Route\Route::factory(Kohana::$config->load('meerkat/user.url.public_register'))
            ->controller('Register')
            ->directory('Public')
            ->with_item(false)
            ->put();
    }

    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller_Public_Login::links', function (sfEvent $event, $links) {
            $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_register'), __('Зарегистрироваться'));
            return $links;
        });

    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller_Public_Lostpass::links', function (sfEvent $event, $links) {
            $links[] = HTML::anchor(Kohana::$config->load('meerkat/user.url.public_register'), __('Зарегистрироваться'));
            return $links;
        });

    \Meerkat\Event\Event::dispatcher()
        ->connect('Controller_Public_Register::done', function (sfEvent $event) {
            $params        = $event->getParameters();
            $user          = Arr::get($params, 'user');
            $email         = $user->email;
            $login         = $user->login;
            $password      = Arr::get($params, 'password');
            $activate_code = Arr::get($params, 'activate_code');
            //отправить письмо юзеру
            $tpl = Twig::from_template('!/mail/register_to_user');
            $tpl->set('username', $login);
            $tpl->set('email', $email);
            $tpl->set('password', $password);
            if (Kohana::$config->load('meerkat/user.require_confirm')) {
                $tpl->set('activate_code', $activate_code);
            }
            \Meerkat\Email\Email::send($email, Kohana::$config->load('meerkat/user.mail.register.user'), $tpl->render());
            //отправить письмо админу
            $tpl = Twig::from_template('!/mail/register_to_admin');
            $tpl->set('username', $login);
            $tpl->set('email', $email);
            $tpl->set('password', $password);
            if (Kohana::$config->load('meerkat/user.require_confirm')) {
                $tpl->set('activate_code', $activate_code);
            }
            \Meerkat\Email\Email::send($email, Kohana::$config->load('meerkat/user.mail.register.admin'), $tpl->render());
        });
