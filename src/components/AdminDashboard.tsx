import React, { useState, useEffect } from 'react';
import {
  Shield,
  LayoutDashboard,
  Luggage,
  MapPin,
  FileText,
  Mail,
  Settings as SettingsIcon,
  LogOut,
  Plus,
  Trash2,
  Edit2,
  CheckCircle,
  Clock,
  AlertCircle,
  Eye,
  RefreshCw,
  Search,
  Filter,
  Download,
  Image,
  Users,
  Compass,
  FileSpreadsheet,
  Globe,
  Sliders,
  Calendar,
  Layers,
  ArrowRight,
  TrendingUp,
  History,
  MessageSquare,
} from 'lucide-react';
import {
  TravelPackage,
  Destination,
  BookingRequest,
  ContactMessage,
  SiteSettings,
  AdminUser,
  CmsPage,
  CmsBanner,
  NavigationItem,
  MediaAsset,
  BookingHistoryItem,
} from '../types/travel.ts';
import { formatMZN } from '../utils/travel-logic.ts';
import { BrandLogo } from './BrandLogo.tsx';

interface AdminDashboardProps {
  token: string;
  adminUser: AdminUser;
  onLogout: () => void;
  onClose: () => void;
  onRefreshPublicData: () => void;
}

type AdminTab =
  | 'dashboard'
  | 'bookings'
  | 'packages'
  | 'destinations'
  | 'content'
  | 'pages'
  | 'banners'
  | 'media'
  | 'navigation'
  | 'users'
  | 'messages'
  | 'reports'
  | 'settings'
  | 'audit';

