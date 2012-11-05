<?php 

class ecomCartZone extends jZone {
	
	protected $_tplname='ecom~cart';
	
	protected function _prepareTpl () {
		$tpl = $this->param('template',NULL);
		if ($tpl) {
			$this->_tplname = $tpl;
		}
		
		jClasses::inc('ecom~ecomCart');
		$this->_tpl->assign('cart', ecomCart::getCartSession());
		$this->_tpl->assign('editable', $this->param('editable', True));
		$this->_tpl->assign('currenturl', urlencode(jUrl::getCurrentUrl()));
	}
}
