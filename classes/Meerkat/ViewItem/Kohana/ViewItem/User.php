<?php

    namespace Meerkat\ViewItem;

    use Meerkat\StaticFiles\Js;
    use \Kohana;

    class Kohana_ViewItem_User extends \Meerkat\ViewItem\ViewItem {

        function field__email() {
            $email      = $this->model->email;
            $url        = $email;
            $safe_email = $safe_url = '';
            for ($i = 0; $i < mb_strlen($email); $i++) {
                $safe_email .= '&#' . ord(mb_substr($email, $i, 1)) . ';';
            }
            for ($i = 0; $i < mb_strlen($url); $i++) {
                $safe_url .= '&#' . ord(mb_substr($url, $i, 1)) . ';';
            }
            return '<i class="iconfam-email"></i> ' . \Meerkat\Html\A::factory()
                ->set_content($safe_email)
                ->set_href("mailto: " . $safe_url);
        }

        function field__login() {
            return '@' . $this->model->login;
        }

        function field__regdate() {
            $diff = \Meerkat\Helper\Helper_Date::diff_datetime($this->model->regdate, null, 'years,months,days');
            if (!$diff) {
                $diff = 'сегодня';
            }
            $ret = \Meerkat\Html\Abbr::factory()
                ->set_content($diff)
                ->set_title(\Meerkat\Helper\Helper_Date::from_datetime($this->model->regdate));
            return $ret;
        }

        function field__is_ban() {
            $ret = \Meerkat\Html\Span::factory()
                ->addClass('label');
            if ($this->model->is_ban) {
                $ret
                    ->set_content('Забанен')
                    ->addClass('label-danger');
            }
            else {
                $ret
                    ->set_content('Активен')
                    ->addClass('label-info');
            }
            //if (!\Meerkat\Acl\Acl::factory($this->model)
            //    ->can('is_ban')
            if (\Meerkat\User\Me::is_admin()) {
                Js::instance()
                    ->add_onload('
            jQuery(document).on("click","[data-model=' . $this->model->object_name() . '][data-action=is_ban]",function(){
                jQuery.getScript("' . Kohana::$config->load('meerkat/admin.url.admin') . 'users/is_ban?id="+jQuery(this).attr("data-id"));
            });', __CLASS__ . 'is_ban');
                $ret
                    ->setAttribute('data-id', $this->model->pk())
                    ->setAttribute('data-action', 'is_ban')
                    ->setAttribute('data-model', $this->model->object_name())
                    ->addClass('pointer');
            }
            return $ret;
        }

        function field__is_man() {
            return $this->field__gender_icon() . ' ' . $this->field__gender_text();
        }

        function field__gender_icon() {
            switch ($this->model->is_man) {
                case 1:
                    return \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER);
                    break;
                case -1:
                    return \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER_FEMALE);
                    break;
                default:
                    return \Meerkat\Html\Icon_Famfamfam::icon(\Meerkat\Html\Icon_Famfamfam::_USER_BROWN);
                    break;
            }
        }

        function field__gender_text() {
            switch ($this->model->is_man) {
                case 1:
                    return '<span class="label label-info">мужчина</span>';
                    break;
                case -1:
                    return '<span class="label label-pink">женщина</span>';
                    break;
                default:
                    return '<span class="label label-default">не определился</span>';
                    break;
            }
        }

        function field__is_admin() {
            $ret = \Meerkat\Html\Span::factory()
                ->addClass('label')
                ->set_content('АДМ');
            if ($this->model->is_admin) {
                $ret->addClass('label-success');
            } else {
                $ret->addClass('label-default');
            }
            if (!\Meerkat\Acl\Acl::factory($this->model)
                ->can('is_admin')
            ) {
                if ($this->model->is_admin) {
                    $ret->setAttribute('title', 'Снять права админа');
                }
                else {
                    $ret->setAttribute('title', 'Выдать права админа');
                }
                Js::instance()
                    ->add_onload('
            jQuery(document).on("click","[data-model=' . $this->model->object_name() . '][data-action=is_admin]",function(){
                jQuery.getScript("' . Kohana::$config->load('meerkat/admin.url.admin') . 'users/is_admin?id="+jQuery(this).attr("data-id"));
            });', __CLASS__ . 'is_admin');
                $ret
                    ->setAttribute('data-id', $this->model->pk())
                    ->setAttribute('data-action', 'is_admin')
                    ->setAttribute('data-model', $this->model->object_name())
                    ->addClass('pointer');
            }
            return \Meerkat\Html\Div::factory()
                ->set_content($ret);
        }

        function field__last_login() {
            if (!$this->model->last_login) {
                return 'никогда';
            }
            $diff = \Meerkat\Helper\Helper_Date::diff_datetime($this->model->last_login, null, 'years,months,days, hours,minutes');
            if (!$diff) {
                $diff = 'сегодня';
            }
            $ret = \Meerkat\Html\Abbr::factory()
                ->set_content($diff)
                ->set_title(\Meerkat\Helper\Helper_Date::from_datetime($this->model->last_login));
            return $ret;
        }

        function __toString() {
            if (!$this->model->pk()) {
                return '';
            }
            return '<a title="' . $this->model->username . '" href="/users/' . $this->model->login . '">' . $this->field__gender_icon() . '</i> ' . $this->model->username . '</a>';
        }

        function initFields() {
            parent::initFields();
            $k = array_search('password', $this->fields);
            unset($this->fields[$k]);
            $k = array_search('activate_code', $this->fields);
            unset($this->fields[$k]);
            $k = array_search('lostpass_code', $this->fields);
            unset($this->fields[$k]);
            $k = array_search('email_code', $this->fields);
            unset($this->fields[$k]);
        }

        function field__thumb() {
            return $this->thumb('list', 'logo');
        }

        function field__thumb_small_logo() {
            return $this->thumb('small', array(), 'logo');
        }

        function field__thumb_medium_logo() {
            return $this->thumb('medium', array(), 'logo');
        }

        function field__thumb_big_logo() {
            return $this->thumb('big', array(), 'logo');
        }


    }