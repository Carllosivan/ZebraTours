<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->render('contact/index', [
            'pageTitle'       => 'Contacte a Zebra Tours Moçambique | Maputo',
            'metaDescription' => 'Fale connosco por telefone, WhatsApp ou visite-nos em Maputo. Estamos disponíveis para esclarecer dúvidas e planear a sua próxima viagem.',
            'errors'          => [],
            'old'             => [],
            'successMessage'  => Session::getFlash('contact_success'),
        ]);
    }

    public function submit(Request $request): Response
    {
        $ip = RateLimiter::getClientIp();
        $rateLimiter = new RateLimiter();

        // 1. Honeypot check
        $honeypot = $request->postString('website');
        if (!empty($honeypot)) {
            // Silently fake success
            $msg = 'A sua mensagem foi enviada com sucesso! A equipa da Zebra Tours entrará em contacto com a maior brevidade.';
            if ($request->isAjax()) {
                return $this->json(['success' => true, 'message' => $msg]);
            }
            Session::setFlash('contact_success', $msg);
            return $this->redirect('/contactos');
        }

        // 2. Rate Limiting: 5 requests per 10 minutes (600s) per IP
        $rateKey = "contact_ip_{$ip}";
        if (!$rateLimiter->check($rateKey, 5, 600)) {
            if ($request->isAjax()) {
                return $this->json([
                    'success' => false,
                    'error'   => 'Demasiadas mensagens enviadas recentemente. Por favor aguarde 10 minutos.',
                ], 429);
            }
            return Response::html(
                '<!DOCTYPE html><html lang="pt"><head><meta charset="utf-8"><title>429 Demasiados Pedidos</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-950 text-white min-h-screen flex items-center justify-center p-6"><div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 text-center space-y-4"><h1 class="text-xl font-bold text-amber-400">429 - Limite Excedido</h1><p class="text-sm text-slate-400">Por razões de segurança, limitamos os contactos a 5 por cada 10 minutos. Por favor aguarde alguns instantes.</p><a href="/contactos" class="inline-block mt-4 px-6 py-2.5 bg-amber-500 text-slate-950 font-bold rounded-xl text-xs uppercase">Voltar aos Contactos</a></div></body></html>',
                429
            );
        }

        $name = $request->postString('name');
        $email = $request->postString('email');
        $subject = $request->postString('subject');
        $message = $request->postString('message');

        $errors = [];

        $nameLen = mb_strlen($name);
        if ($nameLen < 2 || $nameLen > 120) {
            $errors[] = 'O seu nome deve conter entre 2 e 120 carateres.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Por favor indique um endereço de email com formato válido.';
        }

        $subjLen = mb_strlen($subject);
        if ($subjLen < 2 || $subjLen > 150) {
            $errors[] = 'O assunto da mensagem é obrigatório (até 150 carateres).';
        }

        $msgLen = mb_strlen($message);
        if ($msgLen < 5 || $msgLen > 2500) {
            $errors[] = 'O texto da mensagem deve conter entre 5 e 2500 carateres.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'errors' => $errors], 422);
            }
            return $this->render('contact/index', [
                'pageTitle'      => 'Contactos | Zebra Tours',
                'metaDescription'=> 'Contacte a equipa da Zebra Tours',
                'errors'         => $errors,
                'old'            => $request->all(),
                'successMessage' => null,
            ], 'layouts/main', 422);
        }

        $rateLimiter->hit($rateKey, 600);

        $contactModel = new ContactMessage();
        $contactData = [
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'message' => $message,
        ];
        $contactModel->create($contactData);

        \App\Services\NotificationService::notifyContact($contactData);

        $msg = 'A sua mensagem foi enviada com sucesso! A equipa da Zebra Tours entrará em contacto com a maior brevidade.';
        Session::setFlash('contact_success', $msg);

        if ($request->isAjax()) {
            return $this->json(['success' => true, 'message' => $msg]);
        }

        return $this->redirect('/contactos');
    }
}
