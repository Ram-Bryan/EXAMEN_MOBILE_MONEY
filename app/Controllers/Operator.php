<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\TransactionModel;
use App\Models\FeeModel;
use App\Models\ClientModel;

class Operator extends BaseController
{
    protected $operatorModel;
    protected $transactionModel;
    protected $feeModel;
    protected $clientModel;
    protected $session;

    public function __construct()
    {
        $this->operatorModel = new OperatorModel();
        $this->transactionModel = new TransactionModel();
        $this->feeModel = new FeeModel();
        $this->clientModel = new ClientModel();
        $this->session = session();
        
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'operator') {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $data = [
            'page_title' => 'Dashboard Opérateur',
            'stats' => [
                'clients' => $this->clientModel->countAll(),
                'transactions' => $this->transactionModel->getTodayTransactions(),
                'volume' => $this->transactionModel->getTodayVolume(),
                'gains' => $this->transactionModel->getTodayGains()
            ],
            'prefixes' => $this->operatorModel->getPrefixes(),
            'recentTransactions' => $this->transactionModel->getRecentTransactions(5)
        ];
        
        return view('operator/dashboard', $data);
    }

    public function prefixConfig()
    {
        $data = [
            'page_title' => 'Configuration des préfixes',
            'prefixes' => $this->operatorModel->getPrefixes()
        ];
        
        return view('operator/prefix-config', $data);
    }

    public function updatePrefix()
    {
        $prefixes = $this->request->getPost('prefixes');
        
        if ($this->operatorModel->updatePrefixes($prefixes)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Préfixes mis à jour avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
        ]);
    }

    public function operationsTypes()
    {
        $data = [
            'page_title' => 'Types d\'opérations',
            'types' => $this->feeModel->getOperationTypes()
        ];
        
        return view('operator/operations-types', $data);
    }

    public function feesConfig()
    {
        $data = [
            'page_title' => 'Configuration des frais',
            'fees' => $this->feeModel->findAll()
        ];
        
        return view('operator/fees-config', $data);
    }

    public function gains()
    {
        $data = [
            'page_title' => 'Gains',
            'gains' => $this->transactionModel->getGainsByPeriod(),
            'totalGains' => $this->transactionModel->getTotalGains()
        ];
        
        return view('operator/gains', $data);
    }

    public function clients()
    {
        $data = [
            'page_title' => 'Clients',
            'clients' => $this->clientModel->findAll()
        ];
        
        return view('operator/clients', $data);
    }
}