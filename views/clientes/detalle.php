<?php $page_title = 'Perfil del Cliente'; ?>

<div class="px-6 py-4">
    <!-- Action Bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-wrap justify-between items-center border border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-inventory bg-opacity-20 flex items-center justify-center text-inventory font-bold text-xl">
                <?= strtoupper(substr($cliente['nombre'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></h2>
                <div class="text-sm font-mono text-gray-500">ID/CÓDIGO: <?= $cliente['codigo'] ?></div>
            </div>
        </div>
        
        <div class="flex items-center space-x-2 mt-4 sm:mt-0">
            <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $cliente['estado']=='activo'?'bg-green-100 text-green-800':'bg-red-100 text-red-800' ?> mr-3">
                <?= ucfirst($cliente['estado']) ?>
            </span>
            <a href="<?= $baseUrl ?>clientes/<?= $cliente['id_cliente'] ?>/editar" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
            <a href="<?= $baseUrl ?>facturas/crear?cliente=<?= $cliente['id_cliente'] ?>" class="bg-inventory text-white hover:bg-blue-600 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-file-invoice mr-2"></i>Nueva Factura
            </a>
            <a href="<?= $baseUrl ?>clientes/<?= $cliente['id_cliente'] ?>/exportar-facturas" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-file-excel mr-2"></i>Exportar Facturas
            </a>
        </div>
    </div>

    <!-- Cards Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Facturas</p>
                <div class="text-2xl font-bold text-gray-900"><?= $total_facturas ?></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="p-3 rounded-lg bg-yellow-50 text-yellow-600">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Facturas Pendientes</p>
                <div class="text-2xl font-bold text-gray-900"><?= $facturas_pendientes ?></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center">
            <div class="p-3 rounded-lg bg-green-50 text-green-600">
                <i class="fas fa-hand-holding-usd text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500">Total Comprado</p>
                <div class="text-2xl font-bold text-gray-900">$<?= number_format($total_facturado, 2) ?></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 flex flex-col h-full">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider"><i class="fas fa-address-card mr-2 text-inventory"></i>Detalle de Contacto</h3>
                </div>
                <div class="p-6 flex-1 text-sm">
                    <div class="space-y-4 text-gray-600">
                        <div>
                            <span class="block text-xs uppercase text-gray-400 font-bold">RUC / ID</span>
                            <span class="font-medium text-gray-900"><?= $cliente['ruc'] ?: 'No registrado' ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-gray-400 font-bold">Grupo/Categoría</span>
                            <span class="font-medium text-gray-900"><?= $cliente['grupo'] ?: 'Ninguno' ?></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-gray-400 font-bold">Email</span>
                            <span class="font-medium text-gray-900"><a href="mailto:<?= $cliente['email'] ?>" class="text-inventory hover:underline"><?= $cliente['email'] ?></a></span>
                        </div>
                        <div>
                            <span class="block text-xs uppercase text-gray-400 font-bold">Teléfono/WhatsApp</span>
                            <span class="font-medium text-gray-900"><?= $cliente['telefono'] ?: 'No registrado' ?></span>
                        </div>
                        <div class="pt-4 border-t border-gray-100">
                            <span class="block text-xs uppercase text-gray-400 font-bold mb-1">Dirección Registrada</span>
                            <span class="block"><?= $cliente['direccion'] ?: 'Sin dirección' ?></span>
                            <span class="block mt-1"><?= $cliente['ciudad'] ?>, <?= $cliente['pais'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial Facturas -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-full">
                <div class="bg-white px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider"><i class="fas fa-history mr-2 text-inventory"></i>Últimas Facturas</h3>
                    <a href="<?= $baseUrl ?>facturas?q=<?= urlencode($cliente['codigo']) ?>" class="text-xs text-inventory hover:text-blue-700 font-medium tracking-wide">VER TODAS <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Factura</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Emisión</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if(empty($facturas)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-sm text-gray-500">Este cliente aún no tiene facturas registradas.</td></tr>
                            <?php else: ?>
                                <?php foreach($facturas as $f): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-inventory"><?= $f['codigo_factura'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?= date('d/m/y', strtotime($f['fecha_emision'])) ?></td>
                                    <td class="px-6 py-4">
                                        <?php 
                                            $estadoCls = match($f['estado']) {
                                                'Pagada' => 'bg-green-100 text-green-800',
                                                'Pendiente' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-red-100 text-red-800'
                                            };
                                        ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-4 font-semibold rounded-full <?= $estadoCls ?>">
                                            <?= $f['estado'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">$<?= number_format($f['total'], 2) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>" class="text-gray-400 hover:text-inventory transition">
                                            <i class="far fa-eye"></i>
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
    </div>
</div>
