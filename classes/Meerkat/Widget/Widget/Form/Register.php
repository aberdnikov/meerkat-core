<?php
    namespace Meerkat\Widget;
    use \Arr;
    use \Kohana;
    use \ORM;

    class Widget_Form_Register extends Widget {

       public $model;

        static function model($model) {
            self::instance()->model = $model;
        }

        /**
         * @return Widget_GeoCity
         */
        static function instance() {
            return parent::_instance();
        }

        static function form(){
            $widget = self::instance();
            $form         = \Meerkat\Form\Form::factory('register');
            $form->set_action(Kohana::$config->load('meerkat/user.url.public_register'));
            $email        = $widget->field_email($form);
            $login        = $widget->field_login($form);
            if (Kohana::$config->load('meerkat/user.url.public_users')) {
                $login->set_prepend('http://' . $_SERVER['HTTP_HOST'] . Kohana::$config->load('meerkat/user.url.public_users'));
            }
            $widget->field_submit($form);
            $event = new \sfEvent($form, 'Controller_Public_Register::form_build');
            \Meerkat\Event\Event::dispatcher()
                ->notify($event);
                return $form;
        }

        static function to_html($params = null) {
            $form         = self::form();
            return $form->render();
        }

        function field_email($form) {
            return $form
                ->add_email('email')
                ->set_label('Электронный адрес')
                ->rule_callback(array($this,
                    'check_email'), 'На проекте уже есть участник с таким электронным адресом')
                ->set_prepend(\Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_EMAIL))
                ->add_class('form-control')
                ->rule_required()
                ->set_desc('Обязательно указывайте свой почтовый ящик, к которому у вас есть доступ, так как на него придет письмо с паролем для входа');
        }

        function field_login($form) {
            return $form
                ->add_text('login')
                ->set_label('Логин')
                ->set_append(\Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER))
                ->add_class('form-control')
                ->set_example('groove')
                ->set_example('alexey')
                ->rule_callback(array($this,
                    'check_login'), 'На проекте уже есть участник с таким логином')
                ->set_desc('Допустимы латинские буквы, цифры, тире и знак подчеркивания. <br />Это ваш идентификатор на проекте, в дальнейшем его поменять будет нельзя')
                ->rule_regexp('/^[a-zA-Z0-9-]+$/', 'Допускаются только латинские буквы, цифры, тире и знак подчеркивания'
)


          ->rule_required();

        }

        function field_submit($form) {
            $gr = $form->add_actions_group();
            $gr
                ->add_submit('s')
                ->add_class('btn btn-success btn-lg btn-block')
                ->set_label('Создать аккаунт!');
            $links = array();
            $event = new \sfEvent(null, 'Controller_Public_Register::links');
            \Meerkat\Event\Event::dispatcher()->filter($event, $links);
            $links = $event->getReturnValue();
            if (count($links)) {
                $gr->add_static('<hr class="soften soften-sm">'. implode(' или ', $links));
            }

        }

        function check_email($email) {
          return $this->model->unique('email', $email);
        }

        function check_login($login) {
            return $this->model->unique('login', $login);
        }

    }