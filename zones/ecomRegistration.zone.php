<?php 


class ecomRegistrationZone extends jZone {
	
	protected $_tplname='ecom~registration';
	
	protected function _prepareTpl () {
		$form = jForms::get('ecom~registration');
		if (! $form) {
			$form = jForms::create('ecom~registration');
		}
		
		$coord = $GLOBALS['gJCoord'];
		$currentUrl = $coord->moduleName . '~' . $coord->actionName;
		
		$form->setData('registration_url_redirect', $this->param('registration_url_redirect', $currentUrl));
		$form->setData('registration_url_error', $this->param('registration_url_error', $currentUrl));
		
		$this->_tpl->assign('form',$form);
	}
}