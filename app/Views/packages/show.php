<!-- Package Header Banner -->
<section class="bg-navy py-12 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-7xl mx-auto space-y-4">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <a href="/" class="hover:text-gold transition">Início</a>
            <span>/</span>
            <a href="/pacotes" class="hover:text-gold transition">Pacotes</a>
            <span>/</span>
            <span class="text-gold"><?= e($package['title']) ?></span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <span class="inline-block bg-gold/20 text-gold-light border border-gold/40 text-xs font-bold uppercase px-3 py-1 rounded-full">
                    <?= e($package['destination_name'] ?? 'Moçambique') ?> &bull; <?= e($package['destination_country'] ?? 'África') ?>
                </span>
                <h1 class="text-3xl sm:text-5xl font-serif font-bold tracking-tight text-white"><?= e($package['title']) ?></h1>
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed"><?= e($package['summary']) ?></p>
            </div>

            <!-- Price Box -->
            <div class="bg-navy-dark p-6 rounded-2xl border border-navy-light shrink-0 text-right space-y-1">
                <span class="text-xs text-slate-400 block font-medium">Preço Indicativo</span>
                <p class="text-3xl font-serif font-bold text-gold">
                    <?= money($package['price_per_person'] ?: $package['total_price'], $package['currency'] ?? 'MT') ?>
                </p>
                <?php if ($package['price_per_person']): ?>
                    <span class="text-xs text-slate-400 block">por pessoa (base quarto duplo)</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Content & Details Grid -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Left 2 Cols: Gallery, Description, Itinerary, Inclusions -->
        <div class="lg:col-span-2 space-y-12">
            
            <!-- Main Photo Gallery -->
            <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200">
                <img src="<?= e($package['cover_image']) ?>" alt="<?= e($package['title']) ?>" class="w-full h-96 sm:h-[460px] object-cover">
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-4">
                <h2 class="text-2xl font-serif font-bold text-slate-900">Sobre Esta Experiência</h2>
                <div class="text-slate-700 leading-relaxed text-sm sm:text-base space-y-4">
                    <?= nl2br(e($package['description'])) ?>
                </div>
            </div>

            <!-- Day-by-Day Itinerary -->
            <?php if (!empty($package['itineraries'])): ?>
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-6">
                    <h2 class="text-2xl font-serif font-bold text-slate-900">Itinerário Detalhado</h2>
                    <div class="space-y-6 border-l-2 border-gold pl-6 ml-2">
                        <?php foreach ($package['itineraries'] as $it): ?>
                            <div class="relative space-y-2">
                                <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-gold border-4 border-white shadow"></span>
                                <span class="text-xs font-bold uppercase tracking-wider text-gold-dark block">
                                    Dia <?= e($it['day_number']) ?>
                                </span>
                                <h3 class="text-lg font-serif font-bold text-slate-900"><?= e($it['title']) ?></h3>
                                <p class="text-sm text-slate-600 leading-relaxed"><?= nl2br(e($it['description'])) ?></p>
                                <?php if (!empty($it['meals_included'])): ?>
                                    <span class="inline-block text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded">
                                        Refeições: <?= e($it['meals_included']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Inclusions & Exclusions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Included -->
                <div class="bg-emerald-50/60 border border-emerald-200 rounded-2xl p-6 space-y-3">
                    <h3 class="font-serif font-bold text-lg text-emerald-950 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        O Que Está Incluído
                    </h3>
                    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                        <?= e($package['included_services'] ?: 'Voos nacionais ou transfers terrestres, alojamento selecionado com pequeno-almoço, taxas locais e assistência 24/7.') ?>
                    </p>
                </div>

                <!-- Excluded -->
                <div class="bg-rose-50/60 border border-rose-200 rounded-2xl p-6 space-y-3">
                    <h3 class="font-serif font-bold text-lg text-rose-950 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Não Incluído
                    </h3>
                    <p class="text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                        <?= e($package['excluded_services'] ?: 'Despesas de carácter pessoal, bebidas alcoólicas não mencionadas, vistos internacionais e seguro de cancelamento opcional.') ?>
                    </p>
                </div>
            </div>

            <!-- Conditions & Disclaimer Notice -->
            <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-5 text-xs text-amber-900 leading-relaxed space-y-2">
                <p class="font-bold flex items-center gap-1.5 text-amber-950">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aviso Importante e Condições Gerais:
                </p>
                <p>
                    <?= e($package['conditions'] ?: 'Os preços indicados estão sujeitos a confirmação no momento da reserva definitiva e podem variar em função da época do ano, disponibilidade hoteleira e flutuações cambiais. O envio do formulário constitui um pedido de cotação e não implica confirmação imediata.') ?>
                </p>
            </div>
        </div>

        <!-- Right Col: Booking Form Card -->
        <div class="space-y-6">
            <div class="sticky top-28 bg-white rounded-2xl p-6 sm:p-8 shadow-xl border border-gold/30 space-y-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gold block mb-1">Reserva Sem Compromisso</span>
                    <h3 class="text-xl font-serif font-bold text-slate-900">Solicitar Cotação</h3>
                    <p class="text-xs text-slate-500 mt-1">Preencha os seus dados para receber a proposta final com confirmação de datas e lugares.</p>
                </div>

                <form action="/cotacao" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="package_id" value="<?= e($package['id']) ?>">
                    <input type="hidden" name="destination" value="<?= e($package['destination_name'] ?? $package['title']) ?>">
                    <input type="hidden" name="service_type" value="Pacote Turístico">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nome Completo *</label>
                        <input type="text" name="customer_name" required placeholder="O seu nome" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Código</label>
                            <input type="text" name="phone_country_code" value="+258" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2 py-2.5 text-sm text-center outline-none">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Telefone / WhatsApp *</label>
                            <input type="tel" name="customer_phone" required placeholder="84 123 4567" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-gold outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Endereço de Email</label>
                        <input type="email" name="customer_email" placeholder="seuemail@exemplo.com" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nº Viajantes</label>
                            <input type="number" name="travelers_count" min="1" value="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-gold outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Datas Previstas</label>
                            <input type="text" name="preferred_dates" placeholder="Ex: Dezembro 2026" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-gold outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mensagem ou Pedidos Especiais</label>
                        <textarea name="message" rows="3" placeholder="Indique pedidos de quartos, voos pretendidos ou preferências..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-gold outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-gold to-gold-dark text-slate-950 font-bold uppercase text-xs tracking-wider rounded-lg shadow-md hover:brightness-110 active:scale-95 transition">
                        Submeter Pedido de Cotação
                    </button>
                </form>

                <div class="pt-2 border-t border-slate-100 text-center">
                    <a href="https://wa.me/258851041826?text=Olá,%20pretendo%20informações%20sobre%20o%20pacote%20<?= urlencode($package['title']) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.101.005.242-.038.375.281.145.347.491 1.2.534 1.288.043.088.072.191.014.307-.058.116-.087.188-.173.289l-.26.302c-.087.086-.177.18-.076.353.101.173.45 1.087 1.201 1.758.966.862 1.78 1.131 2.032 1.258.252.126.399.111.547-.058.147-.169.635-.741.805-.995.17-.254.341-.212.571-.127.23.085 1.458.687 1.71.813.252.126.42.188.481.293.061.105.061.609-.083 1.014z"/></svg>
                        Falar no WhatsApp sobre este pacote
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
