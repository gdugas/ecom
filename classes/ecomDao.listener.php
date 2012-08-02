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
		$dao = $e->getParam('dao');
		$record = $e->getParam('record');
		
		// Insert product in cart: quantity must be > 0
		if ($dao == 'ecom~cart') {
			$this->_check_quantity($record);
		
		// New account: auto login generation
		} elseif ($dao == 'ecom~account') {
			if (! $record->login) {
				$record->login = $record->email;
				$record->reference = 'CL'.rand(10000,99999);
			}
		}
	}
	
	function onDaoUpdateBefore ($e) {
		$dao = $e->getParam('dao');
		$record = $e->getParam('record');
		
		// Update cart: quantity must be > 0
		if ($dao == 'ecom~cart') {
			$this->_check_quantity($record);
		}
	}
	
	
	
	// Check if deleted object is in cart
	function onDaoDeleteAfter ($e) {
		$this->_check_deleted($e->getParam('dao'), $e->getParam('keys'));
	}
	function onDaoDeleteByAfter ($e) {
		$this->_check_deleted($e->getParam('dao'), $e->getParam('keys'));
	}
}