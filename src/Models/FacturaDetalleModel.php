<?php
namespace App\Models;

class FacturaDetalleModel extends BaseModel {
    protected string $table = 'tbl_factura_detalles';
    protected string $primaryKey = 'id';

    public function getByFactura(int $facturaId): array {
        $stmt = $this->db->prepare("
            SELECT d.*
            FROM {$this->table} d
            WHERE d.id_factura = :factura_id
        ");
        $stmt->execute(['factura_id' => $facturaId]);
        return $stmt->fetchAll();
    }

    public function deleteByFactura(int $facturaId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id_factura = :id");
        return $stmt->execute(['id' => $facturaId]);
    }
}
