<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect('/admin');
        }

        return $this->render('admin/login', [
            'pageTitle' => 'Acesso Restrito ao Painel de Gestão | Zebra Tours',
            'error'     => Session::getFlash('error'),
        ], 'layouts/empty');
    }

    public function login(Request $request): Response
    {
        $email = (string) $request->post('email', '');
        $password = (string) $request->post('password', '');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Preencha o email e a palavra-passe.');
            return $this->redirect('/admin/login');
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if (!$user) {
            Session::setFlash('error', 'Credenciais inválidas. Verifique o email e palavra-passe.');
            return $this->redirect('/admin/login');
        }

        Auth::login($user);
        Session::setFlash('success', "Bem-vindo de volta, {$user['name']}!");
        return $this->redirect('/admin');
    }

    public function logout(Request $request): Response
    {
        Auth::logout();
        Session::setFlash('success', 'Sessão terminada com sucesso.');
        return $this->redirect('/admin/login');
    }
}
