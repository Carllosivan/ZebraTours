<div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl space-y-8">
    <div class="text-center space-y-3">
        <div class="bg-white px-4 py-2 rounded-2xl border border-gold/40 mx-auto inline-flex items-center justify-center shadow-lg shadow-gold/10">
            <img src="/assets/zebra-tours-logo.svg" alt="Zebra Tours" class="h-12 w-auto object-contain block" onerror="this.onerror=null; this.src='/assets/aistudio/ZebraTours_Logo_c3eb31fa.svg'">
        </div>
        <div>
            <h1 class="text-2xl font-serif font-black text-white tracking-wide">ZEBRA TOURS</h1>
            <span class="text-[10px] tracking-[0.25em] uppercase text-gold font-bold block">MOÇAMBIQUE</span>
            <span class="text-[11px] text-amber-200/90 font-serif italic block mt-0.5">Mais do que viagens, experiências!</span>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="p-4 rounded-xl bg-red-950/60 border border-red-800 text-red-300 text-xs text-center">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form action="/admin/login" method="POST" class="space-y-5">
        <?= csrf_field() ?>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Institucional</label>
            <input type="email" name="email" required autocomplete="username" placeholder="nome@zebratours.co.mz" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold outline-none">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Palavra-passe</label>
            <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••••••" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold outline-none">
        </div>

        <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-gold to-gold-dark text-slate-950 font-bold uppercase text-xs tracking-wider shadow-lg hover:brightness-110 active:scale-95 transition">
            Iniciar Sessão no Painel
        </button>
    </form>

    <div class="pt-4 border-t border-slate-800 text-center">
        <a href="/" class="text-xs text-slate-500 hover:text-gold transition">
            &larr; Voltar à página principal da Zebra Tours
        </a>
    </div>
</div>
