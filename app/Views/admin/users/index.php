<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-slate-900">Gestão de Utilizadores</h1>
            <p class="text-xs text-slate-500 mt-0.5">Controlo de acessos, perfis e credenciais de segurança do CMS</p>
        </div>
        <button onclick="document.getElementById('modal-new-user').classList.remove('hidden')" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Utilizador
        </button>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs">
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="p-4 rounded-xl bg-red-50 border border-red-300 text-red-800 text-xs">
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-500 border-b border-slate-200 uppercase font-semibold">
                <tr>
                    <th class="p-4">Utilizador</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Perfil</th>
                    <th class="p-4">Estado</th>
                    <th class="p-4 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 font-bold text-slate-900"><?= e($u['name']) ?></td>
                        <td class="p-4 text-slate-600 font-mono"><?= e($u['email']) ?></td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $u['role'] === 'superadmin' ? 'bg-purple-100 text-purple-800' : ($u['role'] === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') ?>">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $u['status'] === 'ativo' ? 'bg-emerald-100 text-emerald-800' : ($u['status'] === 'bloqueado' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= e($u['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <form action="/admin/utilizadores/<?= e($u['id']) ?>/estado" method="POST" class="inline-flex items-center gap-2">
                                <?= csrf_field() ?>
                                <select name="status" class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                                    <option value="ativo" <?= $u['status'] === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                    <option value="inativo" <?= $u['status'] === 'inativo' ? 'selected' : '' ?>>Inativo</option>
                                    <option value="bloqueado" <?= $u['status'] === 'bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
                                </select>
                                <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-lg text-xs">
                                    Gravar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Novo Utilizador -->
    <div id="modal-new-user" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-200 space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="font-serif font-bold text-lg text-slate-900">Novo Utilizador</h3>
                <button onclick="document.getElementById('modal-new-user').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="/admin/utilizadores" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nome Completo</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Institucional</label>
                    <input type="email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Perfil / Papel</label>
                    <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                        <option value="editor">Editor (Pedidos e Multimédia)</option>
                        <option value="admin">Administrador (Gestão Integral)</option>
                        <option value="superadmin">Superadministrador</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Palavra-passe (mín. 12 carateres)</label>
                    <input type="password" name="password" minlength="12" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs">
                </div>
                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-new-user').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold">Criar Utilizador</button>
                </div>
            </form>
        </div>
    </div>
</div>
