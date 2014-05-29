<?php
    namespace Meerkat\CRUD;
    class CRUD_Action_Index extends CRUD_Action {
        function execute(){
            $this->item();
            $list = \Meerkat\ListView\ListView::factory($this->model);
            if ($actions) {
                $list->setActions($actions);
            }
            if ($fields) {
                $list->setFields($fields);
            }
            $list->setBaseUrl($this->base_url);
            Page_TplVar::instance()
                ->set_body($list);
        }
    }