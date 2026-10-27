<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-white font-serif">Destinos Turísticos</h2>
            <p class="text-xs text-slate-400">Destinos de Moçambique e além para associação de pacotes turísticos.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($destinations as $d): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow flex flex-col">
                <div class="h-44 relative bg-slate-950">
                    <img src="<?= e($d['cover_image']) ?>" alt="<?= e($d['name']) ?>" class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-sm text-[10px] text-white px-2.5 py-1 rounded-full font-bold uppercase">
                        <?= e($d['country']) ?>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <h3 class="font-serif font-bold text-lg text-white"><?= e($d['name']) ?></h3>
                        <p class="text-xs text-slate-400 line-clamp-2 mt-1"><?= e($d['summary']) ?></p>
                    </div>
                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-mono"><?= e($d['slug']) ?></span>
                        <a href="/destinos/<?= e($d['slug']) ?>" target="_blank" class="text-gold hover:underline font-semibold">Ver Público &rarr;</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
