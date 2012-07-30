<?php 

class ecomAuthListener extends jEventListener {
	function onAuthLogin ($e) {
		$user = jAuth::getUserSession();
		if (! isset($_SESSION)) {
			session_start();
		}
		$dao = jDao::get('ecom~cart');
		$items = $dao->findBySession(session_id());
		foreach ($items as $item) {
			$item->user = $user->login;
			$dao->update($item);
		}
	}
}
