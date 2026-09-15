<?php
namespace App\Models;

class ServicioModel extends BaseModel {
    protected string $table = 'tbl_servicios';
    protected string $primaryKey = 'id_servicio';

    public function getByCodigo(string $codigo) {
        $servicios = $this->where('codigo', $codigo);
        return $servicios ? $servicios[0] : null;
    }

    public function getActivos(): array {
        return $this->where('estado', 'activo');
    }
}
