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
		$cart = ecomCart::get();
		
		$item = $cart->getItem($this->param('id', 0));
		if ($item) {
			$cart->update($item->product, array(
				'quantity' => $this->param('qtt', 0),
				'dao' => $item->dao
			));
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
		$cart = ecomCart::get();
		
		$item = $cart->getItem($this->param('id', 0));
		if ($item) {
			$cart->delete($item->product, $item->dao);
		}
		
		$resp->url = urldecode($redirect);
		return $resp;
	}
}
