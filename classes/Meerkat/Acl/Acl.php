<?php

namespace Meerkat\Acl;

/**
 * Class Acl
 *
 * @package Meerkat\Core
 */
class Acl {

    protected $_model;

    static function factory($model) {
        if (($model instanceof \ORM)) {
            $class = '\Meerkat\Acl\\Acl_' . \Text::ucfirst($model->object_name(), '_');
        } else {
            $class = '\Meerkat\Acl\\Acl_' . $model;
        }
        if (!class_exists($class)) {
            $class = __CLASS__;
        }
        return new $class($model);
    }

    function __construct($model) {
        if (($model instanceof \ORM)) {
            $this->_model = $model;
        }
    }

    function only_admin() {
        return !\Meerkat\User\Me::is_admin() ? 'Действие доступно только администратору' : false;
    }

    /**
     * Возвращает причину отказа на доступ
     * Если причин нет - возвращает false, что означает наличие доступа
     * @param type $action
     * @return boolean
     */
    function can($action='manage') {
        $callback = array($this, 'can__' . $action);
        if (is_callable($callback)) {
            return call_user_func($callback);
        }
        //по умолчанию разрешаем все!
        return false;
    }

}