<?php $page_title = 'Nuevo Servicio'; $page_subtitle = 'Agregar un concepto de facturación'; ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-cube mr-2 text-inventory"></i>Formulario de Servicio</h3>
            <a href="<?= $baseUrl ?>servicios" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <form method="POST" action="<?= $baseUrl ?>servicios/crear" class="p-6 sm:p-8">
            <div class="space-y-6">
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código Rápido <span class="text-red-500">*</span></label>
                        <input type="text" name="codigo" id="codigo" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2" placeholder="Ej: FLETE01">
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="estado" id="estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                            <option value="activo">Activo (Visible en facturas)</option>
                            <option value="inactivo">Inactivo (Oculto)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" id="nombre" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2" placeholder="Ej: Flete Aéreo por Libra">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">Tarifa Base / Precio ($) <span class="text-red-500">*</span></label>
                        <input type="number" name="precio" id="precio" required min="0" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2 text-right" placeholder="0.00">
                    </div>
                    <div>
                        <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">Unidad de Medida</label>
                        <input type="text" name="unidad_medida" id="unidad_medida" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2" placeholder="Ej: Lb, Kg, Unidad, Envío">
                    </div>
                </div>

                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción / Notas</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2" placeholder="Detalles usados para uso interno o impresos en la factura..."></textarea>
                </div>

                <!-- WhatsApp -->
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h4 class="text-sm font-semibold text-green-800 mb-3"><i class="fab fa-whatsapp mr-2"></i>Configuración de Mensaje WhatsApp</h4>
                    <div class="space-y-4">
                        <div>
                            <label for="whatsapp_tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Mensaje</label>
                            <select name="whatsapp_tipo" id="whatsapp_tipo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                                <option value="regular">Regular — paquete disponible en oficina</option>
                                <option value="maritimo">Marítimo — carga desde China disponible</option>
                                <option value="solo_foto">Solo Foto — enviar imagen sin texto</option>
                            </select>
                        </div>
                        <div id="mensajeContainer">
                            <label for="whatsapp_mensaje" class="block text-sm font-medium text-gray-700 mb-1">
                                Mensaje Personalizado <span class="text-xs text-gray-400">(opcional, reemplaza el mensaje por defecto)</span>
                            </label>
                            <textarea name="whatsapp_mensaje" id="whatsapp_mensaje" rows="5"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2 font-mono text-xs"
                                placeholder="Dejar vacío para usar el mensaje por defecto según el tipo..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Variables disponibles: <code class="bg-gray-100 px-1 rounded">{nombre}</code> <code class="bg-gray-100 px-1 rounded">{recibo}</code> <code class="bg-gray-100 px-1 rounded">{total}</code></p>
                        </div>
                    </div>
                </div>
            </div>

<?php ob_start(); ?>
<script>
    document.getElementById('whatsapp_tipo').addEventListener('change', function() {
        document.getElementById('mensajeContainer').style.display = this.value === 'solo_foto' ? 'none' : '';
    });
</script>
<?php $extra_js = ob_get_clean(); ?>

            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= $baseUrl ?>servicios" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                    Cancelar
                </a>
                <button type="submit" class="bg-inventory hover:bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white transition shadow-md">
                    Guardar Servicio
                </button>
            </div>
        </form>
    </div>
</div>
