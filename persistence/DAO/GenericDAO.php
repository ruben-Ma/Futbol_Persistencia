<?php
// Fichero: app/persistence/DAO/GenericDAO.php

// ¡¡CAMBIO IMPORTANTE!!
// La ruta sube un nivel (..) para entrar en la carpeta conf/
require_once __DIR__ . '/../conf/PersistentManager.php';

/**
 * Clase base para todos los DAOs.
 * Proporciona la conexión a la BBDD ($conn) a sus hijos.
 */
abstract class GenericDAO {

    /** @var mysqli $conn Conexión a BD */
    protected $conn = null;

    public function __construct() {
        $manager = PersistentManager::getInstance();
        $this->conn = $manager->getConnection();
    }
}