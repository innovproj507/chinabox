<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Flash;
use App\Core\Paginator;
use App\Models\ClienteModel;
use App\Models\FacturaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ClienteController {
    private ClienteModel $clienteModel;

    public function __construct() {
        if (!Auth::check()) {
            Response::redirect('');
        }
        $this->clienteModel = new ClienteModel();
    }

    public function lista() {
        $search = Request::get('q', '');
        $grupo = Request::get('grupo', '');
        $estado = Request::get('estado', '');
        $page = (int) Request::get('page', 1);
        $perPage = 15;

        // Auto-bloquear clientes sin facturas en más de 1 año (máx 1 vez por día)
        $hoy = date('Y-m-d');
        if (($_SESSION['last_inactivos_check'] ?? '') !== $hoy) {
            $bloqueados = $this->clienteModel->bloquearInactivos();
            $_SESSION['last_inactivos_check'] = $hoy;
            if ($bloqueados > 0) {
                Flash::warning("{$bloqueados} cliente(s) bloqueado(s) automáticamente por inactividad (más de 1 año sin facturar).");
            }
        }

        // Recuperar y filtrar clientes basico
        $clientes = $this->clienteModel->all('codigo', 'ASC');
        
        $filtered = array_filter($clientes, function($c) use ($search, $grupo, $estado) {
            $match = true;
            if ($search) {
                $searchLower = strtolower($search);
                $match = $match && (
                    str_contains(strtolower($c['codigo'] ?? ''), $searchLower) ||
                    str_contains(strtolower($c['nombre'] ?? ''), $searchLower) ||
                    str_contains(strtolower($c['apellido'] ?? ''), $searchLower) ||
                    str_contains(strtolower($c['email'] ?? ''), $searchLower) ||
                    str_contains(strtolower($c['ruc'] ?? ''), $searchLower)
                );
            }
            if ($grupo) {
                $match = $match && ($c['grupo'] === $grupo);
            }
            if ($estado) {
                $match = $match && ($c['estado'] === $estado);
            }
            return $match;
        });

        // Paginación en memoria (simplificada)
        $totalItems = count($filtered);
        $paginator = new Paginator($totalItems, $perPage, $page);
        
        $pagedClientes = array_slice($filtered, $paginator->getOffset(), $paginator->getLimit());

        View::render('clientes/lista', [
            'clientes' => $pagedClientes,
            'search_query' => $search,
            'grupo_filter' => $grupo,
            'estado_filter' => $estado,
            'paginator' => $paginator
        ]);
    }

    public function crear() {
        if (Request::isPost()) {
            $codigo = Request::post('codigo');
            $email = Request::post('email');

            if ($this->clienteModel->getByCodigo($codigo)) {
                Flash::error('El código ya está registrado');
                Response::redirect('clientes/crear');
            }

            // Validar email solo si se proporciona
            if (!empty($email) && $this->clienteModel->getByEmail($email)) {
                Flash::error('El email ya está registrado');
                Response::redirect('clientes/crear');
            }

            try {
                $this->clienteModel->create([
                    'grupo' => Request::post('grupo'),
                    'codigo' => $codigo,
                    'nombre' => Request::post('nombre'),
                    'apellido' => Request::post('apellido'),
                    'email' => $email,
                    'telefono' => Request::post('telefono'),
                    'ruc' => Request::post('ruc'),
                    'direccion' => Request::post('direccion'),
                    'ciudad' => Request::post('ciudad'),
                    'pais' => Request::post('pais', 'Panamá'),
                    'estado' => 'activo',
                    'fecha_registro' => date('Y-m-d H:i:s')
                ]);
                Flash::success("Cliente {$codigo} creado exitosamente");
                Response::redirect('clientes');
            } catch (\Exception $e) {
                Flash::error('Error al crear cliente: ' . $e->getMessage());
            }
        }

        View::render('clientes/crear');
    }

    public function editar($id) {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            Flash::error('Cliente no encontrado');
            Response::redirect('clientes');
        }

        if (Request::isPost()) {
            $email = Request::post('email');
            
            // Validar email único excluyendo el actual
            // Validar email solo si se proporciona y ha cambiado
            if (!empty($email)) {
                $existente = $this->clienteModel->getByEmail($email);
                if ($existente && $existente['id_cliente'] != $id) {
                    Flash::error('El email ya está registrado en otro cliente');
                    Response::redirect("clientes/{$id}/editar");
                    exit;
                }
            }

            try {
                $this->clienteModel->update($id, [
                    'grupo' => Request::post('grupo'),
                    'nombre' => Request::post('nombre'),
                    'apellido' => Request::post('apellido'),
                    'email' => !empty($email) ? $email : null,
                    'telefono' => Request::post('telefono'),
                    'ruc' => Request::post('ruc'),
                    'direccion' => Request::post('direccion'),
                    'ciudad' => Request::post('ciudad'),
                    'pais' => Request::post('pais', 'Panamá'),
                    'estado' => Request::post('estado', 'activo')
                ]);
                Flash::success("Cliente {$cliente['codigo']} actualizado exitosamente");
                Response::redirect('clientes');
            } catch (\Exception $e) {
                Flash::error('Error al actualizar cliente: ' . $e->getMessage());
            }
        }

        View::render('clientes/editar', ['cliente' => $cliente]);
    }

    public function detalle($id) {
        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            Flash::error('Cliente no encontrado');
            Response::redirect('clientes');
        }

        $facturaModel = new FacturaModel();
        $facturas = $facturaModel->where('id_cliente', $id);
        
        $totalFacturado = array_reduce($facturas, function($carry, $f) {
            return $carry + $f['total'];
        }, 0);
        
        $pendientes = array_filter($facturas, function($f) {
            return $f['estado'] === 'Pendiente';
        });

        View::render('clientes/detalle', [
            'cliente' => $cliente,
            'facturas' => array_slice($facturas, 0, 10), // últimas 10
            'total_facturas' => count($facturas),
            'facturas_pendientes' => count($pendientes),
            'total_facturado' => $totalFacturado
        ]);
    }

    public function eliminar($id) {
        if (!Request::isPost()) Response::redirect('clientes');

        $cliente = $this->clienteModel->find($id);
        if (!$cliente) {
            Response::redirect('clientes');
        }

        $facturaModel = new FacturaModel();
        if (count($facturaModel->where('id_cliente', $id)) > 0) {
            Flash::error("No se puede eliminar el cliente {$cliente['codigo']} porque tiene facturas asociadas");
        } else {
            $this->clienteModel->delete($id);
            Flash::success("Cliente {$cliente['codigo']} eliminado exitosamente");
        }

        Response::redirect('clientes');
    }

    public function buscar() {
        $query = Request::get('q', '');
        $data = [];
        if ($query) {
            $clientes = $this->clienteModel->search($query);
            foreach ($clientes as $c) {
                $data[] = [
                    'id' => $c['id_cliente'],
                    'codigo' => $c['codigo'],
                    'nombre' => trim(($c['nombre'] ?? '') . ' ' . ($c['apellido'] ?? '')),
                    'email' => $c['email'],
                    'telefono' => $c['telefono'] ?? '',
                    'ruc' => $c['ruc'] ?? ''
                ];
            }
        }
        Response::json($data);
    }

    public function exportarFacturas($id) {
        ini_set('memory_limit', '512M');
        set_time_limit(180);

        try {
            $cliente = $this->clienteModel->find($id);
            if (!$cliente) {
                Flash::error('Cliente no encontrado');
                Response::redirect('clientes');
            }

            $facturaModel = new FacturaModel();
            $facturas = $facturaModel->where('id_cliente', $id);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Facturas - ' . $cliente['codigo']);

            // Encabezados
            $headers = ['Código Factura', 'Fecha Emisión', 'Estado', 'Subtotal', 'ITBMS', 'Descuento', 'Total', 'Método de Pago'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++;
            }

            // Estilo encabezado
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']], // Blue-800
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

            // Datos
            $rowNum = 2;
            foreach ($facturas as $f) {
                $sheet->setCellValueExplicit('A' . $rowNum, (string)$f['codigo_factura'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $rowNum, date('d/m/Y', strtotime($f['fecha_emision'])), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $rowNum, (string)$f['estado'], DataType::TYPE_STRING);
                $sheet->setCellValue('D' . $rowNum, $f['subtotal']);
                $sheet->setCellValue('E' . $rowNum, $f['impuestos']);
                $sheet->setCellValue('F' . $rowNum, $f['descuento']);
                $sheet->setCellValue('G' . $rowNum, $f['total']);
                $sheet->setCellValueExplicit('H' . $rowNum, (string)$f['metodo_pago'], DataType::TYPE_STRING);
                $sheet->getStyle('D'.$rowNum.':G'.$rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                $rowNum++;
            }

            // Totales
            if ($rowNum > 2) {
                $sheet->setCellValue('F' . $rowNum, 'TOTAL GENERAL:');
                $sheet->setCellValue('G' . $rowNum, '=SUM(G2:G' . ($rowNum - 1) . ')');
                $sheet->getStyle('F' . $rowNum . ':G' . $rowNum)->getFont()->setBold(true);
                $sheet->getStyle('G' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('A1:H' . $rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            // Descarga segura
            $filename = "Facturas_{$cliente['codigo']}_" . date('Ymd_His') . ".xlsx";
            $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_fact_');
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

    public function exportarClientes() {
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        
        try {
            $search = Request::get('q', '');
            $estado = Request::get('estado', '');

            $clientes = $this->clienteModel->getFiltered($search, $estado);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Directorio_Clientes');

            // Encabezados
            $headers = ['Código', 'Nombre', 'Apellido', 'Email', 'Teléfono', 'RUC', 'Grupo', 'Ciudad', 'País', 'Estado'];
            $column = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($column . '1', $header);
                $sheet->getColumnDimension($column)->setAutoSize(true);
                $column++;
            }

            // Estilo encabezado
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']], // Blue-800
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

            // Datos
            $rowNum = 2;
            foreach ($clientes as $c) {
                $sheet->setCellValueExplicit('A' . $rowNum, (string)$c['codigo'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('B' . $rowNum, (string)$c['nombre'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('C' . $rowNum, (string)$c['apellido'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D' . $rowNum, (string)$c['email'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('E' . $rowNum, (string)$c['telefono'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('F' . $rowNum, (string)$c['ruc'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('G' . $rowNum, (string)$c['grupo'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('H' . $rowNum, (string)$c['ciudad'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('I' . $rowNum, (string)$c['pais'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('J' . $rowNum, (string)$c['estado'], DataType::TYPE_STRING);
                $rowNum++;
            }

            if ($rowNum > 2) {
                $sheet->getStyle('A1:J' . ($rowNum - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            }

            $filename = "Directorio_Clientes_" . date('Ymd_His') . ".xlsx";
            $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_cli_');
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
            die("Error exporting clients: " . $e->getMessage());
        }
    }

    public function importar() {
        if (Request::isPost()) {
            if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                Flash::error('Error al subir el archivo');
                Response::redirect('clientes-importar');
            }

            try {
                $inputFileName = $_FILES['excel_file']['tmp_name'];
                $spreadsheet = IOFactory::load($inputFileName);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                if (count($rows) <= 1) {
                    Flash::error('El archivo está vacío o no tiene datos');
                    Response::redirect('clientes-importar');
                }

                $headers = array_shift($rows); // Quitar encabezados
                $mapping = $this->mapHeaders($headers);

                $updated = 0;
                $created = 0;
                $errors = 0;

                foreach ($rows as $row) {
                    $data = [];
                    foreach ($mapping as $dbField => $index) {
                        if (isset($row[$index])) {
                            $data[$dbField] = trim((string)$row[$index]);
                        }
                    }

                    // Validar codigo
                    if (empty($data['codigo'])) continue;

                    $codigo = $data['codigo'];
                    $existente = $this->clienteModel->getByCodigo($codigo);
                    
                    try {
                        if ($existente) {
                            // Si existe, actualizamos lo que viene en el excel
                            $this->clienteModel->update($existente['id_cliente'], $data);
                            $updated++;
                        } else {
                            // Si no existe, creamos con valores por defecto
                            $insertData = array_merge([
                                'grupo' => 'CLIENTE',
                                'nombre' => 'Sin Nombre',
                                'apellido' => '',
                                'email' => null,
                                'ruc' => null,
                                'telefono' => null,
                                'direccion' => null,
                                'ciudad' => 'Panamá',
                                'pais' => 'Panamá',
                                'estado' => 'activo',
                                'fecha_registro' => date('Y-m-d H:i:s')
                            ], $data);

                            $this->clienteModel->create($insertData);
                            $created++;
                        }
                    } catch (\Exception $e) {
                        $errors++;
                    }
                }

                Flash::success("Importación finalizada: {$created} creados, {$updated} actualizados. Errores: {$errors}");
                Response::redirect('clientes');

            } catch (\Exception $e) {
                Flash::error('Error al procesar el archivo: ' . $e->getMessage());
                Response::redirect('clientes-importar');
            }
        }

        View::render('clientes/importar');
    }

    private function mapHeaders($headers): array {
        $map = [
            'codigo' => -1,
            'nombre' => -1,
            'apellido' => -1,
            'email' => -1,
            'telefono' => -1,
            'ruc' => -1,
            'grupo' => -1,
            'direccion' => -1,
            'ciudad' => -1,
            'pais' => -1
        ];

        $variants = [
            'codigo' => ['codigo', 'id', 'code', 'codigo cliente'],
            'nombre' => ['nombre', 'name', 'first name'],
            'apellido' => ['apellido', 'last name', 'surname'],
            'email' => ['email', 'correo', 'e-mail'],
            'telefono' => ['telefono', 'phone', 'celular', 'tel'],
            'ruc' => ['ruc', 'cedula', 'id', 'taxid'],
            'grupo' => ['grupo', 'group', 'tipo'],
            'direccion' => ['direccion', 'address'],
            'ciudad' => ['ciudad', 'city'],
            'pais' => ['pais', 'country']
        ];

        foreach ($headers as $index => $header) {
            $header = strtolower(trim($header));
            foreach ($variants as $dbField => $list) {
                if (in_array($header, $list) || str_contains($header, $dbField)) {
                    if ($map[$dbField] === -1) {
                        $map[$dbField] = $index;
                        break;
                    }
                }
            }
        }

        // Eliminar los que no se mapearon
        return array_filter($map, fn($v) => $v !== -1);
    }
}
