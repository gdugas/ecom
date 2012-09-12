<?php

jClasses::incIface('ecom~IEcomPayment');

class ecomPayment {
	
	public static $_paymentIndex = array();
	public static $_included = False;
	
	public static function includeAll ($force = False) {
		if (self::$_included == True && ! $force) {
			return;
		}
		
		foreach (jApp::config()->_modulesPathList as $module => $path) {
			$findIn = array($path . '/classes', $path . '/classes/ecomPayments');
			foreach($findIn as $dpath) {
				if (file_exists($dpath) && is_dir($dpath)) {
					$d = dir($dpath);
					while ($f = $d->read()) {
						if (strlen($f) > strlen('.payment.php')) {
							if (substr($f, - strlen('.payment.php')) == '.payment.php') {
								
								$deliveryName = substr($f, 0, strlen($f) - strlen('.payment.php')); 
								require_once($dpath . '/' . $f);
								self::$_paymentIndex[$deliveryName] = realpath($dpath . '/' . $f);
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
		if (! isset(self::$_paymentIndex[$name])) {
			return NULL;
			
		} else {
			$class = $name . 'EcomPayment';
			return new $class();
		}
	}
}
