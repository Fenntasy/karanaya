<?php
class AuthController extends Zend_Controller_Action {

	public function indexAction() {
		$this->_redirect('/Auth/login');
		return;
	}

	public function loginAction() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->_redirect('/');
			return;
		} else {
			$loginForm = new Application_Form_Auth_Login($_POST);
			
			if ($loginForm->isValid($_POST)) {
				
				$adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
				$adapter->setTableName('users')
					->setIdentityColumn('username')
					->setCredentialColumn('password');
				
				$adapter->setIdentity($loginForm->getValue('username'))
					->setCredential(md5($loginForm->getValue('password')));
				
				$result = $auth->authenticate($adapter);
				
				if ($result->isValid()) {
					$user = Application_Model_UserMapper::getInstance()->find($adapter->getResultRowObject()->id);
					$session = new Zend_Session_Namespace('karanaya');
					$session->user = $user;
					$this->_redirect('/');
					return;
				}
				
			}
			
			$this->view->form = $loginForm;
		}
	}

	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		$session = new Zend_Session_Namespace('karanaya');
		unset($session);
		$this->_redirect('/');
		return;
	}

	public function registerAction() {
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->_redirect('/');
			return;
		} else {
			$registerForm = new Application_Form_Auth_Register();
			
			if ($this->getRequest()->isPost() && $registerForm->isValid($this->getRequest()->getPost())) {
				$user = new Application_Model_User($registerForm->getValues());
				$user->save();
				
				$adapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
				$adapter->setTableName('users')
					->setIdentityColumn('username')
					->setCredentialColumn('password');
				
				$adapter->setIdentity($registerForm->getValue('username'))
					->setCredential(md5($registerForm->getValue('password')));
				
				$result = $auth->authenticate($adapter);
				
				if ($result->isValid()) {
					$session = new Zend_Session_Namespace('karanaya');
					$session->user = $user;
					$this->_redirect('/');
					return;
				}
				
			}
			
			$this->view->form = $registerForm;
		}
	}

	public function manageAction() {

	}

}