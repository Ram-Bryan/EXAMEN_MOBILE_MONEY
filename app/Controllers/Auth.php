<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    public function login()
    {
        // If already logged in, redirect to appropriate dashboard
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($role === 'operator') {
                return redirect()->to('/operator/dashboard');
            } else {
                return redirect()->to('/client/dashboard');
            }
        }
        
        return view('auth/login');
    }

    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->userModel->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $this->session->set([
                'isLoggedIn' => true,
                'user_id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role'],
                'phone' => $user['phone'] ?? null
            ]);
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($user['role'] === 'operator') {
                return redirect()->to('/operator/dashboard');
            } else {
                return redirect()->to('/client/dashboard');
            }
        }
        
        return redirect()->back()->with('error', 'Identifiants incorrects');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}