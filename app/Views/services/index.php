<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-7xl mx-auto space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-gold block">Zebra Tours Moçambique</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">Os Nossos Serviços</h1>
        <p class="text-slate-300 max-w-2xl text-sm sm:text-base leading-relaxed">
            Uma abordagem completa e integrada para viagens corporativas, férias em família e assistência especializada. Conheça as nossas principais valências.
        </p>
    </div>
</section>

<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="space-y-12">
        <?php $i = 0; foreach ($services as $srv): $isEven = ($i % 2 === 0); $i++; ?>
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200 transition-all duration-300 flex flex-col <?= $isEven ? 'lg:flex-row' : 'lg:flex-row-reverse' ?>">
                <div class="lg:w-1/2 h-72 lg:h-auto overflow-hidden bg-slate-100">
                    <img src="<?= e($srv['image']) ?>" alt="<?= e($srv['title']) ?>" class="w-full h-full object-cover">
                </div>
                <div class="lg:w-1/2 p-8 sm:p-12 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <span class="text-xs font-bold uppercase tracking-widest text-gold block"><?= e($srv['tagline']) ?></span>
                        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900"><?= e($srv['title']) ?></h2>
                        <p class="text-slate-600 text-sm leading-relaxed"><?= e($srv['description']) ?></p>
                        
                        <ul class="space-y-2 pt-2">
                            <?php foreach ($srv['features'] as $f): ?>
                                <li class="flex items-center gap-2.5 text-xs text-slate-700 font-medium">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span><?= e($f) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center gap-4">
                        <a href="/servicos/<?= e($srv['id']) ?>" class="px-5 py-2.5 rounded-lg bg-navy text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition">
                            Saber Mais
                        </a>
                        <a href="/cotacao?servico=<?= urlencode($srv['title']) ?>" class="px-5 py-2.5 rounded-lg bg-gold text-slate-950 text-xs font-bold uppercase tracking-wider hover:brightness-110 transition">
                            Pedir Cotação
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
