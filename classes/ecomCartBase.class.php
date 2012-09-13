<?php 

jClasses::inc('ecom~ecomContentManager');
jClasses::inc('ecom~ecomCart');
jClasses::inc('ecom~ecomOrder');

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

class ecomCartBase extends ecomContentManager {
	
	protected $_conditions = NULL;
	
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
		jEvent::notify('ecomCartAddItem', array('cart' => $this, 'item' => ecomContentManager::format_item($item)));
		
		return True;
	}
	
	public function count () {
		$cnd = $this->_base_conditions();
		return jDao::get('ecom~cart')->countBy($cnd);
	}
	
	public function delete ($record, $dao=NULL) {
		$cnd = $this->_fk_conditions($record, $dao);
		jDao::get('ecom~cart')->deleteBy($cnd);
		$this->_init_resultset();
	}
	
	public function drop () {
		$cnd = $this->_base_conditions();
		jEvent::notify('ecomCartBeforeDropCart', array('cart' => $this, 'cnd' => $cnd));
		jDao::get('ecom~cart')->deleteBy($cnd);
		$this->_init_resultset();
		jEvent::notify('ecomCartDropCart', array('cart' => $this));
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
			$item = ecomContentManager::format_item($item);
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
		jEvent::notify('ecomCartUpdateItem', array('cart' => $this, 'item' => ecomContentManager::format_item($item)));
		return True;
	}
	
	
	
	private function _base_conditions () {
		$cnd = jDao::createConditions();
		if ($this->user) {
			$cnd->addCondition('user','=',$this->user->login);
		} else {
			$cnd->addCondition('user', '=', NULL);
		}
		$cnd->addCondition('session', '=', $this->session);
		
		return $cnd;
	}
	
	private function _fk_conditions ($record, $dao) {
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
	
	private function _init_resultset () {
		$dao = jDao::get('ecom~cart');
		
		$cnd = $this->_base_conditions();
		$this->_resultset = $dao->findBy($cnd);
	}
}
