import React from 'react';
import { BrandLogo } from './BrandLogo.tsx';
import { Phone, Mail, MapPin, MessageCircle, Heart } from 'lucide-react';
import { buildWhatsAppLink } from '../utils/travel-logic.ts';

interface FooterProps {
  onNavigate: (section: string) => void;
  onOpenPrivacy: () => void;
  onOpenTerms: () => void;
  onOpenFaq: () => void;
  phone?: string;
  whatsapp?: string;
  email?: string;
  location?: string;
}

export const Footer: React.FC<FooterProps> = ({
  onNavigate,
  onOpenPrivacy,
  onOpenTerms,
  onOpenFaq,
  phone = '+258 85 104 1826',
  whatsapp = '+258 85 104 1826',
  email = 'info@ztours.co.mz',
  location = 'Maputo, Moçambique',
}) => {
  const currentYear = new Date().getFullYear();
  const whatsappUrl = buildWhatsAppLink({ phone: whatsapp });

  return (
    <footer className="bg-[#071629] text-slate-300 pt-16 pb-12 border-t border-blue-950">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 text-left">
          {/* Brand Info */}
          <div className="lg:col-span-2 space-y-4 text-left">
            <BrandLogo variant="footer" showSlogan={true} />
            <p className="text-xs sm:text-sm text-slate-400 leading-relaxed pr-6 text-left">
              Mais do que organizar viagens, criamos oportunidades para descobrir o mundo. A sua agência de confiança em Maputo para voos, alojamento e experiências turísticas inesquecíveis.
            </p>
            <div className="pt-2 flex items-center gap-3">
              <a
                href={whatsappUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-950/80 border border-emerald-600/50 text-emerald-300 hover:text-white font-medium text-xs transition-colors"
              >
                <MessageCircle className="w-3.5 h-3.5 text-emerald-400" />
                <span>WhatsApp: {whatsapp}</span>
              </a>
            </div>
          </div>

          {/* Destinos */}
          <div className="space-y-3 text-left">
            <h4 className="text-xs uppercase font-extrabold text-amber-400 tracking-wider text-left">
              Destinos em Foco
            </h4>
            <ul className="text-xs space-y-2 text-slate-400 text-left">
              <li>
                <button onClick={() => onNavigate('destinos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  África do Sul (Cape Town)
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('destinos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Portugal (Lisboa & Sintra)
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('destinos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Zimbabwe (Harare)
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('destinos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  China (Pequim & Guangzhou)
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('destinos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Moçambique (Bazaruto & Gorongosa)
                </button>
              </li>
            </ul>
          </div>

          {/* Serviços */}
          <div className="space-y-3 text-left">
            <h4 className="text-xs uppercase font-extrabold text-amber-400 tracking-wider text-left">
              Serviços
            </h4>
            <ul className="text-xs space-y-2 text-slate-400 text-left">
              <li>
                <button onClick={() => onNavigate('voos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Passagens Aéreas
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('hoteis')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Hotéis e Alojamento
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('pacotes')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Pacotes Turísticos
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('servicos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Viagens Corporativas
                </button>
              </li>
              <li>
                <button onClick={() => onNavigate('servicos')} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                  Assistência ao Viajante
                </button>
              </li>
            </ul>
          </div>

          {/* Institucional & Contactos */}
          <div className="space-y-3 text-left">
            <h4 className="text-xs uppercase font-extrabold text-amber-400 tracking-wider text-left">
              Institucional
            </h4>
            <div className="space-y-2 text-xs text-slate-400 text-left">
              <div className="flex items-start gap-2 text-left">
                <MapPin className="w-3.5 h-3.5 text-amber-400 shrink-0 mt-0.5" />
                <span className="text-left">{location}</span>
              </div>
              <div className="flex items-center gap-2 text-left">
                <Phone className="w-3.5 h-3.5 text-amber-400 shrink-0" />
                <a href={`tel:${phone.replace(/\s+/g, '')}`} className="hover:text-white text-left">
                  {phone}
                </a>
              </div>
              <div className="flex items-center gap-2 text-left">
                <Mail className="w-3.5 h-3.5 text-amber-400 shrink-0" />
                <div className="flex flex-col text-left">
                  <a href={`mailto:${email}`} className="hover:text-white text-left">{email}</a>
                  <a href="mailto:reservas@ztours.co.mz" className="hover:text-white text-left text-[11px] text-amber-400">reservas@ztours.co.mz</a>
                </div>
              </div>
            </div>

            <div className="pt-2 flex flex-col gap-1.5 text-xs text-slate-400 text-left">
              <button onClick={onOpenFaq} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                Perguntas Frequentes (FAQ)
              </button>
              <button onClick={onOpenPrivacy} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                Política de Privacidade
              </button>
              <button onClick={onOpenTerms} className="text-left block w-full hover:text-white transition-colors cursor-pointer">
                Termos e Condições
              </button>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-8 border-t border-slate-800 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-4 text-left">
          <p className="text-left">
            © {currentYear} Zebra Tours Moçambique. Todos os direitos reservados.
          </p>
          <div className="flex items-center gap-1.5 text-[11px] text-slate-400 text-left sm:text-right">
            <span>Slogan oficial:</span>
            <span className="text-amber-400 font-semibold italic">
              "Mais do que viagens, experiências!"
            </span>
          </div>
        </div>
      </div>
    </footer>
  );
};
