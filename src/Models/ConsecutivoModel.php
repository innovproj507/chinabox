<?php
namespace App\Models;

class ConsecutivoModel extends BaseModel {
    protected string $table = 'tbl_consecutivos_codigos';
    protected string $primaryKey = 'id';

    public function getNextConsecutivo(string $tipo, string $prefijo): int {
        // Usar transacción para evitar colisiones
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE tipo = :tipo AND prefijo = :prefijo FOR UPDATE");
            $stmt->execute(['tipo' => $tipo, 'prefijo' => $prefijo]);
            $consecutivo = $stmt->fetch();

            if ($consecutivo) {
                $nextNum = $consecutivo['ultimo_numero'] + 1;
                $stmtUpdate = $this->db->prepare("UPDATE {$this->table} SET ultimo_numero = :num WHERE id = :id");
                $stmtUpdate->execute(['num' => $nextNum, 'id' => $consecutivo['id']]);
            } else {
                $nextNum = 1;
                $stmtInsert = $this->db->prepare("INSERT INTO {$this->table} (tipo, prefijo, ultimo_numero) VALUES (:tipo, :prefijo, :num)");
                $stmtInsert->execute(['tipo' => $tipo, 'prefijo' => $prefijo, 'num' => $nextNum]);
            }

            $this->db->commit();
            return $nextNum;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
