<?php
namespace App\Models;

class FacturaModel extends BaseModel {
    protected string $table = 'tbl_facturas';
    protected string $primaryKey = 'id_factura';

    public function getRecientes(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT f.*, 
                   c.nombre as cliente_nombre, c.apellido as cliente_apellido,
                   s.nombre as servicio_nombre, s.codigo as servicio_codigo
            FROM {$this->table} f
            JOIN tbl_clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN tbl_servicios s ON f.id_servicio = s.id_servicio
            ORDER BY f.fecha_emision DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("
            SELECT f.*,
                   s.nombre as servicio_nombre, s.codigo as servicio_codigo,
                   s.whatsapp_tipo as servicio_whatsapp_tipo,
                   s.whatsapp_mensaje as servicio_whatsapp_mensaje
            FROM {$this->table} f
            LEFT JOIN tbl_servicios s ON f.id_servicio = s.id_servicio
            WHERE f.id_factura = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getPendientesCount(): int {
        return count($this->where('estado', 'Pendiente'));
    }

    public function getIngresosTotales(): float {
        $stmt = $this->db->query("SELECT SUM(total) FROM {$this->table} WHERE estado = 'Pagada'");
        return (float) $stmt->fetchColumn();
    }

    public function getAllWithClient(int $offset, int $limit, string $search = '', string $estado = ''): array {
        $query = "
            SELECT f.*, 
                   c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.codigo as cliente_codigo,
                   s.nombre as servicio_nombre, s.codigo as servicio_codigo
            FROM {$this->table} f
            JOIN tbl_clientes c ON f.id_cliente = c.id_cliente
            LEFT JOIN tbl_servicios s ON f.id_servicio = s.id_servicio
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (f.codigo_factura LIKE :s1 OR c.nombre LIKE :s2 OR c.apellido LIKE :s3 OR c.codigo LIKE :s4)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        if (!empty($estado)) {
            $query .= " AND f.estado = :estado";
            $params[':estado'] = $estado;
        }

        $query .= " ORDER BY f.fecha_emision DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countWithClient(string $search = '', string $estado = ''): int {
        $query = "
            SELECT COUNT(*) 
            FROM {$this->table} f
            JOIN tbl_clientes c ON f.id_cliente = c.id_cliente
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (f.codigo_factura LIKE :s1 OR c.nombre LIKE :s2 OR c.apellido LIKE :s3 OR c.codigo LIKE :s4)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        if (!empty($estado)) {
            $query .= " AND f.estado = :estado";
            $params[':estado'] = $estado;
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getVentasHoy(): array {
        $stmt = $this->db->query("
            SELECT COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total
            FROM {$this->table}
            WHERE DATE(fecha_emision) = CURDATE() AND estado != 'Anulada'
        ");
        return $stmt->fetch();
    }

    public function getVentasMes(): array {
        $stmt = $this->db->query("
            SELECT COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total
            FROM {$this->table}
            WHERE YEAR(fecha_emision) = YEAR(NOW())
              AND MONTH(fecha_emision) = MONTH(NOW())
              AND estado != 'Anulada'
        ");
        return $stmt->fetch();
    }

    public function getVentasMesAnterior(): array {
        $stmt = $this->db->query("
            SELECT COUNT(*) as cantidad, COALESCE(SUM(total), 0) as total
            FROM {$this->table}
            WHERE YEAR(fecha_emision)  = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))
              AND MONTH(fecha_emision) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
              AND estado != 'Anulada'
        ");
        return $stmt->fetch();
    }

    public function getTopClientes(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT c.id_cliente, c.codigo, c.nombre, c.apellido,
                   COUNT(f.id_factura) as total_facturas,
                   COALESCE(SUM(f.total), 0) as total_comprado
            FROM {$this->table} f
            JOIN tbl_clientes c ON f.id_cliente = c.id_cliente
            WHERE f.estado != 'Anulada'
            GROUP BY c.id_cliente, c.codigo, c.nombre, c.apellido
            ORDER BY total_comprado DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getVentasPorServicio(): array {
        $stmt = $this->db->query("
            SELECT COALESCE(s.nombre, 'Sin servicio') as nombre,
                   COALESCE(s.codigo, '-') as codigo,
                   COUNT(f.id_factura) as cantidad,
                   COALESCE(SUM(f.total), 0) as total
            FROM {$this->table} f
            LEFT JOIN tbl_servicios s ON f.id_servicio = s.id_servicio
            WHERE f.estado != 'Anulada'
              AND YEAR(f.fecha_emision)  = YEAR(NOW())
              AND MONTH(f.fecha_emision) = MONTH(NOW())
            GROUP BY f.id_servicio, s.nombre, s.codigo
            ORDER BY total DESC
        ");
        return $stmt->fetchAll();
    }

    public function getVentasUltimosDias(int $dias = 7): array {
        $stmt = $this->db->prepare("
            SELECT DATE(fecha_emision) as dia,
                   COUNT(*) as cantidad,
                   COALESCE(SUM(total), 0) as total
            FROM {$this->table}
            WHERE estado != 'Anulada'
              AND fecha_emision >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
            GROUP BY DATE(fecha_emision)
            ORDER BY dia ASC
        ");
        $stmt->bindValue(':dias', $dias, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getExportData(string $search = '', string $estado = ''): array {
        $query = "
            SELECT f.*, c.nombre as cliente_nombre, c.apellido as cliente_apellido, c.codigo as cliente_codigo
            FROM {$this->table} f
            JOIN tbl_clientes c ON f.id_cliente = c.id_cliente
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (f.codigo_factura LIKE :s1 OR c.nombre LIKE :s2 OR c.apellido LIKE :s3 OR c.codigo LIKE :s4)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
        }

        if (!empty($estado)) {
            $query .= " AND f.estado = :estado";
            $params[':estado'] = $estado;
        }

        $query .= " ORDER BY f.fecha_emision DESC";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
