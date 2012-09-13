<?php 

class ecomAuthListener extends jEventListener {
	function onAuthLogin ($e) {
		$user = jAuth::getUserSession();
		if (! isset($_SESSION)) {
			session_start();
		}
		$dao = jDao::get('ecom~cart');
		$cnd = jDao::createConditions();
		$cnd->addCondition('session', '=', session_id());
		$cnd->addCondition('user', '=', NULL);
		$items = $dao->findBy($cnd);
		foreach ($items as $item) {
			$item->user = $user->login;
			$dao->update($item);
		}
	}
}
