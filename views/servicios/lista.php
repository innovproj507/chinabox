<?php $page_title = 'Servicios'; $page_subtitle = 'Gestión de servicios y tarifas'; ?>

<div class="px-6 py-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <!-- Control Superior -->
        <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-boxes text-inventory mr-2"></i>Catálogo de Servicios</h3>
            <a href="<?= $baseUrl ?>servicios/crear" class="bg-inventory hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200 flex items-center shadow-sm">
                <i class="fas fa-plus mr-2"></i> Nuevo Servicio
            </a>
        </div>

        <!-- Tabla Servicios -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre del Servicio</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tarifa/Precio</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if(empty($servicios)): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No hay servicios registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach($servicios as $s): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-500">
                                <?= $s['codigo'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900"><?= $s['nombre'] ?></div>
                                <div class="text-xs text-gray-400 mt-1"><?= mb_strimwidth($s['descripcion'], 0, 50, '...') ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-inventory">
                                $<?= number_format($s['precio'], 2) ?> <span class="text-gray-400 text-xs font-normal">/ <?= $s['unidad_medida'] ?: 'u' ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full <?= $s['estado']=='activo'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' ?>">
                                    <?= ucfirst($s['estado']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="<?= $baseUrl ?>servicios/<?= $s['id_servicio'] ?>" class="text-gray-500 hover:text-gray-900 bg-gray-100 p-2 rounded-lg transition-colors" title="Ver Detalles">
                                        <i class="far fa-chart-bar"></i>
                                    </a>
                                    <a href="<?= $baseUrl ?>servicios/<?= $s['id_servicio'] ?>/editar" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-colors" title="Editar">
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
