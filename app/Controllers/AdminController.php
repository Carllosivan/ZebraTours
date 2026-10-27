<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Destination;

class AdminController extends Controller
{
    private Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function dashboard(Request $request): Response
    {
        $db = Database::getConnection();

        $stats = [
            'totalPackages'     => (int) $db->query("SELECT count(*) FROM packages")->fetchColumn(),
            'publishedPackages' => (int) $db->query("SELECT count(*) FROM packages WHERE status = 'publicado'")->fetchColumn(),
            'totalDestinations' => (int) $db->query("SELECT count(*) FROM destinations")->fetchColumn(),
            'totalBookings'     => (int) $db->query("SELECT count(*) FROM booking_requests")->fetchColumn(),
            'pendingBookings'   => (int) $db->query("SELECT count(*) FROM booking_requests WHERE status = 'Pendente'")->fetchColumn(),
            'confirmedBookings' => (int) $db->query("SELECT count(*) FROM booking_requests WHERE status = 'Confirmado'")->fetchColumn(),
            'pendingContacts'   => (int) $db->query("SELECT count(*) FROM contact_messages WHERE status = 'Pendente'")->fetchColumn(),
            'totalMedia'        => (int) $db->query("SELECT count(*) FROM media_library")->fetchColumn(),
        ];

        $stats['conversionRate'] = $stats['totalBookings'] > 0 
            ? round(($stats['confirmedBookings'] / $stats['totalBookings']) * 100) 
            : 0;

        $recentBookings = $db->query("
            SELECT b.*, p.title as package_title
            FROM booking_requests b
            LEFT JOIN packages p ON p.id = b.package_id
            ORDER BY b.created_at DESC
            LIMIT 6
        ")->fetchAll();

        return $this->render('admin/dashboard', [
            'pageTitle'      => 'Painel Principal | Zebra Tours CMS',
            'stats'          => $stats,
            'recentBookings' => $recentBookings,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function bookings(Request $request): Response
    {
        $bookingModel = new Booking();
        $status = $request->get('status', 'all');
        $search = $request->get('q', '');

        $bookings = $bookingModel->getAll($status, $search);

        return $this->render('admin/bookings/index', [
            'pageTitle'      => 'Gestão de Pedidos e Cotações (CRM) | Zebra Tours CMS',
            'bookings'       => $bookings,
            'selectedStatus' => $status,
            'searchQuery'    => $search,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function updateBookingStatus(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id'));
        $status = (string) $request->post('status');
        $notes = (string) $request->post('notes');

        $bookingModel = new Booking();
        $bookingModel->updateStatus($id, $status, $notes ?: null);

        Session::setFlash('success', "Estado do pedido atualizado para: {$status}");
        return $this->redirect('/admin/pedidos');
    }

    public function exportCsv(Request $request): Response
    {
        $db = Database::getConnection();
        $rows = $db->query("
            SELECT reference_code, customer_name, customer_email, phone_country_code || ' ' || customer_phone as phone,
                   service_type, destination, preferred_dates, travelers_count, status, created_at, admin_notes
            FROM booking_requests
            ORDER BY created_at DESC
        ")->fetchAll();

        $csv = "Referencia,Cliente,Email,Telefone,Servico,Destino,Datas,Viajantes,Estado,Data Registo,Notas\n";
        foreach ($rows as $r) {
            $csv .= sprintf(
                "%s,\"%s\",%s,\"%s\",\"%s\",\"%s\",\"%s\",%d,%s,%s,\"%s\"\n",
                $r['reference_code'],
                str_replace('"', '""', $r['customer_name'] ?? ''),
                $r['customer_email'] ?? '',
                str_replace('"', '""', $r['phone'] ?? ''),
                str_replace('"', '""', $r['service_type'] ?? ''),
                str_replace('"', '""', $r['destination'] ?? ''),
                str_replace('"', '""', $r['preferred_dates'] ?? ''),
                $r['travelers_count'],
                $r['status'],
                $r['created_at'],
                str_replace('"', '""', $r['admin_notes'] ?? '')
            );
        }

        return new Response(
            "\xEF\xBB\xBF" . $csv,
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="pedidos_zebratours_' . date('Ymd_His') . '.csv"',
            ]
        );
    }

    public function packages(Request $request): Response
    {
        $db = Database::getConnection();
        $packages = $db->query("
            SELECT p.*, d.name as destination_name
            FROM packages p
            LEFT JOIN destinations d ON d.id = p.primary_destination_id
            ORDER BY p.is_featured DESC, p.created_at DESC
        ")->fetchAll();

        return $this->render('admin/packages/index', [
            'pageTitle'      => 'Catálogo de Pacotes Turísticos | Zebra Tours CMS',
            'packages'       => $packages,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function destinations(Request $request): Response
    {
        $destinationModel = new Destination();
        $destinations = $destinationModel->getAll(false);

        return $this->render('admin/destinations/index', [
            'pageTitle'      => 'Gestão de Destinos | Zebra Tours CMS',
            'destinations'   => $destinations,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function settings(Request $request): Response
    {
        $db = Database::getConnection();
        $rows = $db->query("SELECT setting_key, setting_value, description FROM settings")->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }

        return $this->render('admin/settings/index', [
            'pageTitle'      => 'Definições Gerais da Agência | Zebra Tours CMS',
            'settings'       => $settings,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function updateSettings(Request $request): Response
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value, updated_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(setting_key) DO UPDATE SET
                setting_value = excluded.setting_value,
                updated_at = CURRENT_TIMESTAMP
        ");

        $fields = [
            'company_name', 'phone', 'whatsapp', 'email', 'location', 
            'currency', 'agency_license', 'opening_hours', 'facebook_url', 'instagram_url'
        ];

        foreach ($fields as $field) {
            $val = $request->post($field);
            if ($val !== null) {
                $stmt->execute([$field, trim((string) $val)]);
            }
        }

        Session::setFlash('success', 'Definições da agência atualizadas com sucesso.');
        return $this->redirect('/admin/definicoes');
    }

    public function pages(Request $request): Response
    {
        $db = Database::getConnection();
        $pages = $db->query("SELECT * FROM pages ORDER BY updated_at DESC")->fetchAll();

        return $this->render('admin/pages/index', [
            'pageTitle'      => 'Páginas Editoriais (CMS) | Zebra Tours CMS',
            'pages'          => $pages,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }

    public function banners(Request $request): Response
    {
        $db = Database::getConnection();
        $banners = $db->query("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC")->fetchAll();

        return $this->render('admin/banners/index', [
            'pageTitle'      => 'Banners e Campanhas Promocionais | Zebra Tours CMS',
            'banners'        => $banners,
            'successMessage' => Session::getFlash('success'),
        ], 'layouts/admin');
    }
}
