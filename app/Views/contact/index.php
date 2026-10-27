<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-7xl mx-auto space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-gold block">Atendimento Dedicado</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">Fale Connosco</h1>
        <p class="text-slate-300 max-w-2xl text-sm sm:text-base leading-relaxed">
            Tem alguma dúvida, deseja reservar uma viagem corporativa ou falar diretamente com a nossa equipa em Maputo? Estamos à sua disposição.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <!-- Contact Info Cards -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-gold flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-serif font-bold text-lg text-slate-900">Sede Principal</h3>
                <p class="text-xs text-slate-600 leading-relaxed"><?= e($siteSettings['location'] ?? 'Maputo, Moçambique') ?></p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-gold flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="font-serif font-bold text-lg text-slate-900">Telefone & WhatsApp</h3>
                <p class="text-xs text-slate-600">
                    <a href="tel:<?= e($siteSettings['phone'] ?? '+258851041826') ?>" class="hover:text-gold block font-semibold text-slate-900">
                        <?= e($siteSettings['phone'] ?? '+258 85 104 1826') ?>
                    </a>
                    <span>Horário: Seg a Sex, 08h00 - 18h00 &bull; Sáb, 08h30 - 13h00</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-3">
                <div class="w-10 h-10 rounded-lg bg-amber-50 text-gold flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-serif font-bold text-lg text-slate-900">Correio Eletrónico</h3>
                <p class="text-xs text-slate-600">
                    <a href="mailto:<?= e($siteSettings['email'] ?? 'info@zebratours.co.mz') ?>" class="hover:text-gold block font-semibold text-slate-900">
                        <?= e($siteSettings['email'] ?? 'info@zebratours.co.mz') ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-6">
            <?php if (!empty($successMessage)): ?>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm">
                    <?= e($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="p-4 rounded-xl bg-red-50 border border-red-300 text-red-800 text-xs space-y-1">
                    <p class="font-bold">Verifique os seguintes campos:</p>
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $e): ?>
                            <li><?= e($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/contactos" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nome Completo *</label>
                        <input type="text" name="name" required value="<?= e($old['name'] ?? '') ?>" placeholder="Seu nome" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email *</label>
                        <input type="email" name="email" required value="<?= e($old['email'] ?? '') ?>" placeholder="seuemail@exemplo.com" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Telefone</label>
                        <input type="tel" name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="+258 84 000 0000" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Assunto *</label>
                        <input type="text" name="subject" required value="<?= e($old['subject'] ?? '') ?>" placeholder="Ex: Parceria corporativa, informações de pacote..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mensagem *</label>
                    <textarea name="message" required rows="5" placeholder="Escreva a sua mensagem..." class="w-full bg-slate-50 border border-slate-300 rounded-lg p-4 text-sm focus:ring-2 focus:ring-gold outline-none"><?= e($old['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="py-3.5 px-8 rounded-xl bg-navy text-white font-bold uppercase text-xs tracking-wider hover:bg-gold hover:text-slate-950 transition">
                    Enviar Mensagem
                </button>
            </form>
        </div>

    </div>
</div>
