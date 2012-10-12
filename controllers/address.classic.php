<?php 

class addressCtrl extends jControllerDaoCrud {
	
	public $pluginParams = array(
		'*' => array('auth.required' => True),
	);
	
	protected $dao = 'ecom~address';
	protected $form = 'ecom~address';
	
	protected $listTemplate = 'ecom~address_list';
    protected $editTemplate = 'ecom~address_edit';
    protected $viewTemplate = 'ecom~address_view';
	
	protected $propertiesForList = array(
		'label','civility', 'firstname', 'lastname', 'company',
		'address', 'city', 'state', 'zip_code', 'country', 'phone');
	
	
	
	private function _account_redirect () {
		$resp = $this->getResponse('redirect');
		$resp->action = 'ecom~account:index';
		return $resp;
	}
	
	private function _get_safe_billing ($id) {
		$user = jAuth::getUserSession();
		$id = $this->param('id', NULL);
		
		return jDao::get($this->dao)->countByUserId($user->login, $id);
	}
	
	// Some pre validations
	function _indexSetConditions($cnd) {
		$user = jAuth::getUserSession();
		$cnd->addCondition('user', '=', $user->login);
	}
	
	function view () {
		if (! $this->_get_safe_billing($this->param('id',NULL))) {
			return $this->_account_redirect();
		} else {
			return parent::view();
		}
	}
	function editupdate () {
		if (! $this->_get_safe_billing($this->param('id',NULL))) {
			return $this->_account_redirect();
		} else {
			return parent::editupdate();
		}
	}
	function delete () {
		if (! $this->_get_safe_billing($this->param('id',NULL))) {
			return $this->_account_redirect();
		} else {
			return parent::delete();
		}
	}
	
	function  _create ($form, $resp, $tpl) {
		$user = jAuth::getUserSession();
		$form->setData('user', $user->login);
	}
	
	function _view ($form, $resp, $tpl) {
	}
	
	function _editUpdate ($form, $resp, $tpl) {
	}
}