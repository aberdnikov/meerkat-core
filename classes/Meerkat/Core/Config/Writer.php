<?php

namespace Meerkat\Core;

class Config_Writer extends \Kohana_Config_File_Reader implements \Kohana_Config_Writer {

    public function write($group, $key, $config) {
        $data = \Kohana::$config->reload($group)->as_array();
        \Arr::set_path($data, $key, $config);
        file_put_contents(DOMAINPATH . 'config/' . $group . EXT, '<?php' . PHP_EOL .'return '. var_export($data, true).';');
    }

}