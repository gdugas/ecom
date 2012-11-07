<?php 

jIncluder::inc(new jSelectorDao('ecom~cart_item', ''));
jClasses::inc('ecom~ecomCart');

abstract class ecomCartDaoRecord extends cDaoUserRecord_ecom_Jx_cart_Jx {
    
    function addItem(ecomCartItemDaoRecord $item) {
        if ($this->hasItem($item)) {
            return False;
            
        } else {
            $item->cart_id = $this->id;
            $item->save();
            // TODO: notify even in other item adding methods
//            jEvent::notify('ecomCartItemAdd', array('cart' => $this, 'item' => $item));
            return True;
        }
    }
    
    function afterSave() {
        ecomCart::updateCart($this);
    }
    
    function beforeDelete() {
        jDao::get('ecom~cart_item')->deleteByCart($this->id);
    }
    
    function beforeSave() {
        $this->price = 0;
        $this->price_full = 0;
        $this->weight = 0;
        foreach ($this->items() as $item) {
            $this->price += $item->price * $item->quantity;
            $this->price_full += $item->getPriceFull() * $item->quantity;
            $this->weight += $item->weight * $item->quantity;
        }
    }
    
    function createItem(jDaoRecordBase $record, array $params=array()) {
        if (! $this->id) {
            $this->save();
        }

        $default = array(
            'namefield' => 'name',
            'pricefield' => 'price',
            'weightfield' => 'weight',
            'weight_unit' => NULL,
            'tax' => 0,
            'detail' => NULL,
            'quantity' => 1,
            'thumbnail' => NULL
        );
        $params = array_merge($default, $params);
        
        $item = jDao::createRecord('ecom~cart_item');
        $item->cart_id = $this->id;
        $item->dao = $record->getSelector();
        $item->foreignkeys = ecomCartItemDaoRecord::fkformat($record);
        $item->namefield = $params['namefield'];
        $item->pricefield = $params['pricefield'];
        $item->weightfield = $params['weightfield'];
        $item->name = $record->$params['namefield'];
        $item->price = $record->$params['pricefield'];
        $item->weight = $record->$params['weightfield'];
        $item->weight_unit = $params['weight_unit'];
        $item->tax = $params['tax'];
        $item->detail = $params['detail'];
        $item->quantity = $params['quantity'];
        $item->thumbnail = $params['thumbnail'];
        
        return $item;
    }
    
    function countItems() {
        $cnd = jDao::createConditions();
        $cnd->addCondition('cart_id', '=', $this->id);
        return jDao::get('ecom~cart_item')->countBy($cnd);
    }
    
    function deleteItem(ecomCartItemDaoRecord $item) {
        $item->price = 0;
        $item->save();
    }
    
    function getItem(jDaoRecordBase $record) {
        $cnd = jDao::createConditions();
        $cnd->addCondition('cart_id', '=', $this->id);
        $cnd->addCondition('dao', '=', $record->getSelector());
        $cnd->addCondition('foreignkeys', '=', ecomCartItemDaoRecord::fkformat($record));
        return jDao::get('ecom~cart_item')->findBy($cnd)->fetch();
    }
    
    function hasItem(ecomCartItemDaoRecord $item) {
        if (! $this->id) {
            $this->save();
        }
        
        $cnd = jDao::createConditions();
        if ($item->id) {
            $cnd->addCondition('id', '=', $item->id);
            
        } else {
            $cnd->addCondition('cart_id', '=', $this->id);
            $cnd->addCondition('dao', '=', $item->dao);
            $cnd->addCondition('foreignkeys', '=', $item->foreignkeys);
        }
        return jDao::get('ecom~cart_item')->countBy($cnd) > 0;
    }
    
    function items() {
        return jDao::get('ecom~cart_item')->findByCart($this->id);
    }
    
    
    function toOrder ($params) {
        jClasses::inc('ecom~ecomOrder');
        $defaults = array(
                'reference' => ecomOrder::genRef(),
                'address_delivery' => NULL,
                'address_facturation' => NULL,
                'delivery' => NULL,
                'payment' => NULL,
                'status' => 'waiting'
        );
        
        $user = jAuth::getUser($this->user);
        
        // PARAMS VALIDATION
        $params = array_merge($defaults, $params);
        foreach ($params as $key => $value) {
            if (! $value) {
                throw new Exception ("$key parameter could not be null");
                	
            } elseif ($key == 'address_delivery'
                    || $key == 'address_facturation'
                    //						|| $key == 'delivery'
            ) {
                $dao = 'ecom~address';
                if (! $value instanceof jDaoRecordBase) {
                    throw new Exception ("$key parameter must be a jDaoRecordBase instance");
                    	
                } elseif (method_exists($value, 'getSelector')) {
                    /*					if ($key == 'delivery') {
                     $dao = 'ecom~delivery';
                    } else {
                    $dao = 'ecom~account_address';
                    }*/
                    $dao = 'ecom~address';
                    if ($value->getSelector() != $dao) {
                        throw new Exception ("$key parameter: invalid dao: '".$value->getSelector()."' instead of '$dao'");
                    }
                }
            }
        }
        
        $order = jDao::createRecord('ecom~order');
        
        $order->user = $this->user;
        $order->reference = $params['reference'];
        $order->delivery = $params['delivery'];
        $order->payment = $params['payment'];
        $order->status = $params['status'];
//        $order->price = $cart->price;
//        $order->price_full = $cart->price_full;
        
        $address_fields = array(
                'civility', 'firstname', 'lastname', 'company', 'address',
                'city', 'state', 'zip_code', 'country', 'phone'
        );
        
        // Setting addresses
        foreach ($address_fields as $value) {
            $field = 'fact_'.$value;
            $order->$field = $params['address_facturation']->$value;
        }
        foreach ($address_fields as $value) {
            $field = 'delivery_'.$value;
            $order->$field = $params['address_delivery']->$value;
        }
        jDao::get('ecom~order')->insert($order);
        
        
        // Adding cart items to order
        foreach($this->items() as $item) {
            $orderitem = jDao::createRecord('ecom~order_item');
            $product = $item->product;
            
            $orderitem->order_id = $order->id;
            $orderitem->dao = $item->dao;
            $orderitem->foreignkeys = $item->foreignkeys;
            $orderitem->namefield = $item->namefield;
            $orderitem->pricefield = $item->pricefield;
            $orderitem->tax = $item->tax;
            $orderitem->quantity = $item->quantity;
            $orderitem->thumbnail = $item->thumbnail;
            
            $orderitem->name = $item->name;
            $orderitem->price = $item->price;
            
            jDao::get('ecom~order_item')->insert($orderitem);
        }
        jDao::get('ecom~cart')->delete($this->id);
        
        return $order;
    }
}
