<?php
    namespace Meerkat\Html;
    use Meerkat\Core\Meerkat;

    class Icon {

        protected static $lib = null;
        protected static $icon_class = '';

        static function all_icons() {
            $table = \Meerkat\Html\Table::factory()->addClass('table table-condensed');
            $i=0;
            foreach(self::all_classes() as $class){
                if(!($i%3)){
                    $tr = $table->add_row();
                }
                $i++;
                $content = sprintf('<nobr>%s %s</nobr>', static::icon($class),$class);
                $tr->add_cell()->set_content($content);
            }
            return $table;
        }

        static function all_classes() {
            $oClass = new \ReflectionClass (get_called_class());
            return array_values($oClass->getConstants());
        }

        /**
         * @param      $class
         * @param null $attr
         * @return \Meerkat\Html\I
         */
        static function icon($class) {
            \Meerkat\StaticFiles\File::need_lib(static::$lib);
            return \Meerkat\Html\I::factory()
                ->addClass(static::$icon_class . $class);
        }
    }