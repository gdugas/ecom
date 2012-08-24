<?php 

jClasses::inc('ecom~ecomCart');

/*
 * Methods:
 * 	add ( $record, $qtt, $params );
 * 		record: jelix dao record
 * 		qtt: integer (default: 1)
 * 		params:
 * 			- dao (ignored in jelix 1.4)
 * 			- namefield (default: name)
 * 			- pricefield (default: price)
 * 			- thumbnail (default: NULL)
 */
class ecomCartBase implements Iterator {
	
	protected $_conditions = NULL;
	protected $_resultset = NULL;
	
	public $user = NULL;
	public $session = NULL;
	
	public function __construct ($user=NULL, $session=NULL) {
		
		$this->user = $user;
		$this->session = $session;
		
		$this->_init_resultset();
	}
	
	public function add (jDaoRecordBase $record, array $params=array()) {
		// Setting params
		$defaults = array(
			'quantity' => 1,
			'namefield' => 'name',
			'pricefield' => 'price',
			'thumbnail' => NULL,
			'tax' => NULL, 
			'dao' => NULL
		);
		$params = array_merge($defaults,$params);
		$quantity = $params['quantity'];
		
		// Getting dao selector
		if (method_exists($record, 'getSelector')) {
			$params['dao'] = $record->getSelector();
		} elseif (! $params['dao']) {
			throw new Exception('ecomChart::add(): Undefined "dao" parameter');
		}
		
		// Only updating quantity if product already in cart
		$dao = jDao::get('ecom~cart');
		if ($this->exist($record, $params['dao'])) {
			$params['rquantity'] = $quantity;
			return $this->update($record,$params);
		}
		
		// Putting product in cart
		$item = jDao::createRecord('ecom~cart');
		$item->dao = $params['dao'];
		$item->foreignkeys = ecomCart::foreignkeys($record);
		$item->quantity = $quantity;
		
		$item->namefield = $params['namefield'];
		$item->pricefield = $params['pricefield'];
		$item->thumbnail = $params['thumbnail'];
		$item->tax = $params['tax'];
		
		
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
	
	public function count () {
		$cnd = $this->_base_conditions();
		return $this->_resultset = jDao::get('ecom~cart')->countBy($cnd);
	}
	
	public function delete ($record, $dao=NULL) {
		$cnd = $this->_fk_conditions($record, $dao);
		jDao::get('ecom~cart')->deleteBy($cnd);
	}
	
	public function drop () {
		$cnd = $this->_base_conditions();
		$this->_resultset = jDao::get('ecom~cart')->deleteBy($cnd);
	}
	
	public function exist ($record, $dao=NULL) {
		$cnd = $this->_fk_conditions($record, $dao);
		return jDao::get('ecom~cart')->countBy($cnd);
	}
	
	public function getItem ($id) {
		$cnd = $this->_base_conditions();
		$cnd->addCondition('id', '=', $id);
		$item = jDao::get('ecom~cart')->findBy($cnd)->fetch();
		if ($item) {
			$item = $this->_format_item($item);
		}
		
		return $item;
	}
	
	
	public function update ($record, array $params = array()) {
		// Setting params
		$defaults = array('dao' => NULL);
		$params = array_merge($defaults,$params);
		
		$dao = jDao::get('ecom~cart');
		
		$cnd = $this->_fk_conditions($record, $params['dao']);
		$item = $dao->findBy($cnd)->fetch();
		if (! $item) {
			return False;
		}
		
		foreach ($params as $key => $value) {
			if ($key == 'rquantity') {
				$item->quantity += $value;
			} elseif ($key == 'thumbnail' || $key == 'quantity' || $key == 'tax') {
				$item->$key = $value;
			}
		}
		
		$dao->update($item);
		return True;
	}
	
	
	
	
	
	protected function _base_conditions () {
		$cnd = jDao::createConditions();
		$cnd->startGroup('OR');
		if ($this->user) {
			$cnd->addCondition('user','=',$this->user->login);
		}
		$cnd->addCondition('session', '=', $this->session);
		$cnd->endGroup();
		
		return $cnd;
	}
	
	protected function _fk_conditions ($record, $dao) {
		$cnd = $this->_base_conditions();
		$cnd->addCondition('foreignkeys', '=', ecomCart::foreignkeys($record));
		
		// Getting dao selector
		if (method_exists($record, 'getSelector')) {
			$dao = $record->getSelector();
		} elseif (! $dao) {
			throw new Exception('ecomChartBase conditions: "dao" cannot be null');
		}
		$cnd->addCondition('dao', '=', $dao);
		
		return $cnd;
	}
	
	protected function _format_item ($item) {
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
	
	
	// Item iterator
	public function current () {
		$item = $this->_resultset->current();
		if (! $item) {
			return $item;
		}
		
		$item = $this->_format_item($item);
		return $item;
	}
	
	public function key () {
		if (! method_exists($this->_resultset, 'key')) {
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
	
	
	private function _init_resultset () {
		$dao = jDao::get('ecom~cart');
		if (! $this->user) {
			$cnd = $this->_base_conditions();
			$cnd->addCondition('user', '!=', NULL);
			$rec = $dao->findBy($cnd)->fetch();
			if ($rec) {
				$this->user = jAuth::getUser($rec->user);
			}
		}
		
		$cnd = $this->_base_conditions();
		$this->_resultset = $dao->findBy($cnd);
	}
	
}
