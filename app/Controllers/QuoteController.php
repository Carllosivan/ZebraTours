<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Package;

class QuoteController extends Controller
{
    private const ALLOWED_SERVICES = [
        'Pacote Turístico Completo',
        'Passagens Aéreas',
        'Reserva de Hotel / Alojamento',
        'Consultoria de Vistos',
        'Viagens Corporativas / Grupos',
        'Outro Serviço',
        'Outro',
    ];

    public function create(Request $request): Response
    {
        $packageSlug = $request->getString('pacote_id');
        $prefilledPackage = null;
        if (!empty($packageSlug)) {
            $packageModel = new Package();
            $prefilledPackage = $packageModel->findBySlug($packageSlug, true);
        }

        return $this->render('quotes/create', [
            'pageTitle'        => 'Pedido de Cotação de Viagem | Zebra Tours Moçambique',
            'metaDescription'  => 'Solicite uma cotação personalizada e sem compromisso para as suas passagens aéreas, alojamentos ou pacotes turísticos completos.',
            'prefilledPackage' => $prefilledPackage,
            'service'          => $request->getString('servico', 'Pacote Turístico Completo'),
            'destination'      => $request->getString('destino', ''),
            'dates'            => $request->getString('datas', ''),
            'travelers'        => $request->getInt('viajantes', 1),
            'errors'           => [],
            'old'              => [],
        ]);
    }

    public function submit(Request $request): Response
    {
        $ip = RateLimiter::getClientIp();
        $rateLimiter = new RateLimiter();

        // 1. Honeypot check: anti-spam bot trap
        $honeypot = $request->postString('website');
        if (!empty($honeypot)) {
            // Silently fake success to thwart bots
            $fakeRef = 'ZT-' . date('Y') . '-BOTX';
            if ($request->isAjax()) {
                return $this->json(['success' => true, 'reference_code' => $fakeRef]);
            }
            return $this->redirect("/cotacao/sucesso?ref={$fakeRef}");
        }

        // 2. Rate Limiting: 5 submissions per 10 minutes (600s) per IP
        $rateKey = "quote_ip_{$ip}";
        if (!$rateLimiter->check($rateKey, 5, 600)) {
            if ($request->isAjax()) {
                return $this->json([
                    'success' => false,
                    'error'   => 'Demasiados pedidos enviados recentemente. Por favor aguarde 10 minutos.',
                ], 429);
            }
            return Response::html(
                '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8"><title>429 Demasiados Pedidos</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6"><div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center space-y-4"><h1 class="text-xl font-bold text-amber-400">429 - Limite de Pedidos Excedido</h1><p class="text-sm text-slate-400">Por razões de segurança, limitamos os pedidos a 5 por cada 10 minutos. Por favor aguarde alguns instantes antes de submeter uma nova cotação.</p><a href="/" class="inline-block mt-4 px-6 py-2.5 bg-amber-500 text-slate-950 font-bold rounded-xl text-xs uppercase">Página Inicial</a></div></body></html>',
                429
            );
        }

        // 3. Strict Input Sanitization & Validation
        $name = $request->postString('customer_name');
        $phone = $request->postString('customer_phone');
        $email = $request->postString('customer_email');
        $service = $request->postString('service_type');
        $destination = $request->postString('destination');
        $dates = $request->postString('preferred_dates');
        $travelers = $request->getInt('travelers_count', 1);
        $message = $request->postString('message');
        $packageIdRaw = $request->post('package_id');

        $errors = [];

        // Name: 2 to 120 characters
        $nameLen = mb_strlen($name);
        if ($nameLen < 2 || $nameLen > 120) {
            $errors[] = 'O nome completo deve conter entre 2 e 120 carateres.';
        }

        // Phone: 7 to 15 digits
        $digitsOnly = preg_replace('/[^\d]/', '', $phone);
        $digitsCount = strlen($digitsOnly);
        if ($digitsCount < 7 || $digitsCount > 15) {
            $errors[] = 'O contacto telefónico deve conter entre 7 e 15 dígitos numéricos.';
        }

        // Email: optional, but if provided must be valid
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'O email fornecido não possui um formato válido.';
        }

