<!DOCTYPE html>
<html lang="es">
<?php date_default_timezone_set('America/Panama'); ?>
<head>
    <meta charset="UTF-8">
    <title>Recibo <?= $factura['codigo_factura'] ?></title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
        }
        body {
            font-family: 'Century Gothic', 'Arial', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .recibo {
            width: 7.4in;
            height: 3.4in;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            position: relative;
        }
        
        /* Estilos de tabla del PDF */
        .table_1 {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #000;
            table-layout: fixed;
        }
        .table_1 td, .table_1 th {
            padding: 2px 4px;
            vertical-align: middle;
        }
        .borde-derecho { border-right: 1px solid #000; }
        .borde-inferior { border-bottom: 1px solid #000; }
        .borde-superior { border-top: 1px solid #000; }
        .borde-izquierdo { border-left: 1px solid #000; }
        .gris-claro { background-color: #f0f0f0; }
        .texto-rojo { color: #c00000; }
        .negrita { font-weight: bold; }
        .centrado { text-align: center; }
        .derecha { text-align: right; }
        .mayuscula { text-transform: uppercase; }
        
        .check-box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            margin-right: 5px;
            text-align: center;
            line-height: 16px;
            font-size: 10px;
        }
        .pie-nota {
            font-size: 7pt;
            color: #666;
            margin-top: 5px;
            padding-left: 5px;
        }

        /* Botones flotantes */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-print { background: #2563eb; color: white; }
        .btn-close { background: #6b7280; color: white; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .recibo { 
                margin: 0.15in auto 0 auto; 
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <!-- Botones de Control -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Imprimir Recibo</button>
        <button onclick="window.close()" class="btn btn-close">Cerrar</button>
    </div>

    <div class="recibo">
        <table class="table_1" cellpadding="0" cellspacing="0" style="height: 3.4in;">
            <!-- CUERPO PRINCIPAL -->
            <tr>
                <!-- COLUMNA IZQUIERDA -->
                <td class="borde-derecho" style="vertical-align: top; height: 2.8in;">
                    <table width="100%" cellpadding="2" cellspacing="0">
                        <!-- Fila 1: Logo y datos -->
                        <tr>
                            <td colspan="5" class="borde-inferior" style="height: 50px;">
                                <table width="100%">
                                    <tr>
                                        <td width="60%" class="centrado">
                                            <?php if (isset($logo_base64) && $logo_base64): ?>
                                                <img src="data:image/png;base64,<?= $logo_base64 ?>" style="max-height: 45px;">
                                            <?php else: ?>
                                                <span style="font-size: 22px; font-weight: bold; color: #c00000;">China Box</span>
                                            <?php endif; ?>
                                        </td>
                                        <td width="40%" style="font-size: 8pt;">
                                            Plaza 100, El Ingenio, Ave. La Paz<br>
                                            Cel: 507 6768-3586 | WeChat: ChinaBox507
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Fila 2: Título -->
                        <tr>
                            <td colspan="5" class="borde-inferior gris-claro">
                                <span class="negrita" style="font-size: 11pt;"><?= $factura['codigo_factura'] ?></span>
                            </td>
                        </tr>
                        <!-- Datos Cliente -->
                        <tr>
                            <td width="15%" class="borde-derecho borde-inferior">Código:</td>
                            <td width="35%" class="borde-derecho borde-inferior"><?= $cliente['codigo'] ?></td>
                            <td width="15%" class="borde-derecho borde-inferior">Teléfono:</td>
                            <td colspan="2" class="borde-inferior"><?= $cliente['telefono'] ?></td>
                        </tr>
                        <tr>
                            <td class="borde-derecho borde-inferior">Nombre:</td>
                            <td class="borde-derecho borde-inferior mayuscula truncate"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></td>
                            <td class="borde-derecho borde-inferior">RUC:</td>
                            <td colspan="2" class="borde-inferior"><?= $cliente['ruc'] ?></td>
                        </tr>
                        <tr>
                            <td class="borde-derecho borde-inferior">Envio:</td>
                            <td class="borde-derecho borde-inferior"><?= $factura['servicio_nombre'] ?? 'Aarea' ?></td>
                            <td class="borde-derecho borde-inferior">Fecha:</td>
                            <td colspan="2" class="borde-inferior"><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                        </tr>
                        <!-- Encabezados Items -->
                        <tr class="gris-claro negrita centrado" style="font-size: 8pt;">
                            <td colspan="2" class="borde-derecho borde-inferior" style="height: 18px;">GUIA</td>
                            <td width="12%" class="borde-derecho borde-inferior">PESO/VOL</td>
                            <td width="12%" class="borde-derecho borde-inferior">PRECIO</td>
                            <td width="15%" class="borde-inferior">MONTO</td>
                        </tr>
                        <!-- Cuerpo Items -->
                        <tr>
                            <td colspan="2" class="borde-derecho" style="vertical-align: top; height: 1.8in; padding: 0;">
                                <table width="100%" style="border-collapse: collapse;">
                                    <tr>
                                        <td height="1.2in" style="vertical-align: top; padding: 5px;">
                                            <?php foreach($detalles as $it): ?>
                                                <?= $it['guia'] ?><br>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                    <?php if(!empty($factura['observaciones'])): ?>
                                    <tr>
                                        <td style="vertical-align: bottom; padding: 5px; font-size: 8pt;">
                                            <?= $factura['observaciones'] ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </td>
                            <td class="borde-derecho centrado" style="vertical-align: top; height: 1.8in;">
                                <?php foreach($detalles as $it): ?>
                                    <?= $it['cantidad'] ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td class="borde-derecho centrado" style="vertical-align: top; height: 1.8in;">
                                <?php foreach($detalles as $it): ?>
                                    <?= number_format($it['precio_unitario'], 2) ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td class="centrado" style="vertical-align: top; height: 1.8in;">
                                <?php foreach($detalles as $it): ?>
                                    <?= number_format($it['subtotal'], 2) ?><br>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </table>
                </td>
                
                <!-- COLUMNA DERECHA -->
                <td width="30%" style="vertical-align: top; height: 2.8in;">
                    <table width="100%" height="100%" cellpadding="3" cellspacing="0">
                        <tr>
                            <td class="borde-inferior gris-claro centrado">
                                COMPROBANTE #<br>
                                <span class="texto-rojo negrita"><?= $factura['codigo_factura'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="borde-inferior" style="padding: 10px; vertical-align: top;">
                                <?php 
                                $metodo = strtoupper($factura['metodo_pago'] ?? '');
                                $pagos = ['EFECTIVO' => 'Efectivo', 'TARJETA' => 'Tarjeta', 'TRANSFERENCIA' => 'Transferencia', 'YAPPY' => 'Yappy'];
                                foreach($pagos as $key => $label):
                                    $checked = ($metodo == $key || ($key == 'TRANSFERENCIA' && $metodo == 'ACH'));
                                ?>
                                <div style="margin-bottom: 8px;">
                                    <span class="check-box"><?= $checked ? 'X' : '&nbsp;' ?></span>
                                    <span style="font-size: 12pt;"><?= $label ?></span>
                                </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- FILA INFERIOR -->
            <tr>
                <td class="borde-derecho borde-superior" style="vertical-align: middle;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="50%" style="padding-left: 5px; font-size: 8pt;"><?= date('d/m/Y h:i A') ?></td>
                            <td width="20%" class="derecha">TOTAL</td>
                            <td width="30%" class="borde-izquierdo centrado" style="font-size: 12pt;"><?= number_format($factura['total'], 2) ?></td>
                        </tr>
                    </table>
                </td>
                <td class="borde-superior" style="vertical-align: middle; text-align: center;">
                    <span style="font-size: 14pt; color: #9e9a9aff;">FIRMA</span>
                </td>
            </tr>
        </table>
        <div class="pie-nota">
            * Este comprobante no constituye factura fiscal. Para su elaboración, favor enviar RUC y correo electrónico.
        </div>
    </div>

    <script>
        // Auto-impresión al cargar si no se especifica lo contrario
        window.onload = function() {
            <?php if (!isset($autoPrintBrowser) || $autoPrintBrowser): ?>
            setTimeout(function() { window.print(); }, 500);
            <?php endif; ?>
        };
    </script>
</body>
</html>
