<?php
namespace App\Models;

class ClienteModel extends BaseModel {
    protected string $table = 'tbl_clientes';
    protected string $primaryKey = 'id_cliente';

    public function getByCodigo(string $codigo) {
        $clientes = $this->where('codigo', $codigo);
        return $clientes ? $clientes[0] : null;
    }

    public function getByEmail(string $email) {
        $clientes = $this->where('email', $email);
        return $clientes ? $clientes[0] : null;
    }

    public function search(string $query): array {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE estado = 'activo' AND (
                nombre LIKE :q1 OR 
                apellido LIKE :q2 OR 
                codigo LIKE :q3 OR 
                email LIKE :q4
            )
            ORDER BY codigo ASC
            LIMIT 10
        ");
        
        $searchTerm = "%{$query}%";
        $stmt->bindValue(':q1', $searchTerm);
        $stmt->bindValue(':q2', $searchTerm);
        $stmt->bindValue(':q3', $searchTerm);
        $stmt->bindValue(':q4', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Bloquea (inactivo) clientes activos cuya última factura no anulada
     * tiene más de 1 año. Solo aplica a clientes que tienen al menos 1 factura.
     */
    public function bloquearInactivos(): int {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} c
            SET c.estado = 'inactivo'
            WHERE c.estado = 'activo'
              AND EXISTS (
                  SELECT 1 FROM tbl_facturas f WHERE f.id_cliente = c.id_cliente
              )
              AND NOT EXISTS (
                  SELECT 1 FROM tbl_facturas f
                  WHERE f.id_cliente = c.id_cliente
                    AND f.estado != 'Anulada'
                    AND f.fecha_emision >= DATE_SUB(NOW(), INTERVAL 1 YEAR)
              )
        ");
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getFiltered(string $search = '', string $estado = ''): array {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (codigo LIKE :s1 OR nombre LIKE :s2 OR apellido LIKE :s3 OR email LIKE :s4 OR ruc LIKE :s5)";
            $params[':s1'] = "%{$search}%";
            $params[':s2'] = "%{$search}%";
            $params[':s3'] = "%{$search}%";
            $params[':s4'] = "%{$search}%";
            $params[':s5'] = "%{$search}%";
        }

        if (!empty($estado)) {
            $query .= " AND estado = :estado";
            $params[':estado'] = $estado;
        }

        $query .= " ORDER BY codigo ASC";
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
