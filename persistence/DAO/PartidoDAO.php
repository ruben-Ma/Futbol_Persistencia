<?php
// Fichero: app/persistence/DAO/PartidoDAO.php
require_once __DIR__ . '/GenericDAO.php';

class PartidoDAO extends GenericDAO {

    public function __construct() {
        parent::__construct();
    }

    public function getByJornada(int $jornada): array {
        $sql = "SELECT p.jornada, el.nombre AS local, ev.nombre AS visitante, p.resultado, p.estadio_partido
                FROM partidos p
                JOIN equipos el ON p.id_equipo_local = el.id
                JOIN equipos ev ON p.id_equipo_visitante = ev.id
                WHERE p.jornada = ? ORDER BY p.id ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $jornada);
        $stmt->execute();
        $result = $stmt->get_result();
        $partidos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $partidos;
    }

    public function getByEquipoId(int $equipoId): array {
        $sql = "SELECT p.jornada, el.nombre AS local, ev.nombre AS visitante, p.resultado, p.estadio_partido
                FROM partidos p
                JOIN equipos el ON p.id_equipo_local = el.id
                JOIN equipos ev ON p.id_equipo_visitante = ev.id
                WHERE p.id_equipo_local = ? OR p.id_equipo_visitante = ?
                ORDER BY p.jornada ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $equipoId, $equipoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $partidos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $partidos;
    }
    
    public function getAllJornadas(): array {
        $query = "SELECT DISTINCT jornada FROM partidos ORDER BY jornada ASC";
        $result = $this->conn->query($query);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conn->error);
        }
        $jornadas = [];
        while ($row = $result->fetch_row()) {
            $jornadas[] = (int)$row[0];
        }
        return $jornadas;
    }

    public function checkPartidoExists(int $localId, int $visitanteId): bool {
        $sql = "SELECT 1 FROM partidos 
                WHERE (id_equipo_local = ? AND id_equipo_visitante = ?) 
                   OR (id_equipo_local = ? AND id_equipo_visitante = ?)";
                   
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iiii', $localId, $visitanteId, $visitanteId, $localId);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    public function insert(int $jornada, int $localId, int $visitanteId, string $resultado, string $estadio): bool {
        $sql = "INSERT INTO partidos (jornada, id_equipo_local, id_equipo_visitante, resultado, estadio_partido) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('iisss', $jornada, $localId, $visitanteId, $resultado, $estadio);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al insertar partido: " . $stmt->error);
            return false;
        }
        $stmt->close();
        return $success;
    }
}