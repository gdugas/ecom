<?php 


class ecomRegistrationZone extends jZone {
	protected $_tplname='ecom~registration';
	
	protected function _prepareTpl () {
		$form = jForms::get('ecom~registration');
		if (! $form) {
			$form = jForms::create('ecom~registration');
		}
		
		$this->_tpl->assign('form',$form);
	}
}