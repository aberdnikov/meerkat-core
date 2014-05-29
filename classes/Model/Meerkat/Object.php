<?php

use Meerkat\User\Me;

class Model_Meerkat_Object extends ORM {

    function meta_keywords($meta_keywords) {
        //удалить старые кейворды
        $keywords = ORM::factory('Meerkat_Object_Keyword')
                ->where('meerkat_id', '=', $this->pk())
                ->where('usr_id', '=', Me::id())
                ->find_all();
        foreach ($keywords as $keyword) {
            $keyword->delete();
        }
        //добавить новые
        $keywords = explode(',', $meta_keywords);
        foreach ($keywords as $keyword) {
            
        }
        //если ключевики правил владелец - 
        if ($this->meerkar_owner_id == Me::id()) {
            $this->meerkat_meta_keywords = implode(',', $ret);
            $this->save();
        }
    }

    /**
     * Добавление ключевика
     * @param type $keyword
     */
    function keywords($keywords, $usr_id = 0) {
        //удалить старые кейворды
        $this->keyword_remove_by_user($usr_id);
        $keywords = explode(',', $keywords);
        //добавить новые
        $ret = array();
        foreach ($keywords as $keyword) {
            $k = $this->keyword_add($keyword, $usr_id);
            if ($k) {
                $ret[$k] = $k;
            }
        }
        //возвратить строку с ключевиками с разделителями
        return implode(', ', $ret);
    }

    /**
     * Добавление ключевика
     * @param type $keyword
     */
    function keyword_add($keyword, $usr_id = 0) {
        $usr_id = $usr_id ? $usr_id : Me::id();
        $keyword = $this->keyword_prepare($keyword);
        $len = mb_strlen($keyword);
        if ($len > 0 && $len < 31) {
            $kwd = ORM::factory('Meerkat_Keyword')->get_or_create(
                    array(
                        'value' => $keyword
                    )
            );
            $ret[] = $keyword;
            ORM::factory('Meerkat_Object_Keyword')->get_or_create(array(
                'keyword_id' => $kwd->pk(),
                'usr_id' => $usr_id,
                'meerkat_id' => $this->pk(),
            ));
        } else {
            return false;
        }
        return $kwd->value;
    }

    function keyword_remove_by_keyword($keyword) {
        $keyword = $this->keyword_prepare($keyword);
        $kwd = ORM::factory('Meerkat_Keyword')->where('value', '=', $keyword)->find();
        if (!$kwd->loaded())
            return false;
        $keywords = ORM::factory('Meerkat_Object_Keyword')
                ->where('keyword_id', '=', $kwd->pk())
                ->where('meerkat_id', '=', $this->pk())
                ->find_all();
        foreach ($keywords as $keyword) {
            $keyword->delete();
        }
    }

    function keyword_remove_by_user($usr_id = 0) {
        $usr_id = $usr_id ? $usr_id : Me::id();
        $keywords = ORM::factory('Meerkat_Object_Keyword')
                ->where('usr_id', '=', $usr_id)
                ->where('meerkat_id', '=', $this->pk())
                ->find_all();
        foreach ($keywords as $keyword) {
            $keyword->delete();
        }
    }

    function keyword_prepare($keyword) {
        $keyword = strip_tags($keyword);
        return trim($keyword);
    }

}
