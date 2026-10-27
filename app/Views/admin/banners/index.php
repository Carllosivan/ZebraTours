<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Banners e Campanhas Promocionais</h2>
            <p class="text-xs text-slate-400">Destaques fotográficos e mensagens com chamada para ação.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($banners as $b): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow flex flex-col">
                <div class="h-44 relative bg-slate-950">
                    <img src="<?= e($b['image_url']) ?>" alt="<?= e($b['title']) ?>" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 bg-slate-950/80 px-2 py-0.5 rounded text-[10px] text-gold font-bold uppercase">
                        Ordem: <?= e($b['sort_order']) ?>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="font-serif font-bold text-lg text-white"><?= e($b['title']) ?></h3>
                        <?php if (!empty($b['subtitle'])): ?>
                            <p class="text-xs text-slate-400 mt-1"><?= e($b['subtitle']) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-500 text-[10px] uppercase font-bold"><?= e($b['status']) ?></span>
                        <a href="<?= e($b['button_url'] ?: '#') ?>" class="text-gold font-semibold"><?= e($b['button_text'] ?: 'Ver Mais') ?> &rarr;</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
