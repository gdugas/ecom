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
	
	public static function get ($user = NULL, $session = NULL) {
		if ($user == NULL && jAuth::isConnected()) {
			$user = jAuth::getUserSession();
		} else {
			$user = NULL;
		}
		
		if ($session == NULL) {
			if (! isset($_SESSION)) {
				session_start();
			}
			$session = session_id();
		}
		
		return new ecomCartBase($user, $session);
	}
}
