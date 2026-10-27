<div class="max-w-3xl space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white font-serif">Definições da Agência de Viagens</h2>
        <p class="text-xs text-slate-400">Configure os dados de contacto, licença turística e moeda padrão visíveis em todo o portal.</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow">
        <form action="/admin/definicoes" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nome Comercial da Agência</label>
                    <input type="text" name="company_name" value="<?= e($settings['company_name'] ?? 'Zebra Tours Moçambique') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Moeda Principal</label>
                    <input type="text" name="currency" value="<?= e($settings['currency'] ?? 'MT') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Telefone Principal</label>
                    <input type="text" name="phone" value="<?= e($settings['phone'] ?? '+258 85 104 1826') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Contacto WhatsApp Direto</label>
                    <input type="text" name="whatsapp" value="<?= e($settings['whatsapp'] ?? '+258 85 104 1826') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Institucional</label>
                    <input type="email" name="email" value="<?= e($settings['email'] ?? 'info@zebratours.co.mz') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Localização / Cidade</label>
                    <input type="text" name="location" value="<?= e($settings['location'] ?? 'Maputo, Moçambique') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Licença / Registo Oficial</label>
                <input type="text" name="agency_license" value="<?= e($settings['agency_license'] ?? 'Registo Nacional de Agências de Viagens - Moçambique') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Horário de Funcionamento</label>
                <input type="text" name="opening_hours" value="<?= e($settings['opening_hours'] ?? 'Segunda a Sexta: 08h00 - 18h00 | Sábado: 08h30 - 13h00') ?>" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
            </div>

            <button type="submit" class="py-3 px-8 rounded-xl bg-gold text-slate-950 font-bold uppercase text-xs tracking-wider hover:brightness-110 active:scale-95 transition">
                Atualizar Definições
            </button>
        </form>
    </div>
</div>
