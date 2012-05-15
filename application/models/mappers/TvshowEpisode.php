<?php

class Application_Model_Mapper_TvshowEpisode {

	private static $_instance = null;
	private static $_showList = null;
	private $_dbTable;
	protected $_dbTableName = 'Application_Model_DbTable_TvshowEpisode';

	private function __construct () {

	}

	public static function getInstance () {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function setDbTable ($dbTable) {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable () {
		if (null === $this->_dbTable) {
			$this->setDbTable($this->_dbTableName);
		}
		return $this->_dbTable;
	}

	public function find ($id) {
		$result = $this->getDbTable()->find($id);

		if (0 == count($result)) {
			return;
		} else if (1 == count($result)) {
			$row = $result->current();

			$entry = new Application_Model_TvshowEpisode();
			$entry->setId($row->id)
				->setName($row->name)
				->setSimpleName($row->season . str_pad($row->number, 2, '0', STR_PAD_LEFT));

			return $entry;
		} else {
			$entries = array();

			foreach ($result as $row) {
				$entry = new Application_Model_TvshowEpisode();
				$entry->setId($row->id)
					->setName($row->name)
					->setSimpleName($row->season . str_pad($row->number, 2, '0', STR_PAD_LEFT));
				$entries[] = $entry;
			}

			return $entries;
		}
	}

	public function findByName ($show, $name, $start = 0, $length = 0, $strict = false) {
		if (empty($name)) {
			return $this->fetchAll();
		}
		$select = $this->getDbTable()->select();
		if (is_numeric($name) && strlen($name) == 3) {
			$season = (int) substr($name, 0, 1);
			$number = (int) substr($name, 1);
			$select->where('tvshow = ?', $show)
				->where('season = ?', $season)
				->where('number = ?', $number);
		} else {
			if ($strict) {
				$select->where('tvshow = ?', $show)
					->where('name LIKE ?', $name);
			} else {
				$select->where('tvshow = ?', $show)
					->where('name LIKE ?', '%' . $name . '%');
			}
		}

		if ($start || $length) {
			$select = $select->limit($length, $start);
		}

		$resultSet = $this->getDbTable()->fetchAll($select);
		$entries = array();

		foreach ($resultSet as $row) {
			$entry = new Application_Model_TvshowEpisode();
			$entry->setId($row->id)
				->setName($row->name)
				->setSimpleName($row->season . str_pad($row->number, 2, '0', STR_PAD_LEFT));
			$entries[] = $entry;
		}

		return $entries;
	}

	public function fetchAll ($ordered = false) {
		if (is_null(self::$_showList)) {
			$select = $this->getDbTable()->select()->order('name');
			$resultSet = $this->getDbTable()->fetchAll($select);

			$entries = array();
			foreach ($resultSet as $row) {
				$entry = new Application_Model_Tvshow();
				$entry->setId($row->id)
					->setName($row->name)
					->setSimpleName($row->season . str_pad($row->number, 2, '0', STR_PAD_LEFT));
				if ($ordered) {
					$entries[$row->id] = $entry;
				} else {
					$entries[] = $entry;
				}
			}

			self::$_showList = $entries;
		}

		return self::$_showList;
	}

	public function save ($showEpisode) {
		$data = $showEpisode->__toArray();

		if (null == ($id = $showEpisode->getId())) {
			unset($data['id']);
			$id = $this->getDbTable()->insert($data);
			$showEpisode->setId($id);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	public function delete ($show) {
		$id = $show->getId();
		$this->getDbTable()->delete(array('id = ?' => $id));
	}

}
