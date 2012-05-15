<?php

class ArtistController extends Application_Plugin_Action_Auth {

	public function init() {
		/* Initialize action controller here */
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'artist', 'action' => 'add'), 'default') . '">Add</a>');
	}

	public function indexAction() {
		$this->_redirect('/artist/list');
		return;
	}

	public function showAction() {
		$id = $this->_request->getParam('id');

		$artist = Application_Model_Mapper_Artist::getInstance()->find($id);
		if ($artist) {
			$this->view->artist = $artist;
			$karaokes = array();
			foreach($artist->getSongs() as $song) {
				$karaoke = Application_Model_KaraokeMapper::getInstance()->findBySong($song->getId());
				$karaokes[$song->getId()] = $karaoke;
			}
			$this->view->karaokes = $karaokes;
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'artist'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'artist'), 'delete') . '\'; }">Delete</a>');
		}
	}
	
	public function listAction() {
	}
	
	public function listjsonAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();	
		$artists = Application_Model_Mapper_Artist::getInstance()->fetchAll();

		$output = array(
				"sEcho" => intval(isset($_GET['sEcho'])?$_GET['sEcho']:0),
				"aaData" => array(),
				'iTotalRecords' => count($artists),
				);

		
		foreach ($artists as $artist) {
			$output['aaData'][] = array($artist->getId(), $artist->getName());
		}

		echo json_encode($output);
	
	}

	public function addAction() {
		$form = new Application_Form_Artist();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$artist = new Application_Model_Artist($form->getValues());
			$artist->save();

			$this->_redirect('/artist/edit/' . $artist->getId());
		}

		$this->view->form = $form;
		$this->_helper->viewRenderer('edit');
	}

	public function editAction() {
		$id = $this->_request->getParam('id');


		$form = new Application_Form_Artist();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$artist = new Application_Model_Artist($form->getValues());
			$artist->save();
		} else {
			$artist = Application_Model_Mapper_Artist::getInstance()->find($id);
		}

		$form->populate($artist->__toArray());
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $artist->getId(), 'controller' => 'artist'), 'delete') . '">Delete</a>');
		$this->view->form = $form;
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$artist = Application_Model_Mapper_Artist::getInstance()->find($id);
		$artist->delete();
		unset($artist);
		$this->_redirect('/artist');
	}

	public function searchAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$artists = Application_Model_Mapper_Artist::getInstance()->findByName($_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($artists as $artist) {
			$output[] = array('id' => $artist->getId(), 'label' => $artist->getName(), 'value' => $artist->getName());
		}
		echo json_encode($output);
	}
}

