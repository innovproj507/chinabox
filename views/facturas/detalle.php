<?php $page_title = 'Detalle de Factura'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<div class="px-6 py-4">
    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap justify-between items-center">
        <div class="flex items-center space-x-4">
            <h2 class="text-xl font-bold font-mono tracking-tight text-inventory">
                <?= $factura['codigo_factura'] ?>
            </h2>
            <?php 
                $estadoCls = match($factura['estado']) {
                    'Pagada' => 'bg-green-100 text-green-800',
                    'Pendiente' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-red-100 text-red-800'
                };
            ?>
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $estadoCls ?>">
                <?= $factura['estado'] ?>
            </span>
        </div>
        
        <div class="flex items-center space-x-2 mt-4 sm:mt-0">
            <?php if ($factura['estado'] !== 'Pagada'): ?>
            <a href="<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/editar" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <?php endif; ?>
            
            
            <a href="<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/imprimir?preview_pdf=1" target="_blank" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-eye mr-2"></i>Vista Previa
            </a>
            
            <a href="<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/imprimir" target="_blank" class="bg-gray-800 text-white hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-print mr-2"></i>Imprimir
            </a>
            <button onclick="notificarWhatsApp()" class="bg-green-500 text-white hover:bg-green-600 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
            </button>

            <?php if ($factura['estado'] == 'Pendiente'): ?>
            <button onclick="anularFactura()" class="bg-white border border-red-300 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-ban mr-2"></i>Anular
            </button>
            <?php endif; ?>
            
            <?php if($factura['estado'] !== 'Pagada' && $user['is_superuser']): ?>
            <form action="<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/eliminar" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta factura?');" class="inline">
                <button type="submit" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Panel Izquierdo: Info Factura y Cliente -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Cliente -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider"><i class="fas fa-user-circle mr-2 text-inventory"></i>Datos del Cliente</h3>
                </div>
                <div class="p-6">
                    <p class="text-lg font-semibold text-gray-900 mb-1"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></p>
                    <p class="text-sm text-gray-500 mb-4 bg-gray-100 px-2 py-1 rounded inline-block font-mono">CÓDIGO: <?= $cliente['codigo'] ?></p>
                    
                    <ul class="space-y-3 text-sm text-gray-600">
                        <?php if($cliente['ruc']): ?>
                        <li class="flex items-start"><i class="fas fa-id-card mt-1 mr-3 w-4 text-gray-400"></i><span>RUC/ID: <?= $cliente['ruc'] ?></span></li>
                        <?php endif; ?>
                        <li class="flex items-start"><i class="fas fa-phone mt-1 mr-3 w-4 text-gray-400"></i><span><?= $cliente['telefono'] ?: 'No registrado' ?></span></li>
                        <li class="flex items-start"><i class="fas fa-envelope mt-1 mr-3 w-4 text-gray-400"></i><span><?= $cliente['email'] ?></span></li>
                    </ul>
                </div>
            </div>

            <!-- Resumen Factura -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider"><i class="fas fa-info-circle mr-2 text-inventory"></i>Detalle de Pago</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-2 text-sm">
                            <span class="text-gray-500">Fecha Emisión:</span>
                            <span class="font-medium"><?= date('d/m/Y g:i A', strtotime($factura['fecha_emision'])) ?></span>
                        </div>
                        <div class="flex justify-between border-b pb-2 text-sm">
                            <span class="text-gray-500">Servicio Global:</span>
                            <span class="font-bold text-inventory"><?= $factura['servicio_nombre'] ?: 'N/A' ?></span>
                        </div>
                        <div class="flex justify-between border-b pb-2 text-sm">
                            <span class="text-gray-500">Anotaciones:</span>
                            <span class="font-medium"><?= $factura['observaciones'] ?: 'Ninguna' ?></span>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg inline-block w-full">
                            <div class="flex justify-between text-gray-600 text-sm mb-1">
                                <span>Subtotal</span>
                                <span>$<?= number_format($factura['subtotal'], 2) ?></span>
                            </div>
                            <?php if($factura['descuento'] > 0): ?>
                            <div class="flex justify-between text-red-500 text-sm mb-1">
                                <span>Descuento</span>
                                <span>-$<?= number_format($factura['descuento'], 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if($factura['impuestos'] > 0): ?>
                            <div class="flex justify-between text-gray-600 text-sm mb-1">
                                <span>ITBMS (7%)</span>
                                <span>$<?= number_format($factura['impuestos'], 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between font-bold text-inventory text-xl mt-3 pt-3 border-t border-gray-200">
                                <span>Total a Pagar</span>
                                <span>$<?= number_format($factura['total'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Servicios e Items -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-list-ul mr-2 text-inventory"></i>Items Listados</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detalle/Nota</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Cant/Peso</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio U.</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php foreach($detalles as $d): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500"><?= $d['guia'] ?: '-' ?></td>
                                <td class="px-6 py-4 text-center text-sm font-medium text-gray-900"><?= $d['cantidad'] ?></td>
                                <td class="px-6 py-4 text-right text-sm text-gray-500">$<?= number_format($d['precio_unitario'], 2) ?></td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">$<?= number_format($d['subtotal'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- HIDDEN CONTAINER FOR WHATSAPP CAPTURE (Matches original layout) -->
    <!-- color-scheme: light FORZA que Chrome ignore el Dark Mode de Windows para este elemento -->
    <div style="position: absolute; left: -9999px; top: 0; color-scheme: light;">
        <div id="capture-wrapper" style="padding: 40px; background-color: #ffffff; color-scheme: light; color: #000000;">
            <div id="invoice-capture" style="width: 19.5cm; background: #ffffff; border: 1px solid black; font-family: Arial, sans-serif; color: #000000; line-height: 1.2; box-sizing: border-box; color-scheme: light;">
            
            <!-- HEADER: Logo & Address -->
            <div style="display: flex; border-bottom: 1px solid black; height: 2.2cm;">
                <div style="width: 50%; display: flex; align-items: center; justify-content: center; padding: 5px;">
                    <?php if ($logo_base64): ?>
                        <img src="data:image/png;base64,<?= $logo_base64 ?>" style="max-height: 1.4cm; max-width: 100%;">
                    <?php else: ?>
                        <h1 style="font-size: 24px; font-weight: bold; color: #dc2626; margin: 0;">China B<span style="color: #1d4ed8;">o</span>x</h1>
                    <?php endif; ?>
                </div>
                <div style="width: 50%; padding: 8px; font-size: 11px; line-height: 1.3; display: flex; flex-direction: column; justify-content: center; color: #000;">
                    <div><strong>Dirección:</strong> Plaza 100, El Ingenio, Ave. La Paz</div>
                    <div><strong>Cel / WhatsApp:</strong> 507 6768-3586</div>
                    <div><strong>WeChat:</strong> ChinaBox507</div>
                    <div><strong>Ruc.</strong> 155665594-2-2018-587207</div>
                </div>
            </div>

            <!-- INVOICE CODE -->
            <div style="border-bottom: 1px solid black; padding: 10px 20px; background-color: #f3f4f6; font-weight: bold; font-size: 14px;">
                <span class="invoice-number-red-force" style="color: #dc2626 !important;"><?= $factura['codigo_factura'] ?></span>
            </div>

            <!-- CLIENT INFO GRID (3 rows) -->
            <table style="width: 100%; border-bottom: 1px solid black; border-collapse: collapse; font-size: 12px; color: #000000;">
                <tr style="height: 40px; border-bottom: 1px solid black;">
                    <td style="width: 2cm; border-right: 1px solid black; padding: 4px 15px; font-weight: bold; vertical-align: middle; color: #000000;">Código:</td>
                    <td style="width: 6cm; border-right: 1px solid black; padding: 4px 15px; text-align: center; font-weight: bold; vertical-align: middle; color: #000000;"><?= $cliente['codigo'] ?></td>
                    <td style="width: 2cm; border-right: 1px solid black; padding: 4px 15px; font-weight: bold; vertical-align: middle; color: #000000;">Teléfono:</td>
                    <td style="padding: 4px 15px; vertical-align: middle; color: #000000;"><?= $cliente['telefono'] ?></td>
                </tr>
                <tr style="height: 40px; border-bottom: 1px solid black;">
                    <td style="border-right: 1px solid black; padding: 4px 15px; font-weight: bold; vertical-align: middle; color: #000000;">Nombre:</td>
                    <td style="border-right: 1px solid black; padding: 4px 15px; text-transform: uppercase; vertical-align: middle; color: #000000;"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></td>
                    <td style="border-right: 1px solid black; padding: 4px 15px; font-weight: bold; vertical-align: middle; color: #000000;">RUC:</td>
                    <td style="padding: 4px 15px; text-align: left; vertical-align: middle; color: #000000;"><?= $cliente['ruc'] ?></td>
                </tr>
                <tr style="height: 40px;">
                    <td style="border-right: 1px solid black; padding: 4px 15px; font-weight: bold; font-style: italic; vertical-align: middle; color: #000000;">Envio</td>
                    <td style="border-right: 1px solid black; padding: 4px 15px; vertical-align: middle; color: #000000;"><?= $factura['servicio_nombre'] ?? 'N/A' ?></td>
                    <td style="border-right: 1px solid black; padding: 4px 15px; font-weight: bold; vertical-align: middle; color: #000000;">Fecha:</td>
                    <td style="padding: 4px 15px; vertical-align: middle; color: #000000;"><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></td>
                </tr>
            </table>

            <!-- ITEMS TABLE -->
            <table style="width: 100%; border-collapse: collapse; font-size: 11px; text-align: center; color: #000000;">
                <thead>
                    <tr style="height: 40px; border-bottom: 1px solid black; font-weight: bold; background-color: #f9fafb;">
                        <th style="width: 70%; border-right: 1px solid black; padding: 2px 15px; text-align: center; vertical-align: middle; color: #000000;">Guia</th>
                        <th style="width: 10%; border-right: 1px solid black; padding: 2px; line-height: 0.9; font-size: 11px; vertical-align: middle; color: #000000;">Peso<br>Volumen</th>
                        <th style="width: 10%; border-right: 1px solid black; padding: 2px; vertical-align: middle; color: #000000;">Precio x LB</th>
                        <th style="width: 10%; padding: 2px; vertical-align: middle; color: #000000;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                    <tr style="height: 22px;">
                        <td style="border-right: 1px solid black; text-align: left; padding: 2px 8px; font-family: monospace; border-bottom: 0.1pt solid #eee; color: #000000;">
                            <?= htmlspecialchars($detalle['guia']) ?>
                        </td>
                        <td style="border-right: 1px solid black; font-weight: bold; border-bottom: 0.1pt solid #eee; color: #000000;">
                            <?= $detalle['cantidad'] ?>
                        </td>
                        <td style="border-right: 1px solid black; border-bottom: 0.1pt solid #eee; color: #000000;">
                            <?= number_format($detalle['precio_unitario'], 2) ?>
                        </td>
                        <td style="font-weight: bold; border-bottom: 0.1pt solid #eee; color: #000000;">
                            <?= number_format($detalle['subtotal'], 2) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Filler rows (Total 9 rows for items) -->
                    <?php 
                    $num_items = count($detalles);
                    $total_rows_needed = 9;
                    $empty = max(0, $total_rows_needed - $num_items);
                    for ($i = 0; $i < $empty; $i++): 
                        $isLast = ($i === $empty - 1);
                    ?>
                    <tr style="height: 22px;">
                        <td style="border-right: 1px solid black; text-align: left; padding: 2px 20px; vertical-align: bottom; color: #000000;">
                            <?php if ($isLast && $factura['observaciones'] && $idServicio != 4): ?>
                                <div style="font-weight: bold; font-size: 15px; margin-bottom: 8px; color: #000000;"><?= htmlspecialchars($factura['observaciones']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="border-right: 1px solid black; color: #000000;"></td>
                        <td style="border-right: 1px solid black; color: #000000;"></td>
                        <td style="color: #000000;"></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr style="height: 45px; border-top: 1px solid black;">
                        <td style="text-align: left; padding: 4px 20px; font-style: italic; font-size: 11px; color: #000000; vertical-align: middle;">
                            <?= date('d/m/Y H:i') ?>
                        </td>
                        <td colspan="2" style="border-right: 1px solid black; text-align: right; font-weight: bold; padding-right: 20px; font-size: 16px; vertical-align: middle; color: #000000;">TOTAL</td>
                        <td style="font-weight: bold; font-size: 20px; background-color: #f3f4f6; vertical-align: middle; color: #000000;"><?= number_format($factura['total'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>

        </div>
        <!-- FISCAL NOTE -->
        <div style="padding: 12px 0 0 40px; text-align: left; font-size: 11px; color: #000; font-weight: bold;">
            * Este comprobante no constituye factura fiscal. Para su elaboración, favor enviar RUC y correo electrónico.
        </div>
    </div>
</div>


<?php ob_start(); ?>
<script>
    async function notificarWhatsApp() {
        let tel = "<?= preg_replace('/[^0-9]/', '', $cliente['telefono'] ?? '') ?>";
        
        // Optimización de número para Panamá (507)
        if (tel.length === 8) {
            tel = "507" + tel;
        } else if (tel.length > 8 && !tel.startsWith('507')) {
            if (tel.length <= 10) tel = "507" + tel;
        }

        if(tel.length < 8) {
            Swal.fire('Atención', 'El cliente no tiene un número de teléfono válido registrado.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Generando imagen...',
            text: 'Preparando recibo para WhatsApp...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        <?php 
            $idServicio = $factura['id_servicio'] ?? 0;
            $servCode = $factura['servicio_codigo'] ?? '';
            $servNombre = $factura['servicio_nombre'] ?? '';
            $esMaritimo = ($idServicio == 4 || $servCode == 'C2' || stripos($servNombre, 'MARITIMA') !== false);
            $soloFoto = in_array($servCode, ['O1', 'O2', 'O3', 'O4', 'O5', 'O6']);
            $nombreCliente = trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? ''));
            
            if ($soloFoto) {
                $msgTemplate = "";
            } else if ($esMaritimo) {
                $msgTemplate = "Hola, *{$nombreCliente}* 👋\nSu carga Maritima desde China ya está disponible 📦\n\n*Recibo:* *{$factura['codigo_factura']}*\n*Total:* *{$factura['total']}*\n\n🔴 Por favor envíenos su pago en los siguientes 5 días . 🔴\n\nPronto le notificaremos detalles sobre la entrega.";
            } else {
                $msgTemplate = "Hola, *{$nombreCliente}* 👋\nSu paquete ya está disponible en nuestras oficinas.\n\n• *Recibo Nº:* *{$factura['codigo_factura']}*\n• *Total:* *$" . number_format($factura['total'], 2) . "*\n\nPuede pasar a retirarlo cuando lo desee ✅";
            }
        ?>

        const msgText = `<?= str_replace('`', '\`', $msgTemplate) ?>`;

        try {
            const captureArea = document.getElementById('capture-wrapper');
            
            // ═══════════════════════════════════════════════════════
            // SOLUCIÓN TRIPLE-CAPA PARA DARK MODE DE WINDOWS
            // ═══════════════════════════════════════════════════════
            // CAPA 1: Quitar clase 'dark' del documento real
            const htmlEl = document.documentElement;
            const wasDark = htmlEl.classList.contains('dark');
            if (wasDark) htmlEl.classList.remove('dark');

            // CAPA 2: Forzar colores como inline styles en TODOS los elementos reales
            // Inline style > cualquier regla CSS de clase (sin importar .dark)
            const allCapture = captureArea.querySelectorAll('*');
            const savedStyles = [];
            allCapture.forEach((el, i) => {
                savedStyles[i] = { color: el.style.color, opacity: el.style.opacity };
                el.style.opacity = '1';
                if (!el.classList.contains('invoice-number-red-force')) {
                    el.style.color = '#000000';
                } else {
                    el.style.color = '#dc2626';
                }
            });
            captureArea.style.color = '#000000';
            captureArea.style.backgroundColor = '#ffffff';

            // CAPA 3: Forzar que el navegador recalcule estilos ANTES de capturar
            document.body.offsetHeight; // fuerza reflow síncrono
            await new Promise(r => setTimeout(r, 150)); // espera repaint

            let canvas;
            try {
                canvas = await html2canvas(captureArea, {
                    scale: 3,
                    useCORS: true,
                    backgroundColor: '#ffffff'
                });
            } finally {
                // Restaurar TODO (aunque falle la captura)
                if (wasDark) htmlEl.classList.add('dark');
                allCapture.forEach((el, i) => {
                    el.style.color = savedStyles[i].color;
                    el.style.opacity = savedStyles[i].opacity;
                });
                captureArea.style.color = '';
                captureArea.style.backgroundColor = '';
            }

            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.95));
            const file = new File([blob], 'Recibo_<?= $factura['codigo_factura'] ?>.jpg', { type: 'image/jpeg' });

            fetch('<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/whatsapp', { method: 'POST' });

            // FLUJO 1: COMPARTIR NATIVO (EL MÁS AUTOMÁTICO - Windows/Móvil)
            if (navigator.share) {
                try {
                    await navigator.share({
                        files: [file],
                        text: msgText
                    });
                    Swal.close();
                    return; 
                } catch (e) {
                    console.warn("Share falló:", e);
                    if (e.name === 'AbortError') { Swal.close(); return; }
                }
            }

            // FLUJO 2: FALLBACK CLIPBOARD (Ctrl+V)
            fetch('<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/abrir-app', { method: 'POST' });

            try {
                if (navigator.clipboard && window.ClipboardItem) {
                    const item = new ClipboardItem({ [blob.type]: blob });
                    await navigator.clipboard.write([item]);
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Imagen Lista!',
                        html: `
                            <div class="text-left text-sm space-y-2">
                                <p>1. La imagen está en tu portapapeles.</p>
                                <p>2. Se descargará el archivo automáticamente.</p>
                                <hr class="my-2">
                                <p><b>¿Cómo enviar?</b></p>
                                <p>En el chat de WhatsApp presiona <b>Ctrl + V</b> para pegar la imagen.</p>
                                <button id="copyTextBtn" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mt-2">Copiar Mensaje de Texto</button>
                            </div>
                        `,
                        confirmButtonText: 'Abrir WhatsApp',
                        confirmButtonColor: '#25D366',
                        didOpen: () => {
                            document.getElementById('copyTextBtn').onclick = () => {
                                navigator.clipboard.writeText(msgText);
                                Swal.showValidationMessage('¡Texto copiado!');
                            };
                        }
                    }).then(() => {
                        window.open(`https://web.whatsapp.com/send?phone=${tel}&text=${encodeURIComponent(msgText)}`, '_blank');
                    });
                } else {
                    await navigator.clipboard.writeText(msgText);
                }

                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none'; a.href = url;
                a.download = `Recibo_<?= $factura['codigo_factura'] ?>.jpg`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                if (!Swal.isVisible()) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Recibo descargado',
                        text: 'Arrastra el archivo al chat para enviarlo.',
                        confirmButtonText: 'Abrir WhatsApp',
                        confirmButtonColor: '#25D366'
                    }).then(() => {
                         window.open(`https://web.whatsapp.com/send?phone=${tel}&text=${encodeURIComponent(msgText)}`, '_blank');
                    });
                }
            } catch (err) {
                console.error("Error fallback:", err);
                Swal.fire('Listo', 'Por favor adjunta la imagen descargada.', 'info');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error', 'No se pudo generar imagen: ' + err.message, 'error');
        }
    }

    function anularFactura() {
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción anulará la factura y no se podrá revertir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, anular factura',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                fetch('<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/anular', {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Anulada!',
                            text: 'La factura ha sido anulada correctamente.',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.error || 'No se pudo anular la factura', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
                });
            }
        });
    }
</script>
<?php $extra_js = ob_get_clean(); ?>

<style>
    /*
     * SOLUCIÓN DEFINITIVA TEXTO GRIS / DARK MODE WINDOWS:
     * color-scheme: light fuerza que Chrome trate este bloque como Light Mode,
     * ignorando la configuración de Dark Mode del sistema operativo.
     */
    #capture-wrapper,
    #invoice-capture {
        color-scheme: light !important;
        forced-color-adjust: none !important;
        background-color: #ffffff !important;
        color: #000000 !important;
    }

    #invoice-capture *,
    #capture-wrapper * { 
        color-scheme: light !important;
        forced-color-adjust: none !important;
        color: #000000 !important; 
        opacity: 1 !important;
        visibility: visible !important;
        text-shadow: none !important;
        filter: none !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }

    /* Excepción: el número de factura ES rojo */
    #invoice-capture .invoice-number-red-force {
        color: #dc2626 !important;
        font-weight: bold !important;
    }
</style>
