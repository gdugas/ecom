<?php 

class ecomCartZone extends jZone {
	
	protected $_tplname='ecom~cart';
	
	protected function _prepareTpl() {
		jClasses::inc('ecom~ecomCart');
		$this->_tpl->assign('cart', ecomCart::current());
	}
}
