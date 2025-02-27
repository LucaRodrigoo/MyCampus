<?php
require_once __DIR__ . "/../config/database.php";

class Amistad {
    private $conn;
    private $table_solicitudes = "solicitudes";
    private $table_notificaciones = "notificaciones";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Enviar solicitud de amistad
    public function enviarSolicitud($id_solicitante, $id_receptor) {
        // Verificar si ya hay una solicitud o son amigos
        $query = "SELECT * FROM {$this->table_solicitudes} 
                  WHERE (id_solicitante = :id_solicitante AND id_receptor = :id_receptor)
                  OR (id_solicitante = :id_receptor AND id_receptor = :id_solicitante)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_solicitante' => $id_solicitante, 'id_receptor' => $id_receptor]);

        if ($stmt->rowCount() > 0) {
            return "Ya existe una solicitud o son amigos.";
        }

        // Insertar la solicitud
        $query = "INSERT INTO {$this->table_solicitudes} (id_solicitante, id_receptor, estado) 
                  VALUES (:id_solicitante, :id_receptor, 'pendiente')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_solicitante' => $id_solicitante, 'id_receptor' => $id_receptor]);

        // Crear la notificación
        $mensaje = "Tienes una nueva solicitud de amistad.";
        $query = "INSERT INTO {$this->table_notificaciones} (id_usuario, tipo, mensaje) 
                  VALUES (:id_receptor, 'solicitud_amistad', :mensaje)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['id_receptor' => $id_receptor, 'mensaje' => $mensaje]);

        return "Solicitud enviada con éxito.";
    }

    // Obtener notificaciones
    public function obtenerNotificacionesAmistad($usuario_id) {
        $query = "SELECT n.*, s.id_solicitante, u.nombre AS emisor_nombre, u.foto_perfil 
                  FROM notificaciones n
                  JOIN solicitudes s ON n.id_usuario = s.id_receptor
                  JOIN users u ON s.id_solicitante = u.id
                  WHERE n.id_usuario = :usuario_id 
                  AND n.tipo = 'solicitud_amistad'
                  ORDER BY n.fecha DESC";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function obtenerSolicitudesPendientes($id_usuario) {
        $query = "
            SELECT s.id, u.id AS id_solicitante, u.nombre, u.foto_perfil
            FROM solicitudes s
            JOIN users u ON s.id_solicitante = u.id
            WHERE s.id_receptor = :id_usuario AND s.estado = 'pendiente'
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // ✅ Aceptar solicitud de amistad
    public function aceptarSolicitud($id_solicitud, $nombreUsuarioActual) {
        // Obtener datos de la solicitud
        $query = "SELECT id_solicitante, id_receptor FROM solicitudes WHERE id = :id_solicitud";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_solicitud", $id_solicitud, PDO::PARAM_INT);
        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($solicitud) {
            try {
                $this->conn->beginTransaction();
    
                // Insertar en la tabla de amigos
                $queryInsert = "INSERT INTO amigos (usuario_id, amigo_id) VALUES (:usuario_id, :amigo_id)";
                $stmtInsert = $this->conn->prepare($queryInsert);
                $stmtInsert->bindParam(":usuario_id", $solicitud['id_solicitante'], PDO::PARAM_INT);
                $stmtInsert->bindParam(":amigo_id", $solicitud['id_receptor'], PDO::PARAM_INT);
                $stmtInsert->execute();
    
                // Enviar notificación al solicitante
                $mensaje = "¡" . $nombreUsuarioActual . " aceptó tu solicitud de amistad!";
                $queryNotificacion = "INSERT INTO notificaciones (id_usuario, tipo, mensaje) 
                                      VALUES (:usuario_id, 'amistad_aceptada', :mensaje)";
                $stmtNotificacion = $this->conn->prepare($queryNotificacion);
                $stmtNotificacion->bindParam(":usuario_id", $solicitud['id_solicitante'], PDO::PARAM_INT);
                $stmtNotificacion->bindParam(":mensaje", $mensaje, PDO::PARAM_STR);
                $stmtNotificacion->execute();
    
                // ACTUALIZAR la solicitud a estado "aceptada"
                $queryUpdate = "UPDATE solicitudes SET estado = 'aceptado' WHERE id = :id_solicitud";
                $stmtUpdate = $this->conn->prepare($queryUpdate);
                $stmtUpdate->bindParam(":id_solicitud", $id_solicitud, PDO::PARAM_INT);
                $stmtUpdate->execute();
    
                $this->conn->commit();
                return true;
            } catch (Exception $e) {
                $this->conn->rollBack();
                return false;
            }
        }
        return false;
    }
    
    // ✅ Rechazar solicitud (actualiza estado en vez de eliminarla)
    public function rechazarSolicitud($id_solicitud) {
        $queryUpdate = "UPDATE solicitudes SET estado = 'rechazado' WHERE id = :id_solicitud";
        $stmtUpdate = $this->conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(":id_solicitud", $id_solicitud, PDO::PARAM_INT);
        return $stmtUpdate->execute();
    }

    public function obtenerMisAmigos($id_usuario) {
        $query = "SELECT u.id, u.nombre 
                  FROM amigos a
                  JOIN users u ON (a.amigo_id = u.id OR a.usuario_id = u.id)
                  WHERE (a.usuario_id = :id_usuario OR a.amigo_id = :id_usuario)
                  AND u.id != :id_usuario";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
    
    
}
?>
