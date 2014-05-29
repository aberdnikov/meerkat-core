<?php

    class Controller_Public_Users extends Controller_Index {


        protected $base_url;

        public function action_item() {
            $model = ORM::factory('User')
                ->where('login', '=', Request::current()
                    ->param('id'))
                ->find();
            if (!$model->loaded()) {
                throw new HTTP_Exception_404('Такой пользователь на проекте отсутствует');
            }
            Meerkat\Core\Seo::instance()
                ->add_breadcrumb($model->username . ' [@' . $model->login . ']', $this->base_url . $model->login);
            \Meerkat\Core\Page_Layout::instance()
                ->template(true);
            \Meerkat\Core\Page_TplVar::instance()
                ->set('user', $model);
        }

        public function action_index() {
            \Meerkat\Core\Page_TplVar::instance()
                ->set('base_url', $this->base_url);
            \Meerkat\Core\Page_Layout::instance()
                ->template(true);
            $per_page = 25;
            \Meerkat\Core\Page_TplVar::instance()
                ->set('per_page', $per_page);
            $users   = ORM::factory('User')
                ->find_all($per_page);
            $cnt_all = Database::instance()
                ->count_last_query();
            \Meerkat\Core\Page_TplVar::instance()
                ->set('cnt_all', $cnt_all);
            \Meerkat\Core\Page_TplVar::instance()
                ->set('users', $users);
            $event = new \sfEvent(null, 'User::links');
            $links = array();
            \Meerkat\Event\Event::dispatcher()
                ->filter($event, $links);
            $links = $event->getReturnValue();
            \Meerkat\Core\Page_TplVar::instance()
                ->set('links', $links);
            //{{ widget('Paginator',{per_page:per_page,cnt_all:cnt_all,url:base_url})|raw }}
        }

        public function before() {
            parent::before();
            $this->base_url = Kohana::$config->load('meerkat/user.url.public_users');
            Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Пользователи проекта', $this->base_url);
        }

    }