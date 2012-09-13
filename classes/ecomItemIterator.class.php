<?php 

jClasses::inc('ecom~ecomContentManager');

class ecomItemIterator implements Iterator {
	
	protected $_resultset = NULL;
	
	function __construct($resultset) {
		$this->_resultset = $resultset;
	}
	
	
	// Item iterator
	public function current () {
		$item = $this->_resultset->current();
		if (! $item) {
			return $item;
		}
		
		$item = ecomContentManager::format_item($item);
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