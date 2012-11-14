<?php 

abstract class ecomCartItemDaoRecord extends cDaoUserRecord_ecom_Jx_cart_item_Jx {
    
    private $_product = NULL;
    
    function afterSave() {
        $cart = jDao::get('ecom~cart')->get($this->cart_id);
        if ($cart) {
            $cart->save();
        }
    }
    
    function beforeDelete() {
        $cart = jDao::get('ecom~cart')->get($this->cart_id);
        $this->price = 0;
        $this->save();
        if ($cart) {
            $cart->save();
        }
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
    
    public function getProduct () {
        if ($this->_product === NULL) {
            $this->_product = jDao::get($this->dao)->get(unserialize($this->foreignkeys));
        }
        return $this->_product;
    }
    
    // Return price with taxes
    function getPriceFull() {
        return $this->price + ($this->price * $this->tax / 100);
    }

}
