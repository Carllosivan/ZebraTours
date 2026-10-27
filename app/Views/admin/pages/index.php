<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Páginas Editoriais (CMS)</h2>
            <p class="text-xs text-slate-400">Gira os conteúdos textuais, termos, políticas e comunicados da agência.</p>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/60 uppercase tracking-wider text-slate-400 border-b border-slate-800">
                <tr>
                    <th class="py-3 px-6">Título da Página</th>
                    <th class="py-3 px-6">Slug / Rota</th>
                    <th class="py-3 px-6">Estado</th>
                    <th class="py-3 px-6 text-right">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                <?php foreach ($pages as $p): ?>
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-4 px-6 font-bold text-white"><?= e($p['title']) ?></td>
                        <td class="py-4 px-6 font-mono text-slate-400">/pagina/<?= e($p['slug']) ?></td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-950 text-emerald-400 border border-emerald-800">
                                <?= e($p['status']) ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="/pagina/<?= e($p['slug']) ?>" target="_blank" class="text-xs text-gold hover:underline font-semibold">
                                Visualizar &rarr;
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
