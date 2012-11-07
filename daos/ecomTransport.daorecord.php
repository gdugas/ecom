<?php 

abstract class ecomTransportDaoRecord extends cDaoUserRecord_ecom_Jx_transport_Jx {
    
    function getPrice($country, $zone_code, $weight, $withtax=false) {
        $default = null;
        
        jClasses::inc('ecom~ecomTransportRuleCompiler');
        foreach(explode("\n", $this->rule) as $rule) {
            $compiled = ecomTransportRuleCompiler::compile($this->id, $rule);
            
            if ($compiled) {
                if ($compiled->isDefault()) {
                    $default = $compiled;
                } elseif ($compiled->verified($country, $zone_code, $weight) ) {
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
