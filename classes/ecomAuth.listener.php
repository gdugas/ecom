<?php 

class ecomAuthListener extends jEventListener {
	function onAuthLogin ($e) {
		$user = jAuth::getUserSession();
		if (! isset($_SESSION)) {
			session_start();
		}
		
		jClasses::inc('ecom~ecomCart');
		$cartSession = ecomCart::getCartSession();
		$cartUser = jDao::get('ecom~cart')->getByUser($user->login);
		
		if (! $cartUser) {
		    $cartSession->user = $user->login;
		    $cartSession->save();
		    
		} elseif ($cartUser->id != $cartSession->id) {
		    foreach ($cartSession->items() as $item) {
		        $cartUser->addItem($item);
		    }
		    jDao::get('ecom~cart')->delete($cartSession->id);
		    unset($_SESSION['ecom_cart']);
		}
	}
}
