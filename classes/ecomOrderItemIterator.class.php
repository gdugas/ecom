<?php 


class ecomOrderItemIterator implements Iterator {
	
	protected $_resultset = NULL;
	
	function __construct($resultset) {
		$this->_resultset = $resultset;
	}
	
	protected function _format_item ($item) {
		$dao = jDao::get($item->dao);
		
		$cnd = jDao::createConditions();
		foreach (unserialize($item->foreignkeys) as $field => $value) {
			$cnd->addCondition($field, '=', $value);
		}
		$product = $dao->findBy($cnd)->fetch();
		
		$item->product = $product;
		
		$item->price			= number_format($item->price, 2, '.', ' ');
		$item->price_tax		= number_format($item->price * $item->tax / 100, 2, '.', ' ');
		$item->price_dutyfree	= number_format($item->price - $item->price_tax, 2, '.', ' ');
		
		$item->total_price		= number_format($item->price * $item->quantity, 2, '.', ' ');
		$item->total_tax		= number_format($item->total_price * $item->tax / 100, 2, '.', ' ');
		$item->total_dutyfree	= number_format($item->total_price - $item->total_tax, 2, '.', ' ');		
		
		return $item;
	}
	
	
	// Item iterator
	public function current () {
		$item = $this->_resultset->current();
		if (! $item) {
			return $item;
		}
		
		$item = $this->_format_item($item);
		return $item;
	}
	
	public function key () {
		if (! method_exists($this->_resultset, 'key')) {
			return False;
		} else {
			return $this->_resultset->key();
		}
	}
	public function next () {
		if (! method_exists($this->_resultset, 'next')) {
			return ;
		} else {
			return $this->_resultset->next();
		}
	}
	public function rewind () {
		if (! method_exists($this->_resultset, 'rewind')) {
			return;
		} else {
			return $this->_resultset->rewind();
		}
	}
	public function valid () {
		if (! method_exists($this->_resultset, 'valid')) {
			return False;
		} else {
			return $this->_resultset->valid();
		}
	}
}