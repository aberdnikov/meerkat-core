<?php
    namespace Meerkat\Widget;
    use \Arr;
    use \Kohana;
    use \ORM;
    use \Meerkat\Html\Icon_Famfamfam;

    class Widget_Form_Login extends Widget {

        /**
         * @return Widget_GeoCity
         */
        static function instance() {
            return parent::_instance();
        }

        static function form(){
            $form         = \Meerkat\Form\Form::factory('login');
            $form->set_action(Kohana::$config->load('meerkat/user.url.public_login'));
            //$form->set_layout(Form::LAYOUT_HORIZONTAL);
            $form
                ->add_text('login')
                ->set_label('Электронный адрес/логин')
                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_EMAIL))
                ->add_class('form-control')//                ->set_append(Meerkat\Html\Icon::factory()->as_icon_fam_user())
                //->set_desc('Его необходимо будет подтвердить')
                ->rule_required();
            $pass = $form
                ->add_password('pass')
                ->add_class('form-control')
                ->set_label('Пароль')
                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_KEY))
                ->rule_required();

            $gr = $form->add_actions_group();
            $gr
                ->add_submit('s')
                ->add_class('btn btn-primary btn-lg btn-block')
                ->set_label('Войти');
            $links = array();
            $event = new \sfEvent(null, 'Controller_Public_Login::links');
            \Meerkat\Event\Event::dispatcher()
                ->filter($event, $links);
            $links = $event->getReturnValue();
            if (count($links)) {
                $gr->add_static('<hr class="soften soften-sm">' . implode(' или ', $links));
            }
                return $form;
        }

        static function to_html($params = null) {
            $form         = self::form();
            return $form->render();
        }

    }