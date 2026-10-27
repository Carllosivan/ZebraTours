import React from 'react';
import { Calendar, Clock, MapPin, ChevronRight, MessageCircle, AlertCircle } from 'lucide-react';
import { TravelPackage } from '../types/travel.ts';
import { formatMZN, buildWhatsAppLink } from '../utils/travel-logic.ts';

interface PackageCardProps {
  pkg: TravelPackage;
  onViewDetails: (pkg: TravelPackage) => void;
  onRequestQuote: (pkg: TravelPackage) => void;
  whatsappPhone?: string;
}

export const PackageCard: React.FC<PackageCardProps> = ({
  pkg,
  onViewDetails,
  onRequestQuote,
  whatsappPhone = '+258 85 104 1826',
}) => {
  const isPendingValidation = pkg.status === 'pendente_validacao';
  const priceDisplay = formatMZN(pkg.price_per_person ?? pkg.total_price, pkg.currency, pkg.price_type);

  const whatsappUrl = buildWhatsAppLink({
    phone: whatsappPhone,
    packageTitle: pkg.title,
  });

  return (
    <div className="flex flex-col bg-white rounded-xl border border-stone-200/90 shadow-sm hover:shadow-xl hover:border-[#D4AF37]/60 transition-all duration-300 overflow-hidden group">
      {/* Cover Image Container */}
      <div className="relative h-56 sm:h-60 w-full overflow-hidden bg-stone-900">
        <img
          src={pkg.cover_image || 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80'}
          alt={pkg.title}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
          loading="lazy"
          onError={(e) => {
            (e.target as HTMLImageElement).src = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80';
          }}
        />

        <div className="absolute inset-0 bg-gradient-to-t from-[#071629]/90 via-[#071629]/20 to-transparent" />

        {/* Featured Badge */}
        {pkg.is_featured ? (
          <div className="absolute top-3 left-3 bg-[#D4AF37] text-[#071629] font-bold text-[10px] px-2.5 py-1 rounded uppercase tracking-wider shadow">
            Destaque
          </div>
        ) : null}

        {/* Validation Status for Cape Town reference poster */}
        {isPendingValidation && (
          <div className="absolute top-3 right-3 bg-[#071629]/90 border border-[#D4AF37]/50 text-[#D4AF37] font-semibold text-[10px] px-2.5 py-1 rounded backdrop-blur-sm flex items-center gap-1">
            <AlertCircle className="w-3 h-3 text-[#D4AF37]" />
            <span>Oferta Especial</span>
          </div>
        )}

        {/* Destination & Duration in Image Overlay */}
        <div className="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white text-xs">
          <span className="flex items-center gap-1.5 font-semibold text-stone-200">
            <MapPin className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>{pkg.destination_name || 'Destino Internacional'}</span>
            {pkg.country && <span className="text-stone-400 font-normal">({pkg.country})</span>}
          </span>

          <span className="flex items-center gap-1 bg-[#071629]/70 border border-stone-700/60 px-2 py-0.5 rounded text-[10px] text-stone-300">
            <Clock className="w-3 h-3 text-[#D4AF37]" />
            <span>{pkg.duration_nights}N / {pkg.duration_days}D</span>
          </span>
        </div>
      </div>

      {/* Content */}
      <div className="flex flex-col flex-1 p-5 space-y-3 justify-between">
        <div className="space-y-2">
          <h3 className="text-lg font-normal font-editorial text-stone-900 group-hover:text-[#071629] transition-colors leading-snug line-clamp-2">
            {pkg.title}
          </h3>

          <p className="text-xs text-stone-600 line-clamp-2 leading-relaxed">
            {pkg.summary}
          </p>

          {/* Dates preview if available */}
          {pkg.travel_dates && (
            <div className="flex items-center gap-1.5 text-[11px] text-stone-500 font-medium">
              <Calendar className="w-3.5 h-3.5 text-stone-400 shrink-0" />
              <span className="truncate">{pkg.travel_dates}</span>
            </div>
          )}

          {/* Special notice for Cape Town or validation pending */}
          {isPendingValidation && (
            <p className="text-[10px] text-amber-900 bg-[#F5F3EF] border border-[#D4AF37]/30 p-2 rounded leading-tight">
              Tarifa promocional do cartaz. Validação de lugares e datas operacionais no pedido de cotação.
            </p>
          )}
        </div>

        {/* Price & Actions Box */}
        <div className="pt-3 border-t border-stone-200 flex items-end justify-between gap-2">
          <div>
            <span className="text-[10px] uppercase font-semibold text-stone-400 block tracking-wider">
              {pkg.price_type === 'sob_consulta' ? 'Tarifa' : 'A partir de'}
            </span>
            <div className="flex items-baseline gap-1">
              <span className="text-xl font-bold text-[#071629]">
                {priceDisplay}
              </span>
              {pkg.price_type !== 'sob_consulta' && (
                <span className="text-[11px] text-stone-500 font-medium">/ pessoa</span>
              )}
            </div>
          </div>

          <div className="flex items-center gap-1.5">
            <a
              href={whatsappUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-colors"
              title="Informação via WhatsApp"
              aria-label={`WhatsApp para ${pkg.title}`}
            >
              <MessageCircle className="w-4 h-4" />
            </a>

            <button
              onClick={() => onViewDetails(pkg)}
              className="px-3 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-semibold text-xs flex items-center gap-1 transition-colors cursor-pointer"
            >
              <span>Detalhes</span>
              <ChevronRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
