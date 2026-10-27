<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-7xl mx-auto space-y-4">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="/" class="hover:text-gold transition">Início</a>
            <span>/</span>
            <a href="/servicos" class="hover:text-gold transition">Serviços</a>
            <span>/</span>
            <span class="text-gold"><?= e($service['title']) ?></span>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest text-gold block"><?= e($service['tagline']) ?></span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight"><?= e($service['title']) ?></h1>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-8">
            <div class="rounded-2xl overflow-hidden shadow-md">
                <img src="<?= e($service['image']) ?>" alt="<?= e($service['title']) ?>" class="w-full h-80 sm:h-96 object-cover">
            </div>

            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-2xl font-serif font-bold text-slate-900">Descrição do Serviço</h2>
                <p class="text-slate-700 leading-relaxed text-sm sm:text-base"><?= e($service['description']) ?></p>
            </div>

            <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-2xl font-serif font-bold text-slate-900">O Que Oferecemos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach ($service['features'] as $f): ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <svg class="w-5 h-5 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-xs sm:text-sm font-medium text-slate-800"><?= e($f) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar CTA -->
        <div class="space-y-6">
            <div class="bg-navy text-white rounded-2xl p-8 space-y-6 shadow-xl border border-navy-light">
                <span class="text-xs font-bold uppercase tracking-widest text-gold block">Cotação Rápida</span>
                <h3 class="text-xl font-serif font-bold text-white">Precisa de <?= e($service['title']) ?>?</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Contacte os nossos consultores especializados para obter as melhores opções de datas e tarifas.
                </p>
                <a href="/cotacao?servico=<?= urlencode($service['title']) ?>" class="block w-full text-center py-3 rounded-lg bg-gold text-slate-950 font-bold uppercase text-xs tracking-wider hover:brightness-110 transition shadow">
                    Pedir Cotação Online
                </a>
                <a href="https://wa.me/258851041826?text=Olá,%20pretendo%20informações%20sobre%20o%20serviço%20<?= urlencode($service['title']) ?>" target="_blank" rel="noopener noreferrer" class="block w-full text-center py-3 rounded-lg bg-emerald-600 text-white font-bold uppercase text-xs tracking-wider hover:bg-emerald-700 transition">
                    WhatsApp Direto
                </a>
            </div>

            <!-- Other services -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                <h4 class="font-serif font-bold text-base text-slate-900">Outros Serviços Disponíveis</h4>
                <div class="space-y-2 text-xs font-semibold">
                    <?php foreach ($allServices as $k => $s): if ($k === $service['id']) continue; ?>
                        <a href="/servicos/<?= e($k) ?>" class="block p-2.5 rounded-lg text-slate-700 hover:bg-slate-50 hover:text-gold transition">
                            <?= e($s['title']) ?> &rarr;
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
