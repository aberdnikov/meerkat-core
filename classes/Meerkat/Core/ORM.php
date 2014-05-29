<?php
    namespace Meerkat\Core;
    use \Debug, \Arr, \DB;

    class ORM extends \ORM {
        protected $_meerkat_owner_id_field_name = null;
        protected $_meerkat_model_type_id = null;
        protected $_track_changes = false;

        function track_changes($val = 1) {
            $this->_track_changes = (bool)$val;
        }

        protected function _initialize() {
            $_object_name                 = strtolower(substr(get_class($this), 6));
            $slot                         = \Meerkat\Slot\Slot_Meerkat_Object_Type::factory($_object_name)
                ->get();
            $this->_meerkat_model_type_id = Arr::get($slot, 'id');
            $this->_has_one['meerkat']    = array(
                'model'       => 'Meerkat_Object',
                'foreign_key' => 'meerkat_model_id',
                'condition'   => array(
                    array('{target}.meerkat_model_type_id',
                        '=',
                        DB::expr($this->_meerkat_model_type_id)
                    )
                ),
            );
            $this->_load_with[]           = 'meerkat';
            parent::_initialize();

        }

        public function get($column) {
            //Debug::stop($column);
            $ret = parent::get($column);
            if ($column == 'meerkat') {
                if (!$ret->id) {
                    //автосоздать ?
                    $ret = ORM::factory('Meerkat_Object')
                        ->get_or_create(array(
                            'meerkat_model_id'      => $this->pk(),
                            'meerkat_model_type_id' => $this->_meerkat_model_type_id,
                        ), $this->_meerkat_owner_id_field_name ? array(
                            'meerkat_owner_id' => $this->get($this->_meerkat_owner_id_field_name)
                        ) : array());
                }
            }
            return $ret;
        }


    }