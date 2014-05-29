<?php

    class Controller_Admin extends Controller_Index {

        function action_index() {
            Meerkat\Core\Page_Layout::instance()
                ->template(true);
            \Meerkat\Core\Page_TplVar::instance()->set_body(\Meerkat\Html\Icon_Famfamfam::all_icons());
        }

        function before() {
            parent::before();
            if (!\Meerkat\User\Me::id()) {
                Meerkat\Widget\Widget_Alert::factory('Требуется авторизация')
                    ->as_error()
                    ->put();
                $this->redirect(Kohana::$config->load('meerkat/user.url.public_login'));
            }
            if (!\Meerkat\User\Me::is_admin()) {
                Meerkat\Widget\Widget_Alert::factory('Вы не являетесь администратором')
                    ->as_error()
                    ->put();
                $this->redirect('/');
            }
            Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Система администрирования', Kohana::$config->load('meerkat/admin.url.admin'));
            Meerkat\Core\Page_Layout::instance()
                ->template('!/layouts/admin');
        }

    }
