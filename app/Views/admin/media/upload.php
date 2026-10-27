<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white font-serif">Carregar Ficheiro para a Biblioteca</h2>
        <p class="text-xs text-slate-400">Adicione novas imagens via upload direto do dispositivo ou através de URL web.</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow space-y-6">
        <form action="/admin/media/upload" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ficheiro de Imagem do Disco</label>
                <input type="file" name="media_file" accept="image/*" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gold file:text-slate-950 hover:file:brightness-110">
            </div>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-4 text-slate-500 text-xs uppercase tracking-widest font-bold">OU insira um URL web</span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">URL da Imagem (Unsplash, CDN, etc.)</label>
                <input type="url" name="url" placeholder="https://images.unsplash.com/..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Título do Recurso</label>
                    <input type="text" name="title" placeholder="Ex: Praia de Benguerra" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Categoria Multimédia</label>
                    <select name="category" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
                        <option value="pacotes">Pacotes Turísticos</option>
                        <option value="destinos">Destinos</option>
                        <option value="banners">Banners e Destaques</option>
                        <option value="geral">Geral</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Texto Alternativo (Alt Text)</label>
                <input type="text" name="alt_text" placeholder="Descrição acessível da imagem..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-gold outline-none">
            </div>

            <div class="pt-2 flex items-center justify-between">
                <a href="/admin/media" class="text-xs text-slate-400 hover:text-white">Cancelar e Voltar</a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-gold text-slate-950 font-bold uppercase text-xs tracking-wider hover:brightness-110 active:scale-95 transition">
                    Guardar na Biblioteca
                </button>
            </div>
        </form>
    </div>
</div>
