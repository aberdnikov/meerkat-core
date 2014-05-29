<?php
    namespace Meerkat\ListView;
    use Meerkat\Html\Span;
    use Meerkat\ViewItem\ViewItem;

    class Kohana_ListView_User extends ListView {
        protected $fields = array(
            'thumb_small_logo',
            'email',
            'username',
        );

        function __construct($model) {
            parent::__construct($model);
            $this
                ->setBaseUrl(\Kohana::$config->load('meerkat/user.url.admin_users'))
                ->setFieldCallback('logi1n', function ($value, $item) {
                    return '@' . $value;
                });
        }

        function callbackAction__is_admin($item) {
            return ViewItem::factory($item)->get('is_admin');
        }

        function callbackAction__is_ban($item) {
            return ViewItem::factory($item)->get('is_ban');
        }
    }