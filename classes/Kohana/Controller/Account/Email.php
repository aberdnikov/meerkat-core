<?php
    use \Meerkat\Html\Icon_Famfamfam;

    class Kohana_Controller_Account_Email extends Controller_Account {

        function check_code($code) {
            $model = ORM::factory('User', \Meerkat\User\Me::id());
            return ($model->loaded() && ($model->email_code == $code));
        }

        function check_email($email) {
            $model = ORM::factory('User', \Meerkat\User\Me::id());
            return $model->unique('email', $email);
        }

        function action_index() {
            Meerkat\Core\Seo::instance()
                ->add_breadcrumb('Изменить e-mail', Kohana::$config->load('meerkat/user.url.account_email'));
            $form = \Meerkat\Form\Form::factory('email');
            $form
                ->add_email('email')
                ->set_label('Введите новый e-mail')
                ->set_desc('Тот, на который вы хотите сменить текущий')
                ->add_class('form-control')//                ->set_append(Meerkat\Html\Icon::factory()->as_icon_fam_user())
                ->rule_callback(array($this,
                    'check_email'), 'На проекте уже есть участник с таким e-mail')
                //->set_desc('Его необходимо будет подтвердить')
                ->rule_required();
            Meerkat\StaticFiles\Js::instance()
                ->add_onload('
                $("[data-action=request_email_code]").click(function(){
                    $.getScript("' . Kohana::$config->load('meerkat/user.url.account_email') . 'code");
                    return false;
                });
            ');
            $form
                ->add_text('code')
                ->set_label('Код активации')
                ->set_desc('Если у вас его нет - получите его на почту &darr;&darr;&darr;')
                ->set_prepend(Icon_Famfamfam::icon(Icon_Famfamfam::_KEY))
                ->add_class('form-control')//                ->set_append(Meerkat\Html\Icon::factory()->as_icon_fam_user())
                //->set_desc('Его необходимо будет подтвердить')
                ->rule_callback(array($this,
                    'check_code'), 'Не верный код подтверждения')
                ->rule_required();

            $gr = $form->add_actions_group();
            $gr
                ->add_submit('Получить код активации', 'request')
                ->set_attribute('data-action', 'request_email_code')
                ->add_class('btn btn-info btn-lg');
            $gr
                ->add_submit('s')
                ->add_class('btn btn-success btn-lg')
                ->set_label('Сменить E-mail!');

            $f = $form->get_form();
            $form->init_values(array(
                'email' => \Meerkat\User\Me::email()
            ));
            if ($f->validate()) {
                $values            = $f->getValue();
                $email             = Arr::get($values, 'email');
                $model             = ORM::factory('User', \Meerkat\User\Me::id());
                $model->email      = $email;
                $model->email_code = '';
                $model->save();
                Auth::instance()->force_login(\Meerkat\User\Me::login());
                $this->redirect_msg_info('E-mail изменен');
            }
            \Meerkat\StaticFiles\File::need_lib('toastr');

            Meerkat\Core\Page_TplVar::instance()
                ->set_body($form);
        }

        function action_code() {
            $model             = ORM::factory('User', \Meerkat\User\Me::id());
            $email_code        = mb_substr(md5(microtime(true)), 12, 4);
            $model->email_code = $email_code;
            $model->save();
            //отправить письмо юзеру
            $tpl = Meerkat\Twig\Twig::from_template('!/mail/email_code_change');
            $tpl->set('username', \Meerkat\User\Me::username());
            $tpl->set('activate_code', $email_code);
            \Meerkat\Email\Email::send(\Meerkat\User\Me::email(), Kohana::$config->load('meerkat/user.mail.email_change.user'), $tpl->render());
            \Meerkat\Ajax\Ajax::response_javascript('toastr.info("Код активации был выслан на электронный адрес, указанный при регистрации!");');
        }

    }