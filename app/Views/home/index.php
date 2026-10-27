<!-- HERO SECTION WITH INTEGRATED TRAVEL SEARCH ENGINE -->
<section class="relative bg-navy text-white overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=2000&q=85" 
             alt="Praia Paradisíaca em Moçambique" 
             class="w-full h-full object-cover object-center opacity-25 scale-105 transform motion-safe:animate-[pulse_12s_ease-in-out_infinite]">
        <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/85 to-navy/70"></div>
        <div class="absolute inset-0 bg-pattern"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 lg:pt-24 lg:pb-32">
        
        <!-- Header Text -->
        <div class="max-w-3xl space-y-5 text-center mx-auto mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gold/15 border border-gold/40 text-gold-light text-xs font-semibold tracking-wider uppercase backdrop-blur-md shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Zebra Tours Moçambique &bull; Mais do que viagens, experiências!
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-black tracking-tight text-white leading-[1.12]">
                O Seu Próximo Destino Começa Aqui.
            </h1>
            
            <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl mx-auto">
                Passagens aéreas aos melhores preços, escapadinhas inesquecíveis, alojamentos exclusivos e programas completos com assistência dedicada em Moçambique e no mundo.
            </p>

            <!-- Quick Pill Badges -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-2 text-xs">
                <span class="text-slate-400 font-medium">Em alta:</span>
                <a href="#campanhas" class="px-3 py-1 rounded-full bg-white/10 hover:bg-gold hover:text-slate-950 transition border border-white/20 text-slate-200 font-medium flex items-center gap-1.5">
                    <span>🇨🇳</span> China Guangzhou 169.500 MT
                </a>
                <a href="#campanhas" class="px-3 py-1 rounded-full bg-white/10 hover:bg-gold hover:text-slate-950 transition border border-white/20 text-slate-200 font-medium flex items-center gap-1.5">
                    <span>🇿🇦</span> Cape Town 59.500 MT
                </a>
                <a href="#campanhas" class="px-3 py-1 rounded-full bg-white/10 hover:bg-gold hover:text-slate-950 transition border border-white/20 text-slate-200 font-medium flex items-center gap-1.5">
                    <span>🇵🇹</span> Emigre Portugal
                </a>
            </div>
        </div>

        <!-- SEARCH ENGINE CONTAINER -->
        <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl p-6 sm:p-8 text-slate-900 border border-amber-300/30">
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 mb-6 gap-2 sm:gap-6 text-sm font-bold">
                <button type="button" class="tab-btn pb-3 px-3 border-b-2 border-gold text-slate-900 flex items-center gap-2 transition" data-target="tab-pacotes">
                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span data-i18n="hero.tab_packages">Pacotes Turísticos</span>
                </button>
                <button type="button" class="tab-btn pb-3 px-3 border-b-2 border-transparent text-slate-400 hover:text-slate-800 flex items-center gap-2 transition" data-target="tab-voos">
                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span data-i18n="hero.tab_flights">Passagens Aéreas</span>
                </button>
                <button type="button" class="tab-btn pb-3 px-3 border-b-2 border-transparent text-slate-400 hover:text-slate-800 flex items-center gap-2 transition" data-target="tab-hoteis">
                    <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span data-i18n="hero.tab_hotels">Hotéis & Lodges</span>
                </button>
            </div>

            <!-- Tab Content 1: Pacotes -->
            <form action="/pacotes" method="GET" id="tab-pacotes" class="tab-panel grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.dest_label">Destino Desejado</label>
                    <select name="destino" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm focus:ring-2 focus:ring-gold focus:border-gold outline-none">
                        <option value="" data-i18n="hero.all_dest">Todos os Destinos</option>
                        <?php foreach ($destinations as $d): ?>
                            <option value="<?= e($d['slug']) ?>"><?= e($d['name']) ?> (<?= e($d['country']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.exp_label">Tipo de Experiência</label>
                    <select name="categoria" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm focus:ring-2 focus:ring-gold focus:border-gold outline-none">
                        <option value="" data-i18n="hero.all_cat">Todas as Categorias</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.kw_label">Palavra-Chave</label>
                    <input type="text" name="pesquisa" placeholder="Ex: Safari, Guangzhou, Vilankulos..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm focus:ring-2 focus:ring-gold focus:border-gold outline-none">
                </div>
                <div>
                    <button type="submit" class="w-full bg-gradient-to-r from-gold via-gold-light to-gold-dark text-slate-950 font-bold py-3 px-4 rounded-xl uppercase text-xs tracking-wider hover:brightness-110 active:scale-95 transition shadow-lg shadow-gold/25 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span data-i18n="hero.search_btn">Pesquisar Pacotes</span>
                    </button>
                </div>
            </form>

            <!-- Tab Content 2: Voos -->
            <form action="/cotacao" method="GET" id="tab-voos" class="tab-panel hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="servico" value="Passagens Aéreas">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.origin_label">Origem</label>
                    <input type="text" name="origem" value="Maputo (MPM)" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.dest_flight_label">Destino do Voo</label>
                    <input type="text" name="destino" placeholder="Ex: Lisboa, Guangzhou, Joanesburgo..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.dates_label">Datas Pretendidas</label>
                    <input type="text" name="datas" placeholder="Ex: Ida e Volta em Outubro" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <button type="submit" class="w-full bg-navy text-white font-bold py-3 px-4 rounded-xl uppercase text-xs tracking-wider hover:bg-navy-light active:scale-95 transition shadow flex items-center justify-center gap-2">
                        <span data-i18n="hero.quote_flights_btn">Cotar Passagens &rarr;</span>
                    </button>
                </div>
            </form>

            <!-- Tab Content 3: Hotéis -->
            <form action="/cotacao" method="GET" id="tab-hoteis" class="tab-panel hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="servico" value="Reserva de Hotéis e Lodges">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.city_label">Cidade / Região</label>
                    <input type="text" name="destino" placeholder="Ex: Bazaruto, Cape Town, Lisboa..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.dates_label">Datas</label>
                    <input type="text" name="datas" placeholder="Ex: 5 Noites no Próximo Mês" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2" data-i18n="hero.guests_label">Hóspedes</label>
                    <input type="number" name="viajantes" value="2" min="1" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-3 text-sm outline-none focus:ring-2 focus:ring-gold">
                </div>
                <div>
                    <button type="submit" class="w-full bg-navy text-white font-bold py-3 px-4 rounded-xl uppercase text-xs tracking-wider hover:bg-navy-light active:scale-95 transition shadow flex items-center justify-center gap-2">
                        <span data-i18n="hero.quote_hotel_btn">Cotar Alojamento &rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- OFFICIAL PROMOTIONAL CAMPAIGNS (FROM FLYERS) -->
<section id="campanhas" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="mb-12 text-center max-w-3xl mx-auto space-y-3">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold uppercase tracking-wider shadow-sm">
            <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
            Campanhas Oficiais em Vigor &bull; 2026
        </span>
        <h2 class="text-3xl sm:text-5xl font-serif font-black text-slate-900 tracking-tight">Destaques Promocionais Zebra Tours</h2>
        <p class="text-slate-600 text-sm sm:text-base leading-relaxed">Mais do que viagens, experiências! Conheça as ofertas e programas com tarifas negociadas e assistência integral.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Campaign 1: China Guangzhou -->
        <div class="bg-gradient-to-b from-white to-amber-50/50 rounded-3xl overflow-hidden border-2 border-amber-400/80 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group">
            <div class="relative h-64 overflow-hidden bg-slate-900">
                <img src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=1200&q=80" alt="China Guangzhou" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                <div class="absolute top-4 left-4 flex flex-col gap-1.5">
                    <span class="bg-red-600 text-white text-[11px] font-black uppercase px-3.5 py-1 rounded-full shadow flex items-center gap-1.5">
                        <span>★</span> Viva Essa Experiência!
                    </span>
                    <span class="bg-navy/90 text-white text-[10px] font-bold uppercase px-3 py-0.5 rounded-full backdrop-blur-sm">
                        Compras & Negócios
                    </span>
                </div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <span class="text-xs text-gold font-bold uppercase tracking-widest block">China &bull; Guangzhou</span>
                    <h3 class="text-2xl font-serif font-black text-white leading-tight">China Guangzhou</h3>
                    <p class="text-xs text-slate-300">Novembro de 2026 &bull; 7 Noites</p>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Uma oportunidade única para conhecer novos fornecedores, fazer grandes compras e explorar um dos maiores centros comerciais do mundo.
                </p>

                <!-- Inclusions Checklist -->
                <div class="space-y-2.5 py-3 border-y border-amber-200/60 text-xs">
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Passagem aérea de ida e volta</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Hotel – 7 Noites</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Assistência durante toda a viagem</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Reserva para imprevistos de custos</span>
                    </div>
                </div>

                <div class="pt-2 flex items-baseline justify-between gap-4">
                    <div>
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Preço de Venda</span>
                        <span class="text-2xl font-serif font-black text-amber-950">169.500 MT</span>
                        <span class="text-[10px] text-slate-500 block">por pessoa</span>
                    </div>
                    <div class="flex flex-col gap-1.5 text-right">
                        <a href="/cotacao?pacote_id=china-guangzhou-compras-negocios" class="px-4 py-2 bg-gradient-to-r from-gold to-gold-dark text-slate-950 font-bold uppercase text-xs tracking-wider rounded-xl shadow hover:brightness-110 active:scale-95 transition text-center">
                            Reservar Já
                        </a>
                        <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20pretendo%20reservar%20o%20pacote%20China%20Guangzhou%20169.500%20MT" target="_blank" rel="noopener noreferrer" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-bold flex items-center justify-end gap-1">
                            <span>WhatsApp</span> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign 2: Cape Town Especial -->
        <div class="bg-gradient-to-b from-white to-blue-50/50 rounded-3xl overflow-hidden border-2 border-blue-400/80 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group">
            <div class="relative h-64 overflow-hidden bg-slate-900">
                <img src="https://images.unsplash.com/photo-1580618672591-eb180b1a973f?auto=format&fit=crop&w=1200&q=80" alt="Cape Town África do Sul" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                <div class="absolute top-4 left-4 flex flex-col gap-1.5">
                    <span class="bg-amber-500 text-slate-950 text-[11px] font-black uppercase px-3.5 py-1 rounded-full shadow flex items-center gap-1.5">
                        <span>★</span> Pacote Especial África do Sul
                    </span>
                    <span class="bg-navy/90 text-white text-[10px] font-bold uppercase px-3 py-0.5 rounded-full backdrop-blur-sm">
                        Natureza &bull; Cultura &bull; Aventura
                    </span>
                </div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <span class="text-xs text-gold font-bold uppercase tracking-widest block">África do Sul &bull; Cape Town</span>
                    <h3 class="text-2xl font-serif font-black text-white leading-tight">Cape Town Adventures</h3>
                    <p class="text-xs text-slate-300">31 Outubro a 2 Novembro &bull; 2 Noites</p>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Conheça a beleza de Cape Town, viva novas experiências de Table Mountain, paisagens incríveis e pinguins em Boulders Beach. Lugares limitados!
                </p>

                <!-- Inclusions Checklist -->
                <div class="space-y-2.5 py-3 border-y border-blue-200/60 text-xs">
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Passagem aérea ida e volta (Maputo ↔ Cape Town)</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Hotel – 2 Noites com pequeno-almoço</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Assistência durante toda a viagem</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Seguro e tranquilidade Zebra Tours</span>
                    </div>
                </div>

                <div class="pt-2 flex items-baseline justify-between gap-4">
                    <div>
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Pacote Completo</span>
                        <span class="text-2xl font-serif font-black text-navy">59.500 MT</span>
                        <span class="text-[10px] text-slate-500 block">por pessoa</span>
                    </div>
                    <div class="flex flex-col gap-1.5 text-right">
                        <a href="/cotacao?pacote_id=pacote-especial-africa-do-sul-cape-town" class="px-4 py-2 bg-navy text-white font-bold uppercase text-xs tracking-wider rounded-xl shadow hover:bg-slate-800 active:scale-95 transition text-center">
                            Reservar Já
                        </a>
                        <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20pretendo%20reservar%20o%20pacote%20Cape%20Town%2059.500%20MT" target="_blank" rel="noopener noreferrer" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-bold flex items-center justify-end gap-1">
                            <span>WhatsApp</span> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign 3: Emigre para Portugal -->
        <div class="bg-gradient-to-b from-white to-emerald-50/50 rounded-3xl overflow-hidden border-2 border-emerald-400/80 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group">
            <div class="relative h-64 overflow-hidden bg-slate-900">
                <img src="https://images.unsplash.com/photo-1509356843151-3e7d96241e11?auto=format&fit=crop&w=1200&q=80" alt="Emigre para Portugal" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
                <div class="absolute top-4 left-4 flex flex-col gap-1.5">
                    <span class="bg-emerald-600 text-white text-[11px] font-black uppercase px-3.5 py-1 rounded-full shadow flex items-center gap-1.5">
                        <span>★</span> Programa Trabalho & Residência
                    </span>
                    <span class="bg-navy/90 text-white text-[10px] font-bold uppercase px-3 py-0.5 rounded-full backdrop-blur-sm">
                        O Seu Futuro em Portugal!
                    </span>
                </div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <span class="text-xs text-gold font-bold uppercase tracking-widest block">Portugal &bull; Lisboa & Porto</span>
                    <h3 class="text-2xl font-serif font-black text-white leading-tight">Emigre Para Trabalhar</h3>
                    <p class="text-xs text-slate-300">Candidaturas 2026 &bull; Assessoria Completa</p>
                </div>
            </div>

            <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Realize o seu sonho de uma vida melhor, com segurança e apoio em todo o processo de imigração, procura de emprego e integração.
                </p>

                <!-- Inclusions Checklist -->
                <div class="space-y-2.5 py-3 border-y border-emerald-200/60 text-xs">
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Orientação de requisitos e vistos de trabalho</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Indicação de oportunidades de emprego</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Apoio na procura de alojamento e integração</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-800 font-semibold">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Acompanhamento em todas as etapas</span>
                    </div>
                </div>

                <div class="pt-2 flex items-baseline justify-between gap-4">
                    <div>
                        <span class="text-[10px] text-slate-500 block uppercase font-bold">Consultoria</span>
                        <span class="text-2xl font-serif font-black text-emerald-800">Sob Consulta</span>
                        <span class="text-[10px] text-slate-500 block">processo personalizado</span>
                    </div>
                    <div class="flex flex-col gap-1.5 text-right">
                        <a href="/cotacao?pacote_id=emigre-para-portugal-trabalhar" class="px-4 py-2 bg-emerald-600 text-white font-bold uppercase text-xs tracking-wider rounded-xl shadow hover:bg-emerald-700 active:scale-95 transition text-center">
                            Falar Connosco
                        </a>
                        <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20pretendo%20informa%C3%A7%C3%B5es%20sobre%20o%20programa%20Emigre%20para%20Portugal" target="_blank" rel="noopener noreferrer" class="text-[11px] text-emerald-700 hover:text-emerald-800 font-bold flex items-center justify-end gap-1">
                            <span>WhatsApp</span> &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- ALL PACKAGES SECTION -->
<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-slate-200">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-gold block mb-1">Catálogo Exclusivo</span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 tracking-tight">Experiências e Pacotes em Destaque</h2>
            <p class="text-sm text-slate-600 mt-2">Escolha entre praias tropicais de Moçambique, metrópoles cosmopolitas e safaris selvagens.</p>
        </div>
        <div class="flex gap-3">
            <a href="/pacotes" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs uppercase tracking-wider transition">
                Ver Todos os Pacotes &rarr;
            </a>
        </div>
    </div>

    <!-- Package Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($featuredPackages as $pkg): ?>
            <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-slate-200 transition-all duration-300 flex flex-col">
                <div class="relative h-60 overflow-hidden bg-slate-100">
                    <img src="<?= e($pkg['main_image'] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e') ?>" 
                         alt="<?= e($pkg['title']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                    
                    <div class="absolute top-4 left-4">
                        <span class="inline-block bg-navy/90 backdrop-blur-md text-white text-[11px] font-semibold uppercase px-3 py-1 rounded-full border border-white/20">
                            <?= e($pkg['destination_name'] ?? 'Destino') ?>
                        </span>
                    </div>

                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <p class="text-xs text-gold font-semibold uppercase tracking-wider">
                            <?= e($pkg['duration_days']) ?> Dias / <?= e($pkg['duration_nights']) ?> Noites
                        </p>
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
                                <?= $pkg['price_per_person'] ? money($pkg['price_per_person']) : 'Sob Consulta' ?>
                            </span>
                            <?php if ($pkg['price_per_person']): ?>
                                <span class="text-[10px] text-slate-500 block">por pessoa</span>
                            <?php endif; ?>
                        </div>
                        <a href="/pacotes/<?= e($pkg['slug']) ?>" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs uppercase tracking-wider group-hover:bg-gold group-hover:text-slate-950 transition">
                            Detalhes &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- POPULAR DESTINATIONS GRID -->
<section class="py-20 bg-slate-100/70 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gold">Descubra Novos Horizontes</span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 tracking-tight">Destinos Mais Procurados</h2>
            <p class="text-sm text-slate-600">Desde as águas cristalinas do Índico aos grandes centros económicos e culturais globais.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($destinations, 0, 4) as $d): ?>
                <a href="/destinos/<?= e($d['slug']) ?>" class="group relative rounded-3xl overflow-hidden h-80 shadow-md flex flex-col justify-end p-6 text-white">
                    <img src="<?= e($d['image_url'] ?? 'https://images.unsplash.com/photo-1544551763-46a013bb70d5') ?>" 
                         alt="<?= e($d['name']) ?>" 
                         class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
                    
                    <div class="relative z-10 space-y-1">
                        <span class="text-[10px] tracking-widest uppercase text-gold font-bold"><?= e($d['country']) ?></span>
                        <h3 class="text-2xl font-serif font-bold"><?= e($d['name']) ?></h3>
                        <p class="text-xs text-slate-300 line-clamp-1"><?= e($d['summary']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- SERVICES HIGHLIGHT SECTION -->
<section class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-gold">Soluções Completas de Turismo</span>
        <h2 class="text-3xl sm:text-4xl font-serif font-bold text-slate-900 tracking-tight">O Que Fazemos Por Si</h2>
        <p class="text-sm text-slate-600">Tratamos de cada detalhe da sua viagem com excelência, segurança e pontualidade.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl transition">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-gold flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            <h3 class="font-serif font-bold text-xl text-slate-900 mb-2">Passagens Aéreas</h3>
            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Parcerias diretas com as principais companhias que operam em Moçambique e no estrangeiro para garantir as rotas mais económicas e convenientes.
            </p>
            <a href="/servicos/passagens-aereas" class="text-xs font-bold text-navy hover:text-gold uppercase tracking-wider">Saber Mais &rarr;</a>
        </div>

        <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl transition">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-gold flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="font-serif font-bold text-xl text-slate-900 mb-2">Hotéis & Lodges</h3>
            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Resorts em ilhas paradisíacas, lodges de safari de luxo e alojamentos executivos nos principais centros urbanos com tarifas negociadas.
            </p>
            <a href="/servicos/hoteis-alojamento" class="text-xs font-bold text-navy hover:text-gold uppercase tracking-wider">Saber Mais &rarr;</a>
        </div>

        <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl transition">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-gold flex items-center justify-center mb-6">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h3 class="font-serif font-bold text-xl text-slate-900 mb-2">Assistência ao Viajante</h3>
            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Consultoria de vistos, seguro de saúde e bagagem, transferes privados de aeroporto e suporte em português e inglês durante toda a sua viagem.
            </p>
            <a href="/servicos/assistencia-viajante" class="text-xs font-bold text-navy hover:text-gold uppercase tracking-wider">Saber Mais &rarr;</a>
        </div>
    </div>
</section>

<!-- CALL TO ACTION BANNER -->
<section class="bg-navy text-white py-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <div class="max-w-5xl mx-auto text-center space-y-6 relative z-10">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-gold/20 text-gold-light border border-gold/40 text-xs font-bold uppercase tracking-wider">
            Zebra Tours Moçambique
        </span>
        <h2 class="text-3xl sm:text-5xl font-serif font-black text-white tracking-tight">
            Planeie a Sua Próxima Viagem Connosco
        </h2>
        <p class="text-slate-300 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
            Diga-nos onde quer ir e quando. A nossa equipa em Maputo prepara uma proposta detalhada com voos, estadias e transfers sem qualquer compromisso.
        </p>
        <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/cotacao" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-gold to-gold-dark text-slate-950 font-bold uppercase text-xs tracking-wider hover:brightness-110 active:scale-95 transition shadow-lg shadow-gold/25">
                Pedir Cotação Online Gratuita
            </a>
            <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20pretendo%20fazer%20uma%20consulta%20de%20viagem" target="_blank" rel="noopener noreferrer" class="px-8 py-3.5 rounded-xl bg-white/10 border border-white/20 text-white font-bold uppercase text-xs tracking-wider hover:bg-white/20 transition flex items-center justify-center gap-2">
                Falar Diretamente no WhatsApp (+258 85 104 1826)
            </a>
        </div>
    </div>
</section>

<!-- Tabs Script -->
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-gold', 'text-slate-900');
                b.classList.add('border-transparent', 'text-slate-400');
            });
            btn.classList.add('border-gold', 'text-slate-900');
            btn.classList.remove('border-transparent', 'text-slate-400');

            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            const targetId = btn.getAttribute('data-target');
            document.getElementById(targetId)?.classList.remove('hidden');
        });
    });
</script>
