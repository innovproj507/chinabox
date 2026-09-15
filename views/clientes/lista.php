<?php $page_title = 'Clientes'; $page_subtitle = 'Directorio de clientes y prospectos'; ?>

<div class="px-6 py-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Controles Superiores -->
        <div class="p-6 border-b border-gray-200">
            <form method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 w-full md:w-1/3 relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($search_query) ?>" placeholder="Buscar por nombre, código, email o RUC..."
                        class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-inventory text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-center space-x-2 w-full md:w-auto">
                    <select name="estado" class="border border-gray-300 rounded-lg text-sm px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-inventory">
                        <option value="">Cualquier estado</option>
                        <option value="activo" <?= $estado_filter == 'activo' ? 'selected' : '' ?>>Activo</option>
                        <option value="inactivo" <?= $estado_filter == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    </select>

                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        Filtrar
                    </button>
                    
                    <a href="<?= $baseUrl ?>clientes-exportar?q=<?= urlencode($search_query) ?>&estado=<?= urlencode($estado_filter) ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Clientes
                    </a>

                    <a href="<?= $baseUrl ?>clientes-importar" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                        <i class="fas fa-file-import mr-2"></i>Importar Clientes
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla Clientes -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código/ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">RUC</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(empty($clientes)): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No se encontraron clientes.</td></tr>
                    <?php else: ?>
                        <?php foreach($clientes as $c): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-inventory">
                                <a href="<?= $baseUrl ?>clientes/<?= $c['id_cliente'] ?>" class="hover:underline"><?= $c['codigo'] ?></a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?= $baseUrl ?>clientes/<?= $c['id_cliente'] ?>" class="text-sm font-bold text-gray-900 hover:text-inventory"><?= $c['nombre'] ?> <?= $c['apellido'] ?? '' ?></a>
                                <div class="text-xs text-gray-500"><?= $c['grupo'] ?: 'Sin Grupo' ?></div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <div class="text-sm text-gray-900"><i class="far fa-envelope text-gray-400 mr-1 w-4"></i><?= $c['email'] ?></div>
                                <div class="text-xs text-gray-500"><i class="fas fa-phone text-gray-400 mr-1 w-4"></i><?= $c['telefono'] ?: 'N/A' ?></div>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell text-sm text-gray-500">
                                <?= $c['ruc'] ?: '-' ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $c['estado']=='activo'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' ?>">
                                    <?= ucfirst($c['estado']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="<?= $baseUrl ?>clientes/<?= $c['id_cliente'] ?>" class="text-inventory hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Ver Detalle">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <a href="<?= $baseUrl ?>clientes/<?= $c['id_cliente'] ?>/editar" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors" title="Editar">
                                        <i class="far fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php include __DIR__ . '/../includes/pagination.php'; ?>

    </div>
</div>
