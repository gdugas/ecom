<?php 

class ecomCart {
	
/*	public static function getCart ($user = NULL, $session = NULL) {
		if ($user == NULL && jAuth::isConnected()) {
			$user = jAuth::getUserSession()->login;
		} else {
		    if (! isset($_SESSION)) {
		        session_start();
		    }
			$user = 'session:'.session_id();
		}
		
		$cart = jDao::get('ecom~cart')->getByUser($user);
		return $cart;
	}
*/
	
	public static function getCartSession() {
	    $cart = NULL;
	    $user = NULL;
	    $session = NULL;
	    if (! isset($_SESSION)) {
	        session_start();
	    }
	    
	    if (! isset($_SESSION['ecom_cart'])) {
    	    // Get cart by user login
    	    if (jAuth::isConnected()) {
    	        $user = jAuth::getUserSession();
                $cart = jDao::get('ecom~cart')->getByUser($user->login);
    	    }
    	    
    	    // If not cart, get by session_id
    	    if (! $user) {
    	        $cart = jDao::get('ecom~cart')->getBySession(session_id());
    	    }
    	    
    	    // If not cart, create it
    	    if (! $cart) {
        	    $cart = jDao::createRecord('ecom~cart');
        	    if ($user && $user->login) {
        	        $cart->user = $user;
        	    }
        	    $cart->session = session_id();
    	        $cart->save();
    	    }
    	    $_SESSION['ecom_cart'] = $cart;
	    }
	    
	    return $_SESSION['ecom_cart'];
	}
	
	public static function updateCart($cart) {
	    $currentCart = self::getCartSession();
	    if ($currentCart->id == $cart->id) {
	        $_SESSION['ecom_cart'] = $cart;
	    }
	}
}
