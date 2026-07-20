<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\OperateurPrefixeModel;
use App\Models\AdminModel;

class Auth extends BaseController
{
    protected $clientModel;
    protected $prefixModel;
    protected $adminModel;
    protected $session;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->prefixModel = new OperateurPrefixeModel();
        $this->adminModel = new AdminModel();
        $this->session = session();
    }

    public function login()
    {
        // If already logged in, redirect to appropriate dashboard
        if ($this->session->get('isLoggedIn')) {
            $role = $this->session->get('role');
            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/client/dashboard');
            }
        }
        
        return view('auth/login');
    }

    public function doLogin()
    {
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email') ?? $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Flow 1: Client Auto-login by Phone Number
        if (!empty($phone)) {
            $phone = trim($phone);
            
            // Validate prefix against operateur_prefixes
            $prefixes = $this->prefixModel->findAll();
            $matchedPrefix = null;
            
            foreach ($prefixes as $p) {
                if (strpos($phone, $p->prefixe) === 0) {
                    $matchedPrefix = $p;
                    break;
                }
            }
            
            if (!$matchedPrefix) {
                return redirect()->back()->withInput()->with('error', 'Numéro de téléphone invalide (préfixe non supporté).');
            }

            // Check if client exists
            $client = $this->clientModel->getByTelephone($phone);
            
            if (!$client) {
                // Auto-create client
                $clientId = $this->clientModel->createClient($phone, $matchedPrefix->id);
                $client = $this->clientModel->find($clientId);
            }

            // Open Client Session
            $this->session->set([
                'isLoggedIn'  => true,
                'client_id'   => $client->id,
                'phone'       => $client->telephone,
                'client_code' => $client->code,
                'name'        => $client->nom,
                'role'        => 'client'
            ]);

            return redirect()->to('/client/dashboard');
        }

        // Flow 2: Admin Login by Email and Password
        if (!empty($email) && !empty($password)) {
            $admin = $this->adminModel->where('email', $email)->first();
            
            if ($admin && password_verify($password, $admin->password)) {
                $this->session->set([
                    'isLoggedIn' => true,
                    'admin_id'   => $admin->id,
                    'email'      => $admin->email,
                    'name'       => 'Administrateur',
                    'role'       => 'admin'
                ]);
                
                return redirect()->to('/admin/dashboard');
            }
            
            return redirect()->back()->withInput()->with('error', 'Identifiants administrateur incorrects.');
        }

        return redirect()->back()->with('error', 'Veuillez remplir les informations de connexion.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/login');
    }
}