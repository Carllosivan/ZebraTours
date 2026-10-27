<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white relative">
    <div class="max-w-7xl mx-auto space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-gold block">Catálogo Oficial de Viagens</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">Pacotes Turísticos</h1>
        <p class="text-slate-300 max-w-2xl text-sm sm:text-base leading-relaxed">
            Descubra os melhores roteiros turísticos em Moçambique e destinos internacionais selecionados. Itinerários completos com voos, estadias de charme e passeios guiados.
        </p>
    </div>
</section>

<!-- Filter & Search Toolbar -->
<section class="py-8 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="/pacotes" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Destino</label>
                <select name="destino" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gold outline-none">
                    <option value="">Todos os Destinos</option>
                    <?php foreach ($destinations as $d): ?>
                        <option value="<?= e($d['slug']) ?>" <?= ($filters['destination'] ?? '') === $d['slug'] ? 'selected' : '' ?>>
                            <?= e($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Categoria</label>
                <select name="categoria" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gold outline-none">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat['slug']) ?>" <?= ($filters['category'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pesquisar</label>
                <input type="text" name="pesquisa" value="<?= e($filters['search'] ?? '') ?>" placeholder="Título, localidade..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gold outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ordenar Por</label>
                <select name="ordenar" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-gold outline-none">
                    <option value="recommended" <?= ($filters['sort'] ?? '') === 'recommended' ? 'selected' : '' ?>>Recomendados</option>
                    <option value="price-asc" <?= ($filters['sort'] ?? '') === 'price-asc' ? 'selected' : '' ?>>Preço: Menor para Maior</option>
                    <option value="price-desc" <?= ($filters['sort'] ?? '') === 'price-desc' ? 'selected' : '' ?>>Preço: Maior para Menor</option>
                    <option value="duration-asc" <?= ($filters['sort'] ?? '') === 'duration-asc' ? 'selected' : '' ?>>Menor Duração</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-navy text-white font-bold py-2.5 px-4 rounded-lg uppercase text-xs tracking-wider hover:bg-slate-800 transition">
                    Filtrar
                </button>
                <a href="/pacotes" class="p-2.5 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 transition" title="Limpar Filtros">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>
</section>

<!-- Packages Grid -->
<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between text-sm text-slate-500">
        <span>A apresentar <strong><?= count($packages) ?></strong> pacotes turísticos</span>
    </div>

    <?php if (empty($packages)): ?>
        <div class="text-center py-20 bg-white rounded-2xl border border-slate-200 p-8 space-y-4">
            <div class="w-16 h-16 rounded-full bg-amber-50 text-gold flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-bold font-serif text-slate-900">Nenhum pacote encontrado</h3>
            <p class="text-slate-600 max-w-md mx-auto text-sm">Não encontramos nenhum pacote correspondente aos filtros selecionados. Tente ajustar os parâmetros ou contacte a nossa equipa para um itinerário personalizado.</p>
            <a href="/pacotes" class="inline-block mt-4 px-6 py-2.5 rounded-lg bg-navy text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-800 transition">
                Ver Todos os Pacotes
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($packages as $pkg): ?>
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200 transition-all duration-300 flex flex-col">
                    <div class="relative h-60 overflow-hidden bg-slate-100">
                        <img src="<?= e($pkg['cover_image']) ?>" alt="<?= e($pkg['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                        
                        <div class="absolute top-4 left-4">
                            <span class="inline-block bg-navy/90 backdrop-blur-md text-white text-[11px] font-semibold uppercase px-3 py-1 rounded-full border border-white/20">
                                <?= e($pkg['destination_name'] ?? 'Moçambique') ?>
                            </span>
                        </div>

                        <div class="absolute bottom-4 left-4 text-white text-xs font-medium flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><?= e($pkg['duration_days']) ?> Dias / <?= e($pkg['duration_nights']) ?> Noites</span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <h3 class="font-serif font-bold text-xl text-slate-900 group-hover:text-gold-dark transition line-clamp-1">
                                <a href="/pacotes/<?= e($pkg['slug']) ?>"><?= e($pkg['title']) ?></a>
                            </h3>
                            <p class="text-sm text-slate-600 line-clamp-2 leading-relaxed">
                                <?= e($pkg['summary']) ?>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-medium">A partir de</span>
                                <span class="text-xl font-bold font-serif text-slate-900">
                                    <?= money($pkg['price_per_person'] ?: $pkg['total_price'], $pkg['currency'] ?? 'MT') ?>
                                </span>
                                <?php if ($pkg['price_per_person']): ?>
                                    <span class="text-[10px] text-slate-500 block">por pessoa</span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="/pacotes/<?= e($pkg['slug']) ?>" class="p-2.5 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-navy transition" title="Ver Detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="/cotacao?pacote_id=<?= e($pkg['slug']) ?>" class="px-3.5 py-2 rounded-lg bg-navy text-white text-xs font-bold hover:bg-gold hover:text-slate-950 transition uppercase tracking-wider">
                                    Pedir Cotação
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
