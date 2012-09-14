<?php

class orderCtrl extends jController {
	
	public $pluginParams = array(
		'*' => array('auth.required' => True)
	);
	
	
    function index () {
    	$resp = $this->getResponse('html');
    	
    	$user = jAuth::getUserSession();
    	$list = jDao::get('ecom~order')->findByUser($user->login);
    	
    	$tpl = new jTpl();
    	$tpl->assign('list', $list);
    	$resp->body->assign('MAIN', $tpl->fetch('ecom~order_list'));
    	
    	return $resp;
    }
    
    function view () {
    	$resp = $this->getResponse('html');
    	
    	jClasses::inc('ecom~ecomOrder');
    	$order = ecomOrder::getById($this->param('id'));
    	
    	$tpl = new jTpl();
    	$tpl->assign('order', $order);
    	
    	$resp->body->assign('MAIN', $tpl->fetch('ecom~order_view'));
    	return $resp;
    }
}
