<?php

namespace App\Controllers;

use App\Models\TransactionModel;
use App\Models\ClientModel;
use App\Models\FeeModel;

class Client extends BaseController
{
    protected $transactionModel;
    protected $clientModel;
    protected $feeModel;
    protected $session;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->clientModel = new ClientModel();
        $this->feeModel = new FeeModel();
        $this->session = session();
        
        // Auto-login with phone number
        $phone = $this->request->getGet('phone') ?? $this->session->get('phone');
        if ($phone) {
            $this->session->set('phone', $phone);
            $this->session->set('isLoggedIn', true);
            $this->session->set('role', 'client');
        }
        
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    }

    public function dashboard()
    {
        $phone = $this->session->get('phone');
        $data = [
            'page_title' => 'Mon Compte',
            'phone' => $phone,
            'balance' => $this->clientModel->getBalance($phone),
            'recentTransactions' => $this->transactionModel->getClientTransactions($phone, 5)
        ];
        
        return view('client/dashboard', $data);
    }

    public function deposit()
    {
        $data = [
            'page_title' => 'Dépôt',
            'fees' => $this->feeModel->getFeesByType('deposit')
        ];
        
        return view('client/deposit', $data);
    }

    public function doDeposit()
    {
        $amount = (float)$this->request->getPost('amount');
        $phone = $this->session->get('phone');
        
        if ($amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide'
            ]);
        }
        
        // Calculate fees
        $fee = $this->feeModel->calculateFee('deposit', $amount);
        
        // Create transaction
        $transactionData = [
            'client_phone' => $phone,
            'type' => 'deposit',
            'amount' => $amount,
            'fee' => $fee,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->transactionModel->save($transactionData)) {
            // Update client balance
            $this->clientModel->addBalance($phone, $amount);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dépôt effectué avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors du dépôt'
        ]);
    }

    public function withdraw()
    {
        $data = [
            'page_title' => 'Retrait',
            'fees' => $this->feeModel->getFeesByType('withdraw')
        ];
        
        return view('client/withdraw', $data);
    }

    public function doWithdraw()
    {
        $amount = (float)$this->request->getPost('amount');
        $phone = $this->session->get('phone');
        
        if ($amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide'
            ]);
        }
        
        // Check balance
        $balance = $this->clientModel->getBalance($phone);
        if ($amount > $balance) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solde insuffisant'
            ]);
        }
        
        // Calculate fees
        $fee = $this->feeModel->calculateFee('withdraw', $amount);
        $totalWithdraw = $amount + $fee;
        
        if ($totalWithdraw > $balance) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solde insuffisant incluant les frais'
            ]);
        }
        
        // Create transaction
        $transactionData = [
            'client_phone' => $phone,
            'type' => 'withdraw',
            'amount' => $amount,
            'fee' => $fee,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->transactionModel->save($transactionData)) {
            // Update client balance
            $this->clientModel->subtractBalance($phone, $totalWithdraw);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Retrait effectué avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors du retrait'
        ]);
    }

    public function transfer()
    {
        $data = [
            'page_title' => 'Transfert',
            'fees' => $this->feeModel->getFeesByType('transfer')
        ];
        
        return view('client/transfer', $data);
    }

    public function doTransfer()
    {
        $amount = (float)$this->request->getPost('amount');
        $recipient = $this->request->getPost('recipient_phone');
        $phone = $this->session->get('phone');
        
        if ($amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Montant invalide'
            ]);
        }
        
        if ($recipient === $phone) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vous ne pouvez pas transférer à vous-même'
            ]);
        }
        
        // Check if recipient exists
        if (!$this->clientModel->exists($recipient)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Destinataire non trouvé'
            ]);
        }
        
        // Check balance
        $balance = $this->clientModel->getBalance($phone);
        $fee = $this->feeModel->calculateFee('transfer', $amount);
        $totalTransfer = $amount + $fee;
        
        if ($totalTransfer > $balance) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Solde insuffisant'
            ]);
        }
        
        // Create transaction
        $transactionData = [
            'client_phone' => $phone,
            'recipient_phone' => $recipient,
            'type' => 'transfer',
            'amount' => $amount,
            'fee' => $fee,
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->transactionModel->save($transactionData)) {
            // Update balances
            $this->clientModel->subtractBalance($phone, $totalTransfer);
            $this->clientModel->addBalance($recipient, $amount);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Transfert effectué avec succès'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors du transfert'
        ]);
    }

    public function history()
    {
        $data = [
            'page_title' => 'Historique',
            'transactions' => $this->transactionModel->getClientTransactions(
                $this->session->get('phone')
            )
        ];
        
        return view('client/history', $data);
    }

    public function balance()
    {
        $balance = $this->clientModel->getBalance($this->session->get('phone'));
        return $this->response->setJSON([
            'balance' => $balance
        ]);
    }
}