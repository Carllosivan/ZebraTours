<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Zebra Tours Moçambique | Agência Oficial de Viagens e Turismo') ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'Website oficial e portal de viagens da Zebra Tours Moçambique. Passagens aéreas, alojamento, pacotes turísticos e assistência ao viajante.') ?>">
    <link rel="icon" type="image/svg+xml" href="/assets/zebra-tours-logo.svg">
    <meta property="og:title" content="<?= e($pageTitle ?? 'Zebra Tours Moçambique') ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? 'Website oficial e portal de viagens da Zebra Tours Moçambique.') ?>">
    <meta property="og:type" content="website">

    <!-- Tailwind CSS CDN with custom config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            DEFAULT: '#071629',
                            dark: '#030A14',
                            light: '#0E2442',
                            accent: '#1A365D'
                        },
                        gold: {
                            DEFAULT: '#C59B27',
                            light: '#E5B942',
                            dark: '#9E7B1D',
                            warm: '#F5C842'
                        },
                        ivory: {
                            DEFAULT: '#FAF9F6',
                            warm: '#F4F1EA'
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;0,900;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FAF9F6; color: #0F172A; }
        .font-serif { font-family: 'Playfair Display', Georgia, serif; }
        .bg-pattern { background-image: radial-gradient(rgba(197, 155, 39, 0.12) 1px, transparent 0); background-size: 24px 24px; }
        .glass-card { background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); }
        .glass-dark { background: rgba(7, 22, 41, 0.94); backdrop-filter: blur(16px); }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#FAF9F6] text-slate-800 antialiased selection:bg-amber-100 selection:text-amber-900">

    <!-- Top Announcement & Contact Bar -->
    <div class="bg-navy-dark text-slate-300 text-xs py-2.5 px-4 border-b border-navy-light/80">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center gap-2 text-gold-light font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span data-i18n="topbar.desk">Atendimento Oficial em Maputo, Moçambique</span>
                </span>
                <span class="hidden md:inline text-slate-600">•</span>
                <span class="hidden md:inline text-slate-400 font-medium" data-i18n="topbar.license">Licença Turística Nacional</span>
                <span class="hidden lg:inline text-slate-600">•</span>
                <span class="hidden lg:inline text-amber-200/90 font-serif italic" data-i18n="topbar.slogan">"Mais do que viagens, experiências!"</span>
            </div>
            <div class="flex items-center gap-5">
                <a href="tel:<?= e($siteSettings['phone'] ?? '+258851041826') ?>" class="hover:text-gold transition flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span><?= e($siteSettings['phone'] ?? '+258 85 104 1826') ?></span>
                </a>
                <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20gostaria%20de%20informa%C3%A7%C3%B5es%20sobre%20pacotes%20e%20passagens%20a%C3%A9reas" target="_blank" rel="noopener noreferrer" class="bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600/30 px-3 py-1 rounded-full border border-emerald-500/40 transition flex items-center gap-1.5 font-semibold">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.101.005.242-.038.375.281.145.347.491 1.2.534 1.288.043.088.072.191.014.307-.058.116-.087.188-.173.289l-.26.302c-.087.086-.177.18-.076.353.101.173.45 1.087 1.201 1.758.966.862 1.78 1.131 2.032 1.258.252.126.399.111.547-.058.147-.169.635-.741.805-.995.17-.254.341-.212.571-.127.23.085 1.458.687 1.71.813.252.126.42.188.481.293.061.105.061.609-.083 1.014z"/></svg>
                    <span data-i18n="topbar.whatsapp">WhatsApp Direto</span>
                </a>
                <a href="/admin/login" class="text-slate-400 hover:text-white transition text-[11px]" data-i18n="topbar.admin">Área Restrita</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-40 bg-navy/95 backdrop-blur-md border-b border-navy-light text-white shadow-xl transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 sm:h-22 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="/" class="flex items-center group focus:outline-none" aria-label="Zebra Tours Moçambique">
                <!-- Authentic Zebra Tours Logo Badge -->
                <div class="bg-white px-3.5 py-1.5 rounded-xl shadow-lg border border-amber-300/40 flex items-center justify-center transition-all duration-300 group-hover:scale-105 group-hover:shadow-amber-500/20">
                    <img src="/assets/zebra-tours-logo.svg" alt="Zebra Tours Moçambique" class="h-10 sm:h-12 w-auto object-contain block" onerror="this.onerror=null; this.src='/assets/aistudio/ZebraTours_Logo_c3eb31fa.svg'">
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-7 text-sm font-semibold tracking-wide">
                <a href="/" data-i18n="nav.home" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Início</a>
                <a href="/destinos" data-i18n="nav.destinations" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Destinos</a>
                <a href="/pacotes" data-i18n="nav.packages" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Pacotes Turísticos</a>
                <a href="/servicos" data-i18n="nav.services" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Serviços</a>
                <a href="/sobre-nos" data-i18n="nav.about" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Sobre Nós</a>
                <a href="/contactos" data-i18n="nav.contact" class="text-slate-200 hover:text-gold transition py-1 border-b-2 border-transparent hover:border-gold">Contactos</a>
            </nav>

            <!-- CTA Actions & Language Switcher -->
            <div class="hidden sm:flex items-center gap-3">
                <!-- Language Switcher (Desktop) -->
                <div class="inline-flex items-center bg-navy-light/90 p-1 rounded-xl border border-navy-accent shadow-inner" role="group" aria-label="Seletor de Idioma">
                    <button type="button" 
                            id="langBtnPt" 
                            onclick="window.switchZebraLang('pt')" 
                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-gradient-to-r from-gold via-gold-light to-gold-dark text-slate-950 shadow-sm"
                            title="Português">
                        <span>🇲🇿</span>
                        <span>PT</span>
                    </button>
                    <button type="button" 
                            id="langBtnEn" 
                            onclick="window.switchZebraLang('en')" 
                            class="px-2.5 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all flex items-center gap-1.5"
                            title="English">
                        <span>🇬🇧</span>
                        <span>EN</span>
                    </button>
                </div>

                <a href="/cotacao" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-gold via-gold-light to-gold-dark text-slate-950 font-bold text-xs uppercase tracking-wider shadow-lg shadow-gold/25 hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span data-i18n="nav.quote">Pedir Cotação</span>
                </a>
            </div>

            <!-- Mobile Controls -->
            <div class="flex items-center gap-2 lg:hidden">
                <!-- Language Switcher (Mobile Header) -->
                <div class="inline-flex sm:hidden items-center bg-navy-light/90 p-0.5 rounded-lg border border-navy-accent text-[11px]" role="group">
                    <button type="button" onclick="window.switchZebraLang('pt')" id="mobileHeaderLangPt" class="px-2 py-0.5 rounded font-bold bg-gold text-slate-950">PT</button>
                    <button type="button" onclick="window.switchZebraLang('en')" id="mobileHeaderLangEn" class="px-2 py-0.5 rounded font-medium text-slate-300">EN</button>
                </div>

                <button id="mobileMenuBtn" aria-label="Abrir Menu" class="p-2 text-slate-300 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-navy-dark border-t border-navy-light px-4 pt-3 pb-6 space-y-3">
            <!-- Language Switcher in Mobile Drawer -->
            <div class="pb-3 border-b border-navy-light flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400" data-i18n="lang.select">Idioma / Language:</span>
                <div class="inline-flex items-center bg-navy-light p-1 rounded-xl border border-navy-accent">
                    <button type="button" onclick="window.switchZebraLang('pt')" id="drawerLangPt" class="px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-gold text-slate-950">
                        <span>🇲🇿</span>
                        <span>Português</span>
                    </button>
                    <button type="button" onclick="window.switchZebraLang('en')" id="drawerLangEn" class="px-3 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition flex items-center gap-1.5">
                        <span>🇬🇧</span>
                        <span>English</span>
                    </button>
                </div>
            </div>

            <a href="/" data-i18n="nav.home" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Início</a>
            <a href="/destinos" data-i18n="nav.destinations" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Destinos</a>
            <a href="/pacotes" data-i18n="nav.packages" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Pacotes Turísticos</a>
            <a href="/servicos" data-i18n="nav.services" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Serviços</a>
            <a href="/sobre-nos" data-i18n="nav.about" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Sobre Nós</a>
            <a href="/contactos" data-i18n="nav.contact" class="block px-3 py-2.5 rounded-lg text-base font-medium text-white hover:bg-navy-light">Contactos</a>
            <div class="pt-2">
                <a href="/cotacao" class="w-full text-center block px-4 py-3 rounded-xl bg-gold text-slate-950 font-bold uppercase text-xs tracking-wider shadow-md">
                    <span data-i18n="nav.quote_full">Pedir Cotação de Viagem</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Floating WhatsApp Action Button -->
    <a href="https://wa.me/258851041826?text=Ol%C3%A1%20Zebra%20Tours,%20gostaria%20de%20informa%C3%A7%C3%B5es%20sobre%20as%20campanhas%20promocionais" 
       target="_blank" 
       rel="noopener noreferrer" 
       title="Falar no WhatsApp"
       class="fixed bottom-6 right-6 z-50 bg-[#25D366] text-white p-4 rounded-full shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center group">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.101.005.242-.038.375.281.145.347.491 1.2.534 1.288.043.088.072.191.014.307-.058.116-.087.188-.173.289l-.26.302c-.087.086-.177.18-.076.353.101.173.45 1.087 1.201 1.758.966.862 1.78 1.131 2.032 1.258.252.126.399.111.547-.058.147-.169.635-.741.805-.995.17-.254.341-.212.571-.127.23.085 1.458.687 1.71.813.252.126.42.188.481.293.061.105.061.609-.083 1.014z"/></svg>
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-300 ease-in-out font-bold text-xs uppercase pl-0 group-hover:pl-2">Falar no WhatsApp</span>
    </a>

    <!-- Footer -->
    <footer class="bg-navy text-slate-300 pt-16 pb-8 border-t border-navy-light mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <!-- Col 1: Brand & Identity -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-white px-3.5 py-1.5 rounded-xl border border-amber-300/40 flex items-center justify-center shadow-lg">
                            <img src="/assets/zebra-tours-logo.svg" alt="Zebra Tours Moçambique" class="h-12 w-auto object-contain block" onerror="this.onerror=null; this.src='/assets/aistudio/ZebraTours_Logo_c3eb31fa.svg'">
                        </div>
                        <div class="flex flex-col">
                            <span class="font-serif font-black text-white text-lg tracking-wider">ZEBRA TOURS</span>
                            <span class="text-[9px] tracking-[0.25em] uppercase text-gold font-bold">MOÇAMBIQUE</span>
                            <span class="text-[10px] text-amber-200/90 font-serif italic">Mais do que viagens, experiências!</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        A sua agência moçambicana de viagens e turismo de referência. Especialistas em criar memórias inesquecíveis, passagens aos melhores preços e turismo de excelência.
                    </p>
                    <div class="text-xs text-gold-muted space-y-1 pt-2">
                        <p class="font-semibold text-slate-300">Licença Turística:</p>
                        <p><?= e($siteSettings['agency_license'] ?? 'Registo Nacional de Agências de Viagens - Moçambique') ?></p>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gold mb-4">Navegação Rápida</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/" class="hover:text-gold transition">Página Inicial</a></li>
                        <li><a href="/destinos" class="hover:text-gold transition">Destinos Populares</a></li>
                        <li><a href="/pacotes" class="hover:text-gold transition">Pacotes Turísticos</a></li>
                        <li><a href="/servicos" class="hover:text-gold transition">Todos os Serviços</a></li>
                        <li><a href="/cotacao" class="hover:text-gold transition">Pedir Cotação Online</a></li>
                        <li><a href="/sobre-nos" class="hover:text-gold transition">Sobre a Zebra Tours</a></li>
                    </ul>
                </div>

                <!-- Col 3: Services -->
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gold mb-4">Serviços Oficiais</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="/servicos/passagens-aereas" class="hover:text-gold transition">Emissão de Passagens Aéreas</a></li>
                        <li><a href="/servicos/hoteis-alojamento" class="hover:text-gold transition">Hotéis & Lodges Exclusivos</a></li>
                        <li><a href="/servicos/escapadinhas" class="hover:text-gold transition">Escapadinhas de Fim de Semana</a></li>
                        <li><a href="/servicos/viagens-lazer-negocios" class="hover:text-gold transition">Viagens Corporativas & Lazer</a></li>
                        <li><a href="/servicos/assistencia-viajante" class="hover:text-gold transition">Assistência & Seguros de Viagem</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contacts -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gold mb-4">Fale Connosco</h3>
                    <div class="text-sm space-y-3 text-slate-300">
                        <p class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-gold shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?= e($siteSettings['location'] ?? 'Maputo, Moçambique') ?></span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:<?= e($siteSettings['phone'] ?? '+258851041826') ?>" class="hover:text-white"><?= e($siteSettings['phone'] ?? '+258 85 104 1826') ?></a>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-gold shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:<?= e($siteSettings['email'] ?? 'info@zebratours.co.mz') ?>" class="hover:text-white"><?= e($siteSettings['email'] ?? 'info@zebratours.co.mz') ?></a>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Bottom Disclaimer & Copyright -->
            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <p>&copy; <?= date('Y') ?> Zebra Tours Moçambique. Todos os direitos reservados.</p>
                <div class="flex items-center gap-6">
                    <a href="/faq" class="hover:text-gold transition">FAQ</a>
                    <a href="/termos" class="hover:text-gold transition">Termos & Condições</a>
                    <a href="/privacidade" class="hover:text-gold transition">Privacidade</a>
                    <a href="/admin/login" class="text-slate-500 hover:text-slate-300 transition">Gestão CMS</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Language switcher & translations engine -->
    <script>
        const zebraTranslations = {
            pt: {
                "topbar.desk": "Atendimento Oficial em Maputo, Moçambique",
                "topbar.license": "Licença Turística Nacional",
                "topbar.slogan": "\"Mais do que viagens, experiências!\"",
                "topbar.whatsapp": "WhatsApp Direto",
                "topbar.admin": "Área Restrita",
                "nav.home": "Início",
                "nav.destinations": "Destinos",
                "nav.packages": "Pacotes Turísticos",
                "nav.services": "Serviços",
                "nav.about": "Sobre Nós",
                "nav.contact": "Contactos",
                "nav.quote": "Pedir Cotação",
                "nav.quote_full": "Pedir Cotação de Viagem",
                "lang.select": "Idioma / Language:",
                "hero.badge": "Agência & Consultoria de Viagens em Moçambique",
                "hero.title": "O mundo espera por si.",
                "hero.subtitle": "Descubra novos destinos com pacotes completos, tarifas aéreas negociadas e apoio personalizado em cada etapa.",
                "hero.tab_packages": "Pacotes Turísticos",
                "hero.tab_flights": "Passagens Aéreas",
                "hero.tab_hotels": "Hotéis & Lodges",
                "hero.dest_label": "Destino Desejado",
                "hero.all_dest": "Todos os Destinos",
                "hero.exp_label": "Tipo de Experiência",
                "hero.all_cat": "Todas as Categorias",
                "hero.kw_label": "Palavra-Chave",
                "hero.search_btn": "Pesquisar Pacotes",
                "hero.origin_label": "Origem",
                "hero.dest_flight_label": "Destino do Voo",
                "hero.dates_label": "Datas Pretendidas",
                "hero.quote_flights_btn": "Cotar Passagens →",
                "hero.city_label": "Cidade / Região",
                "hero.guests_label": "Hóspedes",
                "hero.quote_hotel_btn": "Cotar Alojamento →",
                "camp.tag": "Campanhas Oficiais em Vigor • 2026",
                "camp.title": "Destaques Promocionais Zebra Tours",
                "camp.subtitle": "Mais do que viagens, experiências! Conheça as ofertas e programas com tarifas negociadas e assistência integral.",
                "camp.book_now": "FAÇA JÁ A SUA RESERVA",
                "camp.consult": "Consultar Disponibilidade →",
                "camp.guangzhou_title": "China Guangzhou",
                "camp.guangzhou_badge": "Compras & Negócios",
                "camp.portugal_title": "Portugal para Trabalhar",
                "camp.portugal_badge": "Emigração Segura",
                "camp.capetown_title": "África do Sul • Cape Town",
                "camp.capetown_badge": "Aventura & Cidade",
                "footer.about_title": "ZEBRA TOURS",
                "footer.about_desc": "A sua agência moçambicana de viagens e turismo de referência. Especialistas em criar memórias inesquecíveis, passagens aos melhores preços e turismo de excelência.",
                "footer.quick_links": "Navegação Rápida",
                "footer.services_title": "Serviços Oficiais",
                "footer.contact_title": "Fale Connosco",
                "footer.rights": "Todos os direitos reservados."
            },
            en: {
                "topbar.desk": "Official Travel Desk in Maputo, Mozambique",
                "topbar.license": "National Tourism License",
                "topbar.slogan": "\"More than travel, experiences!\"",
                "topbar.whatsapp": "Direct WhatsApp",
                "topbar.admin": "Client Portal",
                "nav.home": "Home",
                "nav.destinations": "Destinations",
                "nav.packages": "Holiday Packages",
                "nav.services": "Services",
                "nav.about": "About Us",
                "nav.contact": "Contact",
                "nav.quote": "Request Quote",
                "nav.quote_full": "Request Travel Quote",
                "lang.select": "Language / Idioma:",
                "hero.badge": "Travel Agency & Tourism Consulting in Mozambique",
                "hero.title": "The world awaits you.",
                "hero.subtitle": "Discover remarkable destinations with complete packages, negotiated airline fares and dedicated support at every step.",
                "hero.tab_packages": "Tour Packages",
                "hero.tab_flights": "Flight Tickets",
                "hero.tab_hotels": "Hotels & Lodges",
                "hero.dest_label": "Desired Destination",
                "hero.all_dest": "All Destinations",
                "hero.exp_label": "Experience Type",
                "hero.all_cat": "All Categories",
                "hero.kw_label": "Keywords",
                "hero.search_btn": "Search Packages",
                "hero.origin_label": "Origin",
                "hero.dest_flight_label": "Flight Destination",
                "hero.dates_label": "Preferred Dates",
                "hero.quote_flights_btn": "Quote Flights →",
                "hero.city_label": "City / Region",
                "hero.guests_label": "Guests",
                "hero.quote_hotel_btn": "Quote Accommodation →",
                "camp.tag": "Official Featured Campaigns • 2026",
                "camp.title": "Zebra Tours Featured Offers",
                "camp.subtitle": "More than travel, experiences! Discover our exclusive offers and tailored programs with full traveler assistance.",
                "camp.book_now": "BOOK YOUR TRIP NOW",
                "camp.consult": "Check Availability →",
                "camp.guangzhou_title": "China Guangzhou",
                "camp.guangzhou_badge": "Shopping & Business",
                "camp.portugal_title": "Work in Portugal",
                "camp.portugal_badge": "Relocation Support",
                "camp.capetown_title": "South Africa • Cape Town",
                "camp.capetown_badge": "Adventure & City",
                "footer.about_title": "ZEBRA TOURS",
                "footer.about_desc": "Your premier Mozambican travel and tourism agency. Specialists in crafting unforgettable memories, competitive airfares and luxury safari experiences.",
                "footer.quick_links": "Quick Navigation",
                "footer.services_title": "Official Services",
                "footer.contact_title": "Contact Us",
                "footer.rights": "All rights reserved."
            }
        };

        window.switchZebraLang = function(lang) {
            if (lang !== 'pt' && lang !== 'en') lang = 'pt';
            
            // Save preference to localStorage
            try {
                localStorage.setItem('zebratours_lang', lang);
                document.cookie = "zebra_lang=" + lang + ";path=/;max-age=31536000;SameSite=Lax";
            } catch (e) {
                console.warn('Storage not available', e);
            }

            document.documentElement.lang = lang;

            // Update Desktop Switcher Buttons
            const btnPt = document.getElementById('langBtnPt');
            const btnEn = document.getElementById('langBtnEn');
            if (btnPt && btnEn) {
                if (lang === 'pt') {
                    btnPt.className = "px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-gradient-to-r from-gold via-gold-light to-gold-dark text-slate-950 shadow-sm";
                    btnEn.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all flex items-center gap-1.5";
                } else {
                    btnEn.className = "px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-gradient-to-r from-gold via-gold-light to-gold-dark text-slate-950 shadow-sm";
                    btnPt.className = "px-2.5 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition-all flex items-center gap-1.5";
                }
            }

            // Update Mobile Header Switcher
            const mobPt = document.getElementById('mobileHeaderLangPt');
            const mobEn = document.getElementById('mobileHeaderLangEn');
            if (mobPt && mobEn) {
                if (lang === 'pt') {
                    mobPt.className = "px-2 py-0.5 rounded font-bold bg-gold text-slate-950";
                    mobEn.className = "px-2 py-0.5 rounded font-medium text-slate-300";
                } else {
                    mobEn.className = "px-2 py-0.5 rounded font-bold bg-gold text-slate-950";
                    mobPt.className = "px-2 py-0.5 rounded font-medium text-slate-300";
                }
            }

            // Update Mobile Drawer Switcher
            const drawerPt = document.getElementById('drawerLangPt');
            const drawerEn = document.getElementById('drawerLangEn');
            if (drawerPt && drawerEn) {
                if (lang === 'pt') {
                    drawerPt.className = "px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-gold text-slate-950";
                    drawerEn.className = "px-3 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition flex items-center gap-1.5";
                } else {
                    drawerEn.className = "px-3 py-1 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-gold text-slate-950";
                    drawerPt.className = "px-3 py-1 rounded-lg text-xs font-medium text-slate-300 hover:text-white transition flex items-center gap-1.5";
                }
            }

            // Update DOM text for data-i18n elements
            const dict = zebraTranslations[lang] || zebraTranslations.pt;
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) {
                    el.textContent = dict[key];
                }
            });

            // Update input placeholders if available
            document.querySelectorAll('[data-i18n-ph]').forEach(el => {
                const key = el.getAttribute('data-i18n-ph');
                if (dict[key]) {
                    el.setAttribute('placeholder', dict[key]);
                }
            });
        };

        // Initialize language from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            let savedLang = 'pt';
            try {
                savedLang = localStorage.getItem('zebratours_lang') || 'pt';
            } catch (e) {}
            window.switchZebraLang(savedLang);

            // Mobile menu toggle
            const btn = document.getElementById('mobileMenuBtn');
            const menu = document.getElementById('mobileMenu');
            if (btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
