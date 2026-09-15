<?php $page_title = 'Dashboard'; $page_subtitle = 'Resumen general del sistema'; ?>

<?php
    $mesMesActual  = date('F Y');
    $meses = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril',
              'May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto',
              'September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
    $mesLabel = $meses[date('F')] . ' ' . date('Y');
    $mesAntLabel = $meses[date('F', strtotime('first day of last month'))] . ' ' . date('Y', strtotime('first day of last month'));
?>

<div class="px-6 py-4 space-y-6">

    <!-- ── FILA 1: KPIs ── -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">

        <!-- Hoy: monto -->
        <div class="bg-white rounded-xl border-l-4 border-blue-500 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Facturado Hoy</p>
            <p class="text-xl font-bold text-gray-900 mt-1">$<?= number_format($ventas_hoy['total'], 2) ?></p>
            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-file-invoice mr-1"></i><?= $ventas_hoy['cantidad'] ?> factura(s)</p>
        </div>

        <!-- Mes actual: monto -->
        <div class="bg-white rounded-xl border-l-4 border-indigo-500 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide"><?= $mesLabel ?></p>
            <p class="text-xl font-bold text-gray-900 mt-1">$<?= number_format($ventas_mes['total'], 2) ?></p>
            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-file-invoice mr-1"></i><?= $ventas_mes['cantidad'] ?> factura(s)</p>
        </div>

        <!-- Variación vs mes anterior -->
        <div class="bg-white rounded-xl border-l-4 <?= $variacion_pct === null ? 'border-gray-300' : ($variacion_pct >= 0 ? 'border-green-500' : 'border-red-400') ?> shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">vs <?= $mesAntLabel ?></p>
            <?php if ($variacion_pct === null): ?>
                <p class="text-xl font-bold text-gray-400 mt-1">—</p>
                <p class="text-xs text-gray-400 mt-1">Sin datos anteriores</p>
            <?php else: ?>
                <p class="text-xl font-bold mt-1 <?= $variacion_pct >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                    <?= $variacion_pct >= 0 ? '+' : '' ?><?= $variacion_pct ?>%
                </p>
                <p class="text-xs text-gray-400 mt-1">$<?= number_format($ventas_mes_anterior['total'], 2) ?> mes ant.</p>
            <?php endif; ?>
        </div>

        <!-- Pendientes -->
        <div class="bg-white rounded-xl border-l-4 border-yellow-400 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Pendientes</p>
            <p class="text-xl font-bold text-gray-900 mt-1"><?= $facturas_pendientes ?></p>
            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-clock mr-1"></i>Por cobrar</p>
        </div>

        <!-- Clientes activos -->
        <div class="bg-white rounded-xl border-l-4 border-green-500 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Clientes Activos</p>
            <p class="text-xl font-bold text-gray-900 mt-1"><?= $total_clientes ?></p>
            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-users mr-1"></i>Registrados</p>
        </div>

        <!-- Total histórico -->
        <div class="bg-white rounded-xl border-l-4 border-purple-500 shadow-sm p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Histórico</p>
            <p class="text-xl font-bold text-gray-900 mt-1">$<?= $ingresos_totales ?></p>
            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-file-invoice mr-1"></i><?= $total_facturas ?> facturas</p>
        </div>
    </div>

    <!-- ── FILA 2: Últimos 7 días (barras simples) ── -->
    <?php if (!empty($ultimos_7dias)): ?>
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4"><i class="fas fa-chart-bar mr-2 text-indigo-400"></i>Facturación — Últimos 7 días</h3>
        <?php
            $maxTotal = max(array_column($ultimos_7dias, 'total')) ?: 1;
            // Completar los 7 días aunque no haya facturas
            $diasMap = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $diasMap[$d] = ['dia' => $d, 'cantidad' => 0, 'total' => 0];
            }
            foreach ($ultimos_7dias as $row) { $diasMap[$row['dia']] = $row; }
        ?>
        <div class="flex items-end justify-between gap-2 h-24">
            <?php foreach ($diasMap as $d => $row): ?>
            <?php $pct = $maxTotal > 0 ? max(4, round(($row['total'] / $maxTotal) * 100)) : 4; ?>
            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                <div class="absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-0.5 hidden group-hover:block whitespace-nowrap z-10">
                    $<?= number_format($row['total'], 2) ?> (<?= $row['cantidad'] ?>)
                </div>
                <div class="w-full rounded-t <?= $row['total'] > 0 ? 'bg-indigo-400 hover:bg-indigo-500' : 'bg-gray-100' ?> transition-colors"
                     style="height: <?= $pct ?>%"></div>
                <span class="text-[10px] text-gray-400"><?= date('d/m', strtotime($d)) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── FILA 3: Top Clientes + Servicios del mes ── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Top 5 Clientes -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide"><i class="fas fa-trophy mr-2 text-yellow-400"></i>Top Clientes (histórico)</h3>
                <a href="<?= $baseUrl ?>clientes" class="text-xs text-inventory hover:underline">Ver todos <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($top_clientes)): ?>
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Sin datos aún.</p>
                <?php else: ?>
                <?php foreach ($top_clientes as $i => $c): ?>
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                            <?= $i === 0 ? 'bg-yellow-100 text-yellow-700' : ($i === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-50 text-orange-500') ?>">
                            <?= $i + 1 ?>
                        </span>
                        <div>
                            <a href="<?= $baseUrl ?>clientes/<?= $c['id_cliente'] ?>" class="text-sm font-semibold text-gray-900 hover:text-inventory">
                                <?= $c['nombre'] ?> <?= $c['apellido'] ?? '' ?>
                            </a>
                            <p class="text-xs text-gray-400 font-mono"><?= $c['codigo'] ?> · <?= $c['total_facturas'] ?> facturas</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-900">$<?= number_format($c['total_comprado'], 2) ?></span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ventas por servicio (mes actual) -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide"><i class="fas fa-concierge-bell mr-2 text-green-400"></i>Por Servicio — <?= $mesLabel ?></h3>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($por_servicio)): ?>
                <p class="px-5 py-6 text-sm text-gray-400 text-center">Sin facturas este mes.</p>
                <?php else: ?>
                <?php
                    $maxServ = max(array_column($por_servicio, 'total')) ?: 1;
                    $colors  = ['bg-blue-400','bg-green-400','bg-purple-400','bg-orange-400','bg-pink-400'];
                ?>
                <?php foreach ($por_servicio as $idx => $s): ?>
                <div class="px-5 py-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="font-medium text-gray-700 truncate max-w-[130px]" title="<?= $s['nombre'] ?>">
                            <span class="font-mono text-gray-400 mr-1"><?= $s['codigo'] ?></span><?= $s['nombre'] ?>
                        </span>
                        <span class="font-bold text-gray-900">$<?= number_format($s['total'], 2) ?></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full <?= $colors[$idx % count($colors)] ?>"
                             style="width: <?= round(($s['total'] / $maxServ) * 100) ?>%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5"><?= $s['cantidad'] ?> factura(s)</p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── FILA 4: Facturas recientes ── -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide"><i class="fas fa-history mr-2 text-gray-400"></i>Facturas Recientes</h3>
            <a href="<?= $baseUrl ?>facturas" class="text-xs text-inventory hover:underline">Ver todas <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Factura</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Servicio</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Fecha</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($facturas_recientes)): ?>
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No hay facturas registradas.</td></tr>
                    <?php else: ?>
                    <?php foreach($facturas_recientes as $f): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="<?= $baseUrl ?>facturas/<?= $f['id_factura'] ?>" class="text-sm font-bold text-inventory hover:underline"><?= $f['codigo_factura'] ?></a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?= $f['cliente_nombre'] ?> <?= $f['cliente_apellido'] ?? '' ?></td>
                        <td class="px-4 py-3 whitespace-nowrap hidden md:table-cell">
                            <span class="bg-gray-100 text-gray-600 text-xs font-mono px-2 py-0.5 rounded"><?= $f['servicio_codigo'] ?? '—' ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell"><?= date('d/m/Y', strtotime($f['fecha_emision'])) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 text-right">$<?= number_format($f['total'], 2) ?></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <?php $cls = match($f['estado']) {
                                'Pagada'   => 'bg-green-100 text-green-700',
                                'Pendiente'=> 'bg-yellow-100 text-yellow-700',
                                default    => 'bg-red-100 text-red-600'
                            }; ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $cls ?>"><?= $f['estado'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
