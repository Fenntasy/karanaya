<?php

class SongController extends Application_Plugin_Action_Auth {

	public function init() {
		/* Initialize action controller here */
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'song', 'action' => 'add'), 'default') . '">Add</a>');
	}

	public function indexAction() {
		$this->_redirect('/song/show/');
		return;
	}

	public function showAction() {
		$id = $this->_request->getParam('id');

		$song = Application_Model_SongMapper::getInstance()->find($id);
		if ($song) {
			$this->view->song = $song;
			$karaokes = Application_Model_KaraokeMapper::getInstance()->findBySong($id);
			$this->view->karaokes = $karaokes;
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'song'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'song'), 'delete') . '\'; }">Delete</a>');
		}
	}

	public function listAction() {
		
	}

	public function listjsonAction() { 
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();	
		$songs = Application_Model_SongMapper::getInstance()->fetchAll();

		$output = array(
				"sEcho" => intval(isset($_GET['sEcho'])?$_GET['sEcho']:0),
				"aaData" => array(),
				'iTotalRecords' => count($songs),
				);
		
		foreach ($songs as $song) {
			$output['aaData'][] = array($song->getId(), $song->getName());
		}

		echo json_encode($output);
	}

	public function addAction() {
		$form = new Application_Form_Song();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$data = $form->getValues();
			$data = $this->addArtistIfNotExists($data);
			$song = new Application_Model_Song($data);
			$song->save();

			$this->_redirect('/song/edit/' . $song->getId());
		}

		$this->view->form = $form;
		$this->_helper->viewRenderer('edit');
	}

	public function addArtistIfNotExists($data) {
		if (!$data['artist'] && $data['artist_name']) {
			$artist = Application_Model_Mapper_Artist::getInstance()->findByName($data['artist_name'], 0, 0, true);
			if (!$artist) {
				$artist = new Application_Model_Artist();
				$artist->setName($data['artist_name']);
				$artist->save();
			}
			$data['artist'] = $artist->getId();
		}
		unset($data['artist_name']);
		return $data;
	}

	public function editAction() {
		$id = $this->_request->getParam('id');
		$form = new Application_Form_Song();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$data = $form->getValues();
			$data = $this->addArtistIfNotExists($data);
			$song = new Application_Model_Song($data);
			$song->save();

			$this->_redirect('/song/edit/' . $song->getId());
		} else {
			$song = Application_Model_SongMapper::getInstance()->find($id);
		}

		$form->populate($song->__toArray());

		$this->view->form = $form;
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$song = Application_Model_SongMapper::getInstance()->find($id);
		$song->delete();
		unset($song);
		$this->_redirect('/song');
	}

	public function searchAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$songs = Application_Model_SongMapper::getInstance()->findByName($_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($songs as $song) {
			$name = $song->getName();
			if (isset($_GET['withArtist']) && $_GET['withArtist']) {
				$name .= ' - ' . $song->getArtist()->getName();
			}
			$output[] = array('id' => $song->getId(), 'label' => $name, 'value' => $name);
		}
		echo json_encode($output);
	}

}

