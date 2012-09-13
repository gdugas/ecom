<?php

jClasses::inc('ecom~ecomItemIterator');


class ecomContentManager {
	
	private $_items = NULL;
	
	protected $_resultset = NULL;
	protected $_total = NULL;
	
	
	function items ($reset = False) {
		if (! $this->_items || $reset) {
			$this->_items = new ecomItemIterator($this->_resultset);
		}
		return $this->_items;
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
