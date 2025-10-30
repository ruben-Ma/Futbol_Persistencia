<?php
require_once __DIR__ . '/GenericDAO.php';

class PartidoDAO extends GenericDAO {

    public function __construct() {//llama al constructor del padre para conectar con la base de datos
        parent::__construct();
    }

    public function getByJornada(int $jornada): array {//obtiene los partidos de una jornada especifica
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

    public function getByEquipoId(int $equipoId): array {//obtiene los partidos de un equipo especifico
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
    
    public function getAllJornadas(): array {//obtiene todas las jornadas existentes
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

    public function checkPartidoExists(int $localId, int $visitanteId): bool {//verifica si ya existe un partido entre dos equipos
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

    public function insert(int $jornada, int $localId, int $visitanteId, string $resultado, string $estadio): bool {//inserta un nuevo partido en la base de datos
        $sql = "INSERT INTO partidos (jornada, id_equipo_local, id_equipo_visitante, resultado, estadio_partido) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);//prepara la consulta
        $stmt->bind_param('iisss', $jornada, $localId, $visitanteId, $resultado, $estadio);
        $success = $stmt->execute();
        if (!$success) {//si falla la insercion
            error_log("Error al insertar partido: " . $stmt->error);
            return false;
        }
        $stmt->close();//cierra la sentencia
        return $success;
    }
}