<?php

    use Meerkat\Widget\Widget_Navbar_Top;
    use Meerkat\Widget\Widget_NavList;
    use Meerkat\Widget\Widget_Breadcrumbs;
    use Meerkat\Widget\Widget_Seo_Title;
    use Meerkat\User\Me;
    use Meerkat\Core\Page_Layout;
    use Meerkat\Core\Page_TplVar;

    defined('SYSPATH') or die('No direct script access.');

    class Kohana_Controller_Admin_User extends Controller_Admin {

        /**
         * @var \Meerkat\CRUD\CRUD
         */
        protected $crud;

        public function action_item() {
            $viewitem = $this->crud->actionItem();
            Page_TplVar::instance()
                ->set_body($viewitem
                    ->card());
        }

        function flag($field, $on = 'Yes', $off = 'No', $is_reverse = false) {
            $id    = intval(Arr::get($_GET, 'id', 0));
            $model = ORM::factory('User', $id);
            if ($model->loaded()) {
                if (($reason = \Meerkat\Acl\Acl::factory($model)
                    ->can($field))
                ) {
                    print 'toastr.error("' . Meerkat\Ajax\Ajax::html_prepare($reason) . '");';
                    exit;
                }
                $model->$field = (int)!$model->$field;
                $model->save();
                $model->reload();
                $view = Meerkat\ViewItem\ViewItem::factory($model)
                    ->get($field);
                print 'jQuery("[data-action=' . $field . '][data-model=' . $model->object_name() . '][data-id=' . $id . ']").parent().html("' . Meerkat\Ajax\Ajax::html_prepare($view) . '");';
                print 'toastr.success("Операция успешно выполнена");';
                exit;
            }
            exit;
        }

        function action_is_ban() {
            $this->flag('is_ban', 'Забанен', 'Активен', 1);
        }

        function action_is_admin() {
            $this->flag('is_admin', 'Администратор', 'Администратор');
        }


        function action_is_medresp() {
            $this->flag('is_medresp');
        }

        public function action_edit() {
            $this->crud->actionEdit(
                array(
                    'username',
                    'login',
                    'email',
                )
            );
        }

        public function action_delete() {
            $this->crud->actionDelete();
        }

        function action_add() {
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Регистрация нового пользователя', '/register');
            $form = Meerkat\Form\Form::factory('register');
            $f    = $form->add_fieldset('Регистрация нового пользователя');
            $f
                ->add_email('email')
                ->set_label('Электронный адрес')
                ->add_class('form-control')
                ->set_desc('Его необходимо будет подтвердить')
                ->rule_callback('Meerkat\ORMForm\Kohana_ORMForm_User::check_email', 'Не уникально', \HTML_QuickForm2_Rule::SERVER)
                ->rule_required();
            $f
                ->add_text('username')
                ->add_class('form-control')
                ->set_label('ФИО')
                //->set_prepend(Meerkat\Html\Icon::factory()->as_icon_fam_user())
                ->rule_required();
            $f
                ->add_text('login')
                ->add_class('form-control')
                ->set_label('Логин')
                ->rule_callback(function ($login) {
                    return \Meerkat\ORMForm\ORMForm_User::check_login($login, Me::id());
                }, 'На проекте уже есть участник с таким логином')
                ->rule_maxlength(255, 'Не длинее 255 символов')
                ->set_append(\Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER))
                ->set_example('ivanov')
                ->set_example('alexey')
                ->set_example('nickname')
                ->rule_required()
                ->filter_xss()
                ->set_example('nissan')
                ->set_desc('Допустимы латинские буквы, цифры, тире и знак подчеркивания. ')
                ->rule_regexp('/^[a-z0-9-]+$/', 'Допускаются только латинские буквы, цифры, тире и знак подчеркивания');
            $gr = $f
                ->add_group()
                ->add_class('group-inline');
            $gr
                ->add_checkbox('is_admin')
                ->set_label('Администратор?');
            $form
                ->add_actions_group()
                ->add_submit('s')
                ->add_class('btn btn-primary btn-lg')
                ->set_label('Зарегистрировать');
            if ($form
                ->get_element()
                ->validate()
            ) {
                $values = $form
                    ->get_element()
                    ->getValue();
                $user   = ORM::factory('User');
                $user->values($values);
                $user->regdate       = date('Y-m-d H:i:s');
                $user->username      = Arr::get($values, 'username');
                $password            = substr(md5(microtime(true)), 3, 6);
                $user->password      = $password;
                $user->is_ban        = 0;
                $user->is_admin      = Arr::get($_POST, 'is_admin');
                $user->save();
                $message = Meerkat\Twig\Twig::from_template('!/mail/register_by_admin')
                    ->set($values)
                    ->set('password', $password)
                    ->render();
                Meerkat\Email\Email::send($user->email, Arr::get($_SERVER,'HTTP_HOST'). ': Приглашение на проект ' . $_SERVER['HTTP_HOST'], $message);
                $this->redirect_msg_success('Пользователь зарегистрирован, приглашение уже ушло на его почту', $this->base_url);
            }
            Page_TplVar::instance()
                ->set_body($form->render());
        }

        public function action_index() {
            $list = $this->crud->actionIndex();
            Page_TplVar::instance()
                ->set_body($list);
        }


        public function before() {
            parent::before();
            $this->crud = Meerkat\CRUD\CRUD::factory('User');
            $this->crud->setBaseUrl(Kohana::$config->load('meerkat/user.url.admin_user'));
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Пользователи проекта', $this->crud->getBaseUrl());
        }
    }