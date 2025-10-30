<?php
require_once __DIR__ . '/GenericDAO.php';

class EquipoDAO extends GenericDAO {

    public function __construct() {//llama al constructor del padre para conectar con la base de datos
        parent::__construct();//
    }

    public function getAll(): array {//obtiene todos los equipos de la base de datos
        $query = "SELECT id, nombre, estadio FROM equipos ORDER BY nombre ASC";
        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array {//obtiene un equipo por su id
        $query = "SELECT id, nombre, estadio FROM equipos WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $equipo = $result->fetch_assoc();
        $stmt->close();
        return $equipo ?: null;
    }

    public function insert(string $nombre, string $estadio): bool {//inserta un nuevo equipo en la base de datos
        $query = "INSERT INTO equipos (nombre, estadio) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $nombre, $estadio);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            if ($this->conn->errno === 1062) {
                $stmt->close();
                return false; 
            }
            throw new Exception("Error al insertar: " . $stmt->error);
        }
    }
}