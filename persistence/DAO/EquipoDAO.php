<?php
// Fichero: app/persistence/DAO/EquipoDAO.php
require_once __DIR__ . '/GenericDAO.php';

class EquipoDAO extends GenericDAO {

    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $query = "SELECT id, nombre, estadio FROM equipos ORDER BY nombre ASC";
        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id): ?array {
        $query = "SELECT id, nombre, estadio FROM equipos WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $equipo = $result->fetch_assoc();
        $stmt->close();
        return $equipo ?: null;
    }

    public function insert(string $nombre, string $estadio): bool {
        $query = "INSERT INTO equipos (nombre, estadio) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $nombre, $estadio);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            // Error 1062: Entrada duplicada (clave UNIQUE)
            if ($this->conn->errno === 1062) {
                $stmt->close();
                return false; 
            }
            throw new Exception("Error al insertar: " . $stmt->error);
        }
    }
}