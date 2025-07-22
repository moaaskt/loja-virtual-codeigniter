<?php

namespace App\Controllers;

use App\Models\UsuarioModel; // Precisaremos do UsuarioModel em breve

class AuthController extends BaseController
{
    public function __construct()
    {
        helper(['form', 'url']);
    }



    // Mostra o formulário de registro
public function registrar()
{
    helper('form');
    return view('auth/registrar', ['title' => 'Crie sua Conta']);
}

// Salva o novo usuário
public function attemptRegister()
{
    helper('form');
    $model = new UsuarioModel();

    $data = [
        'nome'       => $this->request->getPost('nome'),
        'email'      => $this->request->getPost('email'),
        'senha_hash' => $this->request->getPost('senha_hash'),
        'password_confirm' => $this->request->getPost('password_confirm'),
    ];

    if ($model->save($data)) {
        // Loga o usuário automaticamente após o registro
        $usuario = $model->find($model->getInsertID());
        $session = session();
        $sessionData = [
            'usuario_id' => $usuario['id'],
            'nome'       => $usuario['nome'],
            'isLoggedIn' => TRUE
        ];
        $session->set($sessionData);

        return redirect()->to(site_url('/'))->with('success', 'Conta criada com sucesso!');
    } else {
        return redirect()->back()->withInput()->with('errors', $model->errors());
    }
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