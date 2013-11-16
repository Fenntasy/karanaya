<?php

class Application_Model_AnimeMapper
{

    private static $_instance = null;
    private static $_animeList = null;
    private $_dbTable;
    protected $_dbTableName = 'Application_Model_DbTable_Anime';

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Application_Model_AnimeMapper();
        }

        return self::$_instance;
    }

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbTableName);
        }
        return $this->_dbTable;
    }

    public function find($id)
    {
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return;
        } else if (1 == count($result)) {
            $row = $result->current();

            $db = Zend_Db_Table::getDefaultAdapter();
            $tags_select = $db->select()
                ->from('animeTag', array('id', 'name'))
                ->joinLeft('anime_animeTag', 'animeTag.id = anime_animeTag.tag', '')
                ->where('anime_animeTag.anime = ?', $row->id);
            $tags = $db->fetchAll($tags_select);

            $genres_select = $db->select()
                ->from('animeGenre', array('id', 'name'))
                ->joinLeft('anime_animeGenre', 'animeGenre.id = anime_animeGenre.genre', '')
                ->where('anime_animeGenre.anime = ?', $row->id);
            $genres = $db->fetchAll($genres_select);

            $otherTitles_select = $db->select()
                ->from('anime_otherTitle', array('id', 'type', 'title'))
                ->where('anime = ?', $row->id);
            $otherTitles = array();
            $results = $db->fetchAll($otherTitles_select);
            foreach ($results as $r) {
                if (!isset($otherTitles[$r['type']])) {
                    $otherTitles[$r['type']] = array();
                }
                $otherTitles[$r['type']][] = $r['title'];
            }

            $anime = new Application_Model_Anime();
            $anime->setId($row->id)
                ->setName($row->name)
                ->setMal_id($row->mal_id)
                ->setNeed_update($row->need_update)
                ->setSimpleName($row->simpleName)
                ->setEpisodes($row->episodes)
                ->setStatus($row->status)
                ->setClassification($row->classification)
                ->setSynopsis($row->synopsis)
                ->setTags($tags)
                ->setGenres($genres)
                ->setOtherTitles($otherTitles);

            return $anime;
        } else {
            $entries = array();

            foreach ($result as $row) {
                $entry = new Application_Model_Anime();
                $entry->setId($row->id)
                    ->setName($row->name)
                    ->setMal_id($row->mal_id)
                    ->setNeed_update($row->need_update)
                    ->setSimpleName($row->simpleName)
                    ->setEpisodes($row->episodes)
                    ->setStatus($row->status)
                    ->setClassification($row->classification)
                    ->setSynopsis($row->synopsis);
                $entries[] = $entry;
            }

            return $entries;
        }
    }

    public function getTotalRecordsForFlagSearch($flag, $search, $name = '', $start = 0, $length = 0)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('anime', 'COUNT(*)')
            ->joinLeft('anime_anime' . ucfirst($flag), 'anime_anime' . ucfirst($flag) . '.anime = anime.id', '')
            ->joinLeft('anime' . ucfirst($flag), 'anime_anime' . ucfirst($flag) . '.' . $flag . ' = anime' . ucfirst($flag) . '.id', '')
            ->where('anime' . ucfirst($flag) . '.name LIKE ?', $search)
            ->order('anime.name');

        if ($name) {
            $select->where('anime.name LIKE ?', '%' . $name . '%');
        }

        return $db->fetchOne($select);
    }

    public function getTotalRecords($name = '')
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from('anime', 'COUNT(*)');
        if ($name) {
            $select->where('name LIKE ?', '%' . $name . '%');
        }

        return $db->fetchOne($select);
    }

    public function findByName($name, $start = 0, $length = 0)
    {
        if (empty($name)) {
            return $this->fetchAll();
        }
        $select = $this->getDbTable()->select()
            ->where('name LIKE ?', '%' . $name . '%');

        if ($start || $length) {
            $select = $select->limit($length, $start);
        }

        $resultSet = $this->getDbTable()->fetchAll($select);
        $entries = array();

        foreach ($resultSet as $row) {
            $entry = new Application_Model_Anime();
            $entry->setId($row->id)
                ->setName($row->name)
                ->setSimpleName($row->simpleName);
            $entries[] = $entry;
        }

        return $entries;
    }

    public function fetchFlag($flag, $search, $name, $start = 0, $length = 0)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $select = $db->select()
            ->from('anime')
            ->joinLeft('anime_anime' . ucfirst($flag), 'anime_anime' . ucfirst($flag) . '.anime = anime.id', '')
            ->joinLeft('anime' . ucfirst($flag), 'anime_anime' . ucfirst($flag) . '.' . $flag . ' = anime' . ucfirst($flag) . '.id', '')
            ->where('anime' . ucfirst($flag) . '.name LIKE ?', $search)
            ->order('anime.name');

        if ($start || $length) {
            $select = $select->limit($length, $start);
        }
        if ($name) {
            $select->where('anime.name LIKE ?', '%' . $name . '%');
        }
        $resultSet = $db->fetchAll($select);

        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Anime();
            $entry->setId($row->id)
                ->setName($row->name)
                ->setSimpleName($row->simpleName);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function fetchAll($ordered = false, $start = 0, $length = 0)
    {
        if (is_null(self::$_animeList)) {
            $select = $this->getDbTable()->select()->order('name');
            if ($start || $length) {
                $select = $select->limit($length, $start);
            }

            $resultSet = $this->getDbTable()->fetchAll($select);

            $entries = array();
            foreach ($resultSet as $row) {
                $entry = new Application_Model_Anime();
                $entry->setId($row->id)
                    ->setName($row->name)
                    ->setSimpleName($row->simpleName);
                if ($ordered) {
                    $entries[$row->id] = $entry;
                } else {
                    $entries[] = $entry;
                }
            }

            self::$_animeList = $entries;
        }

        return self::$_animeList;
    }

    public function save($anime)
    {
        $data = $anime->__toArray();

        if (null == ($id = $anime->getId())) {
            unset($data['id']);
            $id = $this->getDbTable()->insert($data);
            $anime->setId($id);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }

    public function delete($anime)
    {
        $id = $anime->getId();
        $this->getDbTable()->delete(array('id = ?' => $id));
    }

}
