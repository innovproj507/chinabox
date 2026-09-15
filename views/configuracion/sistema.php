<?php $page_title = 'Configuración del Sistema'; $page_subtitle = 'Parámetros globales y fiscales'; ?>

<div class="px-6 py-4 max-w-5xl mx-auto">
    
    <!-- Pestañas (Maqueta de tabs para Configuración vs Usuarios) -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="<?= $baseUrl ?>configuracion" class="border-inventory text-inventory whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-cogs mr-2"></i> Parámetros de la Empresa
            </a>
            <a href="<?= $baseUrl ?>configuracion/usuarios" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-users-cog mr-2"></i> Gestión de Usuarios
            </a>
            <!-- Roles dummy tab -->
            <a href="<?= $baseUrl ?>configuracion/roles" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-shield-alt mr-2"></i> Permisos y Roles
            </a>
        </nav>
    </div>

    <!-- Contenido Configuración Empresa -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Parámetros Globales</h3>
        </div>

        <form method="POST" action="<?= $baseUrl ?>configuracion" class="p-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Columna Izquierda: Logo y Empresa -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-inventory uppercase tracking-wider border-b pb-2">Datos de la Empresa</h4>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial de la Empresa</label>
                        <input type="text" name="nombre_empresa" value="<?= htmlspecialchars($config['nombre_empresa']) ?>" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory focus:ring-inventory">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Razón Social</label>
                        <input type="text" name="nombre_comercial" value="<?= htmlspecialchars($config['nombre_comercial']) ?>" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory focus:ring-inventory">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">RUC Fiscal</label>
                            <input type="text" name="ruc_empresa" value="<?= htmlspecialchars($config['ruc_empresa'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Principal</label>
                            <input type="text" name="telefono_empresa" value="<?= htmlspecialchars($config['telefono_empresa'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección <span class="text-xs text-gray-400 font-normal">(Visible en facturas)</span></label>
                        <textarea name="direccion_empresa" rows="2" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory"><?= htmlspecialchars($config['direccion_empresa'] ?? '') ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico (Ventas)</label>
                        <input type="email" name="email_empresa" value="<?= htmlspecialchars($config['email_empresa'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                    </div>
                </div>

                <!-- Columna Derecha: Parámetros y Facturación -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-inventory uppercase tracking-wider border-b pb-2">Parámetros de Facturación</h4>
                    
                    <div class="grid grid-cols-2 gap-4 border bg-gray-50 border-gray-200 p-4 rounded-lg">
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Porcentaje Impuesto (%)</label>
                            <input type="number" name="itbms_porcentaje" value="<?= $config['itbms_porcentaje'] ?>" step="0.01" min="0" required class="w-full rounded-md border-gray-300 border p-2 text-sm font-bold text-inventory font-mono focus:border-inventory">
                            <p class="text-xs text-gray-500 mt-1">Suele ser 7.00 para Panamá.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Moneda Base</label>
                            <input type="text" name="moneda_default" value="<?= htmlspecialchars($config['moneda_default']) ?>" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                            <p class="text-xs text-gray-500 mt-1">Símbolo (Ej: USD, PAB)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prefijo de Factura</label>
                            <input type="text" name="prefijo_factura" value="<?= htmlspecialchars($config['prefijo_factura'] ?? 'FAC-') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Términos de Pago</label>
                            <select name="terminos_pago_default" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                                <option value="Contado" <?= ($config['terminos_pago_default']??'Contado')=='Contado'?'selected':'' ?>>Al Contado</option>
                                <option value="Crédito 15 días" <?= ($config['terminos_pago_default']??'')=='Crédito 15 días'?'selected':'' ?>>Crédito 15 días</option>
                                <option value="Crédito 30 días" <?= ($config['terminos_pago_default']??'')=='Crédito 30 días'?'selected':'' ?>>Crédito 30 días</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="text" name="sitio_web" value="<?= htmlspecialchars($config['sitio_web'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory" placeholder="www.midominio.com">
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-800 mb-1">Ruta de WhatsApp Desktop (Local)</label>
                        <input type="text" name="whatsapp_path" value="<?= htmlspecialchars($config['whatsapp_path'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory" placeholder="C:\Users\Usuario\AppData\Local\WhatsApp\WhatsApp.exe">
                        <p class="text-xs text-gray-500 mt-1">Si se configura, el servidor intentará abrir esta aplicación directamente para evitar pestañas del navegador.</p>
                    </div>
                </div>
            </div>

            <!-- Textos Pie de Página -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h4 class="text-sm font-bold text-inventory uppercase tracking-wider border-b pb-2 mb-4">Textos Predefinidos (Impresión)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nota Predeterminada (En facturas nuevas)</label>
                        <textarea name="nota_factura" rows="3" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory"><?= htmlspecialchars($config['nota_factura'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensaje Pie de Página de Factura</label>
                        <textarea name="pie_pagina_factura" rows="3" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory"><?= htmlspecialchars($config['pie_pagina_factura'] ?? '') ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Aparece en la parte inferior del recibo PDF/Impreso.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-inventory hover:bg-blue-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition flex items-center">
                    <i class="fas fa-save mr-2"></i> Guardar Configuración General
                </button>
            </div>
        </form>
    </div>
</div>
