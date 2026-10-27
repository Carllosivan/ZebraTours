import React, { useState } from 'react';
import { BrandLogo } from './BrandLogo.tsx';
import { Phone, MessageCircle, MapPin, Menu, X, Shield, Clock, ChevronRight } from 'lucide-react';
import { buildWhatsAppLink } from '../utils/travel-logic.ts';
import { NavigationItem } from '../types/travel.ts';

interface HeaderProps {
  onOpenQuoteModal: (service?: string, packageTitle?: string) => void;
  onOpenAdminLogin: () => void;
  isAdminLoggedIn: boolean;
  onOpenAdminDashboard: () => void;
  activeSection: string;
  onNavigate: (section: string) => void;
  phone?: string;
  whatsapp?: string;
  navItems?: NavigationItem[];
}

export const Header: React.FC<HeaderProps> = ({
  onOpenQuoteModal,
  onOpenAdminLogin,
  isAdminLoggedIn,
  onOpenAdminDashboard,
  activeSection,
  onNavigate,
  phone = '+258 85 104 1826',
  whatsapp = '+258 85 104 1826',
  navItems = [],
}) => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const defaultLinks = [
    { id: 'inicio', label: 'Início' },
    { id: 'destinos', label: 'Destinos' },
    { id: 'pacotes', label: 'Pacotes' },
    { id: 'servicos', label: 'Serviços' },
    { id: 'sobre', label: 'Sobre Nós' },
    { id: 'contactos', label: 'Contactos' },
  ];

  // If CMS navigation items available, use active header items
  const activeNavLinks = navItems.length > 0
    ? navItems
        .filter((n) => n.is_active && (n.location === 'header' || n.location === 'both'))
        .map((n) => ({ id: n.url, label: n.label }))
    : defaultLinks;

  const handleNavClick = (id: string) => {
    onNavigate(id);
    setMobileMenuOpen(false);
  };

  const whatsappUrl = buildWhatsAppLink({ phone: whatsapp });

  return (
    <header className="sticky top-0 z-40 bg-[#FAF9F6]/95 backdrop-blur-md border-b border-stone-200/70 transition-all">
      {/* Top Bar - Elegância Editorial */}
      <div className="bg-[#071629] text-stone-300 text-[11px] tracking-wide py-2 px-4 sm:px-6 lg:px-8 border-b border-blue-950">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3">
          <div className="flex items-center flex-wrap gap-5 sm:gap-7">
            <span className="flex items-center gap-1.5 text-stone-300">
              <MapPin className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
              <span>Maputo, Moçambique</span>
            </span>

            <a
              href={`tel:${phone.replace(/\s+/g, '')}`}
              className="flex items-center gap-1.5 text-stone-200 hover:text-[#D4AF37] transition-colors"
            >
              <Phone className="w-3.5 h-3.5 text-[#D4AF37] shrink-0" />
              <span>{phone}</span>
            </a>

            <span className="hidden md:flex items-center gap-1.5 text-stone-400">
              <Clock className="w-3 h-3 text-stone-400 shrink-0" />
              <span>Atendimento personalizado</span>
            </span>
          </div>

          <div className="flex items-center gap-4">
            <span className="text-[#D4AF37] font-medium tracking-wider uppercase text-[10px] hidden sm:inline">
              Metical Moçambicano (MT)
            </span>

            {/* Admin Portal Gateway */}
            {isAdminLoggedIn ? (
              <button
                onClick={onOpenAdminDashboard}
                className="flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 font-semibold px-2.5 py-0.5 rounded bg-emerald-950/60 border border-emerald-700/60 transition-colors"
              >
                <Shield className="w-3 h-3" />
                <span>Centro de Gestão</span>
              </button>
            ) : (
              <button
                onClick={onOpenAdminLogin}
                className="flex items-center gap-1 text-stone-400 hover:text-white transition-colors text-[11px]"
                title="Acesso de Consultores e Gestão"
              >
                <Shield className="w-3 h-3 text-[#D4AF37]" />
                <span>Área de Gestão</span>
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Main Header Bar */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4">
        <div className="flex items-center justify-between gap-6">
          {/* Brand Logo */}
          <button
            onClick={() => handleNavClick('inicio')}
            className="text-left focus:outline-none focus-visible:ring-1 focus-visible:ring-[#071629] rounded p-0.5"
            aria-label="Zebra Tours Moçambique"
          >
            <BrandLogo showSlogan={true} />
          </button>

          {/* Minimalist Editorial Navigation */}
          <nav className="hidden lg:flex items-center gap-1 xl:gap-2 text-[13px] font-medium text-stone-700">
            {activeNavLinks.map((link) => {
              const isActive = activeSection === link.id;
              return (
                <button
                  key={link.id}
                  onClick={() => handleNavClick(link.id)}
                  className={`px-3 py-1.5 rounded transition-all relative ${
                    isActive
                      ? 'text-[#071629] font-bold'
                      : 'text-stone-600 hover:text-[#071629] hover:bg-stone-200/40'
                  }`}
                >
                  {link.label}
                  {isActive && (
                    <span className="absolute bottom-0 left-3 right-3 h-[2px] bg-[#D4AF37]" />
                  )}
                </button>
              );
            })}
          </nav>

          {/* Action CTAs */}
          <div className="hidden sm:flex items-center gap-3">
            <a
              href={whatsappUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-emerald-800 bg-emerald-50/80 hover:bg-emerald-100/90 font-medium text-xs border border-emerald-200/80 transition-colors"
              title="Falar com Consultor no WhatsApp"
            >
              <MessageCircle className="w-3.5 h-3.5 text-emerald-600" />
              <span>WhatsApp</span>
            </a>

            <button
              onClick={() => onOpenQuoteModal()}
              className="px-5 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] hover:text-[#E5C365] border border-[#D4AF37]/40 font-semibold text-xs tracking-wider uppercase transition-all shadow-sm"
            >
              Planear Viagem
            </button>
          </div>

          {/* Mobile Menu Button */}
          <div className="flex items-center gap-2 lg:hidden">
            <button
              onClick={() => onOpenQuoteModal()}
              className="sm:hidden px-3 py-1.5 rounded bg-[#071629] text-[#D4AF37] font-semibold text-xs uppercase"
            >
              Planear
            </button>
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="p-2 rounded text-stone-800 hover:bg-stone-200/60"
              aria-label={mobileMenuOpen ? 'Fechar menu' : 'Abrir menu'}
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>
        </div>

        {/* Mobile Drawer */}
        {mobileMenuOpen && (
          <div className="lg:hidden mt-3 pt-3 border-t border-stone-200 pb-4 animate-in fade-in duration-150">
            <div className="grid grid-cols-2 gap-1 mb-4">
              {activeNavLinks.map((link) => (
                <button
                  key={link.id}
                  onClick={() => handleNavClick(link.id)}
                  className={`text-left px-3 py-2 rounded text-xs font-medium ${
                    activeSection === link.id
                      ? 'bg-stone-200/80 text-[#071629] font-bold'
                      : 'text-stone-700 hover:bg-stone-100'
                  }`}
                >
                  {link.label}
                </button>
              ))}
            </div>

            <div className="flex flex-col gap-2 pt-2 border-t border-stone-200">
              <a
                href={whatsappUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="flex items-center justify-center gap-2 w-full py-2.5 rounded bg-emerald-600 text-white font-medium text-xs shadow-sm"
              >
                <MessageCircle className="w-4 h-4" />
                <span>WhatsApp (+258 85 104 1826)</span>
              </a>

              <button
                onClick={() => {
                  setMobileMenuOpen(false);
                  onOpenQuoteModal();
                }}
                className="w-full py-2.5 rounded bg-[#071629] text-[#D4AF37] font-semibold text-xs uppercase tracking-wider"
              >
                Planear a Minha Viagem
              </button>
            </div>
          </div>
        )}
      </div>
    </header>
  );
};
