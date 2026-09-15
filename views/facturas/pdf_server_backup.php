<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo <?= $factura['codigo_factura'] ?></title>
    <style>
        @page {
            margin: 0;
            size: letter portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            background: white;
        }
        .recibo {
            width: 8.2in;
            height: 5.2in;
            margin: 0 auto;
            background: #fff;
        }
       
        
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
		
        .borde-derecho {
            border-right: 1.5px solid #000;
        }
        .borde-inferior {
            border-bottom: 1.5px solid #000;
        }
        .borde-superior {
            border-top: 1.5px solid #000;
        }
        .gris-claro {
            background-color: #f0f0f0;
        }
        .texto-rojo {
            color: #c00000;
        }
        .negrita {
            font-weight: bold;
        }
        .centrado {
            text-align: center;
        }
        .derecha {
            text-align: right;
        }
        .mayuscula {
            text-transform: uppercase;
        }
        .check-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            text-align: center;
            line-height: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .firma-area {
            height: 120px;
            vertical-align: bottom;
            text-align: center;
            color: #ccc;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .pie-nota {
            font-size: 7pt;
            color: #666;
            margin-top: 10px;
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div class="recibo">
        <table class="table_1" cellpadding="0" cellspacing="0" style="height: 5.2in;">
            <tr>
                <!-- COLUMNA IZQUIERDA (80%) -->
                <td class="borde-derecho" style="vertical-align: top; height: 5.2in;">
                    	<table width="100%" cellpadding="2" cellspacing="0">
                    <!-- Fila 1: Logo y datos de contacto -->
                    <tr>
                        <td colspan="5" class="borde-inferior" style="height: 50px;">
                            <table width="100%">
                                <tr>
                                    <td width="60%" class="centrado" style="padding: 5px 0;">
                                        <?php if (isset($logo_base64) && $logo_base64): ?>
                                            <img src="data:image/png;base64,<?= $logo_base64 ?>" style="max-height: 45px; max-width: 220px;">
                                        <?php else: ?>
                                            <span style="font-size: 22px; font-weight: bold; color: #c00000;">China Box</span>
                                        <?php endif; ?>
                                    </td>
                                    <td width="40%" style="font-size: 8pt; line-height: 1.2;">
                                        Plaza 100, El Ingenio, Ave. La Paz<br>
                                        Cel / WhatsApp: 507 6768-3586<br>
                                        WeChat: ChinaBox507<br>
                                        Ruc. 155665594-2-2018-587207
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Fila 2: Título RECIBO -->
                    <tr>
                        <td colspan="5" class="borde-inferior gris-claro" style="height: 25px;">
                            <span class="negrita texto-rojo" style="font-size: 11pt;">RECIBO <?= $factura['codigo_factura'] ?></span>
                        </td>
                    </tr>
                    
                    <!-- Fila 3: Código y Teléfono -->
                    <tr>
                        <td width="10%" class="borde-derecho borde-inferior negrita" style="height: 22px;">Código:</td>
                        <td width="40%" class="borde-derecho borde-inferior"><?= $cliente['codigo'] ?></td>
                        <td width="10%" class="borde-derecho borde-inferior negrita">Teléfono:</td>
                        <td colspan="2" width="40%" class="borde-inferior"><?= $cliente['telefono'] ?></td>
                    </tr>
                    
                    <!-- Fila 4: Nombre y RUC -->
                    <tr>
                        <td class="borde-derecho borde-inferior negrita" style="height: 22px;">Nombre:</td>
                        <td class="borde-derecho borde-inferior mayuscula"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></td>
                        <td class="borde-derecho borde-inferior negrita">RUC:</td>
                        <td colspan="2" class="borde-inferior"><?= $cliente['ruc'] ?></td>
                    </tr>
                    
                    <!-- Fila 5: Envío y Fecha -->
                    <tr>
                        <td class="borde-derecho borde-inferior negrita" style="height: 22px;">Envio</td>
                        <td class="borde-derecho borde-inferior"><?= $factura['servicio_nombre'] ?? 'USA - PTY (Aerea)' ?></td>
                        <td class="borde-derecho borde-inferior negrita">Fecha:</td>
                        <td colspan="2" class="borde-inferior"><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                    </tr>
                    
                    <!-- Fila 6: Encabezados de tabla de items -->
                    <tr class="gris-claro negrita centrado">
                        <td colspan="2" class="borde-derecho borde-inferior" style="padding: 4px; height: 24px;">Guia</td>
                        <td width="10%" class="borde-derecho borde-inferior">Peso/Vol</td>
                        <td width="10%" class="borde-derecho borde-inferior">Precio x LB</td>
                        <td width="15%" class="borde-inferior">Monto</td>
                    </tr>
                    <tr class="gris-claro negrita centrado">
                        <td colspan="2"></td>
                        <td width="10%"></td>
                        <td width="10%"></td>
                        <td width="15%"></td>
                    </tr>
                    <tr style="vertical-align: top;">
                         <td colspan="2" class="borde-derecho" style="height: 2.5in; padding: 0;">
                            <table width="100%" style="border-collapse: collapse;">
                                <tr>
                                    <td height="1.8in" style="vertical-align: top; padding: 5px; border:none;">
                                        <?php foreach($detalles as $it): ?>
                                            <?= $it['guia'] ?><br>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <?php if(!empty($factura['observaciones'])): ?>
                                <tr>
                                    <td style="vertical-align: bottom; padding: 5px 5px 15px 5px; font-family: sans-serif; font-weight: bold; font-size: 9pt; border:none;">
                                        <?= $factura['observaciones'] ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                         </td>
                        <td class="borde-derecho" style="vertical-align: top; padding-top: 10px; height: 2.5in;">
                            <?php foreach($detalles as $it): ?>
                                <?= $it['cantidad'] ?><br>
                            <?php endforeach; ?>
                        </td>
                        <td class="borde-derecho" style="vertical-align: top; padding-top: 10px; height: 2.5in;">
                            <?php foreach($detalles as $it): ?>
                                <?= number_format($it['precio_unitario'], 2) ?><br> 
                            <?php endforeach; ?>
                        </td>
                        <td class="centrado negrita" style="vertical-align: top; padding-top: 10px; height: 2.5in;">
                            <?php foreach($detalles as $it): ?>
                                <?= number_format($it['subtotal'], 2) ?><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <!-- Fila de Total -->
                    <tr>
                         <td colspan="2" class="borde-superior" style="padding-top: 5px; font-size: 8pt;">
                            <?= date('d/m/Y h:i A') ?>
                        </td>
                        <td colspan="2" class="borde-superior derecha negrita" style="padding-right: 10px; ">TOTAL</td>
                        <td class="borde-superior borde-izquierdo centrado negrita" style="font-size: 12pt; "><?= number_format($factura['total'], 2) ?></td>
                    </tr>

                </table>		
                
                </td>
            
            <!-- COLUMNA DERECHA (20%) -->
            <td width="20%" style="vertical-align: top; height: 5.2in;">
                <table width="100%" height="5.2in;" cellpadding="3" cellspacing="0">
                    <!-- Título RECIBO # -->
                    <tr>
                        <td class="borde-inferior gris-claro centrado negrita" style="padding: 5px 0; height: 35px;">
                            RECIBO #<br>
                            <span class="texto-rojo"><?= $factura['codigo_factura'] ?></span>
                        </td>
                    </tr>
                    
                    <!-- Opciones de pago -->
                    <tr>
                        <td style="padding: 10px 0 0 5px; vertical-align: top;">
                            <?php 
                            $metodo = strtoupper($factura['metodo_pago'] ?? '');
                            $pagos = [
                                'EFECTIVO' => 'Efectivo',
                                'TARJETA' => 'Tarjeta',
                                'TRANSFERENCIA' => 'Transferencia',
                                'YAPPY' => 'Yappy'
                            ];
                            foreach($pagos as $key => $label):
                                $checked = ($metodo == $key || ($key == 'TRANSFERENCIA' && $metodo == 'ACH'));
                            ?>
                            <div style="margin-bottom: 5px;">
                                <span class="check-box"><?= $checked ? 'X' : '&nbsp;' ?></span>
                                <span style="font-size: 9pt; font-weight: bold;"><?= $label ?></span>
                            </div>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    
                    <!-- Espacio flexible -->
                    <tr><td style="height: auto;">&nbsp;</td></tr>

                    <!-- Área de FIRMA -->
                  
                </table>
            </td>
        </tr>
    </table>
    
        <!-- Nota al pie (AHORA DENTRO PARA EVITAR SEGUNDA HOJA) -->
        <div class="pie-nota" style="margin-top: 5px;">
            * Este comprobante no constituye factura fiscal. Para su elaboración, favor enviar RUC y correo electrónico.
        </div>
    </div>
</body>
</html>