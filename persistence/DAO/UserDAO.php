<?php
require 'GenericDAO.php';

class UserDAO extends GenericDAO {

  //Se define una constante con el nombre de la tabla
  const USER_TABLE = 'usuarios';

  public function selectAll() {
    $query = "SELECT * FROM " . UserDAO::USER_TABLE;
    $result = mysqli_query($this->conn, $query);
    $users= array();
    while ($userBD = mysqli_fetch_array($result)) {
      $user = array(
        'id' => $userBD["id"],
        'nombre' => $userBD["nombre"],
        'password' => $userBD["password"],
      );
      array_push($users, $user);
    }
    return $users;
  }



  public function insert($nombre, $password) {
    $query = "INSERT INTO " . UserDAO::USER_TABLE .
      " (nombre, password) VALUES(?,?)";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $nombre, $password);
    return $stmt->execute();
  }

  public function checkExists($nombre, $password) {
    $query = "SELECT nombre, password FROM " . UserDAO::USER_TABLE . " WHERE nombre=? AND password=?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $nombre, $password);
    if(mysqli_stmt_execute($stmt)>0)
      return true;
    else
      return false;
  }


  public function selectById($id) {
    $query = "SELECT nombre, password FROM " . UserDAO::USER_TABLE . " WHERE idUser=?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nombre, $password);

    while (mysqli_stmt_fetch($stmt)) {
      $user = array(
        'id' => $id,
 				'nombre' => $nombre,
 				'password' => $password
 		);
       }

    return $user;
  }

  public function update($id, $nombre, $password) {
    $query = "UPDATE " . UserDAO::USER_TABLE .
      " SET nombre=?, password=?"
      . " WHERE idUser=?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'sssi', $nombre, $password, $id);
    return $stmt->execute();
  }

  public function delete($id) {
    $query = "DELETE FROM " . UserDAO::USER_TABLE . " WHERE idUser =?";
    $stmt = mysqli_prepare($this->conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return $stmt->execute();
  }

}

?>
