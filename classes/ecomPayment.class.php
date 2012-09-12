<?php

jClasses::incIface('ecom~IEcomDelivery');

class ecomDelivery {
	
	public static $_deliveryIndex = array();
	public static $_included = False;
	
	public static function includeAll ($force = False) {
		if (self::$_included == True && ! $force) {
			return;
		}
		
		foreach (jApp::config()->_modulesPathList as $module => $path) {
			$findIn = array($path . '/classes', $path . '/classes/ecomDeliveries');
			foreach($findIn as $dpath) {
				if (file_exists($dpath) && is_dir($dpath)) {
					$d = dir($dpath);
					while ($f = $d->read()) {
						if (strlen($f) > strlen('.delivery.php')) {
							if (substr($f, - strlen('.delivery.php')) == '.delivery.php') {
								
								$deliveryName = substr($f, 0, strlen($f) - strlen('.delivery.php')); 
								require_once($dpath . '/' . $f);
								self::$_deliveryIndex[$deliveryName] = realpath($dpath . '/' . $f);
							}
						}
					}
				}
			}
		}
		self::$_included = True;
	}
	
	public static function get ($name) {
		self::includeAll();
		if (! isset(self::$_deliveryIndex[$name])) {
			return NULL;
			
		} else {
			$class = $name . 'EcomDelivery';
			return new $class();
		}
	}
}
