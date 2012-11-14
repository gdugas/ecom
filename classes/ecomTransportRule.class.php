<?php 

class ecomTransportRuleStopped extends Exception {
}
class ecomTransportRuleError extends Exception {
}


class ecomTransportRule {
	
	public $condition;
	public $expression;
	protected $raw;
	
	private $_default = False;
	private $_processed = NULL;
	private $processors = array();
	private $vars = array();
	private $transport_id;
	
	function __construct($transport_id, $raw, $condition, $expression) {
		$this->condition = $condition;
		$this->expression = $expression;
		$this->transport_id = $transport_id;
		if (! strlen(trim($condition))) {
			$this->_default = True;
		}
	}
	
	
	public function process($string) {
		$processed = $string;
		
		// Var replacements
		foreach($this->getVars() as $var => $value) {
			$processed = str_replace($var, $value, $processed);
		}
		return preg_replace_callback('/([\w]+)\s*\(([^\)]*)\)/', array($this, '_process'), $processed);
	}
	
	public function _process($matches) {
		$fname = $matches[1];
		$args = $matches[2];
		if (! $this->hasProcessor($fname)) {
			throw new ecomTransportRuleError('Invalid rule: '.$this->raw);
		}
		$fname = '_processor_'.$fname;
		return $this->$fname($args);
	}
	
	private function _processor_g($args) {
		$w = NULL;
		eval("\$w = floatval($args);");
		
		$cnd = jDao::createConditions();
		$cnd->addCondition('transport', '=', $this->transport_id);
		$cnd->addCondition('country', '=', $this->getVar('c'));
		$cnd->addCondition('zone_code', '=', $this->getVar('z'));
		$cnd->addCondition('wmin', '<=', $w);
		$cnd->addCondition('wmax', '>=', $w);
		$record = jDao::get('ecom~transport_grid')->findBy($cnd)->fetch();
		
		if (! $record) {
			return NULL;
		}
		return $record->price;
	}
	
	private function _processor_mod($args) {
		return floatval(eval($args));
	}
	
	private function _processor_stop($args) {
		throw new ecomTransportRuleStopped('Rule stopped: '.$this->raw);
	}
	
	
	
	function isDefault() {
		return $this->_default;
	}
	
	function setProcessor($name) {
		$this->processors[$name] = $name;
	}
	function setVar($name, $value) {
		$this->vars[$name] = $value;
	}
	function hasProcessor($name) {
		return isset($this->processors[$name]);
	}
	function getVar($name) {
		if (! isset($this->vars[$name])) {
			return NULL;
		} else {
			return $this->vars[$name];
		}
	}
	function getProcessors() {
		return $this->processors;
	}
	function getVars() {
		return $this->vars;
	}
	function resetProcessors() {
		$this->processors = array();
	}
	function resetVars() {
		$this->vars = array();
	}
	
	
	function verified($country, $zone_code, $weight) {
		$this->resetVars();
		$this->resetProcessors();
		if ($this->isDefault()) {
			return True;
		}
		
		try {
			$this->setVar('c', $country);
			$this->setVar('z', $zone_code);
			$this->setVar('w', $weight);
			$this->setProcessor('g');
			$this->setProcessor('mod');
			$cnd = False;
			$processed = $this->process($this->condition);
			eval("\$cnd = $processed;");
			
		} catch(ecomTransportRuleError $e) {
			echo $e->getMessage();
			exit;
		}
		return $cnd;
	}
	
	function resolve($country, $zone_code, $weight, $withTax=False) {
		$this->resetVars();
		$this->resetProcessors();
		try {
			$this->setVar('c', $country);
			$this->setVar('z', $zone_code);
			$this->setVar('w', $weight);
			$this->setProcessor('g');
			$this->setProcessor('mod');
			$this->setProcessor('stop');
			$processed = $this->process($this->expression);
			
			$expr = 0;
			eval("\$expr = $processed;");
			
		} catch(ecomTransportRuleStopped $e) {
			echo $e->getMessage();
			exit;
		} catch(ecomTransportRuleError $e) {
			echo $e->getMessage();
			exit;
		}
		return $expr;
	}
}
