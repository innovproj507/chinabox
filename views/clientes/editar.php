<?php $page_title = 'Editar Cliente'; $page_subtitle = 'Modificar datos de ' . $cliente['codigo']; ?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-user-edit mr-2 text-inventory"></i>Actualizar Cliente</h3>
            <a href="<?= $baseUrl ?>clientes" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Volver al listado
            </a>
        </div>

        <form method="POST" action="<?= $baseUrl ?>clientes/<?= $cliente['id_cliente'] ?>/editar" class="p-6 sm:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2">Información Básica</h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-500 mb-1">Código Casillero</label>
                            <!-- El código NO se edita -->
                            <input type="text" id="codigo" value="<?= $cliente['codigo'] ?>" disabled readonly class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 sm:text-sm border p-2 cursor-not-allowed text-gray-500">
                        </div>
                        <div>
                            <label for="grupo" class="block text-sm font-medium text-gray-700 mb-1">Grupo/Categoría</label>
                            <input type="text" name="grupo" id="grupo" value="<?= htmlspecialchars($cliente['grupo'] ?? '') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($cliente['nombre']) ?>" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                            <input type="text" name="apellido" id="apellido" value="<?= htmlspecialchars($cliente['apellido'] ?? '') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                        </div>
                    </div>

                    <div>
                        <label for="ruc" class="block text-sm font-medium text-gray-700 mb-1">RUC / Cédula / Pasaporte</label>
                        <input type="text" name="ruc" id="ruc" value="<?= htmlspecialchars($cliente['ruc'] ?? '') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">Estado en el Sistema</label>
                        <select name="estado" id="estado" class="w-full border-gray-300 rounded-md border p-2 sm:text-sm focus:border-inventory">
                            <option value="activo" <?= $cliente['estado']=='activo'?'selected':'' ?>>Activo (Puede facturar)</option>
                            <option value="inactivo" <?= $cliente['estado']=='inactivo'?'selected':'' ?>>Inactivo (Bloqueado)</option>
                        </select>
                    </div>
                </div>

                <!-- Contacto & Ubicación -->
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2">Contacto y Ubicación</h4>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono Móvil (WhatsApp)</label>
                        <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="pais" class="block text-sm font-medium text-gray-700 mb-1">País</label>
                            <input type="text" name="pais" id="pais" value="<?= htmlspecialchars($cliente['pais'] ?? 'Panamá') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Provincia/Ciudad</label>
                            <input type="text" name="ciudad" id="ciudad" value="<?= htmlspecialchars($cliente['ciudad'] ?? 'Colón') ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2">
                        </div>
                    </div>

                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección Completa</label>
                        <textarea name="direccion" id="direccion" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-inventory sm:text-sm border p-2"><?= htmlspecialchars($cliente['direccion'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-between items-center">
                <button type="button" onclick="confirmDelete()" class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center">
                    <i class="fas fa-trash-alt mr-2"></i> Eliminar Cliente
                </button>

                <div class="flex space-x-3">
                    <a href="<?= $baseUrl ?>clientes" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-inventory hover:bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white transition">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Formulario oculto para eliminar -->
        <form id="deleteForm" method="POST" action="<?= $baseUrl ?>clientes/<?= $cliente['id_cliente'] ?>/eliminar" class="hidden"></form>
    </div>
</div>

<?php ob_start(); ?>
<script>
    function confirmDelete(){
        Swal.fire({
            title: '¿Eliminar Cliente?',
            text: "No podrás revertir esta acción de borrado.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        })
    }
</script>
<?php $extra_js = ob_get_clean(); ?>
