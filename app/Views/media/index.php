<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Multimédia | CMS Zebra Tours Moçambique</title>
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
                        navy: { 800: '#0B203B', 900: '#071629' },
                        gold: { 500: '#D4AF37', 600: '#AA8C2C' },
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
                <a href="/media/upload" class="text-xs font-bold px-3.5 py-2 rounded-lg bg-[#D4AF37] hover:bg-[#AA8C2C] text-[#071629] transition-all flex items-center gap-1.5 shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Novo Upload
                </a>
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Flash Message -->
        <?php if (!empty($flashMessage)): ?>
            <div class="mb-6 p-4 rounded-xl <?= $flashMessage['type'] === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' ?> border text-sm flex items-center gap-2 shadow-sm">
                <span><?= htmlspecialchars($flashMessage['message'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <!-- Título e Filtros -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-serif font-bold text-slate-900">Biblioteca Multimédia</h2>
                <p class="text-xs text-slate-500 mt-1">Total de <?= (int) $totalItems ?> ativos registados no acervo.</p>
            </div>
            
            <!-- Barra de Filtros -->
            <div class="flex flex-wrap items-center gap-1.5 text-xs bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
                <?php 
                $categories = [
                    'todos' => 'Todos',
                    'imagem' => 'Imagens',
                    'destinos' => 'Destinos',
                    'pacotes' => 'Pacotes',
                    'hero' => 'Banners/Hero',
                    'video' => 'Vídeos',
                    'audio' => 'Áudios',
                    'documento' => 'PDFs',
                ];
                foreach ($categories as $catKey => $catLabel):
                    $isActive = ($currentCategory === $catKey) || ($catKey === 'todos' && ($currentCategory === '' || $currentCategory === 'todos'));
                ?>
                    <a href="/media<?= $catKey !== 'todos' ? '?categoria=' . $catKey : '' ?>" 
                       class="px-3 py-1.5 rounded-lg font-semibold transition-colors <?= $isActive ? 'bg-[#071629] text-white' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <?= $catLabel ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Grade de Arquivos -->
        <?php if (empty($items)): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Nenhum ativo encontrado</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">Comece por carregar imagens, vídeos ou documentos PDF na sua biblioteca.</p>
                <a href="/media/upload" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#071629] text-[#D4AF37] font-bold text-xs uppercase tracking-wider rounded-lg shadow hover:bg-slate-800 transition-colors">
                    Fazer Primeiro Upload
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php foreach ($items as $item): ?>
                    <div class="group bg-white rounded-xl border border-slate-200 hover:border-[#D4AF37]/50 shadow-sm hover:shadow transition-all overflow-hidden flex flex-col">
                        <!-- Preview do Ficheiro -->
                        <div class="h-32 bg-slate-100 flex items-center justify-center overflow-hidden relative">
                            <?php if (str_starts_with((string)$item['mime_type'], 'image/')): ?>
                                <img src="<?= htmlspecialchars((string)$item['file_path'], ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars((string)($item['alt_text'] ?? $item['title']), ENT_QUOTES, 'UTF-8') ?>" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            <?php elseif (str_starts_with((string)$item['mime_type'], 'video/')): ?>
                                <div class="text-indigo-600 flex flex-col items-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-[9px] font-bold mt-1 uppercase">Vídeo MP4</span>
                                </div>
                            <?php elseif (str_starts_with((string)$item['mime_type'], 'audio/')): ?>
                                <div class="text-amber-600 flex flex-col items-center">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                    <span class="text-[9px] font-bold mt-1 uppercase">Áudio MP3</span>
                                </div>
                            <?php else: ?>
                                <div class="text-rose-600 flex flex-col items-center">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                    <span class="text-[9px] font-bold mt-1 uppercase">PDF</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge de Categoria -->
                            <span class="absolute top-1.5 left-1.5 text-[9px] font-bold uppercase tracking-wider bg-slate-900/80 backdrop-blur-sm text-white px-1.5 py-0.5 rounded">
                                <?= htmlspecialchars((string)($item['category'] ?? 'geral'), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <!-- Detalhes do Ficheiro -->
                        <div class="p-3 flex-1 flex flex-col justify-between text-xs">
                            <div>
                                <p class="font-bold text-slate-900 truncate" title="<?= htmlspecialchars((string)($item['title'] ?? $item['original_name']), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars((string)($item['title'] ?: $item['original_name']), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="text-[10px] text-slate-500 truncate mt-0.5">
                                    <?= round(((int)$item['file_size']) / 1024, 1) ?> KB &bull; <?= htmlspecialchars((string)$item['mime_type'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            </div>

                            <!-- Ações Rápidas -->
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                <a href="<?= htmlspecialchars((string)$item['file_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-blue-600 hover:text-blue-800 font-semibold" title="Abrir arquivo">
                                    Ver
                                </a>
                                <button type="button" onclick="navigator.clipboard.writeText('<?= htmlspecialchars((string)$item['file_path'], ENT_QUOTES, 'UTF-8') ?>'); alert('URL copiada para a área de transferência!');" class="text-slate-500 hover:text-slate-800" title="Copiar URL">
                                    Copiar
                                </button>
                                <form action="/media/delete" method="POST" onsubmit="return confirm('Tem a certeza que deseja eliminar este ficheiro?');" class="inline">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold" title="Eliminar arquivo">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>
