<?php 

class ecomCart {
	
    private static $_cart = NULL;
    
    public static function dropCartSession() {
        self::$_cart = NULL;
    }
    
	public static function getCartSession() {
	    $cart = NULL;
	    $dao = jDao::get('ecom~cart');
	    $user = jAuth::getUserSession();
	    
	    if (! isset($_SESSION)) {
	        session_start();
	    }
	    
	    if (self::$_cart === NULL) {
    	    if (jAuth::isConnected()) {
    	        $cart = $dao->getByUser($user->login);
    	    } else {
    	        $cart = $dao->getByUser('session:'.session_id());
    	    }
    	    
    	    // If not cart, create it
    	    if (! $cart) {
        	    $cart = jDao::createRecord('ecom~cart');
        	    if (jAuth::isConnected()) {
        	        $cart->user = $user->login;
        	    } else {
            	    $cart->user = 'session:'.session_id();
        	    }
    	        $cart->save();
    	    }
    	    self::$_cart = $cart;
	    }
	    
	    return self::$_cart;
	}
	
	public static function updateCartSession($cart) {
	    $currentCart = self::getCartSession();
	    if ($currentCart->id == $cart->id) {
	        self::$_cart = $cart;
	    }
	}
}
