<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Core\View;
use App\Models\FacturaModel;
use App\Models\ClienteModel;

class DashboardController {
    public function index() {
        if (!Auth::check()) {
            Response::redirect('');
        }

        $facturaModel = new FacturaModel();
        $clienteModel = new ClienteModel();

        $ventasHoy     = $facturaModel->getVentasHoy();
        $ventasMes     = $facturaModel->getVentasMes();
        $ventasMesAnt  = $facturaModel->getVentasMesAnterior();
        $topClientes   = $facturaModel->getTopClientes(5);
        $porServicio   = $facturaModel->getVentasPorServicio();
        $ultimos7dias  = $facturaModel->getVentasUltimosDias(7);

        $mesAntTotal  = (float) $ventasMesAnt['total'];
        $mesActTotal  = (float) $ventasMes['total'];
        $variacionPct = $mesAntTotal > 0
            ? round((($mesActTotal - $mesAntTotal) / $mesAntTotal) * 100, 1)
            : null;

        View::render('dashboard', [
            'total_facturas'      => $facturaModel->count(),
            'facturas_pendientes' => $facturaModel->getPendientesCount(),
            'total_clientes'      => count($clienteModel->where('estado', 'activo')),
            'ingresos_totales'    => number_format($facturaModel->getIngresosTotales(), 2),
            'ventas_hoy'          => $ventasHoy,
            'ventas_mes'          => $ventasMes,
            'ventas_mes_anterior' => $ventasMesAnt,
            'variacion_pct'       => $variacionPct,
            'top_clientes'        => $topClientes,
            'por_servicio'        => $porServicio,
            'ultimos_7dias'       => $ultimos7dias,
            'facturas_recientes'  => $facturaModel->getRecientes(8),
        ]);
    }
}
