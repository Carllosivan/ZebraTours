<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-7xl mx-auto space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-gold block">Moçambique & Destinos Globais</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">Destinos Turísticos</h1>
        <p class="text-slate-300 max-w-2xl text-sm sm:text-base leading-relaxed">
            De arquipélagos virgens a santuários de vida selvagem e capitais cosmopolitas. Escolha o seu próximo refúgio e deixe a logística connosco.
        </p>
    </div>
</section>

<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($destinations as $d): ?>
            <a href="/destinos/<?= e($d['slug']) ?>" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200 transition-all duration-300 block flex flex-col">
                <div class="relative h-64 overflow-hidden bg-slate-100">
                    <img src="<?= e($d['cover_image']) ?>" alt="<?= e($d['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-navy/90 text-white text-[11px] font-semibold uppercase px-3 py-1 rounded-full border border-white/20">
                            <?= e($d['country']) ?>
                        </span>
                    </div>
                    <div class="absolute bottom-4 left-4 text-white text-xs font-semibold">
                        <span><?= e($d['package_count'] ?? 0) ?> Pacotes Disponíveis</span>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                    <div class="space-y-2">
                        <h2 class="font-serif font-bold text-2xl text-slate-900 group-hover:text-gold-dark transition"><?= e($d['name']) ?></h2>
                        <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed"><?= e($d['summary']) ?></p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-navy group-hover:text-gold transition">
                        <span>Explorar Roteiros</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
