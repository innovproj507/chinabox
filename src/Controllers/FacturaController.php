<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Flash;
use App\Core\Paginator;
use App\Models\FacturaModel;
use App\Models\FacturaDetalleModel;
use App\Models\ClienteModel;
use App\Models\ServicioModel;
use App\Models\ConsecutivoModel;
use App\Models\ConfiguracionModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class FacturaController {
    private FacturaModel $facturaModel;
    private FacturaDetalleModel $detalleModel;
    private ClienteModel $clienteModel;

    public function __construct() {
        if (!Auth::check()) {
            Response::redirect('');
        }
        $this->facturaModel = new FacturaModel();
        $this->detalleModel = new FacturaDetalleModel();
        $this->clienteModel = new ClienteModel();
    }

    public function lista() {
        $search = Request::get('q', '');
        $estado = Request::get('estado', '');
        $page = (int) Request::get('page', 1);
        $perPage = in_array((int) Request::get('per_page', 10), [10, 25, 50, 100])
            ? (int) Request::get('per_page', 10)
            : 10;
        
        $offset = ($page - 1) * $perPage;

        $facturas = $this->facturaModel->getAllWithClient($offset, $perPage, $search, $estado);
        $totalItems = $this->facturaModel->countWithClient($search, $estado);
        $paginator = new Paginator($totalItems, $perPage, $page);

        View::render('facturas/lista', [
            'facturas' => $facturas,
            'search_query' => $search,
            'estado_filter' => $estado,
            'per_page' => $perPage,
            'paginator' => $paginator
        ]);
    }

    public function crear() {
        $clienteModel = new ClienteModel();
        $servicioModel = new ServicioModel();
        
        if (Request::isPost()) {
            header('Content-Type: application/json');
            try {
                $id_cliente = Request::post('id_cliente');
                $id_servicio = Request::post('id_servicio');
                $metodo_pago = Request::post('metodo_pago', 'Efectivo');
                $aplica_itbms = Request::post('aplica_itbms') == '1';
                $descuento = (float) Request::post('descuento', 0);
                $nota = Request::post('nota', '');
                
                $itemsJson = Request::post('items', '[]');
                $items = json_decode($itemsJson, true);

                if (!$id_cliente) {
                    echo json_encode(['success' => false, 'error' => 'Cliente requerido']);
                    exit;
                }
                if (empty($items)) {
                    echo json_encode(['success' => false, 'error' => 'Debe agregar al menos un servicio']);
                    exit;
                }

                $configModel = new ConfiguracionModel();
                $config = $configModel->getConfig();

                // Generar código
                $consecutivoModel = new ConsecutivoModel();
                
                $meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
                $mes_texto = $meses[date('n') - 1];
                $prefijo_fecha = $mes_texto . date('Y');
                
                $num = $consecutivoModel->getNextConsecutivo('Factura', $prefijo_fecha);
                $codigo_factura = sprintf("%s-%04d", $prefijo_fecha, $num);

                // Crear factura inicial
                $facturaId = $this->facturaModel->create([
                    'id_cliente' => $id_cliente,
                    'id_servicio' => $id_servicio,
                    'codigo_factura' => $codigo_factura,
                    'estado' => Request::post('estado', 'Pendiente'),
                    'subtotal' => 0,
                    'impuestos' => 0,
                    'descuento' => $descuento,
                    'total' => 0,
                    'metodo_pago' => $metodo_pago,
                    'observaciones' => $nota,
                    'moneda' => $config['moneda_default'],
                    'fecha_emision' => date('Y-m-d H:i:s'),
                    'enviado_whatsapp' => 0
                ]);

                // Procesar detalles
                $subtotal = 0;
                foreach ($items as $item) {
                    $precio_unitario = (float) $item['precio_unitario'];
                    $peso = (float) ($item['peso'] ?? 1);
                    $subtotal_linea = $precio_unitario * $peso;
                    
                    $this->detalleModel->create([
                        'id_factura' => $facturaId,
                        'cantidad' => $peso,
                        'precio_unitario' => $precio_unitario,
                        'subtotal' => $subtotal_linea,
                        'guia' => $item['guia'] ?? ''
                    ]);
                    $subtotal += $subtotal_linea;
                }

                $subtotal_con_descuento = $subtotal - $descuento;
                $impuestos = 0;
                if ($aplica_itbms) {
                    $impuestos = $subtotal_con_descuento * ($config['itbms_porcentaje'] / 100);
                }
                
                $total = $subtotal_con_descuento + $impuestos;

                $this->facturaModel->update($facturaId, [
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total
                ]);

                $clienteData  = $clienteModel->find($id_cliente);
                $servicioData = $servicioModel->find($id_servicio);

                $logoBase64 = '';
                $logoPath = __DIR__ . '/../../public/images/logo_chinabox.png';
                if (file_exists($logoPath)) {
                    $logoBase64 = base64_encode(file_get_contents($logoPath));
                }

                $detallesJson = [];
                foreach ($items as $item) {
                    $p = (float)$item['precio_unitario'];
                    $c = (float)($item['peso'] ?? 1);
                    $detallesJson[] = [
                        'guia'           => $item['guia'] ?? '',
                        'cantidad'       => $c,
                        'precio_unitario'=> $p,
                        'subtotal'       => $p * $c
                    ];
                }

                echo json_encode([
                    'success'        => true,
                    'factura_id'     => $facturaId,
                    'codigo_factura' => $codigo_factura,
                    'total'          => $total,
                    'fecha_emision'  => date('Y-m-d H:i:s'),
                    'observaciones'  => $nota,
                    'cliente' => [
                        'nombre'   => $clienteData['nombre']   ?? '',
                        'apellido' => $clienteData['apellido'] ?? '',
                        'codigo'   => $clienteData['codigo']   ?? '',
                        'telefono' => $clienteData['telefono'] ?? '',
                        'ruc'      => $clienteData['ruc']      ?? ''
                    ],
                    'servicio' => [
                        'id'              => (int)$id_servicio,
                        'nombre'          => $servicioData['nombre'] ?? '',
                        'codigo'          => $servicioData['codigo'] ?? '',
                        'whatsapp_tipo'   => $servicioData['whatsapp_tipo'] ?? 'regular',
                        'whatsapp_mensaje'=> $servicioData['whatsapp_mensaje'] ?? '',
                    ],
                    'detalles'    => $detallesJson,
                    'logo_base64' => $logoBase64
                ]);
                exit;
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                exit;
            }
        }

        $clientes = $clienteModel->where('estado', 'activo');
        $servicios = $servicioModel->getActivos();

        // Preview de número
        $meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
        $mes_texto = $meses[date('n') - 1];
        $prefijo_fecha = $mes_texto . date('Y');
        $proximo_numero = $prefijo_fecha . "-Preview";

        View::render('facturas/crear', [
            'clientes' => $clientes,
            'servicios' => $servicios,
            'numero_factura' => $proximo_numero
        ]);
    }

    public function editar($id) {
        $factura = $this->facturaModel->find($id);
        if (!$factura) Response::redirect('facturas');

        if ($factura['estado'] == 'Pagada') {
            Flash::error('No se pueden editar facturas que ya han sido pagadas.');
            Response::redirect('facturas/' . $id);
        }

        $detalles = $this->detalleModel->getByFactura($id);

        if (Request::isPost()) {
            try {
                $this->facturaModel->update($id, [
                    'id_cliente' => Request::post('id_cliente'),
                    'id_servicio' => Request::post('id_servicio'),
                    'fecha_entrega' => Request::post('fecha_entrega') ?: null,
                    'estado' => Request::post('estado', 'Pendiente'),
                    'subtotal' => Request::post('subtotal', 0),
                    'impuestos' => Request::post('impuestos', 0),
                    'descuento' => Request::post('descuento', 0),
                    'total' => Request::post('total', 0),
                    'metodo_pago' => Request::post('metodo_pago'),
                    'observaciones' => Request::post('observaciones', ''),
                    'moneda' => Request::post('moneda', 'USD')
                ]);

                $itemsJson = Request::post('items', '[]');
                $items = json_decode($itemsJson, true);

                if ($items) {
                    $this->detalleModel->deleteByFactura($id);
                    foreach ($items as $item) {
                        $this->detalleModel->create([
                            'id_factura' => $id,
                            'cantidad' => $item['peso'] ?? 1,
                            'precio_unitario' => $item['precio_unitario'],
                            'subtotal' => $item['subtotal'],
                            'guia' => $item['guia'] ?? ''
                        ]);
                    }
                }

                Flash::success("Factura {$factura['codigo_factura']} actualizada exitosamente");
                Response::redirect('facturas/' . $id);
            } catch (\Exception $e) {
                Flash::error('Error: ' . $e->getMessage());
            }
        }

        $clienteModel = new ClienteModel();
        $servicioModel = new ServicioModel();

        View::render('facturas/editar', [
            'factura' => $factura,
            'detalles' => $detalles,
            'detalles_json' => json_encode(array_map(function($d) {
                return [
                    'id' => $d['id'],
                    'precio_unitario' => (float) $d['precio_unitario'],
                    'guia' => $d['guia'] ?? '',
                    'peso' => (float) $d['cantidad'],
                    'subtotal' => (float) $d['subtotal']
                ];
            }, $detalles)),
            'clientes' => $clienteModel->where('estado', 'activo'),
            'servicios' => $servicioModel->getActivos()
        ]);
    }

    public function detalle($id) {
        $factura = $this->facturaModel->find($id);
        if (!$factura) Response::redirect('facturas');

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($factura['id_cliente']);

        $detalles = $this->detalleModel->getByFactura($id);

        $logoBase64 = '';
        $logoPath = __DIR__ . '/../../public/images/logo_chinabox.png';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        View::render('facturas/detalle', [
            'factura' => $factura,
            'cliente' => $cliente,
            'detalles' => $detalles,
            'logo_base64' => $logoBase64
        ]);
    }

    public function eliminar($id) {
        if (!Request::isPost()) Response::redirect('facturas');

        $factura = $this->facturaModel->find($id);
        if (!$factura) Response::redirect('facturas');

        if ($factura['estado'] == 'Pagada') {
            Flash::error('No se pueden eliminar facturas que ya han sido pagadas.');
            Response::redirect('facturas');
        }

        $this->facturaModel->delete($id);
        Flash::success("Factura eliminada exitosamente");
        Response::redirect('facturas');
    }

    public function imprimir($id) {
        $factura = $this->facturaModel->find($id);
        if (!$factura) Response::redirect('facturas');

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($factura['id_cliente']);
        $detalles = $this->detalleModel->getByFactura($id);

        $logoBase64 = '';
        $logoPath = __DIR__ . '/../../public/images/logo_chinabox.png'; // 2 niveles arriba: src -> php -> public
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        // --- LÓGICA DE IMPRESIÓN DIRECTA EN WINDOWS ---

        // Usar $_SERVER o getenv en lugar de $_ENV por temas de configuración de php.ini (variables_order) en Laragon
        $directPrint = trim($_SERVER['DIRECT_PRINT_ENABLED'] ?? getenv('DIRECT_PRINT_ENABLED') ?? 'false', '"');
        $printerName = trim($_SERVER['PRINTER_NAME'] ?? getenv('PRINTER_NAME') ?? '', '"');
        $autoPrintBrowser = (Request::get('preview_pdf') != '1');

        // Modo Depuración de PDF (Ver el resultado de DOMPDF directamente en el navegador)
        if (Request::get('debug_pdf') == '1') {
            try {
                ob_start();
                View::render('facturas/pdf_server', [
                    'useBase' => false,
                    'factura' => $factura,
                    'cliente' => $cliente,
                    'detalles' => $detalles,
                    'logo_base64' => $logoBase64,
                    'full_layout' => true
                ]);
                $html = ob_get_clean();

                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('letter', 'portrait');
                $dompdf->render();
                
                // Mostrar el PDF directamente en el navegador
                $dompdf->stream("debug_factura.pdf", ["Attachment" => false]);
                exit;
            } catch (\Exception $e) {
                die("Error en depuración PDF: " . $e->getMessage());
            }
        }

        if ($directPrint === 'true' && !empty($printerName) && Request::get('preview_pdf') != '1') {
            try {
                ob_start();
                View::render('facturas/pdf_server', [
                    'useBase' => false,
                    'factura' => $factura,
                    'cliente' => $cliente,
                    'detalles' => $detalles,
                    'logo_base64' => $logoBase64,
                    'full_layout' => true
                ]);
                $html = ob_get_clean();

                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('letter', 'portrait');
                $dompdf->render();

                $tempPdf = tempnam(sys_get_temp_dir(), 'fac_') . '.pdf';
                file_put_contents($tempPdf, $dompdf->output());

                $sumatraPath = dirname(__DIR__, 2) . '/bin/SumatraPDF.exe';
                if (file_exists($sumatraPath)) {
                    $cmd = sprintf('"%s" -print-to "%s" -print-settings "noscale" -silent "%s"', $sumatraPath, $printerName, $tempPdf);
                    // Usar shell_exec para capturar cualquier error inmediato (aunque start /B lo manda al fondo)
                    shell_exec('start /B "" ' . $cmd);
                    
                    echo "<script>
                        alert('Recibo enviado a la impresora (" . htmlspecialchars($printerName) . ").');
                        window.close();
                        window.history.back();
                    </script>";
                    exit;
                } else {
                    Flash::error("SumatraPDF no encontrado.");
                }
            } catch (\Exception $e) {
                Flash::error("Error impresión: " . $e->getMessage());
            }
        }
        // --- FIN DE LÓGICA WINDOWS ---

        View::render('facturas/imprimir', [
            'useBase' => false,
            'factura' => $factura,
            'cliente' => $cliente,
            'detalles' => $detalles,
            'logo_base64' => $logoBase64,
            'autoPrintBrowser' => $autoPrintBrowser
        ]);
    }

    public function pdf($id) {
        $factura = $this->facturaModel->find($id);
        if (!$factura) Response::redirect('facturas');

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($factura['id_cliente']);
        $detalles = $this->detalleModel->getByFactura($id);

        $logoBase64 = '';
        $logoPath = __DIR__ . '/../../public/images/logo_chinabox.png';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        ob_start();
        View::render('facturas/pdf_server', [
            'useBase' => false,
            'factura' => $factura,
            'cliente' => $cliente,
            'detalles' => $detalles,
            'logo_base64' => $logoBase64,
            'full_layout' => false // WhatsApp/PDF mode is minimalist
        ]);
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="factura_' . $factura['codigo_factura'] . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $dompdf->output();
        exit;
    }

    public function datosJson($id) {
        header('Content-Type: application/json');
        $factura = $this->facturaModel->find($id);
        if (!$factura) { echo json_encode(['error' => 'not found']); exit; }

        $clienteModel = new ClienteModel();
        $cliente      = $clienteModel->find($factura['id_cliente']);
        $detalles     = $this->detalleModel->getByFactura($id);

        $logoBase64 = '';
        $logoPath   = __DIR__ . '/../../public/images/logo_chinabox.png';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $detallesJson = [];
        foreach ($detalles as $d) {
            $detallesJson[] = [
                'guia'            => $d['guia'] ?? '',
                'cantidad'        => (float)$d['cantidad'],
                'precio_unitario' => (float)$d['precio_unitario'],
                'subtotal'        => (float)$d['subtotal']
            ];
        }

        echo json_encode([
            'factura_id'     => $factura['id_factura'],
            'codigo_factura' => $factura['codigo_factura'],
            'total'          => $factura['total'],
            'fecha_emision'  => $factura['fecha_emision'],
            'observaciones'  => $factura['observaciones'] ?? '',
            'cliente' => [
                'nombre'   => $cliente['nombre']   ?? '',
                'apellido' => $cliente['apellido'] ?? '',
                'codigo'   => $cliente['codigo']   ?? '',
                'telefono' => $cliente['telefono'] ?? '',
                'ruc'      => $cliente['ruc']      ?? ''
            ],
            'servicio' => [
                'id'               => (int)$factura['id_servicio'],
                'nombre'           => $factura['servicio_nombre'] ?? '',
                'codigo'           => $factura['servicio_codigo'] ?? '',
                'whatsapp_tipo'    => $factura['servicio_whatsapp_tipo'] ?? 'regular',
                'whatsapp_mensaje' => $factura['servicio_whatsapp_mensaje'] ?? '',
            ],
            'detalles'    => $detallesJson,
            'logo_base64' => $logoBase64
        ]);
        exit;
    }

    public function whatsapp($id) {
        if (!Request::isPost()) {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        try {
            $this->facturaModel->update($id, ['enviado_whatsapp' => 1]);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function anular($id) {
        if (!Request::isPost()) {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        try {
            $factura = $this->facturaModel->find($id);
            if (!$factura) {
                echo json_encode(['success' => false, 'error' => 'Factura no encontrada']);
                exit;
            }

            if ($factura['estado'] == 'Pagada') {
                echo json_encode(['success' => false, 'error' => 'No se pueden anular facturas que ya han sido pagadas']);
                exit;
            }

            $this->facturaModel->update($id, ['estado' => 'Anulada']);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function abrirApp($id) {
        if (!Request::isPost()) {
            echo json_encode(['success' => false, 'error' => 'Método no permitido']);
            exit;
        }

        try {
            $factura = $this->facturaModel->find($id);
            if (!$factura) throw new \Exception('Factura no encontrada');

            $cliente = $this->clienteModel->find($factura['id_cliente']);
            if (!$cliente) throw new \Exception('Cliente no encontrado');

            $tel = preg_replace('/[^0-9]/', '', $cliente['telefono'] ?? '');
            if (strlen($tel) === 8) {
                $tel = "507" . $tel;
            } else if (strlen($tel) > 8 && !str_starts_with($tel, '507')) {
                if (strlen($tel) <= 10) $tel = "507" . $tel;
            }

            $nombreCliente = trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? ''));

            $servCode = $factura['servicio_codigo'] ?? '';
            $esMaritimo = ($factura['id_servicio'] == 4 || $servCode == 'C2' || stripos($factura['servicio_nombre'] ?? '', 'MARITIMA') !== false);
            
            // Nueva condición: Solo foto para códigos O1, O2, O3, O4, O5, O6
            $soloFoto = in_array($servCode, ['O1', 'O2', 'O3', 'O4', 'O5', 'O6']);

            if ($soloFoto) {
                $msgText = ""; // Solo foto, sin mensaje
            } else if ($esMaritimo) {
                $msgText = "Hola, {$nombreCliente} 👋\nSu paquete o carga ya está disponible 📦\n\nRecibo: {$factura['codigo_factura']}\nTotal: $" . number_format($factura['total'], 2) . "\n\nPuede pasar a retirarlo cuando lo desee ✅\n📌 Le solicitamos retirar su carga dentro de 7 días hábiles.\nPasado este plazo, se aplicará un cargo por almacenamiento.";
            } else {
                $msgText = "Hola {$nombreCliente} 👋\nSu paquete ya está disponible en nuestras oficinas.\n\n• Recibo Nº: {$factura['codigo_factura']}\n• Total: $" . number_format($factura['total'], 2) . "\n\nPuede pasar a retirarlo cuando lo desee ✅";
            }

            $configModel = new \App\Models\ConfiguracionModel();
            $config = $configModel->getConfig();
            $whatsappPath = $config['whatsapp_path'] ?? '';

            if (!empty($whatsappPath) && file_exists($whatsappPath)) {
                $cmd = 'start "" "' . $whatsappPath . '" "whatsapp://send?phone=' . $tel . '&text=' . rawurlencode($msgText) . '"';
            } else {
                $cmd = 'start "" "whatsapp://send?phone=' . $tel . '&text=' . rawurlencode($msgText) . '"';
            }
            
            shell_exec($cmd);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function exportar() {
        ini_set('memory_limit', '512M');
        set_time_limit(180);
        
        try {
            $search = Request::get('q', '');
            $estado = Request::get('estado', '');

            $facturas = $this->facturaModel->getExportData($search, $estado);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Listado_Facturas');

            // Encabezados
            $headers = ['Código Factura', 'Fecha Emisión', 'Cliente', 'Código Cliente', 'Subtotal', 'ITBMS', 'Descuento', 'Total', 'Estado', 'Método Pago'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++;
            }

            // Estilo encabezado
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            // Datos
            $rowNum = 2;
            foreach ($facturas as $f) {
                $sheet->setCellValueExplicit('A' . $rowNum, (string)$f['codigo_factura'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $rowNum, date('d/m/Y', strtotime($f['fecha_emision'])), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $rowNum, (string)($f['cliente_nombre'] . ' ' . $f['cliente_apellido']), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D' . $rowNum, (string)$f['cliente_codigo'], DataType::TYPE_STRING);
                $sheet->setCellValue('E' . $rowNum, $f['subtotal']);
                $sheet->setCellValue('F' . $rowNum, $f['impuestos']);
                $sheet->setCellValue('G' . $rowNum, $f['descuento']);
                $sheet->setCellValue('H' . $rowNum, $f['total']);
                $sheet->setCellValueExplicit('I' . $rowNum, (string)$f['estado'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('J' . $rowNum, (string)$f['metodo_pago'], DataType::TYPE_STRING);

                // Formato moneda
                $sheet->getStyle('E'.$rowNum.':H'.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                $rowNum++;
            }

            // Totales
            if ($rowNum > 2) {
                $sheet->setCellValue('G' . $rowNum, 'TOTALES:');
                $sheet->setCellValue('H' . $rowNum, '=SUM(H2:H' . ($rowNum - 1) . ')');
                $sheet->getStyle('G' . $rowNum . ':H' . $rowNum)->getFont()->setBold(true);
                $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('A1:J' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            // Descarga segura
            $filename = "Listado_Facturas_" . date('Ymd_His') . ".xlsx";
            $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_fact_ls_');
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save($tempFile);

            while (ob_get_level()) ob_end_clean();

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Content-Length: ' . filesize($tempFile));

            readfile($tempFile);
            unlink($tempFile);
            exit;

        } catch (\Exception $e) {
            while (ob_get_level()) ob_end_clean();
            die("Error exporting invoices: " . $e->getMessage());
        }
    }
}
