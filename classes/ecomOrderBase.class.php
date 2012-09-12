<?php

jClasses::inc('ecom~ecomCartBase');
jClasses::inc('ecom~ecomOrderItemIterator');

class ecomOrderBase {
	
	private $_record = NULL;
	protected $_total = NULL;
	
	function __construct($reference, $byId = NULL) {
		if ($byId) {
			$record = jDao::get('ecom~order')->get($reference);
		} else {
			$record = jDao::get('ecom~order')->getByReference($reference);
		}
		
		if (! $record) {
			throw new Exception("Order $reference does not exist");
		}
		foreach ($record as $field => $value) {
			$this->$field = $value;
		}
		$this->billing = jDao::get('ecom~billing')->getByOrder($this->id);
	}
	
	function items () {
		$resultset = jDao::get('ecom~order_item')->findByOrder($this->id);
		return new ecomOrderItemIterator($resultset);
	}
	
	public function total ($field) {
		if (! $this->_total) {
			$this->_total = array(
				'price' => 0,
				'tax' => 0,
				'dutyfree' => 0
			);
			foreach ($this->items() as $item) {
				$this->_total['price']		+= $item->total_price;
				$this->_total['tax']		+= $item->total_tax;
				$this->_total['dutyfree']	+= $item->total_dutyfree;
			}
			$this->_total['price'] = number_format($this->_total['price'], 2, '.', ' ');
			$this->_total['tax'] = number_format($this->_total['tax'], 2, '.', ' ');
			$this->_total['dutyfree'] = number_format($this->_total['dutyfree'], 2, '.', ' ');
		}
		if (isset($this->_total[$field])) {
			return number_format($this->_total[$field], 2, '.', ' ');
		}
	}
}
