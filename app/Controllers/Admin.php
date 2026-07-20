<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\FeeModel;
use App\Models\TransactionModel;
use App\Models\ClientModel;

class Admin extends BaseController
{
    protected $operatorModel;
    protected $feeModel;
    protected $transactionModel;
    protected $clientModel;
    protected $session;

    public function __construct()
    {
        $this->operatorModel = new OperatorModel();
        $this->feeModel = new FeeModel();
        $this->transactionModel = new TransactionModel();
        $this->clientModel = new ClientModel();
        $this->session = session();
        
        // Check if user is logged in and is admin
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $data = [
            'page_title' => 'Dashboard Admin',
            'totalOperators' => $this->operatorModel->countAll(),
            'totalClients' => $this->clientModel->countAll(),
            'totalTransactions' => $this->transactionModel->countAll(),
            'totalVolume' => $this->transactionModel->getTotalVolume(),
            'recentTransactions' => $this->transactionModel->getRecentTransactions(10)
        ];
        
        return view('admin/dashboard', $data);
    }

    public function operators()
    {
        $data = [
            'page_title' => 'Gestion des Opérateurs',
            'operators' => $this->operatorModel->findAll()
        ];
        
        return view('admin/operators', $data);
    }

    public function createOperator()
    {
        $data = $this->request->getPost();
        
        if ($this->operatorModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Opérateur créé avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la création'
        ]);
    }

    public function updateOperator($id)
    {
        $data = $this->request->getPost();
        
        if ($this->operatorModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Opérateur mis à jour avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
        ]);
    }

    public function deleteOperator($id)
    {
        if ($this->operatorModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Opérateur supprimé avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
        ]);
    }

    public function getOperator($id)
    {
        $operator = $this->operatorModel->find($id);
        return $this->response->setJSON($operator);
    }

    public function feesConfig()
    {
        $data = [
            'page_title' => 'Configuration des Frais',
            'fees' => $this->feeModel->findAll()
        ];
        
        return view('admin/fees-config', $data);
    }

    public function createFee()
    {
        $data = $this->request->getPost();
        
        if ($this->feeModel->save($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Frais configuré avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la configuration'
        ]);
    }

    public function updateFee($id)
    {
        $data = $this->request->getPost();
        
        if ($this->feeModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Frais mis à jour avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
        ]);
    }

    public function deleteFee($id)
    {
        if ($this->feeModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Frais supprimé avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la suppression'
        ]);
    }

    public function getFee($id)
    {
        $fee = $this->feeModel->find($id);
        return $this->response->setJSON($fee);
    }

    public function clients()
    {
        $data = [
            'page_title' => 'Gestion des Clients',
            'clients' => $this->clientModel->findAll()
        ];
        
        return view('admin/clients', $data);
    }

    public function clientList()
    {
        $clients = $this->clientModel->findAll();
        return $this->response->setJSON($clients);
    }

    public function transactions()
    {
        $data = [
            'page_title' => 'Transactions',
            'transactions' => $this->transactionModel->getAllWithClientInfo()
        ];
        
        return view('admin/transactions', $data);
    }

    public function gains()
    {
        $data = [
            'page_title' => 'Gains',
            'gains' => $this->transactionModel->getGainsByPeriod()
        ];
        
        return view('admin/gains', $data);
    }

    public function operationsTypes()
    {
        $data = ['page_title' => 'Types d\'opérations'];
        return view('admin/operations-types', $data);
    }
}