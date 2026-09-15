<?php
namespace App\Models;

class ConfiguracionModel extends BaseModel {
    protected string $table = 'tbl_configuracion';
    protected string $primaryKey = 'id';

    public function getConfig() {
        // En Django siempre usan el id=1 (Singleton)
        $config = $this->find(1);
        if (!$config) {
            // Valores por defecto
            $data = [
                'id' => 1,
                'nombre_empresa' => 'CHINABOX',
                'nombre_comercial' => 'Servicios Logísticos POBOX',
                'itbms_porcentaje' => 7.00,
                'moneda_default' => 'USD',
                'prefijo_factura' => 'FAC-',
            ];
            $this->create($data);
            return $data;
        }
        return $config;
    }

    public function updateConfig(array $data) {
        return $this->update(1, $data);
    }
}
