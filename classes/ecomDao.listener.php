<?php 

class ecomDaoListener extends jEventListener {
	
	private function _check_quantity ($record) {
		if ($record->quantity <= 0) {
			$record->quantity = 1;
		}
	}
	private function _check_deleted ($dao, $keys) {
		jDao::get('ecom~cart')->deleteByForeignKeys($dao, serialize($keys));
	}
	
	
	function onDaoInsertBefore ($e) {
		$this->_check_quantity($e->getParam('record'));
	}
	function onDaoUpdateBefore ($e) {
		$this->_check_quantity($e->getParam('record'));
	}
	
	
	function onDaoDeleteAfter ($e) {
		$this->_check_deleted($this->getParam('dao'), $this->getParam('keys'));
	}
	function onDaoDeleteByAfter ($e) {
		$this->_check_deleted($this->getParam('dao'), $this->getParam('keys'));
	}
}