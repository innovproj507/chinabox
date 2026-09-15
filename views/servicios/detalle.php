<?php $page_title = 'Detalle del Servicio'; ?>

<div class="px-6 py-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Tarjeta info principal -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-inventory bg-opacity-10 py-8 px-6 text-center border-b border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-inventory border-opacity-20 text-inventory text-2xl">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1"><?= $servicio['nombre'] ?></h2>
                    <p class="text-sm font-mono text-gray-500 mb-3"><?= $servicio['codigo'] ?></p>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                        <?= ucfirst($servicio['estado']) ?>
                    </span>
                </div>
                <div class="p-6">
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500 font-medium">Tarifa Base</span>
                            <span class="font-bold text-gray-900">$<?= number_format($servicio['precio'], 2) ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-gray-500 font-medium">U. Medida</span>
                            <span class="text-gray-900"><?= $servicio['unidad_medida'] ?: 'Unidad' ?></span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium block mb-1">Descripción</span>
                            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100"><?= $servicio['descripcion'] ?: 'Sin descripción adicional.' ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200 flex space-x-3">
                        <a href="<?= $baseUrl ?>servicios/<?= $servicio['id_servicio'] ?>/editar" class="flex-1 bg-inventory hover:bg-blue-600 text-white text-center py-2 rounded-lg text-sm font-medium transition">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                        <a href="<?= $baseUrl ?>servicios" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-2 px-4 rounded-lg text-sm font-medium transition">
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas de uso -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Rendimiento e Historial</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <p class="text-sm font-medium text-gray-500 mb-1">Veces Facturado</p>
                        <h4 class="text-2xl font-bold text-blue-700"><?= $total_veces_usado ?></h4>
                        <p class="text-xs text-blue-500 mt-2">Ventas con este item</p>
                    </div>
                    
                    <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                        <p class="text-sm font-medium text-gray-500 mb-1">Ingresos Generados</p>
                        <h4 class="text-2xl font-bold text-green-700">$<?= number_format($total_ingresos, 2) ?></h4>
                        <p class="text-xs text-green-500 mt-2">Ganancia bruta aproximada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
