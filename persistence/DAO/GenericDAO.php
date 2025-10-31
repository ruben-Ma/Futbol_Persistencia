<?php


require_once __DIR__ . '/../conf/PersistentManager.php';


abstract class GenericDAO {// Clase genérica para los DAO (Data Access Object)

    
    protected $conn = null;//conexion a la bbdd

    public function __construct() {//constructor
        $manager = PersistentManager::getInstance();
        $this->conn = $manager->get_Connection();
    }
}