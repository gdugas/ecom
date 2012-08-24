<?php

class orderCtrl extends jController {
	
	public $pluginParams = array(
		'*' => array('auth.required' => True),
	);
	
	function ordering () {
    	$error = False;
    	if (! $this->param('delivery', NULL)) { jMessage::add('Vous devez définir un mode de livraison'); $error = True; }
    	if (! $this->param('payment', NULL)) { jMessage::add('Vous devez définir un mode de paiement'); $error = True; }
       	if (! $this->param('cgi_status', NULL)) { jMessage::add('Vous devez définir un moyen de paiement'); $error = True; }
       	
       	if ($error) {
    		$resp = $this->getResponse('redirect');
    		$resp->action = 'webfiltration~questionnaire:step6';
    		print_r($resp->action);
    		return $resp;
       	}
	}

}