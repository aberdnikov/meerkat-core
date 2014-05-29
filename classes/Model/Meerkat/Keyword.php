<?php

class Model_Meerkat_Keyword extends ORM {

    function __toString() {
        return $this->value;
    }

}