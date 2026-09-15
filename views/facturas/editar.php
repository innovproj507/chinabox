<?php $page_title = 'Editar Factura ' . $factura['codigo_factura']; $page_subtitle = 'Modificar detalles de la factura'; ?>

<div class="px-6 py-4">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form id="facturaForm" class="p-6">
            
            <!-- Cabecera Factura -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-user-circle text-inventory mr-2"></i>Datos del Cliente</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar y Seleccionar Cliente</label>
                            <select id="id_cliente" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm p-2 bg-gray-50 border">
                                <?php foreach($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>" <?= $c['id_cliente'] == $factura['id_cliente'] ? 'selected' : '' ?>>
                                    <?= $c['codigo'] ?> - <?= $c['nombre'] ?> <?= $c['apellido'] ?? '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Selección de Servicio Global -->
                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1"><i class="fas fa-concierge-bell text-inventory mr-1"></i>Servicio para esta Factura</label>
                            <select id="global_id_servicio" name="id_servicio" class="w-full border border-gray-300 rounded-lg p-2.5 focus:border-inventory focus:ring focus:ring-inventory focus:ring-opacity-50 text-sm bg-white" required>
                                <option value="">-- Seleccionar Servicio --</option>
                                <?php foreach($servicios as $s): ?>
                                <option value="<?= $s['id_servicio'] ?>" data-precio="<?= $s['precio'] ?>" <?= $s['id_servicio'] == $factura['id_servicio'] ? 'selected' : '' ?>>
                                    <?= $s['codigo'] ?> - <?= $s['nombre'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">El servicio seleccionado se aplicará a todos los ítems de esta factura.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select id="estado" class="w-full border-gray-300 rounded-lg p-2 border sm:text-sm">
                                <option value="Pendiente" <?= $factura['estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                <option value="Pagada" <?= $factura['estado'] == 'Pagada' ? 'selected' : '' ?>>Pagada</option>
                                <option value="Anulada" <?= $factura['estado'] == 'Anulada' ? 'selected' : '' ?>>Anulada</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-file-invoice text-inventory mr-2"></i>Datos de Factura</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nº Factura</label>
                            <input type="text" value="<?= $factura['codigo_factura'] ?>" readonly disabled class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm sm:text-sm p-2 border text-gray-500 font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Emisión</label>
                            <input type="text" value="<?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?>" readonly disabled class="w-full bg-gray-100 border-gray-300 rounded-lg shadow-sm sm:text-sm p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                            <select id="metodo_pago" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                <option value="Efectivo" <?= $factura['metodo_pago']=='Efectivo'?'selected':'' ?>>Efectivo</option>
                                <option value="Tarjeta" <?= $factura['metodo_pago']=='Tarjeta'?'selected':'' ?>>Tarjeta</option>
                                <option value="Transferencia" <?= $factura['metodo_pago']=='Transferencia'?'selected':'' ?>>Transferencia</option>
                                <option value="Yappy" <?= $factura['metodo_pago']=='Yappy'?'selected':'' ?>>Yappy</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <!-- Si hay impuestos > 0, asumimos itbms estuvo checkeado y 7% (aprox). Esto se controlará via JS -->
                            <input id="aplica_itbms" type="checkbox" <?= $factura['impuestos'] > 0 ? 'checked' : '' ?> class="h-4 w-4 text-inventory focus:ring-inventory border-gray-300 rounded">
                            <label for="aplica_itbms" class="ml-2 block text-sm text-gray-900 font-medium">Aplicar 7% ITBMS</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles/Servicios -->
            <div class="mb-8">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-boxes text-inventory mr-2"></i>Servicios / Contenido</h3>
                    <button type="button" onclick="agregarLinea()" class="bg-success hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Agregar
                    </button>
                </div>
                
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nº Guía / Contenido</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-24">Peso</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-32">Precio ($)</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase w-32">Subtotal ($)</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody" class="bg-white divide-y divide-gray-200">
                            <!-- Llenado via JS con los $detalles_json -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totales -->
            <div class="flex flex-col md:flex-row justify-between items-start pt-6 border-t mt-6">
                <div class="w-full md:w-1/2 mb-6 md:mb-0 pr-0 md:pr-12">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas / Observaciones</label>
                    <textarea id="observaciones" rows="4" class="w-full border-gray-300 border rounded-lg shadow-sm focus:border-inventory focus:ring p-3 text-sm"><?= htmlspecialchars($factura['observaciones']) ?></textarea>
                </div>
                
                <div class="w-full md:w-1/3 bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="space-y-3 font-medium">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="resumenSubtotal">$<?= number_format($factura['subtotal'], 2) ?></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <label>Descuento ($):</label>
                            <input type="number" id="descuento" value="<?= $factura['descuento'] ?>" min="0" step="0.01" class="w-24 border border-gray-300 rounded px-2 py-1 text-right text-sm">
                        </div>
                        <div class="flex justify-between text-gray-600" id="itbmsContainer">
                            <span>ITBMS (7%):</span>
                            <span id="resumenITBMS">$<?= number_format($factura['impuestos'], 2) ?></span>
                        </div>
                        <div class="flex justify-between items-end border-t border-gray-300 pt-3 text-lg font-bold text-gray-900">
                            <span>Total General:</span>
                            <span class="text-2xl text-inventory" id="resumenTotal">$<?= number_format($factura['total'], 2) ?></span>
                        </div>
                    </div>
                    
                    <button type="button" onclick="guardarFactura()" class="mt-6 w-full bg-inventory hover:bg-blue-600 text-white border border-transparent rounded-lg shadow-sm py-3 px-4 inline-flex justify-center items-center text-base font-bold transition">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                    <a href="<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>" class="mt-2 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg shadow-sm py-3 px-4 inline-flex justify-center items-center text-sm font-medium transition text-center">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template oculto -->
<template id="lineaTemplate">
    <tr class="linea-detalle">
        <td class="px-4 py-2"><input type="text" class="guia-input w-full border-gray-300 border rounded p-1.5 text-sm" placeholder="Ej: Tracking, Descripción..."></td>
        <td class="px-4 py-2"><input type="number" class="peso-input w-full border-gray-300 border rounded p-1.5 text-sm text-right" min="0.1" step="0.01"></td>
        <td class="px-4 py-2"><input type="number" class="precio-input w-full border-gray-300 border rounded p-1.5 text-sm text-right" min="0" step="0.01"></td>
        <td class="px-4 py-2 text-right font-medium subtotal-row text-sm">$0.00</td>
        <td class="px-4 py-2 text-center">
            <button type="button" onclick="eliminarLinea(this)" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded transition"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<?php ob_start(); ?>
<script>
    const iniciales = <?= $detalles_json ?>;

    function agregarLinea(detalle = null) {
        const template = document.getElementById('lineaTemplate');
        const clon = template.content.cloneNode(true);
        const tbody = document.getElementById('detallesBody');
        
        const tr = clon.querySelector('tr');
        const inGuia = tr.querySelector('.guia-input');
        const inPeso = tr.querySelector('.peso-input');
        const inPrecio = tr.querySelector('.precio-input');

        if(detalle) {
            inGuia.value = detalle.guia;
            inPeso.value = detalle.peso;
            inPrecio.value = detalle.precio_unitario;
            tr.querySelector('.subtotal-row').innerText = '$' + detalle.subtotal.toFixed(2);
        } else {
            inPeso.value = 1;
            // Aplicar precio del servicio global si está seleccionado
            const selGlobal = document.getElementById('global_id_servicio');
            if(selGlobal.value) {
                const precioOpt = selGlobal.options[selGlobal.selectedIndex].dataset.precio;
                if(precioOpt) inPrecio.value = parseFloat(precioOpt).toFixed(2);
            } else {
                inPrecio.value = 0;
            }
        }

        inPeso.addEventListener('input', calcularTotales);
        inPrecio.addEventListener('input', calcularTotales);

        tbody.appendChild(tr);
    }

    function eliminarLinea(btn) {
        btn.closest('tr').remove();
        calcularTotales();
    }

    function calcularTotales() {
        let subtotal = 0;
        document.querySelectorAll('.linea-detalle').forEach(fila => {
            const peso = parseFloat(fila.querySelector('.peso-input').value) || 0;
            const precio = parseFloat(fila.querySelector('.precio-input').value) || 0;
            const st = peso * precio;
            fila.querySelector('.subtotal-row').innerText = '$' + st.toFixed(2);
            subtotal += st;
        });

        const descuento = parseFloat(document.getElementById('descuento').value) || 0;
        const aplicaItbms = document.getElementById('aplica_itbms').checked;
        
        let subNeto = subtotal - descuento;
        if(subNeto < 0) subNeto = 0;

        let itbms = 0;
        if(aplicaItbms) {
            itbms = subNeto * 0.07;
            document.getElementById('itbmsContainer').style.display = 'flex';
        } else {
            document.getElementById('itbmsContainer').style.display = 'none';
        }

        document.getElementById('resumenSubtotal').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('resumenITBMS').innerText = '$' + itbms.toFixed(2);
        document.getElementById('resumenTotal').innerText = '$' + (subNeto + itbms).toFixed(2);
    }

    document.getElementById('aplica_itbms').addEventListener('change', calcularTotales);
    document.getElementById('descuento').addEventListener('input', calcularTotales);

    // Inicializar lineas
    iniciales.forEach(d => agregarLinea(d));
    if(iniciales.length === 0) agregarLinea();
    calcularTotales();

    function guardarFactura() {
        const ITEMS = [];
        let valid = true;
        let finalSubtotal = 0;
        const global_id_servicio = document.getElementById('global_id_servicio').value;
        if(!global_id_servicio) return Swal.fire('Error', 'Debe seleccionar un servicio para la factura', 'error');

        document.querySelectorAll('.linea-detalle').forEach(fila => {
            let sublinea = (parseFloat(fila.querySelector('.peso-input').value) || 0) * (parseFloat(fila.querySelector('.precio-input').value) || 0);
            finalSubtotal += sublinea;
            ITEMS.push({
                id_servicio: global_id_servicio,
                guia: fila.querySelector('.guia-input').value,
                peso: fila.querySelector('.peso-input').value,
                precio_unitario: fila.querySelector('.precio-input').value,
                subtotal: sublinea
            });
        });

        const desc = parseFloat(document.getElementById('descuento').value)||0;
        let neto = finalSubtotal - desc;
        let impuestos = document.getElementById('aplica_itbms').checked ? neto * 0.07 : 0;
        let totalFinal = neto + impuestos;

        const formData = new URLSearchParams();
        formData.append('id_cliente', document.getElementById('id_cliente').value);
        formData.append('id_servicio', global_id_servicio);
        formData.append('estado', document.getElementById('estado').value);
        formData.append('metodo_pago', document.getElementById('metodo_pago').value);
        formData.append('observaciones', document.getElementById('observaciones').value);
        formData.append('descuento', desc);
        formData.append('subtotal', finalSubtotal);
        formData.append('impuestos', impuestos);
        formData.append('total', totalFinal);
        formData.append('items', JSON.stringify(ITEMS));

        fetch('<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>/editar', {
            method: 'POST',
            body: formData,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
        .then(() => {
            window.location.href = '<?= $baseUrl ?>facturas/<?= $factura['id_factura'] ?>';
        });
    }

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
</script>
<?php $extra_js = ob_get_clean(); ?>
