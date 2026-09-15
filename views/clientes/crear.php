<?php $page_title = 'Nuevo Cliente'; $page_subtitle = 'Registrar un cliente en el sistema'; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-user-plus mr-2 text-inventory"></i>Formulario de Registro</h3>
            <a href="<?= $baseUrl ?>clientes" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Volver al listado
            </a>
        </div>

        <form method="POST" action="<?= $baseUrl ?>clientes/crear" class="p-6 sm:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2">Información Básica</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700 mb-1">Código Casillero <span class="text-red-500">*</span></label>
                            <input type="text" name="codigo" id="codigo" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="grupo" class="block text-sm font-medium text-gray-700 mb-1">Grupo/Categoría</label>
                            <input type="text" name="grupo" id="grupo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2" placeholder="Ej: VIP, Retail...">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="nombre" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                            <input type="text" name="apellido" id="apellido" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                        </div>
                    </div>

                    <div>
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-1">RUC / Cédula / Pasaporte</label>
                        <input type="text" name="ruc" id="ruc" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                    </div>
                </div>

                <!-- Contacto & Ubicación -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2">Contacto y Ubicación</h4>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono Móvil (WhatsApp)</label>
                        <input type="text" name="telefono" id="telefono" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2 placeholder-gray-400" placeholder="+507 " value="+507 ">
                        <p class="text-xs text-gray-500 mt-1">Incluir el código de país para notificaciones WhatsApp.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="pais" class="block text-sm font-medium text-gray-700 mb-1">País</label>
                            <input type="text" name="pais" id="pais" value="Panamá" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2 bg-gray-50">
                        </div>
                        <div>
                            <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Provincia/Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" value="Colón" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2">
                        </div>
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa</label>
                        <textarea name="direccion" id="direccion" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory focus:ring-inventory sm:text-sm border p-2"></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= $baseUrl ?>clientes" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-inventory">
                    Cancelar
                </a>
                <button type="submit" class="bg-inventory hover:bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-inventory transition">
                    Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
