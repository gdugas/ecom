<?php 

require_once (__DIR__ . '/ecomTransportRule.class.php');

class ecomTransportRuleCompiler {
	
	
	private static $validation_patterns = array(
		'/__var_([\w])__/' => '\\1',
		'/__num_([\d]+)__/' => '\\1',
		'/__static_("[\w_:-]+")__/' => '\\1',
		"/__static_('[\w_:-]+')__/" => '\\1',
		'/__cmop_([=<>\|&!*%\/]{1,2})__/' => '\\1',
		'/__asop_([=*%\/+-]{1,2})__/' => '\\1',
		'/__proc_((g|mod|stop)\([^\)]*\))__/' => '\\1',
		'/__sep_:__/' => '',
	);
	
	private static $compilation_patterns = array(
		// allow vars
		' (c) ' => ' __var_\\1__ ',
		' (w) ' => ' __var_\\1__ ',
		' (z) ' => ' __var_\\1__ ',
		
		// allow statics
		' ("[\w_:-]+") ' => ' __static_\\1__ ',
		" ('[\w_:-]+') " => ' __static_\\1__ ',
		
		// allow numerics
		' ([\d]+) ' => ' __num_\\1__ ',
		
		// operators
		' (==) ' => ' __cmop_\\1__ ',
		' (>=) ' => ' __cmop_\\1__ ',
		' (!=) ' => ' __cmop_\\1__ ',
		' (<=) ' => ' __cmop_\\1__ ',
		' (<) ' => ' __cmop_\\1__ ',
		' (>) ' => ' __cmop_\\1__ ',
		' (\|\|) ' => ' __cmop_\\1__ ',
		' (&&) ' => ' __cmop_\\1__ ',
		
		' (\+) ' => ' __asop_\\1__ ',
		' (-) ' => ' __asop_\\1__ ',
		' (\/) ' => ' __asop_\\1__ ',
		' (\*) ' => ' __asop_\\1__ ',
		' (%) ' => ' __asop_\\1__ ',
		
		//processors
		' (g\([\w]+\))' => ' __proc_\\1__ ',
		' (mod\([\w]+\))' => ' __proc_\\1__ ',
		' (stop\(\))' => ' __proc_\\1__ ',
		
		//separator
		' (:) ' => ' __sep_\\1__ ',
	);
	
	
	private static function _format($rule) {
		$patterns = array();
		$replacements = array();
		foreach (self::$compilation_patterns as $pattern => $replacement) {
			$patterns[] = "/$pattern/";
			$replacements[] = $replacement;
		}
		return preg_replace($patterns,$replacements,$rule);
	}
	
	private static function _validate($compiled) {
		$patterns = array();
		foreach(self::$validation_patterns as $pattern => $value) {
			$patterns[] = $pattern;
		}
		$str = preg_replace($patterns, '', $compiled);
		$len = strlen(trim($str));
		if ($len > 0) {
			return False;
		} else {
			return True;
		}
	}
	
	
	static function compile($transport_id, $rule) {
		if (strlen(trim($rule)) == 0) {
			return NULL;
		}
		$rule = ' '.trim($rule).' ';
		$compiled_string = self::_format($rule);
		if (! $compiled_string) {
			return NULL;
			
		} elseif (! self::_validate($compiled_string)) {
			return NULL;
			
		} else {
			$splitted = explode('__sep_:__', $compiled_string);
			if (count($splitted) != 2) {
				return NULL;
			}
			
			$patterns = array();
			$replacements = array();
			foreach (self::$validation_patterns as $p => $r) {
				$patterns[] = $p;
				$replacements[] = $r;
			}
			$condition = preg_replace($patterns, $replacements, $splitted[0]);
			$expression = preg_replace($patterns, $replacements, $splitted[1]);
			return new ecomTransportRule($transport_id, $rule, $condition, $expression);
		}
	}
}
