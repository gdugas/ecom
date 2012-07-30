<?php 

jClasses::inc('ecom~ecomCart');

class ecomCartBase implements Iterator {
	
	protected $_conditions = NULL;
	protected $_resultset = NULL;
	
	public $user = NULL;
	public $session = NULL;
	
	public function __construct ($user=NULL, $session=NULL) {
		if ($session == NULL) {
			if (! isset($_SESSION)) {
				session_start();
			}
			$session = session_id();
		}
		
		if ($user == NULL && jAuth::isConnected()) {
			$user = jAuth::getUserSession();
		}
		$this->user = $user;
		$this->session = $session;
		
		$this->_init_resultset();
	}
	
	
	public function add (jDaoRecordBase $record, $quantity=1, array $params=array()) {
		$defaults = array(
			'namefield' => 'name',
			'pricefield' => 'price',
			'thumbnail' => NULL
		);
		$params = array_merge($defaults,$params);
		if (! isset($params['dao'])) {
			throw new Exception('ecomChart::add(): Undefined "dao" parameter');
		}
		
		$dao = jDao::get('ecom~cart');
		if ($this->exist($record)) {
			$params['rquantity'] = $quantity;
			return $this->update($record,$params);
		}
		
		$item = jDao::createRecord('ecom~cart');
		$item->dao = $params['dao'];
		$item->foreignkeys = ecomCart::foreignkeys($record);
		$item->quantity = $quantity;
		
		$item->namefield = $params['namefield'];
		$item->pricefield = $params['pricefield'];
		$item->thumbnail = $params['thumbnail'];
		
		if (! isset($_SESSION)) {
			session_start();
		}
		$item->session = session_id();
		
		if (jAuth::isConnected()) {
			$user = jAuth::getUserSession();
			$item->user = $user->login;
		}
		
		$dao->insert($item);
		return True;
	}
	
	public function exist ($record) {
		$cnd = jDao::createConditions();
		$cnd->addCondition('foreignkeys','=',ecomCart::foreignkeys($record));
		return jDao::get('ecom~cart')->countBy($cnd);
	}
	
	public function update ($record, array $params = array()) {
		$dao = jDao::get('ecom~cart');
		if (! $this->exist($record)) {
			return False;
		}
		
		$item = $dao->getByForeignKeys(ecomCart::foreignkeys($record));
		foreach ($params as $key => $value) {
			if ($key == 'rquantity') {
				$item->quantity += $value;
			} elseif ($key == 'thumbnail' || $key == 'quantity') {
				$item->$key = $value;
			}
		}
		
		$dao->update($item);
		return True;
	}
	
	public function delete ($record, $dao=NULL) {
		$dao = jDao::get('ecom~cart');
		$dao->deleteByForeignKeys($dao, ecomCart::foreignkeys($record));
	}
	
	
	
	
	
	
	// Item iterator
	public function current () {
		foreach($this->_resultset as $item) { break; }
		if (! $item) { return $item; }
		
		$dao = jDao::get($item->dao);
		
		$cnd = jDao::createConditions();
		foreach (unserialize($item->foreignkeys) as $field => $value) {
			$cnd->addCondition($field, '=', $value);
		}
		$product = $dao->findBy($cnd)->fetch();
		$namefield = $item->namefield;
		$pricefield = $item->pricefield;
		
		$item->name = $product->$namefield;
		$item->price = $product->$pricefield; 
		$item->product = $product;
		
		return $item;
	}
	
	public function key () {
		if (! method_exists($this->_resultset, 'key')) {
			echo 'key -';
			return False;
		} else {
			return $this->_resultset->key();
		}
	}
	public function next () {
		if (! method_exists($this->_resultset, 'next')) {
			return ;
		} else {
			return $this->_resultset->next();
		}
	}
	public function rewind () {
		if (! method_exists($this->_resultset, 'rewind')) {
			return;
		} else {
			return $this->_resultset->rewind();
		}
	}
	public function valid () {
		if (! method_exists($this->_resultset, 'valid')) {
			return False;
		} else {
			return $this->_resultset->valid();
		}
	}
	
	
	// extended iterator
	private function _init_conditions () {
		$cnd = jDao::createConditions();
		$cnd->startGroup('OR');
		if ($this->user) {
			$cnd->addCondition('user','=',$this->user->login);
		}
		$cnd->addCondition('session', '=', $this->session);
		$cnd->endGroup();
		
		$this->_conditions = $cnd;
	}
	
	private function _init_resultset () {
		$this->_init_conditions();
		$this->_resultset = jDao::get('ecom~cart')->findBy($this->_conditions);
	}
}
