<?php

    namespace Meerkat\Widget;

    class Widget_Meerkat_Rating extends Widget {

        /**
         * @return Widget_Ulogin
         */
        static function instance() {
            return self::_instance();
        }

        static function to_html($params = array()) {
            \Meerkat\StaticFiles\Css::instance()->add_inline('
                .objrate .objrate_plus{
                    width:10%;
                    float:left;
                    text-align:right;
                }
                .objrate .objrate_minus{
                    width:10%;
                    float:left;
                    text-align:left;
                }
                .objrate .objrate_value{
                    width:80%;
                    text-align:center;
                    float:left;
                    color:#777;
                }
                .objrate .progress{
                    margin-bottom:3px;
                }
                .objrate{
                    width:100px;
                }
            ');
            return self::get_template()->set('object', $params)->render();
        }

    }