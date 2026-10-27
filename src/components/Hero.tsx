import React, { useState } from 'react';
import { Compass, MessageCircle, ChevronRight, Plane, ShieldCheck, Briefcase, FileText, CheckCircle2 } from 'lucide-react';
import { buildWhatsAppLink } from '../utils/travel-logic.ts';
import { SiteSettings, TravelPackage } from '../types/travel.ts';

interface HeroProps {
  onExploreDestinations: () => void;
  onRequestQuote: (prefilledService?: string, prefilledDestination?: string) => void;
  onSelectCapeTown: () => void;
  onSelectPackage?: (pkg: TravelPackage) => void;
  packages?: TravelPackage[];
  whatsapp?: string;
  settings?: SiteSettings;
}

export const Hero: React.FC<HeroProps> = ({
  onExploreDestinations,
  onRequestQuote,
  onSelectCapeTown,
  onSelectPackage,
  packages = [],
  whatsapp = '+258 85 104 1826',
  settings = {},
}) => {
  const [activeHighlight, setActiveHighlight] = useState<'china' | 'portugal' | 'capetown'>('china');

  const whatsappUrl = buildWhatsAppLink({ phone: whatsapp, packageTitle: 'Informações de Viagem Premium' });

  const heroTitle = settings.hero_title || 'O mundo espera por si.';
  const heroSubtitle = settings.hero_subtitle || 'Descubra novos destinos com experiências de viagem pensadas à sua medida.';
  const ctaPrimary = settings.hero_cta_primary || 'Explorar Destinos';
  const ctaSecondary = settings.hero_cta_secondary || 'Planear a Minha Viagem';
  const bgImage = settings.hero_bg_image || 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1920&q=80';

  // Find relevant packages
  const chinaPkg = packages.find(p => p.slug.includes('guangzhou') || p.title.toLowerCase().includes('guangzhou'));
  const portugalPkg = packages.find(p => p.slug.includes('portugal') && (p.slug.includes('trabalhar') || p.title.toLowerCase().includes('emigre')));
  const capeTownPkg = packages.find(p => p.slug.includes('cape-town') || p.title.toLowerCase().includes('cape town'));

  return (
    <div className="relative bg-[#071629] text-white overflow-hidden border-b border-stone-800">
      {/* Panorâmica fotográfica editorial com tratamento subtil */}
      <div className="absolute inset-0 z-0">
        <img
          src={bgImage}
          alt="Paisagem de Viagem Panorâmica"
          className="w-full h-full object-cover object-center opacity-30 mix-blend-luminosity filter brightness-90"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-[#071629] via-[#071629]/80 to-transparent" />
        <div className="absolute inset-0 bg-gradient-to-t from-[#071629] via-transparent to-transparent" />
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-28">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Main Editorial Text Column */}
          <div className="lg:col-span-7 space-y-6 text-left">
            {/* Tagline com identidade autêntica */}
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#0B2545]/80 border border-[#D4AF37]/30 text-[#D4AF37] text-[11px] font-semibold tracking-widest uppercase">
              <span>Zebra Tours Moçambique</span>
              <span className="w-1 h-1 rounded-full bg-[#D4AF37]" />
              <span>Agência & Consultoria de Viagens</span>
            </div>

            {/* Título Principal Editorial */}
            <h1 className="text-4xl sm:text-5xl lg:text-6xl font-normal font-editorial tracking-tight text-white leading-[1.08]">
              {heroTitle}
            </h1>

            {/* Subtítulo Refinado */}
            <p className="text-base sm:text-lg text-stone-300 font-light leading-relaxed max-w-xl">
              {heroSubtitle}
            </p>

            {/* Slogan Oficial dos Cartazes da Agência */}
            <div className="flex items-center gap-3 border-l-2 border-[#D4AF37] pl-3.5 py-0.5">
              <span className="text-sm font-editorial italic text-[#D4AF37] tracking-wide">
                "Mais do que viagens, experiências!"
              </span>
            </div>

            {/* CTAs Primárias com Hierarquia Clara */}
            <div className="flex flex-wrap items-center gap-3.5 pt-2">
              <button
                onClick={onExploreDestinations}
                className="px-6 py-3 rounded-lg bg-[#D4AF37] hover:bg-[#C59B27] text-[#071629] font-bold text-xs uppercase tracking-wider transition-all shadow-md cursor-pointer"
              >
                {ctaPrimary}
              </button>

              <button
                onClick={() => onRequestQuote()}
                className="px-6 py-3 rounded-lg bg-transparent hover:bg-white/10 text-white font-medium text-xs tracking-wider border border-stone-500/80 transition-all cursor-pointer flex items-center gap-1.5"
              >
                <span>{ctaSecondary}</span>
                <ChevronRight className="w-3.5 h-3.5 text-[#D4AF37]" />
              </button>

              <a
                href={whatsappUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="px-4 py-3 rounded-lg bg-emerald-700/80 hover:bg-emerald-600 text-white font-semibold text-xs transition-colors flex items-center gap-2"
                title="Conversar por WhatsApp (+258 85 104 1826)"
              >
                <MessageCircle className="w-4 h-4 text-emerald-300" />
                <span className="hidden sm:inline">WhatsApp Oficial</span>
              </a>
            </div>

            {/* Pilares da Agência */}
            <div className="grid grid-cols-3 gap-6 pt-6 border-t border-stone-800/80 text-stone-300 text-left">
              <div>
                <span className="block text-xs uppercase tracking-wider font-semibold text-[#D4AF37]">
                  Personalização
                </span>
                <span className="text-[12px] text-stone-400">Roteiros sob medida</span>
              </div>
              <div>
                <span className="block text-xs uppercase tracking-wider font-semibold text-[#D4AF37]">
                  Acompanhamento
                </span>
                <span className="text-[12px] text-stone-400">Apoio e assistência</span>
              </div>
              <div>
                <span className="block text-xs uppercase tracking-wider font-semibold text-[#D4AF37]">
                  Transparência
                </span>
                <span className="text-[12px] text-stone-400">Valores em Meticais</span>
              </div>
            </div>
          </div>

          {/* Right Column: Cartazes e Destaques Reais da Agência */}
          <div className="lg:col-span-5 space-y-3">
            {/* Tabs dos Destaques Oficiais */}
            <div className="flex items-center gap-1.5 bg-[#0B2545]/70 p-1 rounded-xl border border-stone-700/60">
              <button
                onClick={() => setActiveHighlight('china')}
                className={`flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-colors cursor-pointer ${
                  activeHighlight === 'china'
                    ? 'bg-[#D4AF37] text-[#071629] shadow-sm'
                    : 'text-stone-300 hover:text-white'
                }`}
              >
                China 169.500 MT
              </button>
              <button
                onClick={() => setActiveHighlight('portugal')}
                className={`flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-colors cursor-pointer ${
                  activeHighlight === 'portugal'
                    ? 'bg-[#D4AF37] text-[#071629] shadow-sm'
                    : 'text-stone-300 hover:text-white'
                }`}
              >
                Portugal Trabalho
              </button>
              <button
                onClick={() => setActiveHighlight('capetown')}
                className={`flex-1 py-1.5 px-2 rounded-lg text-[11px] font-bold uppercase tracking-wider transition-colors cursor-pointer ${
                  activeHighlight === 'capetown'
                    ? 'bg-[#D4AF37] text-[#071629] shadow-sm'
                    : 'text-stone-300 hover:text-white'
                }`}
              >
                Cape Town
              </button>
            </div>

            {/* 1. China Guangzhou Feature Card */}
            {activeHighlight === 'china' && (
              <div className="relative rounded-2xl overflow-hidden border border-[#D4AF37]/50 bg-[#0B1E36]/95 shadow-2xl backdrop-blur-md group hover:border-[#D4AF37] transition-all">
                <div className="absolute top-3 right-3 z-20 bg-[#D4AF37] text-[#071629] font-black text-[10px] px-2.5 py-1 rounded-md uppercase tracking-wider shadow">
                  Novembro 2026
                </div>

                <div className="relative h-56 sm:h-64 w-full overflow-hidden bg-stone-900">
                  <img
                    src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80"
                    alt="Guangzhou Canton Tower e Centro Comercial"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#0B1E36] via-[#0B1E36]/40 to-transparent" />
                  <div className="absolute bottom-3 left-4 right-4">
                    <span className="text-[10px] uppercase font-bold tracking-widest text-[#D4AF37]">
                      China • Compras e Negócios
                    </span>
                    <h3 className="text-xl sm:text-2xl font-bold font-editorial text-white leading-snug">
                      China Guangzhou Adventure
                    </h3>
                  </div>
                </div>

                <div className="p-5 space-y-4 text-left">
                  <p className="text-xs text-stone-300 leading-relaxed">
                    Uma oportunidade única para conhecer novos fornecedores, fazer grandes compras e explorar um dos maiores centros comerciais do mundo.
                  </p>

                  <div className="grid grid-cols-2 gap-2 text-[11px] text-stone-200">
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Passagem ida e volta</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Hotel 7 noites incluído</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Assistência na viagem</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Reserva para imprevistos</span>
                    </div>
                  </div>

                  <div className="p-3 bg-[#071629]/90 border border-amber-500/30 rounded-xl flex items-center justify-between">
                    <div>
                      <span className="text-[10px] text-stone-400 block uppercase tracking-wider font-semibold">
                        Preço oficial de venda
                      </span>
                      <span className="text-2xl font-black text-[#D4AF37]">169.500 MT</span>
                      <span className="text-[11px] text-stone-400 ml-1">/ pessoa</span>
                    </div>
                    <div className="text-right">
                      <span className="text-[10px] bg-amber-500/10 text-[#D4AF37] border border-[#D4AF37]/30 px-2.5 py-1 rounded font-bold uppercase block">
                        7 Noites
                      </span>
                      <span className="text-[10px] text-stone-400 mt-0.5 block">Nov. 2026</span>
                    </div>
                  </div>

                  <div className="flex items-center gap-2 pt-1">
                    <button
                      onClick={() => {
                        if (chinaPkg && onSelectPackage) {
                          onSelectPackage(chinaPkg);
                        } else {
                          onRequestQuote('Pacote Turístico', 'China Guangzhou');
                        }
                      }}
                      className="flex-1 py-2.5 rounded-lg bg-[#D4AF37] hover:bg-[#C59B27] text-[#071629] font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer"
                    >
                      Ver Detalhes do Pacote
                    </button>
                    <button
                      onClick={() => onRequestQuote('Pacote Turístico', 'China Guangzhou - Compras e Negócios')}
                      className="px-4 py-2.5 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-200 text-xs font-semibold border border-stone-700 transition-colors cursor-pointer"
                    >
                      Reservar
                    </button>
                  </div>
                </div>
              </div>
            )}

            {/* 2. Portugal Emigration Card */}
            {activeHighlight === 'portugal' && (
              <div className="relative rounded-2xl overflow-hidden border border-[#D4AF37]/50 bg-[#0B1E36]/95 shadow-2xl backdrop-blur-md group hover:border-[#D4AF37] transition-all">
                <div className="absolute top-3 right-3 z-20 bg-emerald-600 text-white font-black text-[10px] px-2.5 py-1 rounded-md uppercase tracking-wider shadow">
                  Apoio Integral
                </div>

                <div className="relative h-56 sm:h-64 w-full overflow-hidden bg-stone-900">
                  <img
                    src="https://images.unsplash.com/photo-1513688275180-81775f7d2cf4?auto=format&fit=crop&w=800&q=80"
                    alt="Miradouro e Costa de Lisboa"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#0B1E36] via-[#0B1E36]/40 to-transparent" />
                  <div className="absolute bottom-3 left-4 right-4">
                    <span className="text-[10px] uppercase font-bold tracking-widest text-[#D4AF37]">
                      Portugal • Vistos & Trabalho
                    </span>
                    <h3 className="text-xl sm:text-2xl font-bold font-editorial text-white leading-snug">
                      Emigre para Portugal para Trabalhar
                    </h3>
                  </div>
                </div>

                <div className="p-5 space-y-4 text-left">
                  <p className="text-xs text-stone-300 leading-relaxed">
                    Realize o seu sonho de uma vida melhor, com segurança e apoio em todo o processo de imigração, autorização de residência e emprego.
                  </p>

                  <div className="grid grid-cols-2 gap-2 text-[11px] text-stone-200">
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Requisitos de imigração</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Vistos e residência</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Oportunidades de emprego</span>
                    </div>
                    <div className="flex items-center gap-1.5">
                      <CheckCircle2 className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Alojamento e integração</span>
                    </div>
                  </div>

                  <div className="p-3 bg-[#071629]/90 border border-amber-500/30 rounded-xl flex items-center justify-between">
                    <div>
                      <span className="text-[10px] text-stone-400 block uppercase tracking-wider font-semibold">
                        Consultoria Especializada
                      </span>
                      <span className="text-lg font-bold text-[#D4AF37]">Sob Consulta</span>
                      <span className="text-[11px] text-stone-400 ml-1">/ à medida</span>
                    </div>
                    <span className="text-[10px] bg-emerald-950 text-emerald-400 border border-emerald-600/50 px-2.5 py-1 rounded font-bold uppercase">
                      Inscrições Abertas
                    </span>
                  </div>

                  <div className="flex items-center gap-2 pt-1">
                    <button
                      onClick={() => {
                        if (portugalPkg && onSelectPackage) {
                          onSelectPackage(portugalPkg);
                        } else {
                          onRequestQuote('Assessoria de Vistos', 'Portugal');
                        }
                      }}
                      className="flex-1 py-2.5 rounded-lg bg-[#D4AF37] hover:bg-[#C59B27] text-[#071629] font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer"
                    >
                      Ver Detalhes do Programa
                    </button>
                    <button
                      onClick={() => onRequestQuote('Assessoria de Vistos', 'Emigre para Portugal para Trabalhar')}
                      className="px-4 py-2.5 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-200 text-xs font-semibold border border-stone-700 transition-colors cursor-pointer"
                    >
                      Consultar
                    </button>
                  </div>
                </div>
              </div>
            )}

            {/* 3. Cape Town Weekend Card */}
            {activeHighlight === 'capetown' && (
              <div className="relative rounded-2xl overflow-hidden border border-stone-700/80 bg-[#0B1E36]/90 shadow-2xl backdrop-blur-md group hover:border-[#D4AF37]/60 transition-all">
                <div className="absolute top-3 right-3 z-20 bg-[#071629]/90 border border-[#D4AF37]/50 text-[#D4AF37] font-semibold text-[10px] px-2.5 py-1 rounded uppercase tracking-wider">
                  Oferta Fim de Semana
                </div>

                <div className="relative h-56 sm:h-64 w-full overflow-hidden bg-stone-900">
                  <img
                    src="https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?auto=format&fit=crop&w=800&q=80"
                    alt="Cape Town Table Mountain e Costa"
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#0B1E36] via-[#0B1E36]/40 to-transparent" />
                  <div className="absolute bottom-3 left-4 right-4">
                    <span className="text-[10px] uppercase font-bold tracking-widest text-[#D4AF37]">
                      África do Sul • 2 Noites
                    </span>
                    <h3 className="text-xl font-normal font-editorial text-white leading-snug">
                      Cape Town Especial Promocional
                    </h3>
                  </div>
                </div>

                <div className="p-5 space-y-4 text-left">
                  <div className="grid grid-cols-2 gap-2 text-xs text-stone-300">
                    <div className="flex items-center gap-2">
                      <Plane className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Passagem Aérea Ida/Volta</span>
                    </div>
                    <div className="flex items-center gap-2">
                      <ShieldCheck className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
                      <span>Assistência na Viagem</span>
                    </div>
                  </div>

                  <div className="p-3 bg-[#071629]/80 border border-stone-800 rounded-xl flex items-center justify-between">
                    <div>
                      <span className="text-[10px] text-stone-400 block uppercase tracking-wider">
                        Tarifa Indicativa
                      </span>
                      <span className="text-xl font-bold text-[#D4AF37]">59.500 MT</span>
                      <span className="text-[11px] text-stone-400 ml-1">/ pessoa</span>
                    </div>
                    <span className="text-[10px] bg-stone-800 text-stone-300 px-2 py-1 rounded border border-stone-700">
                      Disponível
                    </span>
                  </div>

                  <div className="flex items-center gap-2 pt-1">
                    <button
                      onClick={onSelectCapeTown}
                      className="flex-1 py-2.5 rounded-lg bg-[#D4AF37] hover:bg-[#C59B27] text-[#071629] font-bold text-xs uppercase tracking-wider transition-colors cursor-pointer"
                    >
                      Ver Detalhes do Pacote
                    </button>
                    <button
                      onClick={() => onRequestQuote('Pacote Turístico', 'Cape Town')}
                      className="px-4 py-2.5 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-200 text-xs font-semibold border border-stone-700 transition-colors cursor-pointer"
                    >
                      Cotar
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
