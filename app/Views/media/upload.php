<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Multimédia | CMS Zebra Tours Moçambique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#071629',
                        navy: {
                            800: '#0B203B',
                            900: '#071629',
                        },
                        gold: {
                            500: '#D4AF37',
                            600: '#AA8C2C',
                        },
                        ivory: '#FBFBFA'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F8F9FA] text-slate-800 font-sans min-h-screen flex flex-col antialiased">

    <!-- Header do CMS -->
    <header class="bg-[#071629] text-white border-b border-slate-800 sticky top-0 z-30 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#D4AF37] to-amber-600 flex items-center justify-center font-bold text-slate-900 shadow">
                    ZT
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight text-white flex items-center gap-2">
                        Zebra Tours Moçambique
                        <span class="text-[10px] bg-[#D4AF37]/20 text-[#D4AF37] border border-[#D4AF37]/30 px-2 py-0.5 rounded font-mono font-semibold">CMS PHP MVC</span>
                    </h1>
                    <p class="text-[11px] text-slate-400">Biblioteca Multimédia & Centro de Ativos Digitais</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="/media" class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-slate-700 hover:border-slate-500 hover:bg-slate-800/80 transition-colors text-slate-300 hover:text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Ver Biblioteca
                </a>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8">
        
        <!-- Breadcrumb & Título -->
        <div class="mb-6">
            <nav class="flex items-center text-xs text-slate-500 mb-2 gap-1.5">
                <a href="/" class="hover:text-slate-800">Início</a>
                <span>/</span>
                <a href="/media" class="hover:text-slate-800">Biblioteca Multimédia</a>
                <span>/</span>
                <span class="text-slate-700 font-medium">Novo Upload</span>
            </nav>
            <h2 class="text-2xl font-serif font-bold text-slate-900">Upload de Arquivos Multimédia</h2>
            <p class="text-sm text-slate-600 mt-1">
                Envie imagens de alta resolução, vídeos de apresentação, podcasts de roteiros ou catálogos e folhetos em PDF.
            </p>
        </div>

        <!-- Alerta de Sucesso -->
        <?php if (!empty($success)): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-3 shadow-sm animate-fade-in">
                <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="flex-1 text-sm">
                    <p class="font-bold text-emerald-950">Sucesso no Envio!</p>
                    <p class="text-emerald-800 mt-0.5"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($asset)): ?>
                        <div class="mt-3 pt-3 border-t border-emerald-200/80 flex items-center justify-between text-xs">
                            <span class="font-mono text-emerald-900"><?= htmlspecialchars($asset['original_name'], ENT_QUOTES, 'UTF-8') ?> (<?= round($asset['file_size'] / 1024, 1) ?> KB)</span>
                            <a href="<?= htmlspecialchars($asset['file_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="font-semibold text-emerald-800 underline hover:text-emerald-950">Visualizar Arquivo &rarr;</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Alertas de Erro -->
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm animate-fade-in">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="flex-1">
                        <p class="font-bold text-rose-950 text-sm">Não foi possível processar o envio:</p>
                        <ul class="list-disc list-inside mt-1.5 space-y-1 text-xs text-rose-800">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Card do Formulário de Upload -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <form action="/media/upload" method="POST" enctype="multipart/form-data" id="uploadForm" class="p-6 sm:p-8 space-y-6">
                
                <!-- Zona de Drag & Drop -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Ficheiro Multimédia <span class="text-rose-500">*</span>
                    </label>
                    
                    <div id="dropzone" class="border-2 border-dashed border-slate-300 hover:border-[#D4AF37] rounded-xl p-6 sm:p-8 text-center transition-all cursor-pointer bg-slate-50/50 hover:bg-amber-50/30 group">
                        <input type="file" name="media_file" id="fileInput" class="hidden" accept=".jpg,.jpeg,.png,.webp,.mp4,.mp3,.pdf" required>
                        
                        <div id="uploadPrompt" class="space-y-3">
                            <div class="w-12 h-12 mx-auto rounded-full bg-amber-100/80 text-[#AA8C2C] flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    <span class="text-blue-700 hover:underline">Clique para selecionar</span> ou arraste o arquivo até aqui
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Suporta Imagens (JPG, PNG, WEBP), Vídeo (MP4), Áudio (MP3) e Documentos (PDF) até 50 MB
                                </p>
                            </div>
                        </div>

                        <!-- Pré-visualização Dinâmica -->
                        <div id="filePreview" class="hidden space-y-3 pt-2">
                            <div id="previewContainer" class="max-h-48 max-w-sm mx-auto flex items-center justify-center overflow-hidden rounded-lg bg-slate-100 border border-slate-200">
                                <!-- Elemento gerado via JS: img, video ou ícone -->
                            </div>
                            <div class="text-xs text-slate-700">
                                <p id="fileName" class="font-bold text-slate-900"></p>
                                <p id="fileSize" class="text-slate-500"></p>
                            </div>
                            <button type="button" id="removeFileBtn" class="text-xs text-rose-600 hover:text-rose-800 font-medium underline">
                                Escolher outro ficheiro
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Grade de Metadados -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    
                    <!-- Título Editorial -->
                    <div>
                        <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Título Descritivo (Opcional)
                        </label>
                        <input type="text" name="title" id="title" 
                            placeholder="Ex: Costa de Vilankulos e Dhow Tradicional" 
                            value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/50 focus:border-[#D4AF37] transition-all">
                        <span class="text-[11px] text-slate-500 mt-1 block">Utilizado na gestão da biblioteca e identificação rápida.</span>
                    </div>

                    <!-- Categoria -->
                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Categoria do Ativo
                        </label>
                        <select name="category" id="category" 
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/50 focus:border-[#D4AF37] transition-all bg-white">
                            <option value="geral">Geral / Sem Categoria</option>
                            <option value="destinos" <?= ($old['category'] ?? '') === 'destinos' ? 'selected' : '' ?>>Destinos Turísticos</option>
                            <option value="pacotes" <?= ($old['category'] ?? '') === 'pacotes' ? 'selected' : '' ?>>Pacotes & Itinerários</option>
                            <option value="hero" <?= ($old['category'] ?? '') === 'hero' ? 'selected' : '' ?>>Banners & Hero Homepage</option>
                            <option value="documento" <?= ($old['category'] ?? '') === 'documento' ? 'selected' : '' ?>>Folhetos & Catálogos PDF</option>
                        </select>
                        <span class="text-[11px] text-slate-500 mt-1 block">Organiza os ativos para filtros na área de administração.</span>
                    </div>

                    <!-- Texto Alternativo (Alt Text / Acessibilidade & SEO) -->
                    <div class="md:col-span-2">
                        <label for="alt_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Texto Alternativo (Alt Text para SEO / Acessibilidade)
                        </label>
                        <input type="text" name="alt_text" id="alt_text" 
                            placeholder="Ex: Praia de areia branca com águas azuis turquesa no Arquipélago de Bazaruto"
                            value="<?= htmlspecialchars($old['alt_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/50 focus:border-[#D4AF37] transition-all">
                        <span class="text-[11px] text-slate-500 mt-1 block">Crucial para leitura de tela e indexação no Google Imagens.</span>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                    <a href="/media" class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                        &larr; Cancelar e Voltar
                    </a>
                    <button type="submit" id="submitBtn" 
                        class="px-6 py-2.5 bg-[#071629] hover:bg-[#0B203B] text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow hover:shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Efetuar Upload Seguro
                    </button>
                </div>

            </form>
        </div>

        <!-- Seção Informativa de Segurança da Aplicação -->
        <div class="mt-8 bg-slate-100 rounded-xl p-5 border border-slate-200/80 text-xs text-slate-600 space-y-2">
            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Medidas de Segurança Ativas neste Módulo:
            </div>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] text-slate-600 list-disc list-inside">
                <li>Validação do MIME type no servidor com <code class="bg-white px-1 py-0.5 rounded text-slate-800">finfo</code> (sem confiar no cliente).</li>
                <li>Geração de hash criptográfico único para evitar sobrescrita de arquivos.</li>
                <li>Diretório de uploads isolado com <code class="bg-white px-1 py-0.5 rounded text-slate-800">.htaccess</code> bloqueando scripts PHP.</li>
                <li>Sanitização de metadados no Model com consultas parametrizadas PDO.</li>
            </ul>
        </div>

    </main>

    <!-- Script de Pré-visualização e Drag & Drop -->
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const filePreview = document.getElementById('filePreview');
        const previewContainer = document.getElementById('previewContainer');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFileBtn = document.getElementById('removeFileBtn');

        dropzone.addEventListener('click', (e) => {
            if (e.target !== removeFileBtn) {
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-[#D4AF37]', 'bg-amber-50/50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-[#D4AF37]', 'bg-amber-50/50');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                fileInput.files = files;
                handleFileSelection(files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                handleFileSelection(fileInput.files[0]);
            }
        });

        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            uploadPrompt.classList.remove('hidden');
            filePreview.classList.add('hidden');
            previewContainer.innerHTML = '';
        });

        function handleFileSelection(file) {
            uploadPrompt.classList.add('hidden');
            filePreview.classList.remove('hidden');
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            previewContainer.innerHTML = '';

            const type = file.type;

            if (type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'max-h-48 object-cover rounded';
                previewContainer.appendChild(img);
            } else if (type.startsWith('video/')) {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.className = 'max-h-48 rounded';
                previewContainer.appendChild(video);
            } else if (type.startsWith('audio/')) {
                const audio = document.createElement('audio');
                audio.src = URL.createObjectURL(file);
                audio.controls = true;
                previewContainer.appendChild(audio);
            } else if (type === 'application/pdf') {
                previewContainer.innerHTML = `
                    <div class="p-6 text-center text-rose-600">
                        <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                        <span class="font-bold text-xs uppercase tracking-wider block mt-2 text-slate-800">Documento PDF</span>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>
