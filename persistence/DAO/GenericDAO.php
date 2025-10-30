<?php


require_once __DIR__ . '/../conf/PersistentManager.php';


abstract class GenericDAO {

    
    protected $conn = null;

    public function __construct() {
        $manager = PersistentManager::getInstance();
        $this->conn = $manager->get_Connection();
    }
}