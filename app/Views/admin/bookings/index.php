<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Gestão de Cotações e Pedidos de Viagem</h2>
            <p class="text-xs text-slate-400">Acompanhe cada solicitação de cliente, atualize estados e registe notas de contacto.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="/admin/pedidos/exportar-csv" class="px-4 py-2 bg-slate-800 text-white font-bold rounded-xl text-xs uppercase tracking-wider hover:bg-slate-700 transition flex items-center gap-2 border border-slate-700">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <a href="/admin/pedidos?status=all" class="px-3 py-1.5 rounded-lg <?= $selectedStatus === 'all' ? 'bg-gold text-slate-950 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">Todos</a>
            <a href="/admin/pedidos?status=Pendente" class="px-3 py-1.5 rounded-lg <?= $selectedStatus === 'Pendente' ? 'bg-gold text-slate-950 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">Pendentes</a>
            <a href="/admin/pedidos?status=Contactado" class="px-3 py-1.5 rounded-lg <?= $selectedStatus === 'Contactado' ? 'bg-gold text-slate-950 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">Contactados</a>
            <a href="/admin/pedidos?status=Confirmado" class="px-3 py-1.5 rounded-lg <?= $selectedStatus === 'Confirmado' ? 'bg-gold text-slate-950 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">Confirmados</a>
            <a href="/admin/pedidos?status=Cancelado" class="px-3 py-1.5 rounded-lg <?= $selectedStatus === 'Cancelado' ? 'bg-gold text-slate-950 font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">Cancelados</a>
        </div>

        <form action="/admin/pedidos" method="GET" class="flex gap-2">
            <input type="text" name="q" value="<?= e($searchQuery) ?>" placeholder="Referência, cliente, telefone..." class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-1.5 text-xs text-white outline-none focus:ring-1 focus:ring-gold">
            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-bold rounded-lg hover:bg-slate-700">Buscar</button>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Ref.</th>
                        <th class="py-3 px-4">Cliente / Contacto</th>
                        <th class="py-3 px-4">Serviço / Destino</th>
                        <th class="py-3 px-4">Datas / Viajantes</th>
                        <th class="py-3 px-4">Mensagem</th>
                        <th class="py-3 px-4">Estado Atual</th>
                        <th class="py-3 px-4">Ação / WhatsApp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-slate-500">Nenhum registo encontrado.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $b): ?>
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="py-4 px-4 font-mono font-bold text-gold"><?= e($b['reference_code']) ?></td>
                                <td class="py-4 px-4">
                                    <span class="block font-semibold text-white"><?= e($b['customer_name']) ?></span>
                                    <span class="block text-slate-400"><?= e($b['customer_phone']) ?></span>
                                    <?php if (!empty($b['customer_email'])): ?>
                                        <span class="block text-slate-500 text-[11px]"><?= e($b['customer_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="block text-white font-medium"><?= e($b['service_type']) ?></span>
                                    <span class="block text-slate-400"><?= e($b['destination'] ?: $b['package_title'] ?: '-') ?></span>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="block text-white"><?= e($b['preferred_dates'] ?: 'A combinar') ?></span>
                                    <span class="block text-slate-500"><?= e($b['travelers_count']) ?> viajante(s)</span>
                                </td>
                                <td class="py-4 px-4 max-w-xs">
                                    <span class="line-clamp-2 text-slate-400 text-[11px]"><?= e($b['message'] ?: 'Sem notas adicionais.') ?></span>
                                    <?php if (!empty($b['admin_notes'])): ?>
                                        <span class="block text-amber-400 text-[10px] mt-1 font-semibold">Nota Interna: <?= e($b['admin_notes']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <form action="/admin/pedidos/<?= e($b['id']) ?>/estado" method="POST" class="flex flex-col gap-1.5">
                                        <?= csrf_field() ?>
                                        <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 rounded px-2 py-1 text-[11px] font-semibold text-white focus:ring-1 focus:ring-gold">
                                            <option value="Pendente" <?= $b['status'] === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                            <option value="Em análise" <?= $b['status'] === 'Em análise' ? 'selected' : '' ?>>Em análise</option>
                                            <option value="Contactado" <?= $b['status'] === 'Contactado' ? 'selected' : '' ?>>Contactado</option>
                                            <option value="Confirmado" <?= $b['status'] === 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                            <option value="Cancelado" <?= $b['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="py-4 px-4">
                                    <?php 
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $b['customer_phone']);
                                        if (!str_starts_with($cleanPhone, '258') && strlen($cleanPhone) <= 9) {
                                            $cleanPhone = '258' . $cleanPhone;
                                        }
                                        $msg = "Olá {$b['customer_name']}, agradecemos o seu contacto com a Zebra Tours sobre o pedido {$b['reference_code']}.";
                                    ?>
                                    <a href="https://wa.me/<?= e($cleanPhone) ?>?text=<?= urlencode($msg) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] text-emerald-400 hover:text-emerald-300 font-bold bg-emerald-950/40 border border-emerald-800 px-2.5 py-1 rounded">
                                        Falar WhatsApp
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
