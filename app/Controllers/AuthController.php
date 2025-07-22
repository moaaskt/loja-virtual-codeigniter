<?php

namespace App\Controllers;

use App\Models\UsuarioModel; // Precisaremos do UsuarioModel em breve

class AuthController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }

    // Mostra o formulário de login
    public function login()
    {
        return view('auth/login', ['title' => 'Login']);
    }

    // Processa a tentativa de login
    public function attemptLogin()
    {
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuario = $model->where('email', $email)->first();

        if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
            $session = session();
            $sessionData = [
                'usuario_id' => $usuario['id'],
                'nome'       => $usuario['nome'],
                'isLoggedIn' => TRUE
            ];
            $session->set($sessionData);

            // Redireciona para a página de produtos do admin após o login
            return redirect()->to(site_url('produtos'));
        } else {
            return redirect()->back()->with('error', 'Email ou senha inválidos.');
        }
    }

    // Faz o logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('/'));
    }
}