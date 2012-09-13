<?php

jClasses::inc('ecom~ecomContentManager');


class ecomOrderBase extends ecomContentManager {
	
	const STATUS_WAITING = 'waiting';
	const STATUS_ONGOING = 'ongoing';
	const STATUS_SENT = 'sent';
	const STATUS_CANCELED = 'canceled';
	
	private $_dao = 'ecom~order';
	private $_daobilling = 'ecom~billing';
	private $_record = NULL;

	
	function __construct($reference, $byId = NULL) {
		if ($byId) {
			$record = jDao::get($this->_dao)->get($reference);
		} else {
			$record = jDao::get($this->_dao)->getByReference($reference);
		}
		
		if (! $record) {
			throw new Exception("Order $reference does not exist");
		}
		
		$this->_record = $record;
		foreach ($record as $field => $value) {
			$this->$field = $value;
		}
		$this->billing = jDao::get($this->_daobilling)->getByOrder($this->id);
		
		$this->_resultset = jDao::get('ecom~order_item')->findByOrder($this->id);
	}
	
	
	
	public function set_status ($status=NULL) {
		// Status param validation
		if (
			$status != self::STATUS_CANCELED &&
			$status != self::STATUS_ONGOING &&
			$status != self::STATUS_SENT &&
			$status != self::STATUS_WAITING) {
				return $this->status;
		}
		
		// Status update validation
		if ($this->status == self::STATUS_SENT || $this->status == self::STATUS_CANCELED) {
			return False;
			
		} elseif ($status == self::STATUS_WAITING && $this->status == self::STATUS_ONGOING) {
			return False;
			
		} else {
			switch ($status) {
				case self::STATUS_ONGOING:	$this->_set('date_ongoing', date('Y-m-d H:i:s')); break;
				case self::STATUS_SENT:		$this->_set('date_sent', date('Y-m-d H:i:s')); break;
				case self::STATUS_CANCELED:	$this->_set('date_canceled', date('Y-m-d H:i:s')); break;
				default: break;
			}
			$this->_set('status', $status);
			$this->_save();
			return True;
		}
	}
	
	
	
	
	private function _save () {
		jDao::get($this->_dao)->update($this->_record);
	}
	
	private function _set ($var, $value) {
		$this->$var = $value;
		$this->_record->$var = $value;
	}
}
