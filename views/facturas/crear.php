<?php $page_title = 'Nueva Factura'; $page_subtitle = 'Crear un nuevo recibo/factura'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<div class="px-6 py-4">
    <!-- El formulario base en HTML -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form id="facturaForm" class="p-6">
            
            <!-- Cabecera Factura -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user-circle text-inventory mr-2"></i>Datos del Cliente</h3>
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar Cliente <span class="text-xs text-gray-400">(código, nombre o email)</span></label>
                            <input type="hidden" id="id_cliente" name="id_cliente" value="">
                            <div class="relative">
                                <input type="text" id="clienteSearch" autocomplete="off" placeholder="Escriba para buscar..." 
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm p-2.5 bg-gray-50 border pl-10">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <button type="button" id="clearCliente" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-500 hidden">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <!-- Dropdown de resultados -->
                            <div id="clienteResults" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                            </div>
                            <!-- Cliente seleccionado -->
                            <div id="clienteSelected" class="hidden mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg flex justify-between items-center">
                                <div>
                                    <span class="text-sm font-bold text-blue-800" id="selectedClienteName"></span>
                                    <span class="text-xs text-blue-600 ml-2" id="selectedClienteCode"></span>
                                </div>
                                <button type="button" onclick="limpiarCliente()" class="text-blue-400 hover:text-red-500 text-sm"><i class="fas fa-times-circle"></i></button>
                            </div>
                        </div>

                        <!-- Selección de Servicio Global -->
                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-concierge-bell text-inventory mr-1"></i>Servicio para esta Factura</label>
                            <select id="global_id_servicio" name="id_servicio" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-inventory focus:ring focus:ring-inventory focus:ring-opacity-50 text-sm bg-white" required>
                                <option value="">-- Seleccionar Servicio --</option>
                                <?php foreach($servicios as $s): ?>
                                <option value="<?= $s['id_servicio'] ?>" data-precio="<?= $s['precio'] ?>"><?= $s['codigo'] ?> - <?= $s['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">El servicio seleccionado se aplicará a todos los ítems de esta factura.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-file-invoice text-inventory mr-2"></i>Datos de Factura</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nº Factura (Preview)</label>
                            <input type="text" value="<?= $numero_factura ?>" readonly disabled class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm sm:text-sm p-2 border text-gray-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Emisión</label>
                            <input type="date" value="<?= date('Y-m-d') ?>" readonly disabled class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm sm:text-sm p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <select id="metodo_pago" name="metodo_pago" class="w-full border border-gray-300 rounded-lg p-2 focus:border-inventory focus:ring focus:ring-inventory focus:ring-opacity-50 text-sm">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta de Crédito/Débito</option>
                                <option value="Transferencia">Transferencia Bancaria</option>
                                <option value="Yappy">Yappy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Factura</label>
                            <select id="estado" name="estado" class="w-full border border-gray-300 rounded-lg p-2 focus:border-inventory focus:ring focus:ring-inventory focus:ring-opacity-50 text-sm">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Pagada">Pagada</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <input id="aplica_itbms" type="checkbox" name="aplica_itbms" value="1" class="h-4 w-4 text-inventory focus:ring-inventory border-gray-300 rounded">
                            <label for="aplica_itbms" class="ml-2 block text-sm text-gray-900 font-medium">Aplicar 7% ITBMS</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles/Servicios -->
            <div class="mb-8">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-boxes text-inventory mr-2"></i>Servicios / Contenido</h3>
                    <button type="button" onclick="agregarLinea()" class="bg-success hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Agregar Línea
                    </button>
                </div>
                
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200" id="tablaDetalles">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nº Guía / Nota (Contenido)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-24">Peso/Cant</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-32">Precio ($)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-32">Subtotal ($)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody" class="bg-white divide-y divide-gray-200">
                            <!-- Las líneas se añaden con JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totales -->
            <div class="flex flex-col md:flex-row justify-between items-start pt-6 border-t mt-6">
                <div class="w-full md:w-1/2 mb-6 md:mb-0 pr-0 md:pr-12">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas / Observaciones</label>
                    <textarea id="nota" name="nota" rows="4" class="w-full border-gray-300 border rounded-lg shadow-sm focus:border-inventory focus:ring focus:ring-inventory p-3 text-sm" placeholder="Opcional: Detalles adicionales de la factura..."></textarea>
                </div>
                
                <div class="w-full md:w-1/3 bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="space-y-3 font-medium">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="resumenSubtotal">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <label>Descuento ($):</label>
                            <input type="number" id="descuento" value="0.00" min="0" step="0.01" class="w-24 border border-gray-300 rounded px-2 py-1 text-right text-sm">
                        </div>
                        <div class="flex justify-between text-gray-600" id="itbmsContainer" style="display:none;">
                            <span>ITBMS (7%):</span>
                            <span id="resumenITBMS">$0.00</span>
                        </div>
                        <div class="flex justify-between items-end border-t border-gray-300 pt-3 text-lg font-bold text-gray-900">
                            <span>Total a Cobrar:</span>
                            <span class="text-2xl text-inventory" id="resumenTotal">$0.00</span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="guardarFactura()" class="mt-6 w-full bg-inventory hover:bg-blue-600 text-white border border-transparent rounded-lg shadow-sm py-3 px-4 inline-flex justify-center items-center text-base font-bold transition">
                        <i class="fas fa-save mr-2"></i> Confirmar Factura
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<template id="lineaTemplate">
    <tr class="linea-detalle">
        <td class="px-4 py-2">
            <input type="text" class="guia-input w-full border border-gray-300 rounded p-1.5 text-sm placeholder-gray-400" placeholder="Ej: Tracking, Descripción de contenido...">
        </td>
        <td class="px-4 py-2">
            <input type="number" class="peso-input w-full border border-gray-300 rounded p-1.5 text-sm text-right" value="1" min="0.1" step="0.01" required>
        </td>
        <td class="px-4 py-2">
            <input type="number" class="precio-input w-full border border-gray-300 rounded p-1.5 text-sm text-right" value="0.00" min="0" step="0.01" required>
        </td>
        <td class="px-4 py-2 text-right font-medium subtotal-row text-sm">
            $0.00
        </td>
        <td class="px-4 py-2 text-center">
            <button type="button" onclick="eliminarLinea(this)" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?php ob_start(); ?>
<script>
    let lineas = 0;

    function agregarLinea() {
        const template = document.getElementById('lineaTemplate');
        const clon = template.content.cloneNode(true);
        const tbody = document.getElementById('detallesBody');
        
        // Attach events
        const tr = clon.querySelector('tr');
        const inPeso = tr.querySelector('.peso-input');
        const inPrecio = tr.querySelector('.precio-input');

        // Aplicar precio del servicio global si está seleccionado
        const selGlobal = document.getElementById('global_id_servicio');
        if(selGlobal.value) {
            const precioOpt = selGlobal.options[selGlobal.selectedIndex].dataset.precio;
            if(precioOpt) inPrecio.value = parseFloat(precioOpt).toFixed(2);
        }

        inPeso.addEventListener('input', calcularTotales);
        inPrecio.addEventListener('input', calcularTotales);

        tbody.appendChild(tr);
        lineas++;
    }

    function eliminarLinea(btn) {
        btn.closest('tr').remove();
        lineas--;
        calcularTotales();
    }

    function calcularTotales() {
        let subtotal = 0;
        const filas = document.querySelectorAll('.linea-detalle');
        
        filas.forEach(fila => {
            const peso = parseFloat(fila.querySelector('.peso-input').value) || 0;
            const precio = parseFloat(fila.querySelector('.precio-input').value) || 0;
            const subtotalLinea = peso * precio;
            
            fila.querySelector('.subtotal-row').innerText = '$' + subtotalLinea.toFixed(2);
            subtotal += subtotalLinea;
        });

        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const aplicaItbms = document.getElementById('aplica_itbms').checked;
        
        let subtotalNeto = subtotal - descuento;
        if(subtotalNeto < 0) subtotalNeto = 0;

        let itbms = 0;
        if(aplicaItbms) {
            itbms = subtotalNeto * 0.07;
            document.getElementById('itbmsContainer').style.display = 'flex';
        } else {
            document.getElementById('itbmsContainer').style.display = 'none';
        }

        const total = subtotalNeto + itbms;

        document.getElementById('resumenSubtotal').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('resumenITBMS').innerText = '$' + itbms.toFixed(2);
        document.getElementById('resumenTotal').innerText = '$' + total.toFixed(2);
    }

    document.getElementById('aplica_itbms').addEventListener('change', calcularTotales);
    document.getElementById('descuento').addEventListener('input', calcularTotales);

    function guardarFactura() {
        if(lineas === 0) {
            Swal.fire('Atención', 'Debe agregar al menos una línea a la factura.', 'warning');
            return;
        }

        const id_cliente = document.getElementById('id_cliente').value;
        if(!id_cliente) {
            Swal.fire('Atención', 'Debe seleccionar un cliente.', 'warning');
            return;
        }

        const global_id_servicio = document.getElementById('global_id_servicio').value;
        if(!global_id_servicio) {
            Swal.fire('Atención', 'Debe seleccionar un servicio.', 'warning');
            return;
        }

        const ITEMS = [];
        document.querySelectorAll('.linea-detalle').forEach(fila => {
            ITEMS.push({
                id_servicio: global_id_servicio,
                guia: fila.querySelector('.guia-input').value,
                peso: fila.querySelector('.peso-input').value,
                precio_unitario: fila.querySelector('.precio-input').value
            });
        });

        Swal.fire({
            title: 'Procesando...',
            text: 'Guardando factura',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        const formData = new URLSearchParams();
        formData.append('id_cliente', id_cliente);
        formData.append('id_servicio', global_id_servicio);
        formData.append('metodo_pago', document.getElementById('metodo_pago').value);
        formData.append('estado', document.getElementById('estado').value);
        formData.append('aplica_itbms', document.getElementById('aplica_itbms').checked ? '1' : '0');
        formData.append('descuento', document.getElementById('descuento').value);
        formData.append('nota', document.getElementById('nota').value);
        formData.append('items', JSON.stringify(ITEMS));

        fetch('<?= $_ENV['APP_URL'] ?? '/' ?>facturas/crear', {
            method: 'POST',
            body: formData,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Factura Creada!',
                    text: 'Documento ' + data.codigo_factura + ' generado.',
                    confirmButtonText: '<i class="fab fa-whatsapp mr-1"></i> WhatsApp',
                    confirmButtonColor: '#25D366',
                    showCancelButton: true,
                    cancelButtonText: 'Volver al listado'
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarWhatsApp(data);
                    } else {
                        window.location.href = '<?= $_ENV['APP_URL'] ?? '/' ?>facturas';
                    }
                });
            } else {
                Swal.fire('Error', data.error || 'No se pudo guardar la factura', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Ocurrió un problema de conexión.', 'error');
        });
    }

    // ===== WHATSAPP =====
    function resolverMensajeWhatsApp(data) {
        const tipo   = data.servicio.whatsapp_tipo    || 'regular';
        const custom = (data.servicio.whatsapp_mensaje || '').trim();
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

    async function enviarWhatsApp(data) {
        const APP_URL = '<?= $_ENV['APP_URL'] ?? '/' ?>';

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

        // Quitar dark mode antes de capturar
        const htmlEl = document.documentElement;
        const wasDark = htmlEl.classList.contains('dark');
        if (wasDark) htmlEl.classList.remove('dark');

        try {
            const canvas = await html2canvas(wrapper, {
                scale: 3,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff',
                windowWidth: wrapper.scrollWidth,
                windowHeight: wrapper.scrollHeight
            });

            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.95));
            const file = new File([blob], `Recibo_${data.codigo_factura}.jpg`, { type: 'image/jpeg' });

            fetch(APP_URL + 'facturas/' + data.factura_id + '/whatsapp', { method: 'POST' });
            document.body.removeChild(wrapper);
            if (wasDark) htmlEl.classList.add('dark');

            const facturaListUrl = '<?= $_ENV['APP_URL'] ?? '/' ?>facturas';
            if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
                try {
                    await navigator.share({ files: [file], text: msgText });
                    await Swal.fire({ icon: 'success', title: 'Compartir', text: 'Se ha abierto WhatsApp.', timer: 2000, showConfirmButton: false });
                } catch (e) {
                    console.error('Error al compartir:', e);
                    await Swal.fire({ icon: 'info', title: 'Cancelado', text: 'No se compartió.', timer: 1500, showConfirmButton: false });
                }
                window.location.href = facturaListUrl;
            } else {
                fetch(APP_URL + 'facturas/' + data.factura_id + '/abrir-app', { method: 'POST' });
                try {
                    await navigator.clipboard.writeText(msgText);
                } catch(e) {}
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `Recibo_${data.codigo_factura}.jpg`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                await Swal.fire({
                    icon: 'success',
                    title: '¡Listo para Enviar!',
                    html: '1. El recibo se ha <b>descargado</b>.<br>2. El texto ha sido <b>copiado</b>.<br><br><b>Instrucción:</b> Arrastra la imagen al chat y presiona <b>Ctrl+V</b> en el comentario.',
                    confirmButtonText: 'Abrir WhatsApp',
                    confirmButtonColor: '#25D366'
                });
                window.location.href = facturaListUrl;
            }
        } catch (err) {
            if (wasDark) htmlEl.classList.add('dark');
            if (document.body.contains(wrapper)) document.body.removeChild(wrapper);
            Swal.fire('Error', 'No se pudo generar la imagen: ' + err.message, 'error');
        }
    }

    // Agregar una línea vacía al cargar
    setTimeout(agregarLinea, 100);

    // Actualizar precios de todas las líneas cuando cambie el servicio global
    document.getElementById('global_id_servicio').addEventListener('change', function() {
        const precio = this.options[this.selectedIndex].dataset.precio;
        if(precio) {
            document.querySelectorAll('.precio-input').forEach(input => {
                input.value = parseFloat(precio).toFixed(2);
            });
            calcularTotales();
        }
    });

    // ===== AUTOCOMPLETE CLIENTE =====
    const searchInput = document.getElementById('clienteSearch');
    const resultsBox = document.getElementById('clienteResults');
    const hiddenId = document.getElementById('id_cliente');
    const selectedBox = document.getElementById('clienteSelected');
    const clearBtn = document.getElementById('clearCliente');
    let searchTimer = null;
    let currentResults = []; // Para guardar resultados y permitir seleccionar con Enter

    // Permitir seleccionar el primer resultado con Enter
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (currentResults.length > 0) {
                seleccionarCliente(currentResults[0]);
            }
        }
    });

    searchInput.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(searchTimer);

        if(q.length < 1) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            clearBtn.classList.add('hidden');
            currentResults = [];
            return;
        }

        clearBtn.classList.remove('hidden');

        searchTimer = setTimeout(() => {
            fetch('<?= $_ENV['APP_URL'] ?? '/' ?>buscar-clientes?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(clientes => {
                resultsBox.innerHTML = '';
                currentResults = clientes; // Guardar lista actual
                
                if(clientes.length === 0) {
                    resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center"><i class="fas fa-exclamation-circle mr-1"></i>No se encontraron clientes</div>';
                } else {
                    clientes.forEach(c => {
                        const item = document.createElement('div');
                        item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors';
                        item.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-sm font-bold text-gray-900">${c.nombre}</span>
                                    <span class="text-xs text-gray-500 ml-1">(${c.email || 'Sin Email'})</span>
                                </div>
                                <span class="text-xs font-mono bg-gray-100 text-inventory px-2 py-0.5 rounded font-bold">${c.codigo}</span>
                            </div>
                            ${c.telefono ? '<div class="text-xs text-gray-400 mt-1"><i class="fas fa-phone mr-1"></i>' + c.telefono + '</div>' : ''}
                        `;
                        item.addEventListener('click', () => seleccionarCliente(c));
                        resultsBox.appendChild(item);
                    });
                }
                resultsBox.classList.remove('hidden');
            })
            .catch(() => {
                resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 text-center">Error de conexión</div>';
                resultsBox.classList.remove('hidden');
            });
        }, 250); // Debounce 250ms
    });

    function seleccionarCliente(c) {
        hiddenId.value = c.id;
        searchInput.value = '';
        searchInput.classList.add('hidden');
        clearBtn.classList.add('hidden');
        resultsBox.classList.add('hidden');
        resultsBox.innerHTML = '';

        document.getElementById('selectedClienteName').textContent = c.nombre;
        document.getElementById('selectedClienteCode').textContent = c.codigo;
        selectedBox.classList.remove('hidden');
    }

    function limpiarCliente() {
        hiddenId.value = '';
        searchInput.value = '';
        searchInput.classList.remove('hidden');
        selectedBox.classList.add('hidden');
        searchInput.focus();
    }

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        resultsBox.classList.add('hidden');
        clearBtn.classList.add('hidden');
        searchInput.focus();
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if(!e.target.closest('#clienteSearch') && !e.target.closest('#clienteResults')) {
            resultsBox.classList.add('hidden');
        }
    });
</script>
<?php $extra_js = ob_get_clean(); ?>
