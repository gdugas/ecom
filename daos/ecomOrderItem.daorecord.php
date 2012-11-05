<?php 

abstract class ecomOrderItemDaoRecord extends cDaoUserRecord_ecom_Jx_order_item_Jx {
    
    function afterSave() {
        $order = jDao::get('ecom~order')->get($this->order_id);
        if ($order) {
            $order->save();
        }
    }
    
    function beforeDelete() {
        $order = jDao::get('ecom~order')->get($this->order_id);
        $this->price = 0;
        $this->save();
        $order->save();
    }
    
    function beforeSave() {
        if ($this->quantity <= 0) {
            $this->quantity = 1;
        }
    }
    
    public static function fkformat (jDaoRecordBase $record) {
        $pks = array();
        foreach ($record->getPrimaryKeyNames() as $field) {
            $pks[$field] = $record->$field;
        }
        return serialize($pks);
    }
    
    // Return price with taxes
    function getPriceFull() {
        return $this->price + ($this->price * $this->tax / 100);
    }

}
