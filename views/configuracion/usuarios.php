<?php $page_title = 'Usuarios del Sistema'; $page_subtitle = 'Gestión de accesos y cuentas'; ?>

<div class="px-6 py-4 max-w-6xl mx-auto">
    
    <!-- Pestañas (Maqueta) -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="<?= $baseUrl ?>configuracion" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-cogs mr-2"></i> Parámetros
            </a>
            <a href="<?= $baseUrl ?>configuracion/usuarios" class="border-inventory text-inventory whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-users-cog mr-2"></i> Usuarios
            </a>
            <a href="<?= $baseUrl ?>configuracion/roles" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-shield-alt mr-2"></i> Accesos / Roles
            </a>
        </nav>
    </div>

    <!-- Contenido Usuarios -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-users text-inventory mr-2"></i>Cuentas del Personal</h3>
            <a href="<?= $baseUrl ?>configuracion/usuarios/crear" class="bg-inventory hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                <i class="fas fa-plus mr-2"></i> Nuevo Usuario
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol / Nivel</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Último Acceso</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(empty($usuarios)): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No se encontraron usuarios.</td></tr>
                    <?php else: ?>
                        <?php foreach($usuarios as $u): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-inventory text-white flex items-center justify-center font-bold text-sm">
                                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900"><?= $u['username'] ?></div>
                                        <div class="text-xs text-gray-500"><?= $u['email'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= $u['first_name'] ?> <?= $u['last_name'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if($u['is_superuser']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-crown mr-1"></i> Admin</span>
                                <?php elseif($u['is_staff']): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Staff</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Básico</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                <?= $u['last_login'] ? date('d/m/y H:i', strtotime($u['last_login'])) : 'Nunca' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full <?= $u['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $u['is_active'] ? 'Activo' : 'Suspendido' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= $baseUrl ?>configuracion/usuarios/<?= $u['id'] ?>/editar" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors">
                                    <i class="far fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
