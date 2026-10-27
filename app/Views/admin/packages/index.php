<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Catálogo de Pacotes Turísticos</h2>
            <p class="text-xs text-slate-400">Gira os itinerários, preços em Meticais, disponibilidades e destaques na página inicial.</p>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-6">Título do Pacote</th>
                        <th class="py-3 px-6">Destino</th>
                        <th class="py-3 px-6">Duração</th>
                        <th class="py-3 px-6">Preço Base</th>
                        <th class="py-3 px-6">Estado</th>
                        <th class="py-3 px-6 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($packages as $pkg): ?>
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-4 px-6">
                                <span class="font-bold text-white block text-sm"><?= e($pkg['title']) ?></span>
                                <span class="text-slate-500 font-mono text-[11px]"><?= e($pkg['slug']) ?></span>
                            </td>
                            <td class="py-4 px-6 text-slate-300 font-medium"><?= e($pkg['destination_name'] ?? 'Moçambique') ?></td>
                            <td class="py-4 px-6"><?= e($pkg['duration_days']) ?>D / <?= e($pkg['duration_nights']) ?>N</td>
                            <td class="py-4 px-6 font-bold text-gold">
                                <?= money($pkg['price_per_person'] ?: $pkg['total_price'], $pkg['currency'] ?? 'MT') ?>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $pkg['status'] === 'publicado' ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-slate-800 text-slate-400' ?>">
                                    <?= e($pkg['status']) ?>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="/pacotes/<?= e($pkg['slug']) ?>" target="_blank" class="text-xs text-gold hover:underline font-semibold">
                                    Ver no Portal
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
