<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Auth;
use App\Models\User;
use App\Services\AuditService;

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
        $email = $request->postString('email');
        $password = (string) $request->post('password', '');

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Preencha o email e a palavra-passe.');
            return $this->redirect('/admin/login');
        }

        $ip = RateLimiter::getClientIp();
        $rateLimiter = new RateLimiter();

        $ipEmailKey = 'login_fail_' . md5($ip . '_' . strtolower($email));
        $ipKey = 'login_fail_ip_' . md5($ip);

        // Check rate limits: 5 fails per 15 min per IP+email; 30 fails per 15 min per IP
        if (!$rateLimiter->check($ipEmailKey, 5, 900) || !$rateLimiter->check($ipKey, 30, 900)) {
            AuditService::log('LOGIN_BLOQUEADO', null, 'users', null, "Bloqueado por força bruta: {$email} (IP: {$ip})");
            Session::setFlash('error', 'Demasiadas tentativas de início de sessão falhadas. Por razões de segurança, a conta está temporariamente bloqueada durante 15 minutos.');
            return $this->redirect('/admin/login');
        }

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if (!$user) {
            $rateLimiter->hit($ipEmailKey, 900);
            $rateLimiter->hit($ipKey, 900);
            AuditService::log('LOGIN_FALHADO', null, 'users', null, "Tentativa de login inválida para o email: {$email}");
            Session::setFlash('error', 'Credenciais de acesso inválidas. Verifique o email e palavra-passe.');
            return $this->redirect('/admin/login');
        }

        // Successful authentication: clear failure rate limit
        $rateLimiter->reset($ipEmailKey);
        $rateLimiter->reset($ipKey);

        Auth::login($user);
        AuditService::log('LOGIN', (int) $user['id'], 'users', (int) $user['id'], "Sessão iniciada por {$user['name']} ({$email})");

        Session::setFlash('success', "Bem-vindo de volta, {$user['name']}!");
        return $this->redirect('/admin');
    }

    public function logout(Request $request): Response
    {
        $user = Auth::user();
        if ($user) {
            AuditService::log('LOGOUT', (int) $user['id'], 'users', (int) $user['id'], "Sessão terminada por {$user['name']}");
        }

        Auth::logout();
        Session::setFlash('success', 'Sessão terminada com sucesso e em segurança.');
        return $this->redirect('/admin/login');
    }
}
