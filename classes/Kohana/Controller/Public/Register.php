<?php

    use Meerkat\Core\Page_TplVar;
    use Meerkat\Widget\Widget_Alert;
    use Meerkat\Widget\Widget_Breadcrumbs;
    use Meerkat\Core\Page_Layout;
    use Meerkat\Core\Map;
    use Meerkat\Form\Form;
    use Meerkat\Html\Fieldset;
    use Meerkat\Twig\Twig;


    class Kohana_Controller_Public_Register extends Controller_Index {

        public function action_index() {
            Meerkat\Core\Page_Layout::instance()
                ->template(true);
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Регистрация', Kohana::$config->load('meerkat/user.url.public_register'));
            Meerkat\Widget\Widget_Form_Register::model(ORM::factory('User'));
            $form         = Meerkat\Widget\Widget_Form_Register::form();
            if ($form
                ->get_element()
                ->validate()
            ) {
                $this->save($form);
            }
            Page_TplVar::instance()
                ->set_body($form->render());
        }

        function save($form) {
            $values              = $form
                ->get_element()
                ->getValue();
            $login               = Arr::get($values, 'login');
            $email               = Arr::get($values, 'email');
            $user                = ORM::factory('User');
            $user->email         = $email;
            $user->username      = $login;
            $user->login         = $login;
            $user->regdate       = date('Y-m-d H:i:s');
            $password            = mb_substr(md5(microtime(true)), 3, 6);
            $activate_code       = mb_substr(md5(microtime(true)), 12, 4);
            $user->activate_code = $activate_code;
            $user->password      = $password;
            $user->save();
            $event = new \sfEvent(null, 'Controller_Public_Register::done', array(
                'user'          => $user,
                'password'      => $password,
                'activate_code' => $activate_code,
            ));
            \Meerkat\Event\Event::dispatcher()
                ->notify($event);
            //если не надо подтверждать мыло - авторизуем юзера и кинем его в кабинет
            if (!Kohana::$config->load('meerkat/user.require_confirm')) {
                Auth::instance()
                    ->force_login($login);
                $this->redirect_msg_success('Вы успешно зарегистрировались', Kohana::$config->load('meerkat/user.url.account'));
            }
            else {
                $this->redirect_msg_success('Вы успешно зарегистрировались, получите почту на указанный при регистрации почтовый ящик - туда был отправлен код активации аккаунта', Kohana::$config->load('meerkat/user.url.public_activate'));
            }
        }

    }