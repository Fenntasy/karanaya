<?php

interface APIRetriever_Model_Interface {
    public function search($name);
    public function fetch($id);
    public function getData();
}
