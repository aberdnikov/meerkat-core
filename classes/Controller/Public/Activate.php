<?php

    use Meerkat\Core\Page_TplVar;
    use Meerkat\Widget\Widget_Alert;
    use Meerkat\Core\Page_Layout;
    use Meerkat\Core\Map;
    use Meerkat\Form\Form;
    use Meerkat\Html\Fieldset;
    use Meerkat\Html\Icon_Famfamfam;
    use Meerkat\Twig\Twig;


    class Controller_Public_Activate extends Controller_Index {

        public function action_index() {
            if ($ret = Arr::get($_GET, 'return')) {
                Session::instance()
                    ->set('return', $ret);
            }
            Page_Layout::instance()
                ->template('!/layouts/offset3_lg6');
            \Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Активация аккаунта', Kohana::$config->load('meerkat/user.url.public_login'));
            $form = Form::factory('login');
            //$form->set_layout(Form::LAYOUT_HORIZONTAL);
            $form
                ->add_text('m')
                ->set_label('Электронный адрес/логин')
                ->rule_callback(function ($val) {
                    $model = ORM::factory('User');
                    $model
                        ->where($model->unique_key($val), '=', $val)
                        ->where('activate_code', '=', Arr::get($_POST, 'c'))
                        ->find();
                    return $model->loaded();
                }, 'Аккаунт не требует активации')

                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_EMAIL))
                ->set_append(Icon_Famfamfam::icon(Icon_Famfamfam::_USER))
                ->add_class('form-control')
                ->rule_required();
            $pass = $form
                ->add_password('c')
                ->add_class('form-control')
                ->set_label('Код активации')
                ->set_desc('Был прислан в письме при регистрации')
                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_KEY))
                ->rule_required();

            $gr = $form->add_actions_group();
            $gr
                ->add_submit('s')
                ->add_class('btn btn-primary btn-lg btn-block')
                ->set_label('Активировать');
            if(\Meerkat\User\Me::id()){
                $_GET['m'] = \Meerkat\User\Me::email();
            }
            if(count($_GET)){
                $form->init_values($_GET);
            }

            if ($form
                ->get_element()
                ->validate()
            ) {
                $model = ORM::factory('User');
                $model
                    ->where($model->unique_key(Arr::get($_POST, 'm')), '=', Arr::get($_POST, 'm'))
                    ->find();
                $model->activate_code = null;
                $model->save();
                if(\Meerkat\User\Me::id()){
                    //перелогинимся
                    Auth::instance()->force_login(\Meerkat\User\Me::login());
                }
                $this->redirect_msg_success('Ваш аккаунт успешно активирован', Session::instance()->get_once('return', '/'));
            }
            Page_TplVar::instance()
                ->set_body($form->render());
        }

    }