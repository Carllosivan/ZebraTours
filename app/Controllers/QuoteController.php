<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Package;

class QuoteController extends Controller
{
    public function create(Request $request): Response
    {
        $packageId = $request->get('pacote_id');
        $prefilledPackage = null;
        if ($packageId) {
            $packageModel = new Package();
            $prefilledPackage = $packageModel->findBySlug($packageId) ?: null;
        }

        return $this->render('quotes/create', [
            'pageTitle'        => 'Pedido de Cotação de Viagem | Zebra Tours Moçambique',
            'metaDescription'  => 'Solicite uma cotação personalizada e sem compromisso para as suas passagens aéreas, alojamentos ou pacotes turísticos completos.',
            'prefilledPackage' => $prefilledPackage,
            'service'          => $request->get('servico', 'Pacote Turístico Completo'),
            'destination'      => $request->get('destino', ''),
            'dates'            => $request->get('datas', ''),
            'travelers'        => $request->get('viajantes', '1'),
            'errors'           => [],
            'old'              => [],
        ]);
    }

    public function submit(Request $request): Response
    {
        $name = trim((string) $request->post('customer_name', ''));
        $phone = trim((string) $request->post('customer_phone', ''));
        $service = trim((string) $request->post('service_type', ''));
        $email = trim((string) $request->post('customer_email', ''));

        $errors = [];
        if (empty($name)) {
            $errors[] = 'O seu nome completo é obrigatório.';
        }
        if (empty($phone)) {
            $errors[] = 'O contacto telefónico ou WhatsApp é obrigatório.';
        }
        if (empty($service)) {
            $errors[] = 'Por favor selecione o serviço pretendido.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'errors' => $errors], 422);
            }
            return $this->render('quotes/create', [
                'pageTitle' => 'Pedido de Cotação | Zebra Tours',
                'errors'    => $errors,
                'old'       => $request->all(),
            ]);
        }

        $bookingModel = new Booking();
        $ref = $bookingModel->create($request->all());

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
        $ref = $request->get('ref', Session::get('last_booking_ref', 'ZT-2026-REF'));
        $name = Session::get('last_booking_name', 'Estimado Cliente');

        return $this->render('quotes/success', [
            'pageTitle' => 'Cotação Registada com Sucesso | Zebra Tours',
            'ref'       => $ref,
            'name'      => $name,
        ]);
    }
}
