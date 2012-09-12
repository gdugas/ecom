<?php


class registrationCtrl extends jController {
	
	private function _auth_dao () {
		$conf = jApp::coord()->getPlugin('auth')->config;
		return $conf['Db']['dao'];
	}
	
	function index () {
		if (jAuth::isConnected()) {
			$resp = $this->getResponse('redirect');
			$resp->action = jApp::config()->startModule .'~' . jApp::config()->startAction;
			return $resp;
		}
		$resp = $this->getResponse('html');
		
		$resp->body->assignZone('MAIN', 'ecom~ecomRegistration');
		return $resp;
	}
	
	
	
	function savecreate() {
		$resp = $this->getResponse('redirect');
		
		// Point d'entre incorrect: erreur
		$form = jForms::fill('ecom~registration');
		if (! $form) {
			throw new Exception('Form error');
		}
		
		$redirect_valid = $this->param('registration_url_redirect',NULL);
		$redirect_error = $this->param('registration_url_error',NULL);
		
		if (! $redirect_valid || ! $redirect_error) {
			throw new Exception('Redirection error');
		}
		
		if (! $form->check()) {
			$resp->action = $redirect_error;
			return $resp;
		}
		
		// Existing account
		$email = $form->getData('email');
		$user = $this->_get_usr($email);
		if ($user) {
			$form->setErrorOn('email', "L'utilisateur spécifié par l'adresse mail existe déjà dans notre base de donnée");
			$resp->action = $redirect_error;
			return $resp;
		
		// User creation
		} else {
			$random = rand(10000, 99999);
			
			$login =  $form->getData('email');
			$password = $form->getData('password');
			
			// Creation ou association du compte
			$form->setData('login', $login);
			$form->saveToDao($this->_auth_dao());
			
			jAuth::changePassword($login, $password);
			
			jAuth::login($login, $password);
			
			jMessage::add("Votre compte viens d'être créer");
			jForms::destroy('ecom~registration');
		}
		
		$resp->action = $redirect_valid;
		return $resp;
	}
	
	
	private function _get_usr($email) {
		$cnd = jDao::createConditions();
		$cnd->addCondition('email','=',$email);
		
		$usr = jDao::get($this->_auth_dao())->findBy($cnd)->fetch();
		
		return $usr;
	}
}
