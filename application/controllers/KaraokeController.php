<?php

class KaraokeController extends Application_Plugin_Action_Auth {

	public function init() {
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'karaoke', 'action' => 'add'), 'default') . '">Add</a>');		
	}

	public function indexAction() {
		$this->_redirect('/karaoke/list/');
	}

	public function showAction() {
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'karaoke', 'action' => 'check'), 'default') . '">Check Karaoke</a>');
		$id = $this->_request->getParam('id');

		$karaoke = Application_Model_KaraokeMapper::getInstance()->find($id);
		if ($karaoke) {
			$this->view->karaoke = $karaoke;
			$karaoke_options = $this->getInvokeArg('bootstrap')->getOption('karaoke');
			$this->view->isAnime = ($karaoke->getSourceType() == Application_Model_KaraokeSourceType::$ANIME)? true : false;

			$this->view->directory = $karaoke_options['directory'];
			$ass_filename = $karaoke->getSubFilename();
			if (file_exists($karaoke_options['directory'] . '/' . $ass_filename)) {
				$ass = new Application_Model_Ass();
				$ass->loadFile($karaoke_options['directory'] . '/' . $ass_filename);
				$this->view->fonts = $ass->getFonts();
			}
			$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('id' => $id, 'controller' => 'karaoke'), 'edit') . '">Edit</a>');
			$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'karaoke'), 'delete') . '\'; }">Delete</a>');
		}
	}
	public function listAction() {
		$this->view->placeholder('actionmenu')->append('<a href="' . $this->view->url(array('controller' => 'karaoke', 'action' => 'check'), 'default') . '">Check Karaoke</a>');
		
	}

	public function listjsonAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$totalRecords = Application_Model_KaraokeMapper::getInstance()->getTotalRecords();

		$output = array(
			"sEcho" => intval((isset($_GET['sEcho']))? $_GET['sEcho']: 0),
			"aaData" => array(),
			'iTotalRecords' => $totalRecords
		);

		if (isset($_GET['sSearch'])) {
			$karaokes = Application_Model_KaraokeMapper::getInstance()->findByName($_GET['sSearch'], $_GET['iDisplayStart'], $_GET['iDisplayLength']);
			$output['iTotalDisplayRecords'] = Application_Model_KaraokeMapper::getInstance()->getTotalRecords($_GET['sSearch']);
		} else {
			$output['iTotalDisplayRecords'] = $totalRecords;
			if (isset($_GET['iDisplayStart']) && isset($_GET['iDisplayLength'])) {
				$karaokes = Application_Model_KaraokeMapper::getInstance()->fetchAll(true, $_GET['iDisplayStart'], $_GET['iDisplayLength']);
			} else {
				$karaokes = Application_Model_KaraokeMapper::getInstance()->fetchAll();
			}
		}
		foreach ($karaokes as $karaoke) {
			$output['aaData'][] = array(
                $karaoke->getId(),
                Application_Model_KaraokeSourceType::getInstance()->getDirectory($karaoke->getSourceType()),
                $karaoke->getSource()->getName(),
                $karaoke->getType()->identifier . $karaoke->getTypeNumber(),
                $karaoke->getSong()->getName(),
                $karaoke->getVersion(),
                $karaoke->getExtension(),
                $karaoke->getHumanDuration(),
                $karaoke->getSubType()->name,
                ($karaoke->getMadeBy() ? $karaoke->getMadeBy()->getDisplayName() : 'Other'),
                $karaoke->getLanguage()->name,
                null
            );
		}

		echo json_encode($output);
	}

	public function editAction() {
		$id = $this->_request->getParam('id');
		$karaoke = Application_Model_KaraokeMapper::getInstance()->find($id);
		$this->view->placeholder('actionmenu')->append('<a href="#" onclick="if(confirm(\'Are you sure ?\')) { window.location.href=\'' . $this->view->url(array('id' => $id, 'controller' => 'karaoke'), 'delete') . '\'; }">Delete</a>');

		$form = Application_Model_KaraokeSourceType::getForm($karaoke->getSourceType());
		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			if ($karaoke->getSourceType() == Application_Model_KaraokeSourceType::$MISC) {
				$values = $form->getValues();
				$misc = new Application_Model_Misc(array('id' => $values['source'], 'name' => $values['misc_name']));
				$misc->save();
			}
			$karaoke = new Application_Model_Karaoke($form->getValues());
			$karaoke->save();
			$this->_redirect('/karaoke/edit/' . $karaoke->getId());
		}

		$form->populate($karaoke->__toArray());
		$this->view->form = $form;
	}

	public function addAction() {
		$form_anime = new Application_Form_Karaoke_Anime();
		$form_game = new Application_Form_Karaoke_Game();
		$form_artist = new Application_Form_Karaoke_Artist();
		$form_misc = new Application_Form_Karaoke_Misc();
		$form_tvshow = new Application_Form_Karaoke_Tvshow();

		if ($this->getRequest()->isPost() && $form_anime->isValid($this->getRequest()->getPost())) {
            $data = $form_anime->getValues();
            if (!$form_anime->getValue('song')) {
                $song = new Application_Model_Song(array('name' => $form_anime->getValue('song_name')));
                $song->save();
                $data['song'] = $song->getId();
            }
			$karaoke = new Application_Model_Karaoke($data);
			if ($karaoke->save()) {
				$this->_redirect('/karaoke/edit/' . $karaoke->getId());
			} else {
				$form_anime->populate($karaoke->__toArray());
			}
		}
		if ($this->getRequest()->isPost() && $form_game->isValid($this->getRequest()->getPost())) {
            $data = $form_game->getValues();
            if (!$form_game->getValue('song')) {
                $song = new Application_Model_Song(array('name' => $form_game->getValue('song_name')));
                $song->save();
                $data['song'] = $song->getId();
            }
			$karaoke = new Application_Model_Karaoke($data);
			if ($karaoke->save()) {
				$this->_redirect('/karaoke/edit/' . $karaoke->getId());
			} else {
				$form_game->populate($karaoke->__toArray());
			}
		}
		if ($this->getRequest()->isPost() && $form_artist->isValid($this->getRequest()->getPost())) {
            $data = $form_artist->getValues();
            if (!$form_artist->getValue('song')) {
                $song = new Application_Model_Song(array('name' => $form_artist->getValue('song_name')));
                $song->save();
                $data['song'] = $song->getId();
            }
			$karaoke = new Application_Model_Karaoke($data);
			if ($karaoke->save()) {
				$this->_redirect('/karaoke/edit/' . $karaoke->getId());
			} else {
				$form_artist->populate($karaoke->__toArray());
			}
		}
		if ($this->getRequest()->isPost() && $form_misc->isValid($this->getRequest()->getPost())) {
            $values = $form_misc->getValues();
            if (!$form_misc->getValue('song')) {
                $song = new Application_Model_Song(array('name' => $form_misc->getValue('song_name')));
                $song->save();
                $values['song'] = $song->getId();
            }
			$misc = new Application_Model_Misc(array('name' => $values['misc_name']));
			$misc->save();
			$values['source'] = $misc->getId();
			$karaoke = new Application_Model_Karaoke($values);
			if ($karaoke->save()) {
				$this->_redirect('/karaoke/edit/' . $karaoke->getId());
			} else {
				$form_artist->populate($karaoke->__toArray());
			}
		}
		if ($this->getRequest()->isPost() && $form_tvshow->isValid($this->getRequest()->getPost())) {
            $data = $form_tvshow->getValues();
            if (!$form_tvshow->getValue('song')) {
                $song = new Application_Model_Song(array('name' => $form_tvshow->getValue('song_name')));
                $song->save();
                $data['song'] = $song->getId();
            }
			$karaoke = new Application_Model_Karaoke($data);
			if ($karaoke->save()) {
				$this->_redirect('/karaoke/edit/' . $karaoke->getId());
			} else {
				$form_artist->populate($karaoke->__toArray());
			}
		}
		$this->view->form_anime = $form_anime->setName('AnimeForm');
		$this->view->form_game = $form_game->setName('GameForm');
		$this->view->form_artist = $form_artist->setName('ArtistForm');
		$this->view->form_misc = $form_misc->setName('MiscForm');
		$this->view->form_tvshow = $form_tvshow->setName('TvshowForm');
		//	$this->_helper->viewRenderer('edit');
	}

	public function deleteAction() {
		$id = $this->_request->getParam('id');
		$karaoke = Application_Model_KaraokeMapper::getInstance()->find($id);
		$karaoke->delete();
		unset($karaoke);
		$this->_redirect('/karaoke');
	}

	public function checkAction() {
		$karaoke_options = $this->getInvokeArg('bootstrap')->getOption('karaoke');
		$directory = $karaoke_options['directory'];
		$karaokes = Application_Model_KaraokeMapper::getInstance()->fetchAll();

		$files = array();
		$files_dirs = array();
		if ($handle = opendir($directory)) {
			while (false !== ($file = readdir($handle))) {
				if (substr($file, 0, 1) != '.' && $file != 'Fonts' && $file != 'Playlists' && $file != 'Temp' && is_dir($directory . $file) && $handle2 = opendir($directory . $file)) {
					while (false !== ($file2 = readdir($handle2))) {
						if (substr($file2, 0, 1) != '.') {
							$files[$file2] = $file2;
							$files_dirs[$file2] = $file;
						}
					}
				}
			}
			closedir($handle);
		}

		$faulty_subs = array();
		$faulty_videos = array();
		$faulty_files = array();
		foreach ($karaokes as $karaoke) {
			$dir = $directory . Application_Model_KaraokeSourceType::getInstance()->getDirectory($karaoke->getSourceType());
			if (!$karaoke->checkSubExists($dir)) {
				$faulty_subs[$dir.'/'.$karaoke->getFilename()] = $karaoke;
			} else {
				unset($files[$karaoke->getFilename()]);
			}
			if (!$karaoke->checkVideoExists($dir)) {
				$faulty_videos[$dir.'/'.$karaoke->getSubFilename()] = $karaoke;
			} else {
				unset($files[$karaoke->getSubFilename()]);
			}
		}


        foreach($files as $f) {
            $faulty_files[] = $files_dirs[$f] . '/' . str_replace(' ', '&nbsp;', $f);
        }

        sort($faulty_files);
        ksort($faulty_videos);
        ksort($faulty_subs);

		$this->view->faulty_subs = $faulty_subs;
		$this->view->faulty_videos = $faulty_videos;
		$this->view->faulty_files = $faulty_files;
	}

	public function formAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$type = $this->_request->getParam('type');
		$form = Application_Model_KaraokeSourceType::getForm($type);
		$form->removeDecorator('Form');
		$form->addDecorator(new Application_Form_Decorator_Form());
		$form->addDecorator(new Application_Form_Decorator_JsValidation());
		echo $form;
	}

}
