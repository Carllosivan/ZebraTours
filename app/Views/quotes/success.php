<section class="min-h-[70vh] flex items-center justify-center py-20 px-4">
    <div class="max-w-lg w-full bg-white rounded-3xl p-8 sm:p-12 shadow-2xl border border-gold/30 text-center space-y-6">
        
        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto text-emerald-600 border border-emerald-200 shadow-sm">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-gold block">Pedido Registado</span>
            <h1 class="text-3xl font-serif font-bold text-slate-900">Obrigado pela Sua Solicitação!</h1>
            <p class="text-slate-600 text-sm">
                Olá, <strong><?= e($name) ?></strong>. O seu pedido de cotação foi registado na nossa plataforma sob o código de referência:
            </p>
        </div>

        <!-- Reference Code Display -->
        <div class="bg-slate-50 border-2 border-dashed border-gold/60 p-4 rounded-2xl">
            <span class="text-xs text-slate-500 uppercase tracking-widest block font-medium">Código de Acompanhamento</span>
            <span class="text-2xl sm:text-3xl font-mono font-bold text-navy select-all tracking-wider block mt-1">
                <?= e($ref) ?>
            </span>
        </div>

        <p class="text-xs text-slate-500 leading-relaxed">
            A equipa da Zebra Tours Moçambique está a analisar a disponibilidade de voos e hotéis. Entraremos em contacto brevemente via WhatsApp ou Email.
        </p>

        <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <a href="https://wa.me/258851041826?text=Olá,%20acabei%20de%20submeter%20o%20pedido%20de%20cotação%20com%20a%20referência%20<?= urlencode($ref) ?>" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="flex-1 py-3 px-4 rounded-xl bg-emerald-600 text-white font-bold uppercase text-xs tracking-wider hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                Agilizar no WhatsApp
            </a>
            <a href="/" class="py-3 px-6 rounded-xl border border-slate-300 text-slate-700 font-bold uppercase text-xs tracking-wider hover:bg-slate-50 transition">
                Voltar ao Início
            </a>
        </div>
    </div>
</section>
