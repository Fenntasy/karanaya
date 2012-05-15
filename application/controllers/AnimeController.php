<?php

class AnimeController extends Application_Plugin_Action_Auth {

	public function init() {
		/* Initialize action controller here */
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'anime', 'action' => 'add'), 'default') . '">Add</a>');
	}

	public function indexAction() {
		$this->_redirect('/anime/list/');
		return;
	}

	public function showAction() {
		$id = $this->_request->getParam('id');
		$anime = Application_Model_AnimeMapper::getInstance()->find($id);
		if ($anime) {
			$this->view->anime = $anime;
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'anime'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'anime'), 'delete') . '">Delete</a>');
		}
	}

	public function listAction() {
		if ($this->_request->getParam('flag')) {
			$authorized_flags = array('genre', 'tag');

			if (!in_array($this->_request->getParam('flag'), $authorized_flags)) {
				header('HTTP/1.0 403 Forbidden');
				echo 'Forbidden';
				$this->_helper->layout->disableLayout();
				$this->_helper->viewRenderer->setNoRender();
				return;
			}
			$this->view->flag = strtolower($this->_request->getParam('flag'));
			$this->view->search = strtolower($this->_request->getParam('search'));
		}
	}

	public function listjsonAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$totalRecords = Application_Model_AnimeMapper::getInstance()->getTotalRecords();

		$output = array(
				"sEcho" => intval(isset($_GET['sEcho'])?$_GET['sEcho']:0),
				"aaData" => array(),
				'iTotalRecords' => $totalRecords
				);

		if ($this->_request->getParam('flag')) {
			$flag = strtolower($this->_request->getParam('flag'));
			$search = strtolower($this->_request->getParam('search'));
			$animes = Application_Model_AnimeMapper::getInstance()->fetchFlag($flag, $search, isset($_GET['sSearch'])?$_GET['sSearch']:null, isset($_GET['iDisplayStart'])?$_GET['iDisplayStart']:null, isset($_GET['iDisplayLength'])?$_GET['iDisplayLength']:null);
			$output['iTotalRecords'] = Application_Model_AnimeMapper::getInstance()->getTotalRecordsForFlagSearch($flag, $search);
			$output['iTotalDisplayRecords'] = Application_Model_AnimeMapper::getInstance()->getTotalRecordsForFlagSearch($flag, $search, $_GET['sSearch'], $_GET['iDisplayStart'], $_GET['iDisplayLength']);
		} else if (isset($_GET['sSearch'])) {
			$animes = Application_Model_AnimeMapper::getInstance()->findByName($_GET['sSearch'], isset($_GET['iDisplayStart'])?$_GET['iDisplayStart']:null, isset($_GET['iDisplayLength'])?$_GET['iDisplayLength']:null);
			$output['iTotalDisplayRecords'] = Application_Model_AnimeMapper::getInstance()->getTotalRecords($_GET['sSearch']);
		} else {
			$output['iTotalDisplayRecords'] = $totalRecords;
			$animes = Application_Model_AnimeMapper::getInstance()->fetchAll(isset($_GET['iDisplayStart'])?$_GET['iDisplayStart']:null, isset($_GET['iDisplayLength'])?$_GET['iDisplayLength']:null);
		}
		foreach ($animes as $anime) {
			$output['aaData'][] = array($anime->getId(), $anime->getName());
		}

		echo json_encode($output);
	}

	public function flagsearchAction() {
		$flag = strtolower($this->_request->getParam('flag'));
		$authorized_flags = array('genre', 'tag');

		if (!in_array($flag, $authorized_flags)) {
			header('HTTP/1.0 403 Forbidden');
			echo 'Forbidden';
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			return;
		}
		$search = strtolower($this->_request->getParam('search'));

		$this->view->animes = Application_Model_AnimeMapper::getInstance()->fetchFlag($flag, $search);
		$this->_helper->viewRenderer('show');
	}

	public function addAction() {
		$form = new Application_Form_Anime();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$anime = new Application_Model_Anime($form->getValues());
			$anime->save();

			$this->_redirect('/anime/edit/' . $anime->getId());
		}
		$this->view->form = $form;
		$this->_helper->viewRenderer('edit');
	}

	public function addmalAction() {
		$id = $this->_request->getParam('id');
		echo 'php ' . APPLICATION_PATH . '/scripts/getAnime.php -i ' . $id . '<br/><br/>';
		system('php ' . APPLICATION_PATH . '/scripts/getAnime.php -i ' . $id);
		die();
	}

	public function editAction() {
		$id = $this->_request->getParam('id');

		$form = new Application_Form_Anime();
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$anime = new Application_Model_Anime($form->getValues());
			$anime->save();

			$this->_redirect('/anime/' . $anime->getId());
		} else {
			$anime = Application_Model_AnimeMapper::getInstance()->find($id);
		}

		$form->populate($anime->__toArray());
		$this->view->form = $form;
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$anime = Application_Model_AnimeMapper::getInstance()->find($id);
		$anime->delete();
		$this->_redirect('/anime/list');
	}

	public function searchAction() {
		/**
		 * Search MAL
		 * 
		 $this->_helper->layout->disableLayout();
		 $this->_helper->viewRenderer->setNoRender();

		 echo file_get_contents('http://mal-api.com/anime/search?q=' . urlencode($post['q']));

		 return;
		*/
		$search = strtolower($this->_request->getParam('search'));

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$animes = Application_Model_AnimeMapper::getInstance()->findByName($_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($animes as $anime) {
			$output[] = array('id' => $anime->getId(), 'label' => $anime->getName(), 'value' => $anime->getName());
		}
		echo json_encode($output);
	}

}

