<?php 
 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initRootes() {
	$controller = Zend_Controller_Front::getInstance();
	$router = $controller->getRouter();

	$router->addRoute('login', new Zend_Controller_Router_Route_Static(
			'login',
			array('controller' => 'auth', 'action' => 'login')
	));
	$router->addRoute('logout', new Zend_Controller_Router_Route_Static(
			'logout',
			array('controller' => 'auth', 'action' => 'logout')
	));
	$router->addRoute('register', new Zend_Controller_Router_Route_Static(
			'register',
			array('controller' => 'auth', 'action' => 'register')
	));
	$router->addRoute('show', new Zend_Controller_Router_Route(
			':controller/:id',
			array('action' => 'show'),
			array('id' => '\d+')
	));
	$router->addRoute('edit', new Zend_Controller_Router_Route(
			':controller/edit/:id',
			array('action' => 'edit'),
			array('id' => '\d+')
	));
	$router->addRoute('delete', new Zend_Controller_Router_Route(
			':controller/delete/:id',
			array('action' => 'delete'),
			array('id' => '\d+')
	));
	$router->addRoute('flaglist', new Zend_Controller_Router_Route(
			':controller/list/:flag/:search',
			array('action' => 'list')			
	));
    }

    protected function _initAcl() {
	$acl = new Zend_Acl();

	$acl->addRole(new Zend_Acl_Role('guest'))
		->addRole(new Zend_Acl_Role('member'), 'guest')
		->addRole(new Zend_Acl_Role('subber', 'member'))
		->addRole(new Zend_Acl_Role('admin'));

	$acl->allow('guest', null, 'view');
	$acl->allow('admin');
    }

    protected function _initPlaceholders() {
	$this->bootstrap('view');
	$view = $this->getResource('view');
	$view->doctype('XHTML1_STRICT');

	// Set the initial title and separator:
	$view->headTitle('karanaya')
		->setSeparator(' - ');

	// Set global CSS file
	$view->headLink()->prependStylesheet('/css/global.css');
    }

    protected function _initSidebars() {
	$this->bootstrap('View');
	$view = $this->getResource('View');

	$view->placeholder('mainmenu')
		->setPrefix("<div id=\"mainmenu\">\n    <div class=\"main\">\n")
		->setSeparator("</div>\n    <div class=\"main\">\n")
		->setPostfix("</div>\n</div>");

	$view->placeholder('actionmenu')
		->setPrefix("<div id=\"actionmenu\">\n    <div class=\"action\">\n")
		->setSeparator("</div>\n    <div class=\"action\">\n")
		->setPostfix("</div>\n</div>");

	$view->placeholder('sidebar')
		->setPrefix("<div id=\"sidebar\">\n    <div class=\"block\">\n")
		->setSeparator("</div>\n    <div class=\"block\">\n")
		->setPostfix("</div>\n</div>");
    }

}

