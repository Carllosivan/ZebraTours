<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use App\Models\User;
use App\Services\AuditService;

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
            'pendingBookings'   => (int) $db->query("SELECT count(*) FROM booking_requests WHERE LOWER(status) = 'pendente'")->fetchColumn(),
            'confirmedBookings' => (int) $db->query("SELECT count(*) FROM booking_requests WHERE LOWER(status) = 'confirmado'")->fetchColumn(),
            'pendingContacts'   => (int) $db->query("SELECT count(*) FROM contact_messages WHERE LOWER(status) = 'pendente'")->fetchColumn(),
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
            'errorMessage'   => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function bookings(Request $request): Response
    {
        $bookingModel = new Booking();
        $status = $request->getString('status', 'all');
        $search = $request->getString('q', '');

        $bookings = $bookingModel->getAll($status, $search);

        return $this->render('admin/bookings/index', [
            'pageTitle'      => 'Gestão de Pedidos e Cotações (CRM) | Zebra Tours CMS',
            'bookings'       => $bookings,
            'selectedStatus' => $status,
            'searchQuery'    => $search,
            'successMessage' => Session::getFlash('success'),
            'errorMessage'   => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function updateBookingStatus(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id'));
        $status = $request->postString('status');
        $notes = $request->postString('notes');

        $user = Auth::user();
        $author = $user ? $user['name'] : 'Consultor';

        $bookingModel = new Booking();
        try {
            $bookingModel->updateStatus($id, $status, $notes ?: null, $author);
            AuditService::log('PEDIDO_ESTADO', $user ? (int) $user['id'] : null, 'booking_requests', $id, "Estado alterado para '{$status}'");
            Session::setFlash('success', "Estado do pedido atualizado para: {$status}");
        } catch (\Throwable $e) {
            Session::setFlash('error', $e->getMessage());
        }

        return $this->redirect('/admin/pedidos');
    }

    public function addBookingNote(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id'));
        $note = $request->postString('note');

        if (!empty($note)) {
            $user = Auth::user();
            $author = $user ? $user['name'] : 'Consultor';
            $bookingModel = new Booking();
            $bookingModel->addNote($id, $note, $author);
            AuditService::log('NOTA_ADICIONADA', $user ? (int) $user['id'] : null, 'booking_requests', $id, "Nota interna adicionada");
            Session::setFlash('success', 'Nota interna adicionada ao histórico do pedido.');
        }

        return $this->redirect('/admin/pedidos');
    }

    public function deleteBookingNote(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id'));
        $bookingModel = new Booking();
        $bookingModel->deleteNote($id);

        $user = Auth::user();
        AuditService::log('NOTA_ELIMINADA', $user ? (int) $user['id'] : null, 'booking_history', $id, "Nota interna eliminada");
        Session::setFlash('success', 'Nota eliminada do histórico.');

        return $this->redirect('/admin/pedidos');
    }

    public function exportCsv(Request $request): Response
    {
        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : null;

        $db = Database::getConnection();
        $rows = $db->query("
            SELECT reference_code, customer_name, customer_email, phone_country_code || ' ' || customer_phone as phone,
                   service_type, destination, preferred_dates, travelers_count, status, created_at, admin_notes
            FROM booking_requests
            ORDER BY created_at DESC
        ")->fetchAll();

        // Helper to neutralize formula injection in spreadsheet applications (Excel, Calc, Sheets)
        $sanitizeCell = function (mixed $value): string {
            $str = (string) ($value ?? '');
            if ($str === '') {
                return '';
            }
            $first = $str[0];
            // Characters that can trigger formula execution: = + - @ TAB (0x09) CR (0x0D)
            if (in_array($first, ['=', '+', '-', '@', "\t", "\r"], true)) {
                return "'" . $str;
            }
            return $str;
        };

        $fp = fopen('php://temp', 'r+');

        // Write UTF-8 BOM
        fwrite($fp, "\xEF\xBB\xBF");

        // Write Header
        fputcsv($fp, [
            'Referencia',
            'Cliente',
            'Email',
            'Telefone',
            'Servico',
            'Destino',
            'Datas',
            'Viajantes',
            'Estado',
            'Data Registo',
            'Notas Internas',
        ]);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $sanitizeCell($r['reference_code']),
                $sanitizeCell($r['customer_name']),
                $sanitizeCell($r['customer_email']),
                $sanitizeCell($r['phone']),
                $sanitizeCell($r['service_type']),
                $sanitizeCell($r['destination']),
                $sanitizeCell($r['preferred_dates']),
                (int) $r['travelers_count'],
                $sanitizeCell($r['status']),
                $sanitizeCell($r['created_at']),
                $sanitizeCell($r['admin_notes']),
            ]);
        }

        rewind($fp);
        $csvContent = stream_get_contents($fp);
        fclose($fp);

        AuditService::log('EXPORTAR_CSV', $userId, 'booking_requests', null, 'Exportação completa de pedidos para CSV');

        return new Response(
            $csvContent,
            200,
            [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="pedidos_zebratours_' . date('Ymd_His') . '.csv"',
                'Cache-Control'       => 'no-store, private',
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
            'errorMessage'   => Session::getFlash('error'),
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
            'errorMessage'   => Session::getFlash('error'),
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
            'errorMessage'   => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function updateSettings(Request $request): Response
    {
        $db = Database::getConnection();
        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : null;

        $fields = [
            'site_name', 'phone', 'whatsapp', 'email', 'location', 
            'currency', 'agency_license', 'opening_hours', 'facebook_url', 'instagram_url',
            'hero_title', 'hero_subtitle', 'hero_cta_primary', 'hero_cta_secondary'
        ];

        // 1. Validate fields strictly: if any field fails validation, NOTHING is saved!
        $validatedData = [];
        $errors = [];

        foreach ($fields as $field) {
            $val = $request->post($field);
            if ($val !== null) {
                $valStr = trim((string) $val);

                if ($field === 'email' && !empty($valStr) && !filter_var($valStr, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'O endereço de email institucional tem um formato inválido.';
                }

                if (in_array($field, ['facebook_url', 'instagram_url'], true) && !empty($valStr)) {
                    if (!filter_var($valStr, FILTER_VALIDATE_URL)) {
                        $errors[] = "O endereço {$field} não é um URL válido.";
                    }
                }

                if (in_array($field, ['phone', 'whatsapp'], true) && !empty($valStr)) {
                    $digits = preg_replace('/[^\d]/', '', $valStr);
                    if (strlen($digits) < 7 || strlen($digits) > 16) {
                        $errors[] = "O contacto {$field} deve conter entre 7 e 16 dígitos.";
                    }
                }

                $validatedData[$field] = $valStr;
            }
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            return $this->redirect('/admin/definicoes');
        }

        // 2. All validations passed: update settings transactionally
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value, updated_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(setting_key) DO UPDATE SET
                setting_value = excluded.setting_value,
                updated_at = CURRENT_TIMESTAMP
        ");

        $db->beginTransaction();
        try {
            foreach ($validatedData as $k => $v) {
                $stmt->execute([$k, $v]);
            }
            $db->commit();
            AuditService::log('DEFINICOES_ATUALIZADAS', $userId, 'settings', null, 'Definições da agência atualizadas');
            Session::setFlash('success', 'Definições da agência atualizadas com sucesso.');
        } catch (\Throwable $e) {
            $db->rollBack();
            Session::setFlash('error', 'Erro ao gravar definições: ' . $e->getMessage());
        }

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
            'errorMessage'   => Session::getFlash('error'),
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
            'errorMessage'   => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function users(Request $request): Response
    {
        $userModel = new User();
        $users = $userModel->getAll();

        return $this->render('admin/users/index', [
            'pageTitle'      => 'Gestão de Utilizadores | Zebra Tours CMS',
            'users'          => $users,
            'successMessage' => Session::getFlash('success'),
            'errorMessage'   => Session::getFlash('error'),
        ], 'layouts/admin');
    }

    public function createUser(Request $request): Response
    {
        $name = $request->postString('name');
        $email = $request->postString('email');
        $role = $request->postString('role', 'editor');
        $password = (string) $request->post('password', '');

        if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::setFlash('error', 'Preencha o nome e um email válido.');
            return $this->redirect('/admin/utilizadores');
        }

        if (mb_strlen($password) < 12) {
            Session::setFlash('error', 'A palavra-passe deve conter pelo menos 12 carateres.');
            return $this->redirect('/admin/utilizadores');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            Session::setFlash('error', 'Já existe um utilizador registado com este endereço de email.');
            return $this->redirect('/admin/utilizadores');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $newId = $userModel->create([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => $hash,
            'role'          => in_array($role, ['superadmin', 'admin', 'editor'], true) ? $role : 'editor',
            'status'        => 'ativo',
        ]);

        $currentUser = Auth::user();
        AuditService::log('CRIAR_UTILIZADOR', $currentUser ? (int) $currentUser['id'] : null, 'users', $newId, "Utilizador {$email} criado com perfil {$role}");

        Session::setFlash('success', "Utilizador {$name} criado com sucesso!");
        return $this->redirect('/admin/utilizadores');
    }

    public function updateUserStatus(Request $request, array $params): Response
    {
        $id = (int) ($params['id'] ?? $request->post('id'));
        $status = $request->postString('status');
        $role = $request->postString('role');

        $currentUser = Auth::user();
        if ($currentUser && (int) $currentUser['id'] === $id && $status !== 'ativo') {
            Session::setFlash('error', 'Não pode desativar ou bloquear a sua própria conta ativa.');
            return $this->redirect('/admin/utilizadores');
        }

        $userModel = new User();
        $data = [];
        if (in_array($status, ['ativo', 'inativo', 'bloqueado'], true)) {
            $data['status'] = $status;
        }
        if (in_array($role, ['superadmin', 'admin', 'editor'], true)) {
            $data['role'] = $role;
        }

        $userModel->update($id, $data);
        AuditService::log('ATUALIZAR_UTILIZADOR', $currentUser ? (int) $currentUser['id'] : null, 'users', $id, "Status/Role alterado para {$status}/{$role}");

        Session::setFlash('success', 'Utilizador atualizado com sucesso.');
        return $this->redirect('/admin/utilizadores');
    }
}
