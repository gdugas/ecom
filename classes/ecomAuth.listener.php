<?php 

class ecomAuthListener extends jEventListener {
	function onAuthLogin ($e) {
		$user = jAuth::getUserSession();
		if (! isset($_SESSION)) {
			session_start();
		}
		
		$cartSession = jDao::get('ecom~cart')->getByUser('session:'.session_id());
		$cartUser = jDao::get('ecom~cart')->getByUser($user->login);
		
		if (! $cartSession) {
		    $cartSession = jDao::createRecord('ecom~cart');
		    $cartSession->user = 'session:'.session_id();
		}
		
		if (! $cartUser && $cartSession) {
		    $cartSession->user = $user->login;
		    $cartSession->save();
		    
		} elseif ($cartUser && $cartSession && $cartUser->id != $cartSession->id) {
		    jDao::get('ecom~cart_item')->moveToCart($cartSession->id, $cartUser->id);
		    jDao::get('ecom~cart')->delete($cartSession->id);
		    $cartUser->save();
		    ecomCart::dropCartSession();
		}
	}
}
