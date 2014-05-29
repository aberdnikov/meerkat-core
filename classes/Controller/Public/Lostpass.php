<?php

    use Meerkat\Html\Icon_Famfamfam;

    class Controller_Public_Lostpass extends Controller_Index {

        function action_index() {
            $model  = ORM::factory('User', Meerkat\User\Me::id());
            $form   = Meerkat\Form\Form::factory('lostpass');
            $_email = $form
                ->add_email('email')
                ->rule_required()
                ->add_class('form-control')
                ->set_label('Email')
                ->set_desc('Введите E-mail, под которым Вы зарегистрированы на проекте');
            $gr     = $form->add_actions_group();
            $gr
                ->add_submit('Запросить инструкции по смене пароля на почту')
                ->add_class('btn btn-primary btn-lg btn-block');
            $event = new \sfEvent(null, 'Controller_Public_Lostpass::links');
            $links = array();
            \Meerkat\Event\Event::dispatcher()
                ->filter($event, $links);
            $links = $event->getReturnValue();
            if (count($links)) {
                $gr->add_static('<hr class="soften soften-sm">' . implode(' или ', $links));
            }

            $f = $form->get_form();
            if ($f->validate()) {
                $email = Arr::get($_POST, 'email');
                $user  = ORM::factory('User');
                $field = Valid::email($email) ? 'email' : 'login';
                $user
                    ->where($field, '=', $email)
                    ->find();
                if (!$user->loaded()) {
                    $_email->set_error('Нет такого пользователя на проекте');
                }
                else {
                    $lostpass_code       = mb_substr(md5(microtime(true)), 5, 6);
                    $user->lostpass_code = $lostpass_code;
                    $user->save();
                    //отправим запрос на смену пароля
                    $tpl        = \Meerkat\Twig\Twig::from_template('!/mail/account_lostpass/user');
                    $tpl->email = $user->email;
                    $tpl->code  = $lostpass_code;
                    Meerkat\Email\Email::send($user->email, Kohana::$config->load('meerkat/user.mail.lostpass.user'), $tpl);
                    $this->redirect_msg_info('Запрос отправлен', Kohana::$config->load('meerkat/user.url.public_lostpass_code'));
                }
            }
            Meerkat\Core\Page_TplVar::instance()
                ->set_body($form);
        }

        function before() {
            parent::before();
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Запросить новый пароль', Kohana::$config->load('meerkat/user.url.public_lostpass'));
            Meerkat\Core\Page_Layout::instance()
                ->template('!/layouts/offset3_lg6');
        }

        function action_code() {
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Подтверждение смены пароля', Kohana::$config->load('meerkat/user.url.public_lostpass_code'));
            $model  = ORM::factory('User', Meerkat\User\Me::id());
            $form   = Meerkat\Form\Form::factory('lostpass');
            $_email = $form
                ->add_email('m')
                ->rule_required()
                ->add_class('form-control')
                ->set_label('Email')
                ->set_desc('Введите E-mail, под которым Вы зарегистрированы на проекте');
            $_code  = $form
                ->add_text('c')
                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_LOCK))
                ->rule_required()
                ->set_label('Код')
                ->add_class('form-control')
                ->set_desc('Для того, чтобы ваш пароль могли изменить только вы - введите код, который был выслан на вашу почту в предыдущем шаге');
            $form
                ->add_submit('Вышлите мне новый пароль')
                ->add_class('btn btn-primary btn-lg');
            $form->init_values($_GET);
            $f = $form->get_form();
            if ($f->validate()) {
                $email = Arr::get($_POST, 'm');
                $code  = Arr::get($_POST, 'c');
                $user  = ORM::factory('User');
                $field = Valid::email($email) ? 'email' : 'login';
                $user
                    ->where($field, '=', $email)
                    ->find();
                if (!$user->loaded()) {
                    $_email->set_error('Нет такого пользователя на проекте');
                }
                else {
                    if (!$user->lostpass_code) {
                        $this->redirect_msg_info('Этот пользователь не запрашивал пароля', Kohana::$config->load('meerkat/user.url.public_lostpass'));
                    }
                    if ($user->lostpass_code != $code) {
                        $_code->set_error('Не правильный код');
                    }
                    else {
                        $pass                = mb_substr(md5(microtime(true)), 5, 6);
                        $user->password      = $pass;
                        $user->lostpass_code = '';
                        $user->save();
                        //отправим письмо с новым паролем
                        $tpl           = \Meerkat\Twig\Twig::from_template('!/mail/account_lostpass2/user');
                        $tpl->password = $pass;
                        Meerkat\Email\Email::send($user->email, Kohana::$config->load('meerkat/user.mail.lostpass2.user'), $tpl);
                        $this->redirect_msg_info('Ваш пароль изменен и выслан вам на почту', Kohana::$config->load('meerkat/user.url.public_login'));
                    }
                }
            }
            Meerkat\Core\Page_TplVar::instance()
                ->set_body($form);
        }

    }