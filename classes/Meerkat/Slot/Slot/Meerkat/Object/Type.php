<?php
    namespace Meerkat\Slot;
    class Slot_Meerkat_Object_Type extends Slot {

        protected $_lifetime = 3600;

        function load() {
            $ret = \ORM::factory('Meerkat_Object_Type')
                ->get_or_create(
                array(
                    'code' => $this->_id
                )
            )
                ->as_array();
            return $ret;
        }

    }