        // Service: in allowed list
        if (!in_array($service, self::ALLOWED_SERVICES, true)) {
            $errors[] = 'Por favor selecione um tipo de serviço válido da lista.';
        }

        // Travelers: 1 to 50
        if ($travelers < 1 || $travelers > 50) {
            $errors[] = 'O número de viajantes deve estar compreendido entre 1 e 50 pessoas.';
        }

        // Message: max 2000 characters
        if (mb_strlen($message) > 2000) {
            $errors[] = 'A mensagem ou observações não podem exceder 2000 carateres.';
        }

        // Package ID validation: if set, verify it exists and is public
        $validPackageId = null;
        $prefilledPackage = null;
        if (!empty($packageIdRaw)) {
            $packageModel = new Package();
            if (is_numeric($packageIdRaw)) {
                $pkg = $packageModel->findById((int) $packageIdRaw);
                if ($pkg && in_array($pkg['status'], ['publicado', 'pendente_validacao'], true)) {
                    $validPackageId = (int) $packageIdRaw;
                    $prefilledPackage = $pkg;
                }
            } else {
                $pkg = $packageModel->findBySlug((string) $packageIdRaw, true);
                if ($pkg) {
                    $validPackageId = (int) $pkg['id'];
                    $prefilledPackage = $pkg;
                }
            }
        }

        // If validation errors exist, re-render preserving all context
        if (!empty($errors)) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'errors' => $errors], 422);
            }
            return $this->render('quotes/create', [
                'pageTitle'        => 'Pedido de Cotação | Zebra Tours',
                'metaDescription'  => 'Pedido de cotação de viagem',
                'prefilledPackage' => $prefilledPackage,
                'service'          => $service,
                'destination'      => $destination,
                'dates'            => $dates,
                'travelers'        => $travelers,
                'errors'           => $errors,
                'old'              => $request->all(),
            ], 'layouts/main', 422);
        }

        // Record hit in rate limiter
        $rateLimiter->hit($rateKey, 600);

        // Create booking in database
        $bookingModel = new Booking();
        $bookingData = $request->all();
        $bookingData['package_id'] = $validPackageId;
        $bookingData['customer_name'] = $name;
        $bookingData['customer_phone'] = $phone;
        $bookingData['customer_email'] = $email ?: null;
        $bookingData['service_type'] = $service;
        $bookingData['destination'] = $destination;
        $bookingData['preferred_dates'] = $dates;
        $bookingData['travelers_count'] = $travelers;
        $bookingData['message'] = $message;

        $ref = $bookingModel->create($bookingData);

        \App\Services\NotificationService::notifyNewBooking(array_merge($bookingData, [
            'reference_code'     => $ref,
            'phone_country_code' => $bookingData['phone_country_code'] ?? '+258',
        ]));

        Session::set('last_booking_ref', $ref);
        Session::set('last_booking_name', $name);

        if ($request->isAjax()) {
            return $this->json([
                'success'        => true,
                'reference_code' => $ref,
                'message'        => "O seu pedido de cotação foi registado com sucesso com a referência {$ref}. A nossa equipa entrará em contacto.",
            ]);
        }

        return $this->redirect("/cotacao/sucesso?ref={$ref}");
    }

    public function success(Request $request): Response
    {
        $ref = $request->getString('ref');
        if (empty($ref)) {
            $ref = (string) Session::get('last_booking_ref', '');
        }

        // Validate reference format: ZT-YYYY-XXXX (where YYYY is 4 digits, XXXX is 4-8 alphanumerics)
        if (!preg_match('/^ZT-\d{4}-[A-Z0-9]{4,8}$/', $ref)) {
            return $this->notFound('/cotacao/sucesso');
        }

        $bookingModel = new Booking();
        $booking = $bookingModel->findByRef($ref);
        $name = $booking ? $booking['customer_name'] : Session::get('last_booking_name', 'Estimado Cliente');

        return $this->render('quotes/success', [
            'pageTitle' => 'Cotação Registada com Sucesso | Zebra Tours',
            'ref'       => $ref,
            'name'      => $name,
            'booking'   => $booking,
        ]);
    }
}
