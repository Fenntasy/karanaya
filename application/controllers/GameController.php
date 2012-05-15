<?php

class GameController extends Application_Plugin_Action_Auth {

	public function init() {
		/* Initialize action controller here */
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'game', 'action' => 'add'), 'default') . '">Add</a>');
	}

	public function indexAction() {
		$this->_redirect('/game/list/' . $id);
		return;
	}

	public function showAction() {
		$id = $this->_request->getParam('id');
		$game = Application_Model_Mapper_Game::getInstance()->find($id);
		if ($game) {
			$this->view->game = $game;
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'game'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'game'), 'delete') . '\'; }">Delete</a>');
		}
	}

	public function listAction() {
	}

	public function listjsonAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();	
		$games = Application_Model_Mapper_Game::getInstance()->fetchAll();

		$output = array(
				"sEcho" => intval(isset($_GET['sEcho'])?$_GET['sEcho']:0),
				"aaData" => array(),
				'iTotalRecords' => count($games),
				);
		
		foreach ($games as $game) {
			$output['aaData'][] = array($game->getId(), $game->getName());
		}

		echo json_encode($output);
	}

	public function addAction() {
		$form = new Application_Form_Game();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$game = new Application_Model_Game($form->getValues());
			$game->save();

			$this->_redirect('/game/edit/' . $game->getId());
		}

		$this->view->form = $form;
		$this->_helper->viewRenderer('edit');
	}

	public function editAction() {
		$id = $this->_request->getParam('id');


		$form = new Application_Form_Game();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$game = new Application_Model_Game($form->getValues());
			$game->save();
		} else {
			$game = Application_Model_Mapper_Game::getInstance()->find($id);
		}

		$form->populate($game->__toArray());
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $game->getId(), 'controller' => 'game'), 'delete') . '">Delete</a>');
		$this->view->form = $form;
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$game = Application_Model_Mapper_Game::getInstance()->find($id);
		$game->delete();
		unset($game);
		$this->_redirect('/game');
	}

	public function searchAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$games = Application_Model_Mapper_Game::getInstance()->findByName($_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($games as $game) {
			$output[] = array('id' => $game->getId(), 'label' => $game->getName(), 'value' => $game->getName());
		}
		echo json_encode($output);
	}
}

