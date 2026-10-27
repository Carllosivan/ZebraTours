<div class="space-y-8">
    
    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 block font-medium">Pedidos & Cotações</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold font-serif text-white"><?= $stats['totalBookings'] ?></span>
                <span class="text-xs text-amber-400 font-semibold"><?= $stats['pendingBookings'] ?> pendentes</span>
            </div>
            <p class="text-[11px] text-slate-500">Taxa de conversão: <?= $stats['conversionRate'] ?>%</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 block font-medium">Pacotes Publicados</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold font-serif text-gold"><?= $stats['publishedPackages'] ?></span>
                <span class="text-xs text-slate-400 font-semibold"><?= $stats['totalPackages'] ?> no total</span>
            </div>
            <p class="text-[11px] text-slate-500">Visíveis aos clientes no portal</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 block font-medium">Destinos Mapeados</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold font-serif text-white"><?= $stats['totalDestinations'] ?></span>
                <span class="text-xs text-emerald-400 font-semibold">100% ativos</span>
            </div>
            <p class="text-[11px] text-slate-500">Moçambique e Internacional</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 p-6 rounded-2xl space-y-2">
            <span class="text-xs uppercase tracking-wider text-slate-400 block font-medium">Biblioteca Multimédia</span>
            <div class="flex items-baseline justify-between">
                <span class="text-3xl font-bold font-serif text-white"><?= $stats['totalMedia'] ?></span>
                <span class="text-xs text-slate-400 font-semibold">Ficheiros</span>
            </div>
            <p class="text-[11px] text-slate-500">Fotos de alta resolução e banners</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap items-center gap-4">
        <a href="/admin/pedidos" class="px-5 py-2.5 bg-gold text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider hover:brightness-110 transition flex items-center gap-2 shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Gerir Cotações e Pedidos
        </a>
        <a href="/admin/pedidos/exportar-csv" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-slate-700 transition flex items-center gap-2 border border-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Exportar Pedidos em CSV
        </a>
        <a href="/admin/media/upload" class="px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-slate-700 transition flex items-center gap-2 border border-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Carregar Nova Imagem
        </a>
    </div>

    <!-- Recent Bookings Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <h2 class="text-base font-bold text-white">Pedidos Recentes Recebidos</h2>
            <a href="/admin/pedidos" class="text-xs text-gold hover:underline font-semibold">Ver Todos &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Referência</th>
                        <th class="py-3 px-6">Cliente</th>
                        <th class="py-3 px-6">Serviço / Destino</th>
                        <th class="py-3 px-6">Viajantes</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6 text-right">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-xs">
                    <?php if (empty($recentBookings)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">Nenhum pedido registado até ao momento.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $b): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-6 font-mono font-bold text-gold"><?= e($b['reference_code']) ?></td>
                                <td class="py-4 px-6">
                                    <span class="block font-semibold text-white"><?= e($b['customer_name']) ?></span>
                                    <span class="block text-slate-500 text-[11px]"><?= e($b['customer_phone']) ?></span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="block text-white font-medium"><?= e($b['service_type']) ?></span>
                                    <span class="block text-slate-400 text-[11px]"><?= e($b['destination'] ?: $b['package_title'] ?: '-') ?></span>
                                </td>
                                <td class="py-4 px-6"><?= e($b['travelers_count']) ?> pess.</td>
                                <td class="py-4 px-6">
                                    <?php 
                                        $statusClass = match($b['status']) {
                                            'Confirmado' => 'bg-emerald-950 text-emerald-400 border-emerald-800',
                                            'Pendente'   => 'bg-amber-950 text-amber-400 border-amber-800',
                                            'Contactado' => 'bg-blue-950 text-blue-400 border-blue-800',
                                            'Cancelado'  => 'bg-rose-950 text-rose-400 border-rose-800',
                                            default      => 'bg-slate-800 text-slate-300 border-slate-700',
                                        };
                                    ?>
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border <?= $statusClass ?>">
                                        <?= e($b['status']) ?>
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right text-slate-400"><?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
