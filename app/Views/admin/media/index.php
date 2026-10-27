<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Biblioteca Multimédia</h2>
            <p class="text-xs text-slate-400">Total de <?= $totalItems ?> recursos multimédia registados.</p>
        </div>
        <a href="/admin/media/upload" class="px-4 py-2 bg-gold text-slate-950 font-bold rounded-xl text-xs uppercase tracking-wider hover:brightness-110 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Carregar Novo Ficheiro
        </a>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <?php foreach ($items as $item): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden group flex flex-col justify-between">
                <div class="h-32 bg-slate-950 relative overflow-hidden">
                    <img src="<?= e($item['file_path']) ?>" alt="<?= e($item['alt_text'] ?: $item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="p-3 space-y-1">
                    <span class="block text-xs font-semibold text-white truncate" title="<?= e($item['title']) ?>"><?= e($item['title']) ?></span>
                    <span class="block text-[10px] text-slate-500 font-mono truncate"><?= e($item['filename']) ?></span>
                </div>
                <div class="px-3 pb-3 flex items-center justify-between pt-1 border-t border-slate-800 text-[10px]">
                    <span class="text-gold font-semibold uppercase"><?= e($item['category'] ?? 'geral') ?></span>
                    <form action="/admin/media/delete" method="POST" onsubmit="return confirm('Eliminar este recurso?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($item['id']) ?>">
                        <button type="submit" class="text-red-400 hover:text-red-300">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
