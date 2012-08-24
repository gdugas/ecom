<?php 

class ecomLoginZone extends jZone {
	
	protected $_tplname='ecom~login';
	
	protected function _prepareTpl() {
		$tpl = $this->param('template',NULL);
		if ($tpl) {
			$this->_tplname = $tpl;
		}
		
		$form = jForms::get('ecom~login');
		if (! $form) {
			$form = jForms::create('ecom~login');
		}
		
		$form->setData('auth_url_return', $this->param('auth_url_return',NULL));
		
		$this->_tpl->assign('form', $form);
	}
}
