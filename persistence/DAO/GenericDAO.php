<?php
// Fichero: app/persistence/DAO/GenericDAO.php


require_once __DIR__ . '/../conf/PersistentManager.php';

/**
 * Clase base para todos los DAOs.
 * Proporciona la conexión a la BBDD ($conn) a sus hijos.
 */
abstract class GenericDAO {

    
    protected $conn = null;

    public function __construct() {
        $manager = PersistentManager::getInstance();
        $this->conn = $manager->get_Connection();
    }
}