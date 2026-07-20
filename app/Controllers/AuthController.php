<?php

namespace App\Controllers;

use App\Models\AdminModel;

class AuthController extends BaseController
{
    protected $adminModel;
    protected $session;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->session = session();
    }

    public function login()
    {
        if ($this->session->get('isLoggedIn') && $this->session->get('role') === 'admin') {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('auth/login');
    }

    public function doLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $admin = $this->adminModel->findByEmail($email);
        
        if ($admin && password_verify($password, $admin->password)) {
            $this->session->set([
                'isLoggedIn' => true,
                'user_id' => $admin->id,
                'email' => $admin->email,
                'role' => 'admin'
            ]);
            return redirect()->to('/admin/dashboard');
        }
        
        return redirect()->back()->with('error', 'Identifiants incorrects');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}