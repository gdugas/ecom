<?php 

require_once(__DIR__.'/ecomTransportRuleCompiler.class.php');

class ecomTransport {
	
	protected $_record;
	
	function __construct(jDaoRecordBase $record) {
		if ($record->getSelector() !== 'ecom~transport') {
			throw new Exeption('$record parameter must be a ecom~transport dao record');
		}
		$this->_record = $record;
	}
	
	function getDefaultPrice($weight, $withtax=false) {
		return NULL;
	}
	
	function getPrice($country, $zone_code, $weight, $withtax=false) {
		
		$default = null;
		foreach(explode("\n", $this->_record->rule) as $rule) {
			$compiled = ecomTransportRuleCompiler::compile($this->_record->id, $rule);
			
			if ($compiled) {
				if ($compiled->isDefault()) {
					$default = $compiled;
				} elseif ($compiled->verified($country, $zone_code, $weight) ) {
					echo 'one';
					$price = $compiled->resolve($country, $zone_code, $weight);
					return $price;
				}
			}
		}
		if ($default) {
			return $default->resolve($country, $zone_code, $weight);
		}
		return NULL;
	}
	
}
