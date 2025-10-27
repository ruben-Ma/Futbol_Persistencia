<?php

require __DIR__ . '/../../persistence/conf/PersistentManager.php';
abstract class GenericDAO {

  //Conexión a BD
  protected $conn = null;
  //Constructor de la clase
  public function __construct() {
    $this->conn = PersistentManager::getInstance()->get_connection();
  }

  // métodos abstractos para CRUD de clases que hereden
  abstract protected function insert($nombre, $password);
  abstract protected function selectAll();
  abstract protected function selectById($id);
  abstract protected function update($id, $nombre, $password);
  abstract protected function delete($id);

}
