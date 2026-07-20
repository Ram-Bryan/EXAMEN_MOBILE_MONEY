<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\BaremeFraisModel;
use App\Models\TypeOperationModel;
use App\Models\OperateurPrefixeModel;

class Client extends BaseController
{
    protected $clientModel;
    protected $transactionModel;
    protected $baremeFraisModel;
    protected $typeOperationModel;
    protected $prefixModel;
    protected $session;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->transactionModel = new TransactionModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->prefixModel = new OperateurPrefixeModel();
        $this->session = session();
    }

    /**
     * Helper to verify client authentication.
     */
    private function checkAuth(): bool
    {
        return $this->session->get('isLoggedIn') === true && $this->session->get('role') === 'client';
    }

    /**
     * Dashboard: Solde + 5 dernières transactions.
     */
    public function dashboard()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $phone = $this->session->get('phone');
        
        $balance = $this->clientModel->getBalance($clientId);
        $recentTransactions = $this->transactionModel->getClientTransactions($clientId, 5);

        $data = [
            'page_title'         => 'Mon Compte',
            'phone'              => $phone,
            'balance'            => $balance,
            'recentTransactions' => $recentTransactions
        ];

        return view('client/dashboard', $data);
    }

    /**
     * Voir le solde (page dédiée).
     */
    public function balance()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $phone = $this->session->get('phone');
        
        $client = $this->clientModel->find($clientId);
        $prefix = $this->prefixModel->find($client->operateur_id);
        
        $balance = $this->clientModel->getBalance($clientId);

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'balance' => $balance
            ]);
        }

        $data = [
            'page_title'      => 'Mon Solde',
            'phone'           => $phone,
            'balance'         => $balance,
            'operator_prefix' => $prefix ? $prefix->prefixe : 'Inconnu'
        ];

        return view('client/balance', $data);
    }

    /**
     * Afficher le formulaire de Dépôt.
     */
    public function deposit()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $client = $this->clientModel->find($clientId);
        
        $fees = $this->baremeFraisModel->getFeesSchedules('DEPOT', $client->operateur_id);

        $data = [
            'page_title' => 'Dépôt',
            'phone'      => $this->session->get('phone'),
            'fees'       => $fees
        ];

        return view('client/deposit', $data);
    }

    /**
     * Exécuter le Dépôt.
     */
    public function doDeposit()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount = (float)$this->request->getPost('amount');
        $clientId = $this->session->get('client_id');

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        // Fetch type operation DEPOT
        $typeOp = $this->typeOperationModel->where('code', 'DEPOT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de dépôt inexistant.');
        }

        // Create transaction
        $inserted = $this->transactionModel->createTransaction($typeOp->id, null, $clientId, $amount);

        if ($inserted) {
            return redirect()->to('/client/dashboard')->with('success', 'Dépôt de ' . number_format($amount, 0, ',', ' ') . ' Ar effectué avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de la transaction.');
    }

    /**
     * Afficher le formulaire de Retrait.
     */
    public function withdraw()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $client = $this->clientModel->find($clientId);
        
        $balance = $this->clientModel->getBalance($clientId);
        $fees = $this->baremeFraisModel->getFeesSchedules('RETRAIT', $client->operateur_id);

        $data = [
            'page_title' => 'Retrait',
            'balance'    => $balance,
            'fees'       => $fees
        ];

        return view('client/withdraw', $data);
    }

    /**
     * Exécuter le Retrait.
     */
    public function doWithdraw()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount = (float)$this->request->getPost('amount');
        $clientId = $this->session->get('client_id');

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->back()->withInput()->with('error', 'Client introuvable.');
        }

        // Fetch type operation RETRAIT
        $typeOp = $this->typeOperationModel->where('code', 'RETRAIT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de retrait inexistant.');
        }

        // Calculate fee
        $fee = $this->baremeFraisModel->getFrais($typeOp->id, $client->operateur_id, $amount);
        if ($fee === null) {
            return redirect()->back()->withInput()->with('error', 'Aucun barème de frais ne couvre ce montant.');
        }

        $totalWithdraw = $amount + $fee;
        
        // Verify balance
        $balance = $this->clientModel->getBalance($clientId);
        if ($totalWithdraw > $balance) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Le montant avec frais (' . number_format($totalWithdraw, 0, ',', ' ') . ' Ar) dépasse votre solde disponible (' . number_format($balance, 0, ',', ' ') . ' Ar).');
        }

        // Insert transaction
        $inserted = $this->transactionModel->createTransaction($typeOp->id, $clientId, null, $amount);

        if ($inserted) {
            return redirect()->to('/client/dashboard')->with('success', 'Retrait de ' . number_format($amount, 0, ',', ' ') . ' Ar (frais: ' . number_format($fee, 0, ',', ' ') . ' Ar) effectué avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors du retrait.');
    }

    /**
     * Afficher le formulaire de Transfert.
     */
    public function transfer()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $client = $this->clientModel->find($clientId);
        
        $balance = $this->clientModel->getBalance($clientId);
        $fees = $this->baremeFraisModel->getFeesSchedules('TRANSFERT', $client->operateur_id);

        $data = [
            'page_title' => 'Transfert',
            'phone'      => $this->session->get('phone'),
            'balance'    => $balance,
            'fees'       => $fees
        ];

        return view('client/transfer', $data);
    }

    /**
     * Exécuter le Transfert.
     */
    public function doTransfer()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount = (float)$this->request->getPost('amount');
        $recipientPhone = trim($this->request->getPost('recipient_phone'));
        $senderId = $this->session->get('client_id');
        $senderPhone = $this->session->get('phone');

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        if ($recipientPhone === $senderPhone) {
            return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');
        }

        // Find or auto-create recipient
        $recipient = $this->clientModel->getByTelephone($recipientPhone);
        if (!$recipient) {
            return redirect()->back()->withInput()->with('error', 'Numéro destinataire introuvable. Le destinataire doit être un client existant.');
        }

        $sender = $this->clientModel->find($senderId);

        // Fetch type operation TRANSFERT
        $typeOp = $this->typeOperationModel->where('code', 'TRANSFERT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de transfert inexistant.');
        }

        // Calculate fee
        $fee = $this->baremeFraisModel->getFrais($typeOp->id, $sender->operateur_id, $amount);
        if ($fee === null) {
            return redirect()->back()->withInput()->with('error', 'Aucun barème de frais ne couvre ce montant.');
        }

        $totalTransfer = $amount + $fee;

        // Verify balance
        $balance = $this->clientModel->getBalance($senderId);
        if ($totalTransfer > $balance) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Le transfert avec frais (' . number_format($totalTransfer, 0, ',', ' ') . ' Ar) dépasse votre solde disponible (' . number_format($balance, 0, ',', ' ') . ' Ar).');
        }

        // Insert transaction
        $inserted = $this->transactionModel->createTransaction($typeOp->id, $senderId, $recipient->id, $amount);

        if ($inserted) {
            return redirect()->to('/client/dashboard')->with('success', 'Transfert de ' . number_format($amount, 0, ',', ' ') . ' Ar vers ' . $recipientPhone . ' effectué avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors du transfert.');
    }

    /**
     * Historique des transactions du client.
     */
    public function history()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login');
        }

        $clientId = $this->session->get('client_id');
        $transactions = $this->transactionModel->getClientTransactions($clientId);

        $data = [
            'page_title'   => 'Historique',
            'transactions' => $transactions
        ];

        return view('client/history', $data);
    }
}