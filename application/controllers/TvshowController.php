<?php

class TvshowController extends Application_Plugin_Action_Auth {

	public function init() {
		/* Initialize action controller here */
	}

	public function indexAction() {
		$this->_redirect('/tvshow/list/');
		return;
	}

	public function showAction() {
		$id = $this->_request->getParam('id');

		$show = Application_Model_Mapper_Tvshow::getInstance()->find($id);
		if ($show) {
			$this->view->show = $show;
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'tvshow'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'tvshow'), 'delete') . '\'; }">Delete</a>');
		}
	}

	public function listAction() {
		
	}

	public function listjsonAction() { 
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();	
		$tvshows = Application_Model_Mapper_Tvshow::getInstance()->fetchAll();

		$output = array(
				"sEcho" => intval(isset($_GET['sEcho'])?$_GET['sEcho']:0),
				"aaData" => array(),
				'iTotalRecords' => count($tvshows),
				);
		
		foreach ($tvshows as $tvshow) {
			$output['aaData'][] = array($tvshow->getId(), $tvshow->getName());
		}

		echo json_encode($output);
	}
	

	public function addAction() {
		$form = new Application_Form_Tvshow();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$show = new Application_Model_Tvshow($form->getValues());
			$show->save();

			$this->_redirect('/tvshow/edit/' . $show->getId());
		}

		$this->view->form = $form;
		$this->_helper->viewRenderer('edit');
	}

	public function editAction() {
		$id = $this->_request->getParam('id');


		$form = new Application_Form_Tvshow();

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$show = new Application_Model_Tvshow($form->getValues());
			$show->save();
		} else {
			$show = Application_Model_Mapper_Tvshow::getInstance()->find($id);
		}

		$form->populate($show->__toArray());
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $show->getId(), 'controller' => 'tvshow'), 'delete') . '">Delete</a>');
		$this->view->form = $form;
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$show = Application_Model_Mapper_Tvshow::getInstance()->find($id);
		$show->delete();
		unset($show);
		$this->_redirect('/tvshow');
	}

	public function searchAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$shows = Application_Model_Mapper_Tvshow::getInstance()->findByName($_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($shows as $show) {
			$output[] = array('id' => $show->getId(), 'label' => $show->getName(), 'value' => $show->getName());
		}
		echo json_encode($output);
	}

	public function searchepisodeAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$showEpisodes = Application_Model_Mapper_TvshowEpisode::getInstance()->findByName($_GET['show'], $_GET['term'], 0, $_GET['length']);
		$output = array();
		foreach ($showEpisodes as $episode) {
			$output[] = array('id' => $episode->getId(), 'label' => $episode->getSimpleName() . ' - ' . $episode->getName(), 'value' => $episode->getSimpleName() . ' - ' . $episode->getName());
		}
		echo json_encode($output);
	}
}

