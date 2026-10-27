<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
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
        $name = trim((string) $request->post('name', ''));
        $email = trim((string) $request->post('email', ''));
        $subject = trim((string) $request->post('subject', ''));
        $message = trim((string) $request->post('message', ''));

        $errors = [];
        if (empty($name)) $errors[] = 'O seu nome é obrigatório.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Por favor indique um email válido.';
        if (empty($subject)) $errors[] = 'O assunto da mensagem é obrigatório.';
        if (empty($message)) $errors[] = 'O texto da mensagem é obrigatório.';

        if (!empty($errors)) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'errors' => $errors], 422);
            }
            return $this->render('contact/index', [
                'pageTitle' => 'Contactos | Zebra Tours',
                'errors'    => $errors,
                'old'       => $request->all(),
            ]);
        }

        $contactModel = new ContactMessage();
        $contactModel->create($request->all());

        $msg = 'A sua mensagem foi enviada com sucesso! A equipa da Zebra Tours entrará em contacto com a maior brevidade.';
        Session::setFlash('contact_success', $msg);

        if ($request->isAjax()) {
            return $this->json(['success' => true, 'message' => $msg]);
        }

        return $this->redirect('/contactos');
    }
}
