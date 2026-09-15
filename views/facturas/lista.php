<?php $page_title = 'Facturas'; $page_subtitle = 'Listado y gestión de facturas'; ?>

<div class="px-6 py-4">
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Header, filtros y búsqueda -->
        <div class="p-6 border-b border-gray-200">
            <form action="<?= $baseUrl ?>facturas" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 w-full md:w-1/3 relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Buscar código, cliente..."
                        class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inventory focus:border-transparent text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-center space-x-2 w-full md:w-auto">
                    <select name="estado" class="border border-gray-300 rounded-lg text-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-inventory w-full md:w-auto">
                        <option value="">Todos los Estados</option>
                        <option value="Pendiente" <?= $estado_filter == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Pagada" <?= $estado_filter == 'Pagada' ? 'selected' : '' ?>>Pagada</option>
                        <option value="Anulada" <?= $estado_filter == 'Anulada' ? 'selected' : '' ?>>Anulada</option>
                    </select>

                    <select name="per_page" class="border border-gray-300 rounded-lg text-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-inventory w-full md:w-auto">
                        <?php foreach ([10, 25, 50, 100] as $n): ?>
                        <option value="<?= $n ?>" <?= $per_page == $n ? 'selected' : '' ?>><?= $n ?> por página</option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Filtrar
                    </button>
                    
                    <a href="<?= $baseUrl ?>facturas-exportar?q=<?= urlencode($search_query) ?>&estado=<?= urlencode($estado_filter) ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center">
                        <i class="fas fa-file-excel mr-2"></i> Exportar
                    </a>
                </div>
            </form>
        </div>

        <!-- Paginación superior -->
        <?php include __DIR__ . '/../includes/pagination.php'; ?>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Emisión</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Servicio</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(empty($facturas)): ?>
                    <tr><td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500 bg-gray-50/50">
                        <i class="fas fa-search mb-3 text-2xl text-gray-300 block"></i>
                        No se encontraron facturas con los filtros aplicados.
                    </td></tr>
                    <?php else: ?>
                        <?php foreach($facturas as $f): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 hidden md:table-cell">
                                <i class="far fa-calendar-alt mr-2 text-gray-400"></i><?= date('d/m/y', strtotime($f['fecha_emision'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-inventory">
                                <?= $f['codigo_factura'] ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium"><?= $f['cliente_nombre'] ?> <?= $f['cliente_apellido'] ?? '' ?></div>
                                <div class="text-xs text-gray-500"><?= $f['cliente_codigo'] ?? '' ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="bg-gray-100 px-2 py-0.5 rounded text-xs text-gray-600 font-medium"><?= $f['servicio_codigo'] ?? 'N/A' ?></span>
                                <div class="text-[10px] text-gray-400 mt-0.5 truncate max-w-[120px]" title="<?= $f['servicio_nombre'] ?>"><?= $f['servicio_nombre'] ?? '' ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                <?= $f['moneda'] ?> <?= number_format($f['total'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                    $estadoCls = match($f['estado']) {
                                        'Pagada' => 'bg-green-100 text-green-800',
                                        'Pendiente' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-red-100 text-red-800'
                                    };
                                    $iconCls = match($f['estado']) {
                                        'Pagada' => 'fa-check',
                                        'Pendiente' => 'fa-clock',
                                        default => 'fa-times'
                                    };
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm <?= $estadoCls ?>">
                                    <i class="fas <?= $iconCls ?> mr-1.5 mt-0.5"></i> <?= $f['estado'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>" class="text-inventory hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Ver Detalle">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <?php if ($f['estado'] !== 'Pagada'): ?>
                                    <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>/editar" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors" title="Editar">
                                        <i class="far fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>/imprimir?preview_pdf=1" target="_blank" class="text-blue-600 hover:text-blue-900 bg-blue-50 p-2 rounded-lg transition-colors" title="Vista Previa (Sin Imprimir)">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>/imprimir" target="_blank" class="text-gray-600 hover:text-gray-900 bg-gray-100 p-2 rounded-lg transition-colors" title="Imprimir/PDF">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button onclick="enviarWhatsApp(<?= $f['id_factura'] ?>)" class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 p-2 rounded-lg transition-colors cursor-pointer" title="Notificar por WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<?php ob_start(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    const BASE_URL = '<?= $baseUrl ?>';

    function resolverMensajeWhatsApp(data) {
        const tipo    = data.servicio.whatsapp_tipo    || 'regular';
        const custom  = (data.servicio.whatsapp_mensaje || '').trim();
        if (tipo === 'solo_foto') return '';

        const nombre  = (data.cliente.nombre + ' ' + (data.cliente.apellido || '')).trim();
        const recibo  = data.codigo_factura;
        const total   = '$' + parseFloat(data.total).toFixed(2);

        const plantilla = custom || (tipo === 'maritimo'
            ? `Hola, *{nombre}* 👋\nSu carga Maritima desde China ya está disponible 📦\n\n*Recibo:* *{recibo}*\n*Total:* *{total}*\n\n🔴 Por favor envíenos su pago en los siguientes 5 días . 🔴\nPronto le notificaremos detalles sobre la entrega.`
            : `Hola, *{nombre}* 👋\nSu paquete ya está disponible en nuestras oficinas.\n\n• *Recibo Nº:* *{recibo}*\n• *Total:* *{total}*\n\nPuede pasar a retirarlo cuando lo desee ✅`);

        return plantilla
            .replace(/{nombre}/g, nombre)
            .replace(/{recibo}/g,  recibo)
            .replace(/{total}/g,   total);
    }

    function escHTML(str) {
        return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function buildCaptureHTML(data) {
        const logoTag = data.logo_base64
            ? `<img src="data:image/png;base64,${data.logo_base64}" style="max-height:1.4cm;max-width:100%;">`
            : `<h1 style="font-size:24px;font-weight:bold;color:#dc2626;margin:0;">China B<span style="color:#1d4ed8;">o</span>x</h1>`;

        const fecha = new Date(data.fecha_emision);
        const fechaFmt = fecha.toLocaleDateString('es-PA', {day:'2-digit',month:'2-digit',year:'numeric'});
        const ahora = new Date().toLocaleString('es-PA');

        const totalRows = 9;
        let itemsHTML = '';
        data.detalles.forEach(d => {
            itemsHTML += `<tr style="height:22px;">
                <td style="border-right:1px solid black;text-align:left;padding:2px 8px;font-family:monospace;border-bottom:0.1pt solid #eee;">${escHTML(d.guia)}</td>
                <td style="border-right:1px solid black;font-weight:bold;border-bottom:0.1pt solid #eee;">${d.cantidad}</td>
                <td style="border-right:1px solid black;border-bottom:0.1pt solid #eee;">${parseFloat(d.precio_unitario).toFixed(2)}</td>
                <td style="font-weight:bold;border-bottom:0.1pt solid #eee;">${parseFloat(d.subtotal).toFixed(2)}</td>
            </tr>`;
        });
        const emptyCount = Math.max(0, totalRows - data.detalles.length);
        for (let i = 0; i < emptyCount; i++) {
            const isLast = (i === emptyCount - 1);
            itemsHTML += `<tr style="height:22px;">
                <td style="border-right:1px solid black;text-align:left;padding:2px 20px;vertical-align:bottom;">
                    ${isLast && data.observaciones ? `<div style="font-weight:bold;font-size:15px;margin-bottom:8px;">${escHTML(data.observaciones)}</div>` : ''}
                </td>
                <td style="border-right:1px solid black;"></td>
                <td style="border-right:1px solid black;"></td>
                <td></td>
            </tr>`;
        }

        return `<div style="padding:40px;background-color:#ffffff;color-scheme:light;">
            <div style="width:19.5cm;background:#ffffff;border:1px solid black;font-family:Arial,sans-serif;color:#000000;line-height:1.2;box-sizing:border-box;">
                <div style="display:flex;border-bottom:1px solid black;height:2.2cm;">
                    <div style="width:50%;display:flex;align-items:center;justify-content:center;padding:5px;">${logoTag}</div>
                    <div style="width:50%;padding:8px;font-size:11px;line-height:1.3;display:flex;flex-direction:column;justify-content:center;color:#000000;">
                        <div>Dirección: Plaza 100, El Ingenio, Ave. La Paz</div>
                        <div>Cel / WhatsApp: 507 6768-3586</div>
                        <div>WeChat: ChinaBox507</div>
                        <div>Ruc. 155665594-2-2018-587207</div>
                    </div>
                </div>
                <div style="border-bottom:1px solid black;padding:10px 20px;background-color:#f3f4f6;font-weight:bold;font-size:14px;">
                    <span style="color:#dc2626;">${escHTML(data.codigo_factura)}</span>
                </div>
                <table style="width:100%;border-bottom:1px solid black;border-collapse:collapse;font-size:12px;color:#000000;">
                    <tr style="height:40px;border-bottom:1px solid black;">
                        <td style="width:2cm;border-right:1px solid black;padding:4px 15px;font-weight:bold;vertical-align:middle;color:#000000;">Código:</td>
                        <td style="width:6cm;border-right:1px solid black;padding:4px 15px;text-align:center;font-weight:bold;vertical-align:middle;color:#000000;">${escHTML(data.cliente.codigo)}</td>
                        <td style="width:2cm;border-right:1px solid black;padding:4px 15px;font-weight:bold;vertical-align:middle;color:#000000;">Teléfono:</td>
                        <td style="padding:4px 15px;vertical-align:middle;color:#000000;">${escHTML(data.cliente.telefono)}</td>
                    </tr>
                    <tr style="height:40px;border-bottom:1px solid black;">
                        <td style="border-right:1px solid black;padding:4px 15px;font-weight:bold;vertical-align:middle;color:#000000;">Nombre:</td>
                        <td style="border-right:1px solid black;padding:4px 15px;text-transform:uppercase;vertical-align:middle;color:#000000;">${escHTML(data.cliente.nombre)} ${escHTML(data.cliente.apellido)}</td>
                        <td style="border-right:1px solid black;padding:4px 15px;font-weight:bold;vertical-align:middle;color:#000000;">RUC:</td>
                        <td style="padding:4px 15px;vertical-align:middle;color:#000000;">${escHTML(data.cliente.ruc)}</td>
                    </tr>
                    <tr style="height:40px;">
                        <td style="border-right:1px solid black;padding:4px 15px;font-weight:bold;font-style:italic;vertical-align:middle;color:#000000;">Envio</td>
                        <td style="border-right:1px solid black;padding:4px 15px;vertical-align:middle;color:#000000;">${escHTML(data.servicio.nombre)}</td>
                        <td style="border-right:1px solid black;padding:4px 15px;font-weight:bold;vertical-align:middle;color:#000000;">Fecha:</td>
                        <td style="padding:4px 15px;vertical-align:middle;color:#000000;">${fechaFmt}</td>
                    </tr>
                </table>
                <table style="width:100%;border-collapse:collapse;font-size:11px;text-align:center;color:#000000;">
                    <thead>
                        <tr style="height:40px;border-bottom:1px solid black;font-weight:bold;background-color:#f9fafb;">
                            <th style="width:70%;border-right:1px solid black;padding:2px 15px;text-align:center;vertical-align:middle;color:#000000;">Guia</th>
                            <th style="width:10%;border-right:1px solid black;padding:2px;line-height:0.9;font-size:11px;vertical-align:middle;color:#000000;">Peso<br>Volumen</th>
                            <th style="width:10%;border-right:1px solid black;padding:2px;vertical-align:middle;color:#000000;">Precio x LB</th>
                            <th style="width:10%;padding:2px;vertical-align:middle;color:#000000;">Monto</th>
                        </tr>
                    </thead>
                    <tbody>${itemsHTML}</tbody>
                    <tfoot>
                        <tr style="height:45px;border-top:1px solid black;">
                            <td style="text-align:left;padding:4px 20px;font-style:italic;font-size:11px;color:#000000;vertical-align:middle;">${ahora}</td>
                            <td colspan="2" style="border-right:1px solid black;text-align:right;font-weight:bold;padding-right:20px;font-size:16px;vertical-align:middle;color:#000000;">TOTAL</td>
                            <td style="font-weight:bold;font-size:20px;background-color:#f3f4f6;vertical-align:middle;color:#000000;">${parseFloat(data.total).toFixed(2)}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div style="padding:12px 0 0 40px;text-align:left;font-size:11px;color:#000000;font-weight:bold;">
                * Este comprobante no constituye factura fiscal. Para su elaboración, favor enviar RUC y correo electrónico.
            </div>
        </div>`;
    }

    async function enviarWhatsApp(facturaId) {
        Swal.fire({
            title: 'Cargando datos...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        let data;
        try {
            const res = await fetch(BASE_URL + 'facturas/' + facturaId + '/json');
            data = await res.json();
        } catch(e) {
            Swal.fire('Error', 'No se pudo cargar la factura.', 'error');
            return;
        }

        let tel = data.cliente.telefono.replace(/[^0-9]/g, '');
        if (tel.length === 8) tel = '507' + tel;
        else if (tel.length > 8 && !tel.startsWith('507') && tel.length <= 10) tel = '507' + tel;

        if (tel.length < 8) {
            Swal.fire('Atención', 'El cliente no tiene un número de teléfono válido registrado.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Generando imagen del recibo...',
            text: 'Capturando comprobante para enviar por WhatsApp...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        const msgText = resolverMensajeWhatsApp(data);

        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:absolute;left:-9999px;top:0;';
        wrapper.innerHTML = buildCaptureHTML(data);
        document.body.appendChild(wrapper);

        // Quitar dark mode antes de capturar (misma solución que detalle.php)
        const htmlEl = document.documentElement;
        const wasDark = htmlEl.classList.contains('dark');
        if (wasDark) htmlEl.classList.remove('dark');

        try {
            const canvas = await html2canvas(wrapper, {
                scale: 3, useCORS: true, logging: false, backgroundColor: '#ffffff',
                windowWidth: wrapper.scrollWidth, windowHeight: wrapper.scrollHeight
            });

            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.95));
            const file = new File([blob], `Recibo_${data.codigo_factura}.jpg`, { type: 'image/jpeg' });

            fetch(BASE_URL + 'facturas/' + facturaId + '/whatsapp', { method: 'POST' });
            document.body.removeChild(wrapper);
            if (wasDark) htmlEl.classList.add('dark'); // restaurar dark mode

            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({ files: [file], text: msgText });
                    Swal.fire({ icon: 'success', title: 'Compartir', text: 'Se ha abierto WhatsApp.', timer: 2000, showConfirmButton: false });
                } catch (e) {
                    Swal.fire({ icon: 'info', title: 'Cancelado', text: 'No se compartió.', timer: 1500, showConfirmButton: false });
                }
            } else {
                fetch(BASE_URL + 'facturas/' + facturaId + '/abrir-app', { method: 'POST' });
                try { await navigator.clipboard.writeText(msgText); } catch(e) {}
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none'; a.href = url;
                a.download = `Recibo_${data.codigo_factura}.jpg`;
                document.body.appendChild(a); a.click();
                window.URL.revokeObjectURL(url);
                Swal.fire({
                    icon: 'success', title: '¡Listo para Enviar!',
                    html: '1. El recibo se ha <b>descargado</b>.<br>2. El texto ha sido <b>copiado</b>.<br><br><b>Instrucción:</b> Arrastra la imagen al chat y presiona <b>Ctrl+V</b> en el comentario.',
                    confirmButtonText: 'Abrir WhatsApp', confirmButtonColor: '#25D366'
                });
            }
        } catch (err) {
            if (wasDark) htmlEl.classList.add('dark'); // restaurar dark mode en caso de error
            if (document.body.contains(wrapper)) document.body.removeChild(wrapper);
            Swal.fire('Error', 'No se pudo generar la imagen: ' + err.message, 'error');
        }
    }
</script>
<?php $extra_js = ob_get_clean(); ?>
