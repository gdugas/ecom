<?php


class registrationCtrl extends jController {
	
	function index () {
		if (jAuth::isConnected()) {
			$resp = $this->getResponse('redirect');
			$resp->action = $GLOBALS['gJConfig']->startModule .'~' . $GLOBALS['gJConfig']->startAction;
			return $resp;
		}
		$resp = $this->getResponse('html');
		
		$resp->body->assign('MAIN', jZone::get('ecom~ecomRegistration'));
		return $resp;
	}
	
	
	
	function savecreate() {
		$resp = $this->getResponse('redirect');
		
		$redirect = $this->param('redirect',NULL);
		$redirect_invalid = $redirect;
		if ($redirect === NULL) {
			$redirect = $GLOBALS['gJConfig']->startModule .'~' . $GLOBALS['gJConfig']->startAction;
			$redirect_invalid = 'webfiltration~registration:index';
		}
		
		// Point d'entre incorrect: on redirige
		$form = jForms::get('webfiltration~registration');
		if (! $form) {
			$resp->action = 'webfiltration~registration:index';
			return $resp;
		}
		$form->initFromRequest();
		
		// Formulaire invalide: on redirige
		if (! $form->check()) {
			$resp->action = $redirect_invalid;
			return $resp;
		}
		
		// L'utilisateur existe et a un compte
		$email = $form->getData('email');
		$user = $this->_get_usr($email);
		if ($user && jDao::get('webfiltration~account')->exist($email)) {
			$form->setErrorOn('email', "L'utilisateur spécifié par l'adresse mail existe déjà dans notre base de donnée");
			$resp->action = $redirect_invalid;
			return $resp;
		
		// Creation de l'utilisateur
		} else {
			if (! $user) {
				$random = rand(10000, 99999);
				$login =  $random . '_' . md5($form->getData('email'));
				$password = $form->getData('password');
				$user = jAuth::createUserObject($login, $password);
				$user->email = $form->getData('email');
				jDao::get('jauthdb~jelixuser')->insert($user);
			
			// Utilisateur existant, et mdp invalide
			} elseif(! jAuth::verifyPassword($user->login, $form->getData('password'))) {
				$form->setErrorOn('email', "Mot de passe invalide");
				$resp->action = $redirect_invalid;
				return $resp;
			}
			
			// Creation ou association du compte
			$form->setData('user', $user->login);
			$form->saveToDao('webfiltration~account');
			
			jAuth::login($user->login, $password);
			
			
			jMessage::add("Votre compte viens d'être créer");
			jForms::destroy('webfiltration~registration');
		}
		
		$resp->action = $redirect;
		return $resp;
	}
	
	
	private function _get_usr($email) {
		$cnd = jDao::createConditions();
		$cnd->addCondition('email','=',$email);
		
		$usr = NULL;
		foreach(jDao::get('jauthdb~jelixuser')->findBy($cnd) as $usr) { break; }
		
		return $usr;
	}
}
