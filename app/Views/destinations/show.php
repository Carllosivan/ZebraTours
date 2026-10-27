<section class="relative bg-navy text-white py-20 px-4 sm:px-6 lg:px-8">
    <div class="absolute inset-0 z-0">
        <img src="<?= e($destination['cover_image']) ?>" alt="<?= e($destination['name']) ?>" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/80 to-transparent"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto space-y-4">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="/" class="hover:text-gold transition">Início</a>
            <span>/</span>
            <a href="/destinos" class="hover:text-gold transition">Destinos</a>
            <span>/</span>
            <span class="text-gold"><?= e($destination['name']) ?></span>
        </div>

        <span class="inline-block bg-gold/20 text-gold-light border border-gold/40 text-xs font-bold uppercase px-3 py-1 rounded-full">
            <?= e($destination['country']) ?>
        </span>
        <h1 class="text-4xl sm:text-6xl font-serif font-bold text-white tracking-tight"><?= e($destination['name']) ?></h1>
        <p class="text-slate-300 max-w-3xl text-base sm:text-lg leading-relaxed"><?= e($destination['summary']) ?></p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
    <!-- About Destination -->
    <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-sm border border-slate-200 space-y-4">
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900">Sobre <?= e($destination['name']) ?></h2>
        <div class="text-slate-700 leading-relaxed space-y-4 text-sm sm:text-base">
            <?= nl2br(e($destination['description'] ?: $destination['summary'])) ?>
        </div>
    </div>

    <!-- Available Packages in this Destination -->
    <div class="space-y-8">
        <div class="flex items-end justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-gold block mb-1">Itinerários Recomendados</span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-slate-900">Pacotes Disponíveis em <?= e($destination['name']) ?></h2>
            </div>
            <a href="/cotacao?destino=<?= urlencode($destination['name']) ?>" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gold text-slate-950 font-bold text-xs uppercase tracking-wider hover:brightness-110 transition">
                Pedir Roteiro Personalizado
            </a>
        </div>

        <?php if (empty($destination['packages'])): ?>
            <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-600">
                <p>Não existem pacotes pré-formatados ativos para este destino de momento.</p>
                <a href="/cotacao?destino=<?= urlencode($destination['name']) ?>" class="mt-4 inline-block text-xs font-bold text-navy uppercase tracking-wider hover:text-gold transition">
                    Solicitar Cotação Sob Medida &rarr;
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($destination['packages'] as $pkg): ?>
                    <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200 transition-all duration-300 flex flex-col">
                        <div class="relative h-56 overflow-hidden bg-slate-100">
                            <img src="<?= e($pkg['cover_image'] ?: $destination['cover_image']) ?>" alt="<?= e($pkg['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute bottom-4 left-4 text-white text-xs font-medium">
                                <?= e($pkg['duration_days']) ?> Dias / <?= e($pkg['duration_nights']) ?> Noites
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <h3 class="font-serif font-bold text-xl text-slate-900 group-hover:text-gold-dark transition line-clamp-1">
                                    <a href="/pacotes/<?= e($pkg['slug']) ?>"><?= e($pkg['title']) ?></a>
                                </h3>
                                <p class="text-sm text-slate-600 line-clamp-2 mt-2"><?= e($pkg['summary']) ?></p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-lg font-bold font-serif text-slate-900">
                                    <?= money($pkg['price_per_person'] ?: $pkg['total_price'], $pkg['currency'] ?? 'MT') ?>
                                </span>
                                <a href="/pacotes/<?= e($pkg['slug']) ?>" class="px-4 py-2 rounded-lg bg-navy text-white text-xs font-bold hover:bg-gold hover:text-slate-950 transition uppercase tracking-wider">
                                    Ver Pacote
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