export const AdminDashboard: React.FC<AdminDashboardProps> = ({
  token,
  adminUser,
  onLogout,
  onClose,
  onRefreshPublicData,
}) => {
  const [activeTab, setActiveTab] = useState<AdminTab>('dashboard');
  const [stats, setStats] = useState<any>(null);
  const [bookings, setBookings] = useState<BookingRequest[]>([]);
  const [packages, setPackages] = useState<TravelPackage[]>([]);
  const [destinations, setDestinations] = useState<Destination[]>([]);
  const [messages, setMessages] = useState<ContactMessage[]>([]);
  const [settings, setSettings] = useState<SiteSettings>({});
  const [pages, setPages] = useState<CmsPage[]>([]);
  const [banners, setBanners] = useState<CmsBanner[]>([]);
  const [mediaList, setMediaList] = useState<MediaAsset[]>([]);
  const [navItems, setNavItems] = useState<NavigationItem[]>([]);
  const [usersList, setUsersList] = useState<AdminUser[]>([]);
  const [auditLogs, setAuditLogs] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [actionSuccess, setActionSuccess] = useState<string | null>(null);

  // Package Form modal with 7 tabs
  const [editingPackage, setEditingPackage] = useState<TravelPackage | null>(null);
  const [isPackageFormOpen, setIsPackageFormOpen] = useState(false);
  const [packageFormActiveTab, setPackageFormActiveTab] = useState<number>(1);

  // Destination Form modal
  const [editingDestination, setEditingDestination] = useState<Destination | null>(null);
  const [isDestinationFormOpen, setIsDestinationFormOpen] = useState(false);

  // Page Form modal
  const [editingPage, setEditingPage] = useState<CmsPage | null>(null);
  const [isPageFormOpen, setIsPageFormOpen] = useState(false);

  // Banner Form modal
  const [editingBanner, setEditingBanner] = useState<CmsBanner | null>(null);
  const [isBannerFormOpen, setIsBannerFormOpen] = useState(false);

  // Media upload modal
  const [isMediaUploadOpen, setIsMediaUploadOpen] = useState(false);
  const [mediaUrlInput, setMediaUrlInput] = useState('');
  const [mediaTitleInput, setMediaTitleInput] = useState('');
  const [mediaAltInput, setMediaAltInput] = useState('');
  const [mediaCategoryInput, setMediaCategoryInput] = useState('geral');

  // Booking detail / note modal with timeline history
  const [selectedBooking, setSelectedBooking] = useState<BookingRequest | null>(null);
  const [bookingHistory, setBookingHistory] = useState<BookingHistoryItem[]>([]);
  const [bookingNoteText, setBookingNoteText] = useState('');
  const [bookingStatusText, setBookingStatusText] = useState<any>('Pendente');

  // Search and filters in bookings
  const [bookingSearch, setBookingSearch] = useState('');
  const [bookingFilterStatus, setBookingFilterStatus] = useState('all');

  const authHeaders = {
    'Content-Type': 'application/json',
    Authorization: `Bearer ${token}`,
  };

  const showNotification = (msg: string) => {
    setActionSuccess(msg);
    setTimeout(() => setActionSuccess(null), 4000);
  };

  const loadAllData = async () => {
    setLoading(true);
    try {
      const [
        statsRes,
        bookRes,
        pkgRes,
        destRes,
        msgRes,
        setRes,
        pagesRes,
        bannersRes,
        mediaRes,
        navRes,
        usersRes,
        auditRes,
      ] = await Promise.all([
        fetch('/api/admin/stats', { headers: authHeaders }),
        fetch('/api/bookings', { headers: authHeaders }),
        fetch('/api/packages?status=all', { headers: authHeaders }),
        fetch('/api/destinations', { headers: authHeaders }),
        fetch('/api/contact', { headers: authHeaders }),
        fetch('/api/settings'),
        fetch('/api/pages', { headers: authHeaders }),
        fetch('/api/banners', { headers: authHeaders }),
        fetch('/api/media', { headers: authHeaders }),
        fetch('/api/navigation'),
        fetch('/api/users', { headers: authHeaders }),
        fetch('/api/admin/audit-logs', { headers: authHeaders }),
      ]);

      if (statsRes.status === 401) {
        onLogout();
        return;
      }

      if (statsRes.ok) setStats(await statsRes.json());
      if (bookRes.ok) setBookings(await bookRes.json());
      if (pkgRes.ok) setPackages(await pkgRes.json());
      if (destRes.ok) setDestinations(await destRes.json());
      if (msgRes.ok) setMessages(await msgRes.json());
      if (setRes.ok) setSettings(await setRes.json());
      if (pagesRes.ok) setPages(await pagesRes.json());
      if (bannersRes.ok) setBanners(await bannersRes.json());
      if (mediaRes.ok) setMediaList(await mediaRes.json());
      if (navRes.ok) setNavItems(await navRes.json());
      if (usersRes.ok) setUsersList(await usersRes.json());
      if (auditRes.ok) setAuditLogs(await auditRes.json());
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadAllData();
  }, []);

  // Load Booking History
  const loadBookingTimeline = async (id: number) => {
    try {
      const res = await fetch(`/api/bookings/${id}/history`, { headers: authHeaders });
      if (res.ok) {
        setBookingHistory(await res.json());
      }
    } catch (e) {
      console.error(e);
    }
  };

  // Update Booking with History Log
  const handleUpdateBookingWithTimeline = async () => {
    if (!selectedBooking) return;
    try {
      const res = await fetch(`/api/bookings/${selectedBooking.id}/history`, {
        method: 'POST',
        headers: authHeaders,
        body: JSON.stringify({
          action: `Estado alterado para ${bookingStatusText}`,
          notes: bookingNoteText,
          new_status: bookingStatusText,
        }),
      });

      if (res.ok) {
        showNotification(`Pedido ${selectedBooking.reference_code} atualizado com sucesso!`);
        setSelectedBooking(null);
        setBookingNoteText('');
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao atualizar pedido');
    }
  };

  // Export Bookings CSV
  const handleExportCsv = () => {
    window.open('/api/bookings/export-csv', '_blank');
  };

  // Save Package (with 7 tabs support)
  const handleSavePackage = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const payload: any = {
      title: formData.get('title'),
      summary: formData.get('summary'),
      description: formData.get('description'),
      primary_destination_id: formData.get('primary_destination_id'),
      total_price: formData.get('total_price') || null,
      price_per_person: formData.get('price_per_person') || null,
      currency: formData.get('currency') || 'MT',
      price_type: formData.get('price_type') || 'indicativo',
      duration_days: formData.get('duration_days') || 3,
      duration_nights: formData.get('duration_nights') || 2,
      travel_dates: formData.get('travel_dates') || '',
      validity_date: formData.get('validity_date') || '',
      status: formData.get('status') || 'publicado',
      is_featured: formData.get('is_featured') === 'on' ? 1 : 0,
      included_services: formData.get('included_services') || '',
      excluded_services: formData.get('excluded_services') || '',
      conditions: formData.get('conditions') || '',
      cover_image: formData.get('cover_image') || '',
    };

    try {
      const isEdit = !!editingPackage;
      const url = isEdit ? `/api/packages/${editingPackage.id}` : '/api/packages';
      const method = isEdit ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: authHeaders,
        body: JSON.stringify(payload),
      });

      if (res.ok) {
        showNotification(isEdit ? 'Pacote atualizado com sucesso!' : 'Novo pacote criado!');
        setIsPackageFormOpen(false);
        setEditingPackage(null);
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao guardar pacote.');
    }
  };

  // Delete Package
  const handleDeletePackage = async (id: number, title: string) => {
    if (!window.confirm(`Tem a certeza que deseja eliminar o pacote "${title}"?`)) return;
    try {
      const res = await fetch(`/api/packages/${id}`, {
        method: 'DELETE',
        headers: authHeaders,
      });
      if (res.ok) {
        showNotification('Pacote eliminado.');
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao eliminar pacote.');
    }
  };

  // Save Media Asset
  const handleSaveMedia = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!mediaUrlInput.trim()) return;
    try {
      const res = await fetch('/api/media', {
        method: 'POST',
        headers: authHeaders,
        body: JSON.stringify({
          url: mediaUrlInput.trim(),
          title: mediaTitleInput.trim(),
          alt_text: mediaAltInput.trim(),
          category: mediaCategoryInput,
        }),
      });

      if (res.ok) {
        showNotification('Imagem adicionada à biblioteca multimédia!');
        setIsMediaUploadOpen(false);
        setMediaUrlInput('');
        setMediaTitleInput('');
        setMediaAltInput('');
        loadAllData();
      }
    } catch (e) {
      alert('Erro ao guardar imagem.');
    }
  };

  // Delete Media
  const handleDeleteMedia = async (id: number) => {
    if (!window.confirm('Tem a certeza que deseja eliminar esta imagem da biblioteca?')) return;
    try {
      const res = await fetch(`/api/media/${id}`, {
        method: 'DELETE',
        headers: authHeaders,
      });
      if (res.ok) {
        showNotification('Imagem eliminada.');
        loadAllData();
      }
    } catch (e) {
      alert('Erro ao eliminar imagem.');
    }
  };

  // Save Page
  const handleSavePage = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const payload = {
      title: formData.get('title'),
      slug: formData.get('slug'),
      excerpt: formData.get('excerpt'),
      content: formData.get('content'),
      cover_image: formData.get('cover_image'),
      status: formData.get('status') || 'publicado',
      seo_title: formData.get('seo_title'),
      seo_description: formData.get('seo_description'),
    };

    try {
      const isEdit = !!editingPage;
      const url = isEdit ? `/api/pages/${editingPage.id}` : '/api/pages';
      const method = isEdit ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: authHeaders,
        body: JSON.stringify(payload),
      });

      if (res.ok) {
        showNotification(isEdit ? 'Página atualizada com sucesso!' : 'Nova página criada!');
        setIsPageFormOpen(false);
        setEditingPage(null);
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao guardar página.');
    }
  };

  // Delete Page
  const handleDeletePage = async (id: number, title: string) => {
    if (!window.confirm(`Tem a certeza que deseja eliminar a página "${title}"?`)) return;
    try {
      const res = await fetch(`/api/pages/${id}`, {
        method: 'DELETE',
        headers: authHeaders,
      });
      if (res.ok) {
        showNotification('Página eliminada.');
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao eliminar página.');
    }
  };

  // Save Banner
  const handleSaveBanner = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const payload = {
      title: formData.get('title'),
      subtitle: formData.get('subtitle'),
      image_url: formData.get('image_url'),
      button_text: formData.get('button_text'),
      button_url: formData.get('button_url'),
      status: formData.get('status') || 'ativo',
      sort_order: formData.get('sort_order') || 0,
    };

    try {
      const isEdit = !!editingBanner;
      const url = isEdit ? `/api/banners/${editingBanner.id}` : '/api/banners';
      const method = isEdit ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: authHeaders,
        body: JSON.stringify(payload),
      });

      if (res.ok) {
        showNotification(isEdit ? 'Banner atualizado!' : 'Novo banner criado!');
        setIsBannerFormOpen(false);
        setEditingBanner(null);
        loadAllData();
      }
    } catch (e) {
      alert('Erro ao guardar banner.');
    }
  };

  // Delete Banner
  const handleDeleteBanner = async (id: number) => {
    if (!window.confirm('Eliminar este banner?')) return;
    try {
      const res = await fetch(`/api/banners/${id}`, {
        method: 'DELETE',
        headers: authHeaders,
      });
      if (res.ok) {
        showNotification('Banner eliminado.');
        loadAllData();
      }
    } catch (e) {
      alert('Erro ao eliminar banner.');
    }
  };

  // Save Destination
  const handleSaveDestination = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const payload = {
      name: formData.get('name'),
      country: formData.get('country'),
      summary: formData.get('summary'),
      description: formData.get('description'),
      cover_image: formData.get('cover_image'),
      status: formData.get('status') || 'publicado',
      is_featured: formData.get('is_featured') === 'on' ? 1 : 0,
    };

    try {
      const isEdit = !!editingDestination;
      const url = isEdit ? `/api/destinations/${editingDestination.id}` : '/api/destinations';
      const method = isEdit ? 'PUT' : 'POST';

      const res = await fetch(url, {
        method,
        headers: authHeaders,
        body: JSON.stringify(payload),
      });

      if (res.ok) {
        showNotification(isEdit ? 'Destino atualizado com sucesso!' : 'Novo destino criado!');
        setIsDestinationFormOpen(false);
        setEditingDestination(null);
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao guardar destino.');
    }
  };

  // Delete Destination
  const handleDeleteDestination = async (id: number, name: string) => {
    if (!window.confirm(`Tem a certeza que deseja eliminar o destino "${name}"?`)) return;
    try {
      const res = await fetch(`/api/destinations/${id}`, {
        method: 'DELETE',
        headers: authHeaders,
      });
      if (res.ok) {
        showNotification('Destino eliminado com sucesso.');
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao eliminar destino.');
    }
  };

  // Save Global Settings (inclui textos do Hero e visual)
  const handleSaveSettings = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget);
    const updates: Record<string, string> = {};
    formData.forEach((val, key) => {
      updates[key] = val.toString();
    });

    try {
      const res = await fetch('/api/settings', {
        method: 'PUT',
        headers: authHeaders,
        body: JSON.stringify(updates),
      });

      if (res.ok) {
        showNotification('Definições e conteúdos atualizados com sucesso!');
        loadAllData();
        onRefreshPublicData();
      }
    } catch (e) {
      alert('Erro ao atualizar definições.');
    }
  };

  // Filtered Bookings
  const filteredBookings = bookings.filter((b) => {
    if (bookingFilterStatus !== 'all' && b.status !== bookingFilterStatus) return false;
    if (bookingSearch.trim()) {
      const q = bookingSearch.toLowerCase();
      return (
        b.reference_code.toLowerCase().includes(q) ||
        b.customer_name.toLowerCase().includes(q) ||
        b.customer_phone.includes(q) ||
        (b.destination && b.destination.toLowerCase().includes(q))
      );
    }
    return true;
  });

  return (
    <div className="min-h-screen bg-[#F4F1EA] text-[#111827] flex flex-col font-sans">
      {/* Top Admin Header Bar - SaaS Executive Feel */}
      <header className="bg-[#071629] text-white px-5 py-3 border-b border-blue-950 flex items-center justify-between shadow-sm">
        <div className="flex items-center gap-4">
          <BrandLogo variant="dark" />
          <div className="hidden sm:block border-l border-blue-900/60 pl-4">
            <div className="flex items-center gap-2">
              <span className="font-bold text-xs uppercase tracking-wider text-slate-300">Backoffice Gestão</span>
              <span className="text-[10px] bg-emerald-950 text-emerald-400 border border-emerald-600/50 px-2 py-0.5 rounded font-mono font-bold">
                SQLite Ativo
              </span>
            </div>
            <span className="text-[11px] text-stone-400">
              {adminUser.name} • Perfil: <strong className="text-[#D4AF37] capitalize">{adminUser.role.replace('_', ' ')}</strong>
            </span>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={() => loadAllData()}
            className="p-1.5 rounded bg-[#0D2646] hover:bg-blue-900 text-stone-300 hover:text-white transition-colors"
            title="Recarregar Dados da Base de Dados"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} />
          </button>

          <button
            onClick={onClose}
            className="px-3 py-1.5 rounded bg-stone-800 hover:bg-stone-700 text-xs font-medium text-stone-200 transition-colors flex items-center gap-1.5"
          >
            <Globe className="w-3.5 h-3.5 text-[#D4AF37]" />
            <span>Ver Website</span>
          </button>

          <button
            onClick={onLogout}
            className="flex items-center gap-1.5 px-3 py-1.5 rounded bg-rose-950/80 hover:bg-rose-900 text-rose-300 text-xs font-semibold border border-rose-800/60 transition-colors"
          >
            <LogOut className="w-3.5 h-3.5" />
            <span>Sair</span>
          </button>
        </div>
      </header>

      {/* Main Workspace */}
      <div className="flex-1 flex flex-col md:flex-row overflow-hidden">
        {/* Sidebar Categorized by Business Areas */}
        <aside className="w-full md:w-64 bg-[#071629] border-r border-stone-800 p-3 shrink-0 flex flex-row md:flex-col gap-1 overflow-x-auto md:overflow-y-auto text-xs">
          {/* Section: Comercial */}
          <div className="hidden md:block px-3 py-1.5 text-[10px] uppercase font-bold text-stone-400 tracking-wider">
            Área Comercial
          </div>

          <button
            onClick={() => setActiveTab('dashboard')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'dashboard'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <LayoutDashboard className="w-4 h-4" />
            <span>Dashboard Executivo</span>
          </button>

          <button
            onClick={() => setActiveTab('bookings')}
            className={`flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'bookings'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <FileText className="w-4 h-4" />
              <span>Pedidos & Reservas</span>
            </div>
            {stats?.pendingBookings > 0 && (
              <span className="text-[10px] bg-[#D4AF37] text-[#071629] px-2 py-0.5 rounded-full font-black">
                {stats.pendingBookings}
              </span>
            )}
          </button>

          <button
            onClick={() => setActiveTab('packages')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'packages'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <Luggage className="w-4 h-4" />
            <span>Pacotes Turísticos</span>
          </button>

          <button
            onClick={() => setActiveTab('destinations')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'destinations'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <MapPin className="w-4 h-4" />
            <span>Destinos</span>
          </button>

          {/* Section: CMS Editorial */}
          <div className="hidden md:block px-3 pt-3 pb-1 text-[10px] uppercase font-bold text-stone-400 tracking-wider border-t border-stone-800/80 mt-2">
            Gestão Editorial (CMS)
          </div>

          <button
            onClick={() => setActiveTab('pages')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'pages'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <FileSpreadsheet className="w-4 h-4" />
            <span>Páginas Editoriais</span>
          </button>

          <button
            onClick={() => setActiveTab('media')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'media'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <Image className="w-4 h-4" />
            <span>Biblioteca Multimédia</span>
          </button>

          {/* Section: Gestão & Operação */}
          <div className="hidden md:block px-3 pt-3 pb-1 text-[10px] uppercase font-bold text-stone-400 tracking-wider border-t border-stone-800/80 mt-2">
            Gestão & Controlo
          </div>

          <button
            onClick={() => setActiveTab('messages')}
            className={`flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'messages'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <div className="flex items-center gap-2.5">
              <Mail className="w-4 h-4" />
              <span>Mensagens</span>
            </div>
            {stats?.pendingContacts > 0 && (
              <span className="text-[10px] bg-sky-400 text-[#071629] px-2 py-0.5 rounded-full font-bold">
                {stats.pendingContacts}
              </span>
            )}
          </button>

          <button
            onClick={() => setActiveTab('users')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'users'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <Users className="w-4 h-4" />
            <span>Utilizadores & Papéis</span>
          </button>

          <button
            onClick={() => setActiveTab('reports')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'reports'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <TrendingUp className="w-4 h-4" />
            <span>Relatórios & Métricas</span>
          </button>

          <button
            onClick={() => setActiveTab('settings')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'settings'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <SettingsIcon className="w-4 h-4" />
            <span>Definições da Agência</span>
          </button>

          <button
            onClick={() => setActiveTab('audit')}
            className={`flex items-center gap-2.5 px-3 py-2 rounded-lg font-medium transition-colors ${
              activeTab === 'audit'
                ? 'bg-[#D4AF37] text-[#071629] font-bold'
                : 'text-stone-300 hover:text-white hover:bg-[#0D2646]'
            }`}
          >
            <History className="w-4 h-4" />
            <span>Auditoria</span>
          </button>
        </aside>

        {/* Workspace Body with White Ivory Clean Background */}
        <main className="flex-1 p-5 sm:p-7 lg:p-8 overflow-y-auto space-y-6">
          {actionSuccess && (
            <div className="p-4 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded-xl text-xs sm:text-sm flex items-center gap-2 shadow-sm animate-in fade-in">
              <CheckCircle className="w-4 h-4 text-emerald-600 shrink-0" />
              <span>{actionSuccess}</span>
            </div>
          )}

          {/* TAB 1: DASHBOARD EXECUTIVO */}
          {activeTab === 'dashboard' && !stats && (
            <div className="flex flex-col items-center justify-center p-16 text-stone-500 space-y-3">
              <RefreshCw className="w-6 h-6 animate-spin text-[#D4AF37]" />
              <p className="text-sm font-medium">A carregar métricas executivas do sistema...</p>
            </div>
          )}

          {activeTab === 'dashboard' && stats && (
            <div className="space-y-6">
              {/* Executive Metrics Grid */}
              <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="bg-white border border-stone-200/90 rounded-xl p-5 shadow-sm space-y-1">
                  <span className="text-[11px] uppercase font-bold text-stone-500 tracking-wider">
                    Pedidos Pendentes
                  </span>
                  <div className="text-3xl font-bold text-[#071629]">
                    {stats.pendingBookings}
                  </div>
                  <span className="text-[11px] text-stone-400">Total de {stats.totalBookings} pedidos</span>
                </div>

                <div className="bg-white border border-stone-200/90 rounded-xl p-5 shadow-sm space-y-1">
                  <span className="text-[11px] uppercase font-bold text-stone-500 tracking-wider">
                    Reservas Confirmadas
                  </span>
                  <div className="text-3xl font-bold text-emerald-700">
                    {stats.confirmedBookings || 0}
                  </div>
                  <span className="text-[11px] text-stone-400">Conversão de {stats.conversionRate || 0}%</span>
                </div>

                <div className="bg-white border border-stone-200/90 rounded-xl p-5 shadow-sm space-y-1">
                  <span className="text-[11px] uppercase font-bold text-stone-500 tracking-wider">
                    Pacotes no Catálogo
                  </span>
                  <div className="text-3xl font-bold text-[#071629]">
                    {stats.totalPackages}
                  </div>
                  <span className="text-[11px] text-stone-400">
                    {stats.publishedPackages} ativos | {stats.pendingValidationPackages} pend. validação
                  </span>
                </div>

                <div className="bg-white border border-stone-200/90 rounded-xl p-5 shadow-sm space-y-1">
                  <span className="text-[11px] uppercase font-bold text-stone-500 tracking-wider">
                    Biblioteca Multimédia
                  </span>
                  <div className="text-3xl font-bold text-[#D4AF37]">
                    {stats.totalMedia || 0}
                  </div>
                  <span className="text-[11px] text-stone-400">Imagens reutilizáveis ativas</span>
                </div>
              </div>

              {/* Cape Town Alert Status Card */}
              <div className="bg-amber-50/80 border border-amber-300 rounded-xl p-5 space-y-2">
                <div className="flex items-center gap-2 text-amber-900 font-bold text-sm">
                  <AlertCircle className="w-4 h-4 text-amber-600" />
                  <span>Estado do Pacote Promocional Cape Town (31 Out - 02 Nov)</span>
                </div>
                <p className="text-xs text-amber-950/80 leading-relaxed">
                  O pacote anunciado no cartaz promocional oficial (59.500 MT/pessoa com passagem aérea, 2 noites de hotel e assistência) encontra-se registado na base de dados com o estado <strong>Pendente de Validação Operacional</strong>. Na aba de Pacotes, os gestores comerciais podem confirmar as datas definitivas de calendário e ativar a publicação.
                </p>
              </div>

              {/* Recent Bookings Table */}
              <div className="bg-white border border-stone-200 rounded-xl p-5 space-y-4 shadow-sm">
                <div className="flex items-center justify-between">
                  <h3 className="text-sm font-bold text-[#071629] uppercase tracking-wider">
                    Últimos Pedidos de Cotação Recebidos
                  </h3>
                  <button
                    onClick={() => setActiveTab('bookings')}
                    className="text-xs font-semibold text-[#D4AF37] hover:underline flex items-center gap-1"
                  >
                    <span>Ver todos</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs text-stone-700">
                    <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                      <tr>
                        <th className="p-3">Referência</th>
                        <th className="p-3">Cliente</th>
                        <th className="p-3">Serviço / Destino</th>
                        <th className="p-3">Contacto</th>
                        <th className="p-3">Data</th>
                        <th className="p-3">Estado</th>
                        <th className="p-3">Ação</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-stone-100">
                      {stats.recentBookings.map((b: any) => (
                        <tr key={b.id} className="hover:bg-stone-50">
                          <td className="p-3 font-mono font-bold text-[#071629]">{b.reference_code}</td>
                          <td className="p-3 font-medium text-stone-900">{b.customer_name}</td>
                          <td className="p-3">{b.service_type} - {b.destination || 'Geral'}</td>
                          <td className="p-3">{b.phone_country_code} {b.customer_phone}</td>
                          <td className="p-3 text-stone-500">{new Date(b.created_at).toLocaleDateString('pt-MZ')}</td>
                          <td className="p-3">
                            <span
                              className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                                b.status === 'Pendente'
                                  ? 'bg-amber-100 text-amber-800'
                                  : b.status === 'Confirmado'
                                  ? 'bg-emerald-100 text-emerald-800'
                                  : 'bg-blue-100 text-blue-800'
                              }`}
                            >
                              {b.status}
                            </span>
                          </td>
                          <td className="p-3">
                            <button
                              onClick={() => {
                                setSelectedBooking(b);
                                setBookingStatusText(b.status);
                                setBookingNoteText(b.admin_notes || '');
                                loadBookingTimeline(b.id);
                              }}
                              className="px-2.5 py-1 rounded bg-[#071629] text-white hover:bg-[#0D2646] font-semibold text-[11px]"
                            >
                              Gerir
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* TAB 2: GESTÃO DE PEDIDOS E RESERVAS (CRM COMERCIAL) */}
          {activeTab === 'bookings' && (
            <div className="space-y-4">
              <div className="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-xl border border-stone-200 shadow-sm">
                <div className="flex items-center gap-3 w-full sm:w-auto">
                  <div className="relative flex-1 sm:w-64">
                    <Search className="w-3.5 h-3.5 text-stone-400 absolute left-3 top-2.5" />
                    <input
                      type="text"
                      placeholder="Pesquisar por referência, cliente, telefone..."
                      value={bookingSearch}
                      onChange={(e) => setBookingSearch(e.target.value)}
                      className="w-full pl-8 pr-3 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-[#071629]"
                    />
                  </div>

                  <select
                    value={bookingFilterStatus}
                    onChange={(e) => setBookingFilterStatus(e.target.value)}
                    className="px-3 py-1.5 bg-stone-50 border border-stone-300 rounded-lg text-xs font-medium focus:outline-none"
                  >
                    <option value="all">Todos os Estados</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Em análise">Em análise</option>
                    <option value="Contactado">Contactado</option>
                    <option value="Cotação enviada">Cotação enviada</option>
                    <option value="Confirmado">Confirmado</option>
                    <option value="Cancelado">Cancelado</option>
                  </select>
                </div>

                <div className="flex items-center gap-3">
                  <span className="text-xs text-stone-500">
                    {filteredBookings.length} pedidos
                  </span>
                  <button
                    onClick={handleExportCsv}
                    className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition-colors cursor-pointer"
                  >
                    <Download className="w-3.5 h-3.5" />
                    <span>Exportar CSV</span>
                  </button>
                </div>
              </div>

              {/* Bookings Table */}
              <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                <div className="overflow-x-auto">
                  <table className="w-full text-left text-xs text-stone-700">
                    <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                      <tr>
                        <th className="p-3">Ref. Única</th>
                        <th className="p-3">Cliente</th>
                        <th className="p-3">Contacto</th>
                        <th className="p-3">Serviço / Pacote</th>
                        <th className="p-3">Datas Pretendidas</th>
                        <th className="p-3">Pax</th>
                        <th className="p-3">Estado</th>
                        <th className="p-3">Notas Internas</th>
                        <th className="p-3">Ações</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-stone-100">
                      {filteredBookings.map((b) => (
                        <tr key={b.id} className="hover:bg-stone-50">
                          <td className="p-3 font-mono font-bold text-[#071629]">{b.reference_code}</td>
                          <td className="p-3 font-medium text-stone-900">
                            <div>{b.customer_name}</div>
                            {b.customer_email && <div className="text-[10px] text-stone-400">{b.customer_email}</div>}
                          </td>
                          <td className="p-3">{b.phone_country_code} {b.customer_phone}</td>
                          <td className="p-3">
                            <span className="font-semibold text-stone-800">{b.service_type}</span>
                            {b.destination && <div className="text-[11px] text-stone-500">{b.destination}</div>}
                          </td>
                          <td className="p-3 text-stone-600">{b.preferred_dates || 'Flexíveis'}</td>
                          <td className="p-3 text-center">{b.travelers_count}</td>
                          <td className="p-3">
                            <span
                              className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                                b.status === 'Pendente'
                                  ? 'bg-amber-100 text-amber-800 border border-amber-300'
                                  : b.status === 'Em análise'
                                  ? 'bg-blue-100 text-blue-800'
                                  : b.status === 'Contactado'
                                  ? 'bg-purple-100 text-purple-800'
                                  : b.status === 'Cotação enviada'
                                  ? 'bg-sky-100 text-sky-800'
                                  : b.status === 'Confirmado'
                                  ? 'bg-emerald-100 text-emerald-800'
                                  : 'bg-rose-100 text-rose-800'
                              }`}
                            >
                              {b.status}
                            </span>
                          </td>
                          <td className="p-3 max-w-[180px] truncate text-[11px] text-stone-500 italic">
                            {b.admin_notes || '—'}
                          </td>
                          <td className="p-3">
                            <button
                              onClick={() => {
                                setSelectedBooking(b);
                                setBookingStatusText(b.status);
                                setBookingNoteText(b.admin_notes || '');
                                loadBookingTimeline(b.id);
                              }}
                              className="px-2.5 py-1 rounded bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-semibold text-[11px]"
                            >
                              Histórico & Estado
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* TAB 3: GESTÃO DE PACOTES (COM 7 SEPARADORES) */}
          {activeTab === 'packages' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Catálogo de Pacotes Turísticos</h2>
                  <p className="text-xs text-stone-500">Gestão comercial de preços, validades, itinerários e publicação</p>
                </div>

                <button
                  onClick={() => {
                    setEditingPackage(null);
                    setPackageFormActiveTab(1);
                    setIsPackageFormOpen(true);
                  }}
                  className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider"
                >
                  <Plus className="w-4 h-4" />
                  <span>Novo Pacote</span>
                </button>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {packages.map((pkg) => (
                  <div
                    key={pkg.id}
                    className="bg-white border border-stone-200 rounded-xl p-4 flex flex-col justify-between space-y-3 shadow-sm hover:border-[#D4AF37] transition-colors"
                  >
                    <div className="space-y-2">
                      <div className="flex items-center justify-between text-xs">
                        <span className="font-semibold text-stone-700">{pkg.destination_name}</span>
                        <span
                          className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                            pkg.status === 'publicado'
                              ? 'bg-emerald-100 text-emerald-800'
                              : pkg.status === 'pendente_validacao'
                              ? 'bg-amber-100 text-amber-800 border border-amber-300'
                              : 'bg-stone-200 text-stone-700'
                          }`}
                        >
                          {pkg.status}
                        </span>
                      </div>

                      <h3 className="text-sm font-bold text-[#071629] line-clamp-1">{pkg.title}</h3>
                      <p className="text-xs text-stone-500 line-clamp-2">{pkg.summary}</p>

                      <div className="text-sm font-bold text-[#071629]">
                        {formatMZN(pkg.price_per_person ?? pkg.total_price, pkg.currency, pkg.price_type)}
                      </div>
                    </div>

                    <div className="pt-3 border-t border-stone-100 flex items-center justify-between">
                      <span className="text-[11px] text-stone-400">
                        {pkg.duration_nights}N / {pkg.duration_days}D
                      </span>

                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => {
                            setEditingPackage(pkg);
                            setPackageFormActiveTab(1);
                            setIsPackageFormOpen(true);
                          }}
                          className="px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-[#071629] text-xs font-semibold flex items-center gap-1"
                        >
                          <Edit2 className="w-3 h-3" />
                          <span>Editar</span>
                        </button>

                        <button
                          onClick={() => handleDeletePackage(pkg.id, pkg.title)}
                          className="p-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700"
                          title="Eliminar Pacote"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 4: DESTINOS TURÍSTICOS */}
          {activeTab === 'destinations' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Destinos Turísticos</h2>
                  <p className="text-xs text-stone-500">Gestão dos destinos nacionais e internacionais do portfólio</p>
                </div>

                <button
                  onClick={() => {
                    setEditingDestination(null);
                    setIsDestinationFormOpen(true);
                  }}
                  className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider"
                >
                  <Plus className="w-4 h-4" />
                  <span>Novo Destino</span>
                </button>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {destinations.map((d) => (
                  <div key={d.id} className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                    <div className="h-32 w-full relative bg-stone-800">
                      <img src={d.cover_image} alt={d.name} className="w-full h-full object-cover" />
                      <div className="absolute inset-0 bg-gradient-to-t from-[#071629] via-transparent" />
                      <div className="absolute bottom-2 left-3 right-3 text-white">
                        <span className="text-[10px] uppercase font-bold text-[#D4AF37]">{d.country}</span>
                        <h3 className="text-base font-bold">{d.name}</h3>
                      </div>
                    </div>

                    <div className="p-4 space-y-3">
                      <p className="text-xs text-stone-600 line-clamp-2">{d.summary}</p>
                      <div className="flex items-center justify-between pt-2 border-t border-stone-100">
                        <span className="text-xs text-stone-400">
                          {d.package_count || 0} pacotes
                        </span>
                        <div className="flex items-center gap-2">
                          <button
                            onClick={() => {
                              setEditingDestination(d);
                              setIsDestinationFormOpen(true);
                            }}
                            className="px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-xs font-semibold"
                          >
                            Editar
                          </button>
                          <button
                            onClick={() => handleDeleteDestination(d.id, d.name)}
                            className="p-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 5: CMS EDITORIAL - HOMEPAGE & HERO */}
          {activeTab === 'content' && (
            <div className="max-w-3xl space-y-5">
              <div>
                <h2 className="text-lg font-bold text-[#071629]">Gestor de Conteúdos da Homepage</h2>
                <p className="text-xs text-stone-500">
                  Edite os títulos, chamadas de ação e imagens principais da página pública sem programar.
                </p>
              </div>

              <form onSubmit={handleSaveSettings} className="bg-white border border-stone-200 rounded-xl p-6 space-y-4 shadow-sm text-xs">
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold text-stone-700 uppercase tracking-wider mb-1">
                      Título Principal do Hero (H1)
                    </label>
                    <input
                      type="text"
                      name="hero_title"
                      defaultValue={settings.hero_title || 'O mundo espera por si.'}
                      className="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-900"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase tracking-wider mb-1">
                      Subtítulo do Hero
                    </label>
                    <textarea
                      rows={2}
                      name="hero_subtitle"
                      defaultValue={settings.hero_subtitle || 'Descubra novos destinos com experiências de viagem pensadas à sua medida.'}
                      className="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm text-stone-900"
                    />
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Texto do Botão Primário
                      </label>
                      <input
                        type="text"
                        name="hero_cta_primary"
                        defaultValue={settings.hero_cta_primary || 'Explorar Destinos'}
                        className="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs text-stone-900"
                      />
                    </div>

                    <div>
                      <label className="block font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Texto do Botão Secundário
                      </label>
                      <input
                        type="text"
                        name="hero_cta_secondary"
                        defaultValue={settings.hero_cta_secondary || 'Planear a Minha Viagem'}
                        className="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs text-stone-900"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase tracking-wider mb-1">
                      URL da Imagem de Fundo Panorâmica
                    </label>
                    <input
                      type="url"
                      name="hero_bg_image"
                      defaultValue={settings.hero_bg_image || 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1920&q=80'}
                      className="w-full px-3.5 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs font-mono"
                    />
                  </div>
                </div>

                <div className="flex justify-end pt-3 border-t border-stone-200">
                  <button
                    type="submit"
                    className="px-6 py-2.5 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold uppercase tracking-wider text-xs shadow-sm cursor-pointer"
                  >
                    Guardar Alterações da Homepage
                  </button>
                </div>
              </form>
            </div>
          )}

          {/* TAB 6: PÁGINAS EDITORIAIS INSTITUCIONAIS */}
          {activeTab === 'pages' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Páginas Editoriais do Website</h2>
                  <p className="text-xs text-stone-500">Crie e edite páginas institucionais com conteúdos ricos e metadados SEO</p>
                </div>

                <button
                  onClick={() => {
                    setEditingPage(null);
                    setIsPageFormOpen(true);
                  }}
                  className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider"
                >
                  <Plus className="w-4 h-4" />
                  <span>Nova Página</span>
                </button>
              </div>

              <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                <table className="w-full text-left text-xs text-stone-700">
                  <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                    <tr>
                      <th className="p-3">Título da Página</th>
                      <th className="p-3">URL / Slug</th>
                      <th className="p-3">Estado</th>
                      <th className="p-3">Última Atualização</th>
                      <th className="p-3">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-stone-100">
                    {pages.map((p) => (
                      <tr key={p.id} className="hover:bg-stone-50">
                        <td className="p-3 font-semibold text-[#071629]">{p.title}</td>
                        <td className="p-3 font-mono text-[11px] text-stone-500">/{p.slug}</td>
                        <td className="p-3">
                          <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                            p.status === 'publicado' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-700'
                          }`}>
                            {p.status}
                          </span>
                        </td>
                        <td className="p-3 text-stone-400">
                          {p.updated_at ? new Date(p.updated_at).toLocaleDateString('pt-MZ') : '—'}
                        </td>
                        <td className="p-3 flex items-center gap-2">
                          <button
                            onClick={() => {
                              setEditingPage(p);
                              setIsPageFormOpen(true);
                            }}
                            className="px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-[#071629] font-medium text-xs"
                          >
                            Editar
                          </button>
                          <button
                            onClick={() => handleDeletePage(p.id, p.title)}
                            className="p-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 7: BANNERS & CAMPANHAS */}
          {activeTab === 'banners' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Banners & Campanhas Promocionais</h2>
                  <p className="text-xs text-stone-500">Gerir destaques visuais temporários e campanhas especiais</p>
                </div>

                <button
                  onClick={() => {
                    setEditingBanner(null);
                    setIsBannerFormOpen(true);
                  }}
                  className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider"
                >
                  <Plus className="w-4 h-4" />
                  <span>Novo Banner</span>
                </button>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {banners.map((b) => (
                  <div key={b.id} className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between">
                    <div className="h-36 w-full relative bg-stone-900">
                      <img src={b.image_url} alt={b.title} className="w-full h-full object-cover" />
                      <div className="absolute inset-0 bg-gradient-to-t from-[#071629] via-black/40 to-transparent" />
                      <div className="absolute bottom-3 left-4 right-4 text-white">
                        <span className="text-[10px] uppercase font-bold text-[#D4AF37]">{b.status}</span>
                        <h4 className="text-base font-bold font-editorial">{b.title}</h4>
                      </div>
                    </div>

                    <div className="p-4 space-y-2 text-xs">
                      <p className="text-stone-600 line-clamp-2">{b.subtitle}</p>
                      <div className="flex items-center justify-between pt-2 border-t border-stone-100">
                        <span className="text-[11px] text-stone-400">Botão: {b.button_text}</span>
                        <div className="flex items-center gap-2">
                          <button
                            onClick={() => {
                              setEditingBanner(b);
                              setIsBannerFormOpen(true);
                            }}
                            className="px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-xs font-semibold"
                          >
                            Editar
                          </button>
                          <button
                            onClick={() => handleDeleteBanner(b.id)}
                            className="p-1 rounded bg-rose-50 text-rose-700"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 8: BIBLIOTECA MULTIMÉDIA */}
          {activeTab === 'media' && (
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Biblioteca Multimédia Centralizada</h2>
                  <p className="text-xs text-stone-500">Armazene, organize e reutilize imagens em pacotes, destinos e banners</p>
                </div>

                <button
                  onClick={() => setIsMediaUploadOpen(true)}
                  className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider cursor-pointer"
                >
                  <Plus className="w-4 h-4" />
                  <span>Adicionar Imagem</span>
                </button>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                {mediaList.map((m) => (
                  <div key={m.id} className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm flex flex-col justify-between group">
                    <div className="h-32 w-full bg-stone-100 overflow-hidden relative">
                      <img
                        src={m.file_path}
                        alt={m.alt_text || m.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        onError={(e) => {
                          (e.currentTarget as HTMLImageElement).src =
                            'https://images.unsplash.com/photo-1509356843151-3e7d96241e11?auto=format&fit=crop&w=1200&q=80';
                        }}
                      />
                      <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                          onClick={() => handleDeleteMedia(m.id)}
                          className="p-1 rounded bg-rose-900/80 text-white"
                          title="Eliminar"
                        >
                          <Trash2 className="w-3 h-3" />
                        </button>
                      </div>
                    </div>

                    <div className="p-3 text-[11px] space-y-1">
                      <div className="font-semibold text-stone-900 truncate" title={m.title}>{m.title}</div>
                      <div className="text-stone-400 truncate text-[10px]">{m.category} • {(m.file_size / 1024).toFixed(0)} KB</div>
                      <button
                        onClick={() => {
                          navigator.clipboard.writeText(m.file_path);
                          showNotification('URL da imagem copiado para a área de transferência!');
                        }}
                        className="text-[10px] text-[#071629] font-bold hover:underline"
                      >
                        Copiar URL
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 9: MENU DE NAVEGAÇÃO */}
          {activeTab === 'navigation' && (
            <div className="max-w-2xl space-y-4">
              <div>
                <h2 className="text-lg font-bold text-[#071629]">Menu de Navegação</h2>
                <p className="text-xs text-stone-500">Configure os links e a ordem de apresentação no cabeçalho e rodapé</p>
              </div>

              <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                <table className="w-full text-left text-xs text-stone-700">
                  <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                    <tr>
                      <th className="p-3">Ordem</th>
                      <th className="p-3">Rótulo (Label)</th>
                      <th className="p-3">Destino (Secção/URL)</th>
                      <th className="p-3">Localização</th>
                      <th className="p-3">Estado</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-stone-100">
                    {navItems.map((n) => (
                      <tr key={n.id}>
                        <td className="p-3 font-bold text-stone-500">{n.sort_order}</td>
                        <td className="p-3 font-semibold text-stone-900">{n.label}</td>
                        <td className="p-3 font-mono text-[11px] text-stone-500">{n.url}</td>
                        <td className="p-3 uppercase text-[10px] text-stone-500">{n.location}</td>
                        <td className="p-3">
                          <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Ativo
                          </span>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 10: UTILIZADORES E PAPÉIS */}
          {activeTab === 'users' && (
            <div className="max-w-4xl space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <h2 className="text-lg font-bold text-[#071629]">Utilizadores & Controlo de Acesso (RBAC)</h2>
                  <p className="text-xs text-stone-500">Perfis de Superadministrador, Gestor Comercial, Editor e Atendimento</p>
                </div>
              </div>

              <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                <table className="w-full text-left text-xs text-stone-700">
                  <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                    <tr>
                      <th className="p-3">Nome</th>
                      <th className="p-3">Email de Acesso</th>
                      <th className="p-3">Papel / Função</th>
                      <th className="p-3">Estado</th>
                      <th className="p-3">Data de Registo</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-stone-100">
                    {usersList.map((u) => (
                      <tr key={u.id} className="hover:bg-stone-50">
                        <td className="p-3 font-semibold text-stone-900">{u.name}</td>
                        <td className="p-3 font-mono text-[11px] text-stone-600">{u.email}</td>
                        <td className="p-3">
                          <span className="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#071629] text-[#D4AF37]">
                            {u.role.replace('_', ' ')}
                          </span>
                        </td>
                        <td className="p-3">
                          <span className="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Ativo
                          </span>
                        </td>
                        <td className="p-3 text-stone-400">
                          {u.status || 'Ativo'}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* TAB 11: RELATÓRIOS E CONVERSÃO */}
          {activeTab === 'reports' && stats && (
            <div className="space-y-6 max-w-4xl">
              <div>
                <h2 className="text-lg font-bold text-[#071629]">Relatórios de Desempenho e Conversão</h2>
                <p className="text-xs text-stone-500">Métricas comerciais calculadas em tempo real a partir dos registos SQLite</p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="p-5 bg-white border border-stone-200 rounded-xl shadow-sm">
                  <span className="text-xs uppercase font-bold text-stone-500">Taxa de Conversão</span>
                  <div className="text-3xl font-black text-emerald-700 mt-1">
                    {stats.conversionRate}%
                  </div>
                  <p className="text-[11px] text-stone-400 mt-1">Pedidos convertidos em reservas confirmadas</p>
                </div>

                <div className="p-5 bg-white border border-stone-200 rounded-xl shadow-sm">
                  <span className="text-xs uppercase font-bold text-stone-500">Total de Pedidos Registados</span>
                  <div className="text-3xl font-black text-[#071629] mt-1">
                    {stats.totalBookings}
                  </div>
                  <p className="text-[11px] text-stone-400 mt-1">{stats.pendingBookings} pendentes | {stats.confirmedBookings} confirmados</p>
                </div>

                <div className="p-5 bg-white border border-stone-200 rounded-xl shadow-sm">
                  <span className="text-xs uppercase font-bold text-stone-500">Ofertas Comerciais Ativas</span>
                  <div className="text-3xl font-black text-[#D4AF37] mt-1">
                    {stats.publishedPackages}
                  </div>
                  <p className="text-[11px] text-stone-400 mt-1">Distribuídas por {stats.totalDestinations} destinos</p>
                </div>
              </div>

              {/* Status Breakdown Bar */}
              <div className="bg-white border border-stone-200 rounded-xl p-5 shadow-sm space-y-3">
                <h4 className="text-xs uppercase font-bold text-stone-700 tracking-wider">
                  Distribuição dos Pedidos por Estado
                </h4>
                <div className="space-y-2 text-xs">
                  <div>
                    <div className="flex justify-between text-stone-600 mb-1">
                      <span>Pendentes ({stats.pendingBookings})</span>
                      <span>{stats.totalBookings > 0 ? Math.round((stats.pendingBookings / stats.totalBookings) * 100) : 0}%</span>
                    </div>
                    <div className="w-full bg-stone-100 rounded-full h-2">
                      <div className="bg-amber-500 h-2 rounded-full" style={{ width: `${stats.totalBookings > 0 ? (stats.pendingBookings / stats.totalBookings) * 100 : 0}%` }} />
                    </div>
                  </div>

                  <div>
                    <div className="flex justify-between text-stone-600 mb-1">
                      <span>Contactados ({stats.contactedBookings || 0})</span>
                      <span>{stats.totalBookings > 0 ? Math.round(((stats.contactedBookings || 0) / stats.totalBookings) * 100) : 0}%</span>
                    </div>
                    <div className="w-full bg-stone-100 rounded-full h-2">
                      <div className="bg-purple-600 h-2 rounded-full" style={{ width: `${stats.totalBookings > 0 ? ((stats.contactedBookings || 0) / stats.totalBookings) * 100 : 0}%` }} />
                    </div>
                  </div>

                  <div>
                    <div className="flex justify-between text-stone-600 mb-1">
                      <span>Confirmados ({stats.confirmedBookings || 0})</span>
                      <span>{stats.conversionRate}%</span>
                    </div>
                    <div className="w-full bg-stone-100 rounded-full h-2">
                      <div className="bg-emerald-600 h-2 rounded-full" style={{ width: `${stats.conversionRate}%` }} />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* TAB 12: MENSAGENS */}
          {activeTab === 'messages' && (
            <div className="space-y-4">
              <h2 className="text-lg font-bold text-[#071629]">Mensagens do Formulário de Contacto</h2>
              <div className="space-y-3">
                {messages.map((m) => (
                  <div key={m.id} className="bg-white border border-stone-200 rounded-xl p-5 space-y-3 shadow-sm">
                    <div className="flex items-start justify-between">
                      <div>
                        <h3 className="text-sm font-bold text-[#071629]">{m.subject}</h3>
                        <div className="text-xs text-stone-500">
                          De: <span className="font-semibold text-stone-800">{m.name}</span> ({m.email})
                          {m.phone && <span className="ml-2">| Tel: {m.phone}</span>}
                        </div>
                      </div>
                      <span className="text-xs text-stone-400">{new Date(m.created_at).toLocaleString('pt-MZ')}</span>
                    </div>
                    <p className="text-xs text-stone-700 bg-stone-50 p-3 rounded-lg leading-relaxed whitespace-pre-line">
                      {m.message}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TAB 13: DEFINIÇÕES GLOBAIS */}
          {activeTab === 'settings' && (
            <div className="max-w-3xl space-y-5">
              <div>
                <h2 className="text-lg font-bold text-[#071629]">Definições Oficiais da Zebra Tours</h2>
                <p className="text-xs text-stone-500">Configuração de contactos, moeda, slogan e dados institucionais</p>
              </div>

              <form onSubmit={handleSaveSettings} className="bg-white border border-stone-200 rounded-xl p-6 space-y-4 shadow-sm text-xs">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">Nome da Agência</label>
                    <input
                      type="text"
                      name="site_name"
                      defaultValue={settings.site_name || 'Zebra Tours Moçambique'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">Slogan</label>
                    <input
                      type="text"
                      name="slogan"
                      defaultValue={settings.slogan || 'O seu próximo destino começa aqui!'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">Telefone Principal</label>
                    <input
                      type="text"
                      name="phone"
                      defaultValue={settings.phone || '+258 85 104 1826'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">WhatsApp</label>
                    <input
                      type="text"
                      name="whatsapp"
                      defaultValue={settings.whatsapp || '+258 85 104 1826'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">Email Institucional</label>
                    <input
                      type="email"
                      name="email"
                      defaultValue={settings.email || 'info@zebratours.co.mz'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold text-stone-700 uppercase mb-1">Sede / Localização</label>
                    <input
                      type="text"
                      name="location"
                      defaultValue={settings.location || 'Maputo, Moçambique'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>
                </div>

                <div className="flex justify-end pt-3 border-t border-stone-200">
                  <button
                    type="submit"
                    className="px-6 py-2.5 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider"
                  >
                    Guardar Definições
                  </button>
                </div>
              </form>
            </div>
          )}

          {/* TAB 14: AUDITORIA */}
          {activeTab === 'audit' && (
            <div className="space-y-4">
              <h2 className="text-lg font-bold text-[#071629]">Histórico e Registos de Auditoria</h2>
              <div className="bg-white border border-stone-200 rounded-xl overflow-hidden shadow-sm">
                <table className="w-full text-left text-xs text-stone-700">
                  <thead className="text-[10px] uppercase bg-stone-100 text-stone-600 border-b border-stone-200">
                    <tr>
                      <th className="p-3">Data / Hora</th>
                      <th className="p-3">Ação</th>
                      <th className="p-3">Entidade</th>
                      <th className="p-3">Operador</th>
                      <th className="p-3">Metadados</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-stone-100">
                    {auditLogs.map((log) => (
                      <tr key={log.id} className="hover:bg-stone-50">
                        <td className="p-3 text-stone-400">{new Date(log.created_at).toLocaleString('pt-MZ')}</td>
                        <td className="p-3 font-bold text-[#071629]">{log.action}</td>
                        <td className="p-3">{log.entity_type} {log.entity_id ? `(#${log.entity_id})` : ''}</td>
                        <td className="p-3 text-stone-800 font-medium">{log.user_name || 'Admin'}</td>
                        <td className="p-3 text-stone-500 truncate max-w-xs">{log.metadata}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </main>
      </div>

      {/* MODAL: SEGUIMENTO COMERCIAL E LINHA DO TEMPO DO PEDIDO */}
      {selectedBooking && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm overflow-y-auto animate-in fade-in">
          <div className="bg-white rounded-2xl w-full max-w-xl p-6 text-stone-800 space-y-4 shadow-2xl border border-stone-200 my-auto">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <div>
                <span className="text-xs font-mono font-bold text-[#D4AF37]">{selectedBooking.reference_code}</span>
                <h3 className="text-base font-bold text-[#071629]">{selectedBooking.customer_name}</h3>
              </div>
              <button onClick={() => setSelectedBooking(null)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            <div className="text-xs space-y-2 bg-[#F4F1EA] p-3 rounded-xl text-stone-700">
              <div className="grid grid-cols-2 gap-2">
                <div><strong>Telefone:</strong> {selectedBooking.phone_country_code} {selectedBooking.customer_phone}</div>
                {selectedBooking.customer_email && <div><strong>Email:</strong> {selectedBooking.customer_email}</div>}
                <div><strong>Serviço:</strong> {selectedBooking.service_type}</div>
                <div><strong>Destino:</strong> {selectedBooking.destination || 'Geral'}</div>
                <div><strong>Datas:</strong> {selectedBooking.preferred_dates || 'Flexíveis'}</div>
                <div><strong>Viajantes:</strong> {selectedBooking.travelers_count}</div>
              </div>
              {selectedBooking.message && (
                <div className="pt-2 border-t border-stone-300">
                  <strong>Mensagem do Viajante:</strong>
                  <p className="mt-0.5 italic text-stone-600">{selectedBooking.message}</p>
                </div>
              )}
            </div>

            {/* Timeline History */}
            {bookingHistory.length > 0 && (
              <div className="space-y-2 pt-1">
                <span className="text-[11px] font-bold uppercase text-stone-500 tracking-wider">
                  Histórico de Contactos e Alterações
                </span>
                <div className="space-y-1.5 max-h-36 overflow-y-auto text-xs pr-1">
                  {bookingHistory.map((h) => (
                    <div key={h.id} className="p-2 bg-stone-50 rounded border border-stone-200 space-y-0.5">
                      <div className="flex justify-between font-semibold text-stone-800">
                        <span>{h.author_name} • {h.action}</span>
                        <span className="text-[10px] text-stone-400">{new Date(h.created_at).toLocaleDateString('pt-MZ')}</span>
                      </div>
                      {h.notes && <p className="text-[11px] text-stone-600 italic">{h.notes}</p>}
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Update Status and Internal Note */}
            <div className="space-y-3 pt-2">
              <div>
                <label className="block text-xs font-bold text-stone-700 uppercase mb-1">
                  Alterar Estado Comercial
                </label>
                <select
                  value={bookingStatusText}
                  onChange={(e: any) => setBookingStatusText(e.target.value)}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#071629]"
                >
                  <option value="Pendente">Pendente</option>
                  <option value="Em análise">Em análise</option>
                  <option value="Contactado">Contactado</option>
                  <option value="Cotação enviada">Cotação enviada</option>
                  <option value="Confirmado">Confirmado</option>
                  <option value="Cancelado">Cancelado</option>
                </select>
              </div>

              <div>
                <label className="block text-xs font-bold text-stone-700 uppercase mb-1">
                  Nota Interna / Registo de Seguimento (Confidencial)
                </label>
                <textarea
                  rows={3}
                  value={bookingNoteText}
                  onChange={(e) => setBookingNoteText(e.target.value)}
                  placeholder="Ex: Cotação oficial enviada por WhatsApp. Aguarda aprovação do bilhete..."
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs"
                />
              </div>
            </div>

            <div className="flex justify-end gap-2 pt-3 border-t border-stone-200">
              <button
                onClick={() => setSelectedBooking(null)}
                className="px-4 py-2 rounded-lg bg-stone-200 hover:bg-stone-300 text-xs font-semibold"
              >
                Cancelar
              </button>
              <button
                onClick={handleUpdateBookingWithTimeline}
                className="px-5 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold text-xs uppercase tracking-wider cursor-pointer"
              >
                Gravar Atualização
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL: FORMULÁRIO DE PACOTE COM 7 SEPARADORES */}
      {isPackageFormOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm overflow-y-auto">
          <div className="bg-white rounded-2xl w-full max-w-3xl p-6 text-stone-800 space-y-4 my-auto max-h-[92vh] overflow-y-auto border border-stone-200 shadow-2xl">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <div>
                <h3 className="text-base font-bold text-[#071629]">
                  {editingPackage ? 'Editar Pacote Turístico' : 'Criar Novo Pacote Turístico'}
                </h3>
                <span className="text-xs text-stone-500">Formulário comercial estruturado</span>
              </div>
              <button onClick={() => setIsPackageFormOpen(false)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            {/* 7 Tabs Navigation */}
            <div className="flex border-b border-stone-200 gap-1 overflow-x-auto text-xs pb-1">
              {[
                { id: 1, label: '1. Geral' },
                { id: 2, label: '2. Destinos' },
                { id: 3, label: '3. Preços & Datas' },
                { id: 4, label: '4. Descrição' },
                { id: 5, label: '5. Serviços' },
                { id: 6, label: '6. Imagem' },
                { id: 7, label: '7. Publicação' },
              ].map((tab) => (
                <button
                  key={tab.id}
                  type="button"
                  onClick={() => setPackageFormActiveTab(tab.id)}
                  className={`px-3 py-1.5 rounded-lg whitespace-nowrap font-semibold transition-colors ${
                    packageFormActiveTab === tab.id
                      ? 'bg-[#071629] text-[#D4AF37]'
                      : 'text-stone-600 hover:bg-stone-100'
                  }`}
                >
                  {tab.label}
                </button>
              ))}
            </div>

            <form onSubmit={handleSavePackage} className="space-y-4 text-xs">
              {/* TAB 1: GERAL */}
              {packageFormActiveTab === 1 && (
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Título do Pacote *</label>
                    <input
                      type="text"
                      name="title"
                      required
                      defaultValue={editingPackage?.title || ''}
                      placeholder="Ex: Cape Town Especial Fim de Semana"
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm"
                    />
                  </div>

                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Resumo Comercial *</label>
                    <input
                      type="text"
                      name="summary"
                      required
                      defaultValue={editingPackage?.summary || ''}
                      placeholder="Escapadinha de 2 noites com voo e alojamento..."
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block font-bold mb-1 text-stone-700">Duração Noites</label>
                      <input
                        type="number"
                        name="duration_nights"
                        defaultValue={editingPackage?.duration_nights || 2}
                        className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                      />
                    </div>
                    <div>
                      <label className="block font-bold mb-1 text-stone-700">Duração Dias</label>
                      <input
                        type="number"
                        name="duration_days"
                        defaultValue={editingPackage?.duration_days || 3}
                        className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                      />
                    </div>
                  </div>
                </div>
              )}

              {/* TAB 2: DESTINOS */}
              {packageFormActiveTab === 2 && (
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Destino Principal</label>
                    <select
                      name="primary_destination_id"
                      defaultValue={editingPackage?.primary_destination_id || destinations[0]?.id}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    >
                      {destinations.map((d) => (
                        <option key={d.id} value={d.id}>{d.name} ({d.country})</option>
                      ))}
                    </select>
                  </div>
                </div>
              )}

              {/* TAB 3: PREÇOS & DATAS */}
              {packageFormActiveTab === 3 && (
                <div className="space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    <div>
                      <label className="block font-bold mb-1 text-stone-700">Preço por Pessoa (MT)</label>
                      <input
                        type="number"
                        name="price_per_person"
                        defaultValue={editingPackage?.price_per_person || ''}
                        placeholder="59500"
                        className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm"
                      />
                    </div>

                    <div>
                      <label className="block font-bold mb-1 text-stone-700">Tipo de Preço</label>
                      <select
                        name="price_type"
                        defaultValue={editingPackage?.price_type || 'indicativo'}
                        className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                      >
                        <option value="indicativo">Indicativo (A partir de)</option>
                        <option value="fixo">Fixo</option>
                        <option value="sob_consulta">Sob Consulta</option>
                      </select>
                    </div>
                  </div>

                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Período Anunciado / Datas de Viagem</label>
                    <input
                      type="text"
                      name="travel_dates"
                      defaultValue={editingPackage?.travel_dates || ''}
                      placeholder="Ex: 31 de Outubro a 02 de Novembro"
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Validade Comercial da Oferta</label>
                    <input
                      type="text"
                      name="validity_date"
                      defaultValue={editingPackage?.validity_date || ''}
                      placeholder="Sujeito a confirmação de disponibilidade"
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>
                </div>
              )}

              {/* TAB 4: DESCRIÇÃO COMPLETA */}
              {packageFormActiveTab === 4 && (
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Descrição Editorial Detalhada *</label>
                  <textarea
                    rows={6}
                    name="description"
                    required
                    defaultValue={editingPackage?.description || ''}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs leading-relaxed"
                  />
                </div>
              )}

              {/* TAB 5: SERVIÇOS & CONDIÇÕES */}
              {packageFormActiveTab === 5 && (
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Serviços Incluídos (separados por ponto e vírgula ;)</label>
                    <input
                      type="text"
                      name="included_services"
                      defaultValue={editingPackage?.included_services || ''}
                      placeholder="Passagem aérea de ida e volta; 2 noites de hotel; Assistência de viagem"
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Serviços Excluídos (separados por ponto e vírgula ;)</label>
                    <input
                      type="text"
                      name="excluded_services"
                      defaultValue={editingPackage?.excluded_services || ''}
                      placeholder="Despesas de carácter pessoal; Vistos ou taxas alfandegárias"
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>

                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Condições de Reserva e Cancelamento</label>
                    <input
                      type="text"
                      name="conditions"
                      defaultValue={editingPackage?.conditions || ''}
                      placeholder="Tarifa indicativa em quarto duplo. Sujeito a confirmação da companhia aérea."
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                    />
                  </div>
                </div>
              )}

              {/* TAB 6: IMAGEM */}
              {packageFormActiveTab === 6 && (
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold mb-1 text-stone-700">URL da Imagem de Capa</label>
                    <input
                      type="url"
                      name="cover_image"
                      defaultValue={editingPackage?.cover_image || ''}
                      placeholder="https://images.unsplash.com/..."
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-mono text-xs"
                    />
                  </div>
                  <p className="text-[11px] text-stone-500">
                    Dica: Pode também copiar URLs diretamente da <strong>Biblioteca Multimédia</strong> da agência.
                  </p>
                </div>
              )}

              {/* TAB 7: PUBLICAÇÃO & ESTADO */}
              {packageFormActiveTab === 7 && (
                <div className="space-y-3">
                  <div>
                    <label className="block font-bold mb-1 text-stone-700">Estado de Publicação</label>
                    <select
                      name="status"
                      defaultValue={editingPackage?.status || 'publicado'}
                      className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs font-semibold"
                    >
                      <option value="publicado">Publicado (Visível no site)</option>
                      <option value="pendente_validacao">Pendente de Validação Operacional</option>
                      <option value="rascunho">Rascunho (Privado)</option>
                    </select>
                  </div>

                  <div className="flex items-center gap-2 pt-2">
                    <input
                      type="checkbox"
                      name="is_featured"
                      id="feat_pkg_box"
                      defaultChecked={!!editingPackage?.is_featured}
                      className="rounded"
                    />
                    <label htmlFor="feat_pkg_box" className="font-bold text-stone-800">
                      Colocar em Destaque na Homepage
                    </label>
                  </div>
                </div>
              )}

              <div className="flex justify-between items-center pt-3 border-t border-stone-200">
                <div className="text-stone-400 text-[11px]">
                  Passo {packageFormActiveTab} de 7
                </div>

                <div className="flex gap-2">
                  <button
                    type="button"
                    onClick={() => setIsPackageFormOpen(false)}
                    className="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 font-semibold"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    className="px-6 py-2 rounded-lg bg-[#071629] hover:bg-[#0D2646] text-[#D4AF37] font-bold uppercase tracking-wider"
                  >
                    Guardar Pacote
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: ADICIONAR IMAGEM À BIBLIOTECA MULTIMÉDIA */}
      {isMediaUploadOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
          <div className="bg-white rounded-2xl w-full max-w-md p-6 text-stone-800 space-y-4 shadow-2xl border border-stone-200">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <h3 className="text-base font-bold text-[#071629]">Adicionar Imagem à Biblioteca</h3>
              <button onClick={() => setIsMediaUploadOpen(false)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            <form onSubmit={handleSaveMedia} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold mb-1 text-stone-700">URL da Imagem *</label>
                <input
                  type="url"
                  required
                  value={mediaUrlInput}
                  onChange={(e) => setMediaUrlInput(e.target.value)}
                  placeholder="https://images.unsplash.com/..."
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-mono text-xs"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Título / Nome</label>
                <input
                  type="text"
                  value={mediaTitleInput}
                  onChange={(e) => setMediaTitleInput(e.target.value)}
                  placeholder="Ex: Costa de Cape Town"
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Texto Alternativo (Alt Text para Acessibilidade)</label>
                <input
                  type="text"
                  value={mediaAltInput}
                  onChange={(e) => setMediaAltInput(e.target.value)}
                  placeholder="Ex: Vista panorâmica da montanha e do oceano"
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Categoria</label>
                <select
                  value={mediaCategoryInput}
                  onChange={(e) => setMediaCategoryInput(e.target.value)}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                >
                  <option value="geral">Geral</option>
                  <option value="destinos">Destinos</option>
                  <option value="pacotes">Pacotes</option>
                  <option value="hero">Hero e Banners</option>
                </select>
              </div>

              <div className="flex justify-end gap-2 pt-2 border-t border-stone-200">
                <button
                  type="button"
                  onClick={() => setIsMediaUploadOpen(false)}
                  className="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-lg bg-[#071629] text-[#D4AF37] font-bold uppercase tracking-wider"
                >
                  Registar Imagem
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: PÁGINA EDITORIAL */}
      {isPageFormOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm overflow-y-auto">
          <div className="bg-white rounded-2xl w-full max-w-2xl p-6 text-stone-800 space-y-4 my-auto max-h-[90vh] overflow-y-auto border border-stone-200 shadow-2xl">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <h3 className="text-base font-bold text-[#071629]">
                {editingPage ? 'Editar Página Editorial' : 'Criar Nova Página Editorial'}
              </h3>
              <button onClick={() => setIsPageFormOpen(false)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            <form onSubmit={handleSavePage} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold mb-1 text-stone-700">Título da Página *</label>
                <input
                  type="text"
                  name="title"
                  required
                  defaultValue={editingPage?.title || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-sm"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Slug / URL Amigável</label>
                <input
                  type="text"
                  name="slug"
                  defaultValue={editingPage?.slug || ''}
                  placeholder="sobre-nos ou requisitos-vistos"
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-mono text-xs"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Resumo / Excerpt</label>
                <input
                  type="text"
                  name="excerpt"
                  defaultValue={editingPage?.excerpt || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Conteúdo HTML Editorial *</label>
                <textarea
                  rows={8}
                  name="content"
                  required
                  defaultValue={editingPage?.content || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg text-xs font-mono leading-relaxed"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Estado</label>
                  <select
                    name="status"
                    defaultValue={editingPage?.status || 'publicado'}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-semibold"
                  >
                    <option value="publicado">Publicado</option>
                    <option value="rascunho">Rascunho</option>
                  </select>
                </div>
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Título SEO</label>
                  <input
                    type="text"
                    name="seo_title"
                    defaultValue={editingPage?.seo_title || ''}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <button
                  type="button"
                  onClick={() => setIsPageFormOpen(false)}
                  className="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-6 py-2 rounded-lg bg-[#071629] text-[#D4AF37] font-bold uppercase tracking-wider"
                >
                  Guardar Página
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: BANNERS */}
      {isBannerFormOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
          <div className="bg-white rounded-2xl w-full max-w-lg p-6 text-stone-800 space-y-4 border border-stone-200 shadow-2xl">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <h3 className="text-base font-bold text-[#071629]">
                {editingBanner ? 'Editar Banner Promocional' : 'Novo Banner Promocional'}
              </h3>
              <button onClick={() => setIsBannerFormOpen(false)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            <form onSubmit={handleSaveBanner} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold mb-1 text-stone-700">Título *</label>
                <input
                  type="text"
                  name="title"
                  required
                  defaultValue={editingBanner?.title || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Subtítulo</label>
                <input
                  type="text"
                  name="subtitle"
                  defaultValue={editingBanner?.subtitle || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">URL da Imagem *</label>
                <input
                  type="url"
                  name="image_url"
                  required
                  defaultValue={editingBanner?.image_url || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-mono text-xs"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Texto do Botão</label>
                  <input
                    type="text"
                    name="button_text"
                    defaultValue={editingBanner?.button_text || 'Saber Mais'}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                  />
                </div>
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Destino do Botão</label>
                  <input
                    type="text"
                    name="button_url"
                    defaultValue={editingBanner?.button_url || '#pacotes-seccao'}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Estado</label>
                  <select
                    name="status"
                    defaultValue={editingBanner?.status || 'ativo'}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                  >
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                  </select>
                </div>
                <div>
                  <label className="block font-bold mb-1 text-stone-700">Ordem</label>
                  <input
                    type="number"
                    name="sort_order"
                    defaultValue={editingBanner?.sort_order || 0}
                    className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                  />
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <button
                  type="button"
                  onClick={() => setIsBannerFormOpen(false)}
                  className="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-6 py-2 rounded-lg bg-[#071629] text-[#D4AF37] font-bold uppercase tracking-wider"
                >
                  Guardar Banner
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL: DESTINOS */}
      {isDestinationFormOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
          <div className="bg-white rounded-2xl w-full max-w-lg p-6 text-stone-800 space-y-4 border border-stone-200 shadow-2xl">
            <div className="flex items-center justify-between border-b border-stone-200 pb-3">
              <h3 className="text-base font-bold text-[#071629]">
                {editingDestination ? 'Editar Destino' : 'Novo Destino'}
              </h3>
              <button onClick={() => setIsDestinationFormOpen(false)} className="text-stone-400 hover:text-stone-800">✕</button>
            </div>

            <form onSubmit={handleSaveDestination} className="space-y-3 text-xs">
              <div>
                <label className="block font-bold mb-1 text-stone-700">Nome do Destino *</label>
                <input
                  type="text"
                  name="name"
                  required
                  defaultValue={editingDestination?.name || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">País *</label>
                <input
                  type="text"
                  name="country"
                  required
                  defaultValue={editingDestination?.country || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">URL da Imagem de Capa</label>
                <input
                  type="url"
                  name="cover_image"
                  defaultValue={editingDestination?.cover_image || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg font-mono text-xs"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Resumo Curto *</label>
                <input
                  type="text"
                  name="summary"
                  required
                  defaultValue={editingDestination?.summary || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div>
                <label className="block font-bold mb-1 text-stone-700">Descrição Detalhada</label>
                <textarea
                  rows={3}
                  name="description"
                  defaultValue={editingDestination?.description || ''}
                  className="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-lg"
                />
              </div>

              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  name="is_featured"
                  id="feat_dest_box"
                  defaultChecked={!!editingDestination?.is_featured}
                  className="rounded"
                />
                <label htmlFor="feat_dest_box" className="font-bold text-stone-800">Destino em Destaque</label>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <button
                  type="button"
                  onClick={() => setIsDestinationFormOpen(false)}
                  className="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 font-semibold"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="px-6 py-2 rounded-lg bg-[#071629] text-[#D4AF37] font-bold uppercase tracking-wider"
                >
                  Guardar Destino
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
