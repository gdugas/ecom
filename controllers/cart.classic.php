<?php

class cartCtrl extends jController {
	
	function update () {
		$resp = $this->getResponse('redirectUrl');
		$redirect = $this->param('redirect', NULL);
		
		if (! $redirect || substr($redirect, 0, 1) === '#') {
			header("Status: 404 Not Found");
			exit;
		}
		
		jClasses::inc('ecom~ecomCart');
		$cart = ecomCart::getCartSession();
		
		$item = jDao::get('ecom~cart_item')->get($this->param('id', 0));
		if ($item) {
		    $item->quantity = $this->param('qtt', 1);
		    $item->save();
		}
		
		$resp->url = urldecode($redirect);
		return $resp;
	}
	
	function delete () {
		$resp = $this->getResponse('redirectUrl');
		$redirect = $this->param('redirect', NULL);
		
		if (! $redirect || substr($redirect, 0, 1) === '#') {
			header("Status: 404 Not Found");
			exit;
		}
		
		jClasses::inc('ecom~ecomCart');
		$cart = ecomCart::getCartSession();
		$cnd = jDao::createConditions();
		$cnd->addCondition('cart_id', '=', $cart->id);
		$cnd->addCondition('id', '=', $this->param('id'));
		jDao::get('ecom~cart_item')->deleteBy($cnd);
		
		$resp->url = urldecode($redirect);
		return $resp;
	}
}
