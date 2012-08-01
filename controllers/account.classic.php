<?php 

class accountCtrl extends jController {
	
	public $pluginParams = array(
		'*' => array('auth.required' => True),
	);
	
	
	function index () {
		$resp = $this->getResponse('html');
		
		$user = jAuth::getUserSession();
		
		$form = jForms::create('ecom~account', $user->login);
		$form->initFromDao('ecom~account');
		
		$tpl = new jTpl();
		$tpl->assign('form', $form);
		$tpl->assign('account_billings', jDao::get('ecom~billing_address')->findByUser($user->login));
		
		$resp->body->assign('MAIN', $tpl->fetch('ecom~account_view'));
		return $resp;
	}
	
	
	function edit () {
		$resp = $this->getResponse('html');
		
		$user = jAuth::getUserSession();
		$form = jForms::get('ecom~account', $user->login);
		if (! $form) {
			$form = jForms::create('ecom~account', $user->login);
		}
		$form->initFromDao('ecom~account');
		
		$tpl = new jTpl();
		$tpl->assign('form', $form);
		
		$resp->body->assign('MAIN', $tpl->fetch('ecom~account_edit'));
		return $resp;
	}
	
	
	function save () {
		$resp = $this->getResponse('redirect');
		
		$user = jAuth::getUserSession();
		$form = jForms::fill('ecom~account', $user->login);
		if (! $form) {
			$resp = $resp->action = 'ecom~account:index';
			return $resp;
			
		} elseif (! $form->check()) {
			$resp = $resp->action = 'ecom~account:edit';
			return $resp;
			
		}
		
		$form->saveToDao('ecom~account');
		$resp->action = 'ecom~account:index';
		return $resp;
	}
}