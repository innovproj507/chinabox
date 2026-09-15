<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Flash;
use App\Core\Paginator;
use App\Models\ServicioModel;
use App\Models\FacturaDetalleModel;

class ServicioController {
    private ServicioModel $servicioModel;

    public function __construct() {
        if (!Auth::check()) {
            Response::redirect('');
        }
        $this->servicioModel = new ServicioModel();
    }

    public function lista() {
        $page = (int) Request::get('page', 1);
        $perPage = 10;
        
        $servicios = $this->servicioModel->all('nombre', 'ASC');
        $totalItems = count($servicios);
        $paginator = new Paginator($totalItems, $perPage, $page);
        
        $pagedServicios = array_slice($servicios, $paginator->getOffset(), $paginator->getLimit());

        View::render('servicios/lista', [
            'servicios' => $pagedServicios,
            'paginator' => $paginator
        ]);
    }

    public function crear() {
        if (Request::isPost()) {
            $codigo = Request::post('codigo');
            
            if ($this->servicioModel->getByCodigo($codigo)) {
                Flash::error('El código ya está registrado');
                Response::redirect('servicios/crear');
            }

            try {
                $this->servicioModel->create([
                    'nombre' => Request::post('nombre'),
                    'codigo' => $codigo,
                    'precio' => Request::post('precio'),
                    'descripcion' => Request::post('descripcion'),
                    'estado' => Request::post('estado'),
                    'unidad_medida' => Request::post('unidad_medida'),
                    'whatsapp_tipo' => Request::post('whatsapp_tipo', 'regular'),
                    'whatsapp_mensaje' => Request::post('whatsapp_mensaje', '') ?: null,
                ]);
                Flash::success("Servicio {$codigo} creado exitosamente");
                Response::redirect('servicios');
            } catch (\Exception $e) {
                Flash::error('Error al crear servicio: ' . $e->getMessage());
            }
        }

        View::render('servicios/crear');
    }

    public function editar($id) {
        $servicio = $this->servicioModel->find($id);
        if (!$servicio) {
            Flash::error('Servicio no encontrado');
            Response::redirect('servicios');
        }

        if (Request::isPost()) {
            $codigo = Request::post('codigo');
            
            $existente = $this->servicioModel->getByCodigo($codigo);
            if ($existente && $existente['id_servicio'] != $id) {
                Flash::error('El código ya está registrado en otro servicio');
            } else {
                try {
                    $this->servicioModel->update($id, [
                        'nombre' => Request::post('nombre'),
                        'codigo' => $codigo,
                        'precio' => Request::post('precio'),
                        'descripcion' => Request::post('descripcion', ''),
                        'estado' => Request::post('estado', 'activo'),
                        'unidad_medida' => Request::post('unidad_medida', ''),
                        'whatsapp_tipo' => Request::post('whatsapp_tipo', 'regular'),
                        'whatsapp_mensaje' => Request::post('whatsapp_mensaje', '') ?: null,
                    ]);
                    Flash::success("Servicio {$codigo} actualizado exitosamente");
                    Response::redirect('servicios');
                } catch (\Exception $e) {
                    Flash::error('Error al actualizar servicio: ' . $e->getMessage());
                }
            }
        }

        View::render('servicios/editar', ['servicio' => $servicio]);
    }

    public function detalle($id) {
        $servicio = $this->servicioModel->find($id);
        if (!$servicio) {
            Flash::error('Servicio no encontrado');
            Response::redirect('servicios');
        }

        $detalleModel = new FacturaDetalleModel();
        $detalles = $detalleModel->where('id_servicio', $id);
        
        $totalVecesUsado = count($detalles);
        $totalIngresos = array_reduce($detalles, function($carry, $d) {
            return $carry + $d['subtotal'];
        }, 0);

        View::render('servicios/detalle', [
            'servicio' => $servicio,
            'total_veces_usado' => $totalVecesUsado,
            'total_ingresos' => $totalIngresos,
            'ultimas_facturas' => [] // Simplificado para que no sea muy pesada la query
        ]);
    }

    public function eliminar($id) {
        if (!Request::isPost()) Response::redirect('servicios');

        $servicio = $this->servicioModel->find($id);
        if (!$servicio) Response::redirect('servicios');

        $detalleModel = new FacturaDetalleModel();
        if (count($detalleModel->where('id_servicio', $id)) > 0) {
            Flash::error("No se puede eliminar el servicio {$servicio['codigo']} porque está siendo usado en facturas");
        } else {
            $this->servicioModel->delete($id);
            Flash::success("Servicio {$servicio['codigo']} eliminado exitosamente");
        }

        Response::redirect('servicios');
    }
}
