<?php

jClasses::inc('ecom~ecomCartBase');
jClasses::inc('ecom~ecomItemIterator');

class ecomOrderBase {
	
	private $_record = NULL;
	
	function __construct($reference) {
		$this->_record = jDao::get('ecom~order')->getByReference($reference);
		if (! $this->_record) {
			throw new Exception("Order $reference does not exist");
		}
	}
	
	function items () {
		$resultset = jDao::get('ecom~order_item')->findByOrder($this->_record->id);
		return new ecomItemIterator($resultset);
	}
}
