<!DOCTYPE html>
<html lang="pt" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Painel de Controlo | Zebra Tours CMS') ?></title>
    <link rel="icon" type="image/svg+xml" href="/assets/zebra-tours-logo.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#071629',
                            dark: '#040D18',
                            light: '#0D2646'
                        },
                        gold: {
                            DEFAULT: '#C59B27',
                            light: '#D4AF37',
                            dark: '#A6801C'
                        }
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex overflow-hidden bg-slate-950">

    <!-- Admin Sidebar -->
    <aside class="w-64 bg-navy-dark border-r border-slate-800 flex flex-col shrink-0">
        <!-- Logo -->
        <div class="h-18 px-5 flex items-center gap-3 border-b border-slate-800">
            <div class="bg-white px-2.5 py-1 rounded-xl border border-gold/40 flex items-center justify-center shrink-0 shadow-md">
                <img src="/assets/zebra-tours-logo.svg" alt="Zebra Tours" class="h-8 w-auto object-contain block" onerror="this.onerror=null; this.src='/assets/aistudio/ZebraTours_Logo_c3eb31fa.svg'">
            </div>
            <div>
                <span class="font-black text-sm tracking-wide text-white block">ZEBRA TOURS</span>
                <span class="text-[9px] text-gold uppercase tracking-widest block font-bold">MOÇAMBIQUE &bull; CMS</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-sm">
            <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dashboard</span>
            </a>
            <a href="/admin/pedidos" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span>Pedidos & CRM</span>
            </a>
            <a href="/admin/pacotes" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Pacotes Turísticos</span>
            </a>
            <a href="/admin/destinos" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Destinos</span>
            </a>
            <a href="/admin/media" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Multimédia</span>
            </a>
            <a href="/admin/paginas" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Páginas CMS</span>
            </a>
            <a href="/admin/banners" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Banners</span>
            </a>
            <a href="/admin/definicoes" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition">
                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Definições</span>
            </a>
        </nav>

        <!-- User / Logout -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50 space-y-2">
            <a href="/" target="_blank" class="flex items-center gap-2 text-xs text-slate-400 hover:text-gold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Ver Website Público</span>
            </a>
            <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                <div class="text-xs">
                    <span class="block text-white font-medium"><?= e($currentUser['name'] ?? 'Administrador') ?></span>
                    <span class="text-slate-500 uppercase text-[10px]"><?= e($currentUser['role'] ?? 'Admin') ?></span>
                </div>
                <form action="/admin/logout" method="POST">
                    <?= csrf_field() ?>
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition" title="Terminar Sessão">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Header Bar -->
        <header class="h-16 bg-slate-900 border-b border-slate-800 px-8 flex items-center justify-between shrink-0">
            <h1 class="text-lg font-semibold text-white"><?= e($pageTitle ?? 'Painel de Gestão') ?></h1>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-800 px-3 py-1 rounded-full font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Base de Dados SQLite Ativa
                </span>
            </div>
        </header>

        <!-- Flash messages -->
        <?php if (!empty($successMessage)): ?>
            <div class="bg-emerald-900/60 border-l-4 border-emerald-500 text-emerald-200 px-6 py-3 text-sm flex items-center justify-between">
                <span><?= e($successMessage) ?></span>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="bg-red-900/60 border-l-4 border-red-500 text-red-200 px-6 py-3 text-sm flex items-center justify-between">
                <span><?= e($errorMessage) ?></span>
            </div>
        <?php endif; ?>

        <!-- Dynamic Page Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <?= $content ?>
        </main>
    </div>

</body>
</html>
