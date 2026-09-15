<?php $page_title = isset($usuario) ? 'Editar Usuario' : 'Nuevo Usuario'; ?>

<div class="px-6 py-4 max-w-3xl mx-auto">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-user-shield mr-2 text-inventory"></i><?= isset($usuario) ? 'Actualizar Cuenta' : 'Crear Cuenta' ?></h3>
            <a href="<?= $baseUrl ?>configuracion/usuarios" class="text-sm text-gray-500 hover:text-gray-700 font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Volver a usuarios
            </a>
        </div>

        <form method="POST" class="p-6">
            <div class="space-y-6">
                
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2">Datos de Acceso</h4>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de Usuario (Login) <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="<?= htmlspecialchars($usuario['username'] ?? '') ?>" <?= isset($usuario) ? 'readonly class="w-full rounded-md border-gray-300 border p-2 text-sm bg-gray-100 text-gray-500"' : 'required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory"' ?>>
                        <?php if(isset($usuario)): ?>
                            <p class="text-xs text-gray-500 mt-1">El username no puede cambiarse.</p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña <?= isset($usuario) ? '(Opcional)' : '<span class="text-red-500">*</span>' ?></label>
                        <input type="password" name="password" <?= !isset($usuario) ? 'required' : '' ?> class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory" placeholder="<?= isset($usuario) ? 'Déjela en blanco para no cambiarla' : 'Nueva contraseña' ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                        <input type="password" name="password_confirm" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory" placeholder="Repita la contraseña">
                    </div>
                </div>

                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mt-8">Datos Personales</h4>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($usuario['first_name'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($usuario['last_name'] ?? '') ?>" class="w-full rounded-md border-gray-300 border p-2 text-sm focus:border-inventory">
                    </div>
                </div>

                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mt-8">Permisos del Sistema</h4>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" id="is_active" <?= (isset($usuario) && $usuario['is_active']) || !isset($usuario) ? 'checked' : '' ?> class="focus:ring-inventory h-4 w-4 text-inventory border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-bold text-gray-700">Activo</label>
                                <p class="text-gray-500 text-xs">Indica si el usuario puede iniciar sesión en la aplicación. Desmarca esto para "suspender" la cuenta.</p>
                            </div>
                        </div>

                        <div class="flex items-start border-t border-gray-200 pt-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_staff" id="is_staff" <?= isset($usuario) && $usuario['is_staff'] ? 'checked' : '' ?> class="focus:ring-inventory h-4 w-4 text-inventory border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_staff" class="font-bold text-gray-700">Staff (Personal Interno)</label>
                                <p class="text-gray-500 text-xs">Identifica a los empleados de la empresa frente a clientes/API externas de existir.</p>
                            </div>
                        </div>

                        <div class="flex items-start border-t border-gray-200 pt-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_superuser" id="is_superuser" <?= isset($usuario) && $usuario['is_superuser'] ? 'checked' : '' ?> class="focus:ring-inventory h-4 w-4 text-inventory border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_superuser" class="font-bold text-purple-700">Superusuario (Admin Total)</label>
                                <p class="text-gray-500 text-xs text-red-600 font-medium">ADVERTENCIA: Obtiene todos los permisos sin asignárselos explícitamente. Riesgo alto.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                <a href="<?= $baseUrl ?>configuracion/usuarios" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="bg-inventory hover:bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white transition">
                    <?= isset($usuario) ? 'Guardar Cambios' : 'Crear Usuario' ?>
                </button>
            </div>
        </form>
    </div>
</div>
