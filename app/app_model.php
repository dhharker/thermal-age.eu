<?php

class AppModel extends Model {


    function idExists ($id) {
        return ($this->find ('count', array (
            'conditions' => array (
                $this->name . "." . 'id' => $id
            ),
            'recursive' => -1
        )) > 0) ? TRUE : FALSE;
    }


}
