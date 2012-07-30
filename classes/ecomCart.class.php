<?php 

jClasses::inc('ecom~ecomCartBase');

class ecomCart {
	
	public static function foreignkeys ($record) {
		$pks = array();
		foreach ($record->getPrimaryKeyNames() as $field) {
			$pks[$field] = $record->$field;
		}
		return serialize($pks);
	}
	
	public static function current () {
		return new ecomCartBase();
	}
}
