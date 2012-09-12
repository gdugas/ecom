<?php

jClasses::inc('ecom~ecomCartBase');
jClasses::inc('ecom~ecomOrderBase');

class ecomOrder {
	
	public static function genRef ($prefix='C') {
		return uniqid($prefix);
	}
	
	public static function create (ecomCartBase $cart, array $params) {
		$defaults = array(
			'reference' => ecomOrder::genRef(),
			'address_delivery' => NULL,
			'address_facturation' => NULL,
			'delivery' => NULL,
			'payment' => NULL
		);
		
		// User control
		if (! isset($cart->user->login) || ! $cart->user->login) {
			throw new Exception("Invalid user object");
		}
		
		// PARAMS VALIDATION
		$params = array_merge($defaults, $params);
		foreach ($params as $key => $value) {
			if (! $value) {
				throw new Exception ("$key parameter could not be null");
			
			} elseif ($key == 'address_delivery'
						|| $key == 'address_facturation'
//						|| $key == 'delivery'
						) {
				$dao = 'ecom~account_address';
				if (! $value instanceof jDaoRecordBase) {
					throw new Exception ("$key parameter must be a jDaoRecordBase instance");
					
				} elseif (method_exists($value, 'getSelector')) {
/*					if ($key == 'delivery') {
						$dao = 'ecom~delivery';
					} else {
						$dao = 'ecom~account_address';
					}*/
					$dao = 'ecom~account_address';
					if ($value->getSelector() != $dao) {
						throw new Exception ("$key parameter: invalid dao: '".$value->getSelector()."' instead of '$dao'");
					}
				}
			}
		}
		
		$record = jDao::createRecord('ecom~order');
		
		$record->user = $cart->user->login;
		$record->reference = $params['reference'];
		$record->delivery = $params['delivery'];
		$record->payment = $params['payment'];
		$record->status = 'ongoing';
		
		$address_fields = array(
			'civility', 'firstname', 'lastname', 'company', 'address',
			'city', 'state', 'postal_code', 'country', 'phone'
		);
		
		// Setting addresses
		foreach ($address_fields as $value) {
			$field = 'fact_'.$value;
			$record->$field = $params['address_facturation']->$value;
		}
		foreach ($address_fields as $value) {
			$field = 'delivery_'.$value;
			$record->$field = $params['address_delivery']->$value;
		}
		jDao::get('ecom~order')->insert($record);
		
		
		// Adding cart items to order
		foreach($cart as $item) {
			$orderitem = jDao::createRecord('ecom~order_item');
			$product = $item->product; 
			
			$orderitem->order = $record->id;
			$orderitem->dao = $item->dao;
			$orderitem->foreignkeys = $item->foreignkeys;
			$orderitem->namefield = $item->namefield;
			$orderitem->pricefield = $item->pricefield;
			$orderitem->tax = $item->tax;
			$orderitem->quantity = $item->quantity;
			$orderitem->thumbnail = $item->thumbnail;
			
			$namefield = $item->namefield;
			$orderitem->name = $product->$namefield;
			
			$pricefield = $item->pricefield;
			$orderitem->price = $product->$pricefield;
			
			jDao::get('ecom~order_item')->insert($orderitem);
		}
		
		$cart->drop();
		return new ecomOrderBase($record->reference);
	}
	
	public static function get ($reference) {
		return new ecomOrderBase ($reference);
	}
	public static function getById ($id) {
		return new ecomOrderBase ($id, True);
	}
}
