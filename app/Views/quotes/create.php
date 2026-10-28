<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <span class="text-xs font-bold uppercase tracking-widest text-gold block">Consultoria Especializada</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">Pedido de Cotação de Viagem</h1>
        <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
            Indique-nos os seus planos de viagem. A nossa equipa desenhará a melhor proposta com voos, transferes, alojamentos e itinerários adaptados ao seu orçamento.
        </p>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200 space-y-8">
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg space-y-1">
                <p class="text-xs font-bold text-red-800 uppercase tracking-wider">Erros no preenchimento:</p>
                <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/cotacao" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <?= honeypot_field() ?>

            <!-- Package prefill badge if applicable -->
            <?php if (!empty($prefilledPackage)): ?>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider font-bold text-amber-800 block">Pacote Selecionado:</span>
                        <span class="text-sm font-bold font-serif text-slate-900"><?= e($prefilledPackage['title']) ?></span>
                    </div>
                    <input type="hidden" name="package_id" value="<?= e($prefilledPackage['id']) ?>">
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Serviço Pretendido *</label>
                    <select name="service_type" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                        <option value="Pacote Turístico Completo" <?= ($service === 'Pacote Turístico Completo' || $service === 'Pacote Turístico') ? 'selected' : '' ?>>Pacote Turístico Completo</option>
                        <option value="Passagens Aéreas" <?= $service === 'Passagens Aéreas' ? 'selected' : '' ?>>Emissão de Passagens Aéreas</option>
                        <option value="Reserva de Hotéis e Lodges" <?= $service === 'Reserva de Hotéis e Lodges' ? 'selected' : '' ?>>Reserva de Hotéis e Lodges</option>
                        <option value="Escapadinhas de Fim de Semana" <?= $service === 'Escapadinhas de Fim de Semana' ? 'selected' : '' ?>>Escapadinhas de Fim de Semana</option>
                        <option value="Viagens de Lazer e Negócios" <?= $service === 'Viagens de Lazer e Negócios' ? 'selected' : '' ?>>Viagens de Lazer e Negócios</option>
                        <option value="Assistência ao Viajante" <?= $service === 'Assistência ao Viajante' ? 'selected' : '' ?>>Assistência ao Viajante e Seguros</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Destino Pretendido</label>
                    <input type="text" name="destination" value="<?= e($destination) ?>" placeholder="Ex: Bazaruto, Lisboa, Gorongosa..." class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nome Completo do Responsável *</label>
                    <input type="text" name="customer_name" required value="<?= e($old['customer_name'] ?? '') ?>" placeholder="Como devemos chamá-lo?" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Endereço de Email</label>
                    <input type="email" name="customer_email" value="<?= e($old['customer_email'] ?? '') ?>" placeholder="Para envio da proposta em PDF" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Telefone / WhatsApp *</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-300 bg-slate-100 text-slate-500 text-xs font-bold">+258</span>
                        <input type="tel" name="customer_phone" required value="<?= e($old['customer_phone'] ?? '') ?>" placeholder="84 123 4567" class="w-full bg-slate-50 border border-slate-300 rounded-r-lg px-3.5 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Número de Viajantes</label>
                    <input type="number" name="travelers_count" min="1" value="<?= e($travelers ?: '1') ?>" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Datas Preferenciais</label>
                    <input type="text" name="preferred_dates" value="<?= e($dates) ?>" placeholder="Ex: 10/12/2026 a 17/12/2026" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-gold outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Detalhes Adicionais ou Requisitos Especiais</label>
                <textarea name="message" rows="4" placeholder="Indique tipos de quarto preferidos, classe de voo, restrições alimentares ou outros pedidos..." class="w-full bg-slate-50 border border-slate-300 rounded-lg p-4 text-sm focus:ring-2 focus:ring-gold outline-none"><?= e($old['message'] ?? '') ?></textarea>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 leading-relaxed">
                <strong>Nota de Confidencialidade e Transparência:</strong> O envio desta solicitação não implica cobrança nem confirmação imediata de lugares. Um consultor entrará em contacto com a proposta detalhada e opções de pagamento.
            </div>

            <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-gold to-gold-dark text-slate-950 font-bold uppercase text-xs tracking-wider shadow-lg hover:brightness-110 active:scale-95 transition">
                Enviar Pedido de Cotação
            </button>
        </form>
    </div>
</div>
