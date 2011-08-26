<?php
/**
* By Joshua Muheim; http://cakephp.1045679.n5.nabble.com/Is-there-a-standard-way-of-making-sure-that-foreign-keys-exist-td3249784.html
* (above cited blog post's blog has vanished)
*
* Modified to use 'validateIt' from BelongsTo as hack
*/
class ForeignKeyVerifiableBehavior extends ModelBehavior {
    function setup(&$Model, $settings) {
        $Model->orphansProtectableOptions = array_merge(array(
        ), $settings);
    }

    /**
    * ???
    */
    function beforeValidate(&$Model) {
        $valid = true;
        foreach($Model->belongsTo as $model => $settings) {
            if (isset ($settings['validateIt']) && $settings['validateIt'] == true) {
                $foreignKey = $settings['foreignKey'];
                $foreignKeyValue = $Model->data[$Model->name][$foreignKey];
                if(!empty($foreignKeyValue)) {
                    $Model->{$model}->id = $foreignKeyValue;
                    if(!$Model->{$model}->exists()) {
                        $Model->invalidate($foreignKey, sprintf(__('ID %s does not
                        exist', true), $foreignKeyValue));
                        $valid = false;
                    }
                }
            }

        }
        return $valid;
    }
}
