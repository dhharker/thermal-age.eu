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

    
    function beforeSave() {
        $exists = $this->exists();
        if ( !$exists && $this->hasField('user_id') && empty($this->data[$this->alias]['user_id']) ) {
            $this->data[$this->alias]['user_id'] = User::get('id');
        }
        
        return true;
    }

}
