<?php
    namespace Meerkat\CRUD;
    class CRUD_Action {
        abstract function factory(CRUD $crud) {
        }

        abstract function execute() {
        }
    }