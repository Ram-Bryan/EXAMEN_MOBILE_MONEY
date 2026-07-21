<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TransactionModel;
use App\Models\BaremeFraisModel;
use App\Models\TypeOperationModel;
use App\Models\OperateurPrefixeModel;

class ClientController extends BaseController
{
    protected $clientModel;
    protected $transactionModel;
    protected $baremeFraisModel;
    protected $typeOperationModel;
    protected $prefixModel;
    protected $session;

    public function __construct()
    {
        $this->clientModel        = new ClientModel();
        $this->transactionModel   = new TransactionModel();
        $this->baremeFraisModel   = new BaremeFraisModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->prefixModel        = new OperateurPrefixeModel();
        $this->session            = session();
    }

    private function checkAuth(): bool
    {
        return $this->session->get('isLoggedIn') === true && $this->session->get('role') === 'client';
    }

    private function getValidPrefixes(): array
    {
        return array_column($this->prefixModel->findAll(), 'prefixe');
    }

    public function dashboard()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');
        $phone    = $this->session->get('phone');

        $balance = $this->clientModel->getBalance($clientId);
        $recent  = $this->transactionModel->getClientTransactions($clientId, 5);

        return view('client/dashboard', [
            'phone'    => $phone,
            'balance'  => $balance,
            'recent'   => $recent,
        ]);
    }

    public function balance()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');
        $balance  = $this->clientModel->getBalance($clientId);

        return view('client/balance', [
            'balance' => $balance,
        ]);
    }

    public function deposit()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');
        $client   = $this->clientModel->find($clientId);

        $fees = $this->baremeFraisModel->getFeesSchedules('DEPOT', $client->operateur_id);

        return view('client/deposit', [
            'phone' => $this->session->get('phone'),
            'fees'  => $fees,
        ]);
    }

    public function doDeposit()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount   = (float)$this->request->getPost('amount');
        $clientId = $this->session->get('client_id');

        if (!$this->clientModel->isNotreOperateur($clientId)) {
            return redirect()->back()->withInput()->with('error', 'Le dépôt est réservé aux clients de notre opérateur.');
        }

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        $typeOp = $this->typeOperationModel->where('code', 'DEPOT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de dépôt inexistant.');
        }

        $inserted = $this->transactionModel->createTransaction($typeOp->id, null, $clientId, $amount);

        if ($inserted) {
            return redirect()->to('/client/dashboard')->with('success', 'Dépôt de ' . number_format($amount, 0, ',', ' ') . ' Ar effectué avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'enregistrement de la transaction.');
    }

    public function withdraw()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');
        $client   = $this->clientModel->find($clientId);

        $balance = $this->clientModel->getBalance($clientId);
        $fees    = $this->baremeFraisModel->getFeesSchedules('RETRAIT', $client->operateur_id);

        return view('client/withdraw', [
            'balance' => $balance,
            'fees'    => $fees,
        ]);
    }

    public function doWithdraw()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount   = (float)$this->request->getPost('amount');
        $clientId = $this->session->get('client_id');

        if (!$this->clientModel->isNotreOperateur($clientId)) {
            return redirect()->back()->withInput()->with('error', 'Le retrait est réservé aux clients de notre opérateur.');
        }

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return redirect()->back()->withInput()->with('error', 'Client introuvable.');
        }

        $balance = $this->clientModel->getBalance($clientId);
        if ($balance <= 0) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant ou inexistant. Veuillez effectuer un dépôt d\'abord.');
        }

        $typeOp = $this->typeOperationModel->where('code', 'RETRAIT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de retrait inexistant.');
        }

        $fee = $this->baremeFraisModel->getFrais($typeOp->id, $client->operateur_id, $amount);
        if ($fee === null) {
            return redirect()->back()->withInput()->with('error', 'Aucun barème de frais ne couvre ce montant.');
        }

        $totalWithdraw = $amount + $fee;

        if ($totalWithdraw > $balance) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Le montant avec frais (' . number_format($totalWithdraw, 0, ',', ' ') . ' Ar) dépasse votre solde disponible (' . number_format($balance, 0, ',', ' ') . ' Ar).');
        }

        $inserted = $this->transactionModel->createTransaction($typeOp->id, $clientId, null, $amount);

        if ($inserted) {
            return redirect()->to('/client/dashboard')->with('success', 'Retrait de ' . number_format($amount, 0, ',', ' ') . ' Ar (frais: ' . number_format($fee, 0, ',', ' ') . ' Ar) effectué avec succès.');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors du retrait.');
    }

    public function transfer()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');
        $client   = $this->clientModel->find($clientId);

        $balance = $this->clientModel->getBalance($clientId);
        $fees    = $this->baremeFraisModel->getFeesSchedules('TRANSFERT', $client->operateur_id);

        return view('client/transfer', [
            'phone'               => $this->session->get('phone'),
            'balance'             => $balance,
            'fees'                => $fees,
            'sender_operateur_id' => $client->operateur_id,
            'validPrefixes'       => $this->getValidPrefixes(),
        ]);
    }

    public function doTransfer()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client')->with('error', 'Non authentifié');
        }

        $amount        = (float)$this->request->getPost('amount');
        $senderId      = $this->session->get('client_id');
        $senderPhone   = $this->session->get('phone');
        $includeFees   = $this->request->getPost('include_fees');
        $transferMode  = $this->request->getPost('transfer_mode') ?? 'single';
        $recipientsRaw = $this->request->getPost('recipients');
        $recipientPhone= trim($this->request->getPost('recipient_phone') ?? '');

        if ($transferMode === 'multiple' && is_array($recipientsRaw)) {
            $recipients = array_filter(array_map('trim', $recipientsRaw), fn($p) => $p !== '');
        } else {
            $recipients = $recipientPhone !== '' ? [$recipientPhone] : [];
        }

        if ($amount <= 0) {
            return redirect()->back()->withInput()->with('error', 'Montant invalide.');
        }

        if (empty($recipients)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez saisir au moins un numéro de destinataire.');
        }

        $sender = $this->clientModel->find($senderId);
        if (!$sender) {
            return redirect()->back()->withInput()->with('error', 'Client introuvable.');
        }

        $balance = $this->clientModel->getBalance($senderId);
        if ($balance <= 0) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Veuillez effectuer un dépôt d\'abord.');
        }

        $typeOp = $this->typeOperationModel->where('code', 'TRANSFERT')->first();
        if (!$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération de transfert inexistant.');
        }

        $validPrefixes = $this->getValidPrefixes();
        $resolvedRecipients = [];

        foreach ($recipients as $phone) {
            if (!preg_match('/^0\d{9}$/', $phone)) {
                return redirect()->back()->withInput()->with('error', 'Numéro invalide : ' . $phone . '. Le numéro doit faire 10 chiffres.');
            }
            if (!in_array(substr($phone, 0, 3), $validPrefixes)) {
                return redirect()->back()->withInput()->with('error', 'Préfixe invalide pour le numéro : ' . $phone);
            }
            if ($phone === $senderPhone) {
                return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas envoyer à votre propre numéro (' . $phone . ').');
            }
            $recipient = $this->clientModel->getByTelephone($phone);
            if (!$recipient) {
                return redirect()->back()->withInput()->with('error', 'Le numéro ' . $phone . ' n\'est pas un client existant.');
            }
            $resolvedRecipients[] = ['phone' => $phone, 'client' => $recipient];
        }

        $count = count($resolvedRecipients);
        $amountPerRecipient = ($transferMode === 'multiple' && $count > 1)
            ? floor($amount / $count)
            : $amount;

        if ($amountPerRecipient <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le montant par destinataire est trop faible.');
        }

        $totalDebit = 0;
        $transactionsData = [];

        foreach ($resolvedRecipients as $entry) {
            $recipientClient = $entry['client'];
            $fee = 0;
            $commission = 0;
            $isInterOp = $this->baremeFraisModel->isInterOperator($sender->operateur_id, $recipientClient->operateur_id);

            $fee = $this->baremeFraisModel->getFrais($typeOp->id, $sender->operateur_id, $amountPerRecipient);
            if ($fee === null) {
                return redirect()->back()->withInput()->with('error', 'Aucun barème de frais ne couvre le montant de ' . number_format($amountPerRecipient, 0, ',', ' ') . ' Ar.');
            }

            if ($isInterOp) {
                $commission = $this->baremeFraisModel->getCommission($recipientClient->operateur_id, $amountPerRecipient);
            }

            $fraisInclus = ($includeFees == '2') ? 1 : 0;

            if ($fraisInclus == 1) {
                $debitPerRecipient = $amountPerRecipient;
            } else {
                $debitPerRecipient = $amountPerRecipient + $fee + $commission;
            }

            $totalDebit += $debitPerRecipient;

            $transactionsData[] = [
                'recipient'     => $recipientClient,
                'phone'         => $entry['phone'],
                'amount'        => $amountPerRecipient,
                'fee'           => $fee,
                'commission'    => $commission,
                'debit'         => $debitPerRecipient,
                'frais_inclus'  => $fraisInclus,
            ];
        }

        if ($totalDebit > $balance) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant. Le total débité (' . number_format($totalDebit, 0, ',', ' ') . ' Ar) dépasse votre solde (' . number_format($balance, 0, ',', ' ') . ' Ar).');
        }

        $insertedCount = 0;

        foreach ($transactionsData as $txData) {
            $ok = $this->transactionModel->createTransaction(
                $typeOp->id,
                $senderId,
                $txData['recipient']->id,
                $txData['amount'],
                $txData['frais_inclus']
            );
            if ($ok) {
                $insertedCount++;
            }
        }

        if ($insertedCount === $count) {
            $msg = $count === 1
                ? 'Transfert de ' . number_format($amountPerRecipient, 0, ',', ' ') . ' Ar vers ' . $resolvedRecipients[0]['phone'] . ' effectué avec succès.'
                : 'Transfert multiple de ' . number_format($amount, 0, ',', ' ') . ' Ar vers ' . $count . ' destinataires effectué avec succès.';
            return redirect()->to('/client/dashboard')->with('success', $msg);
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors du transfert. Seulement ' . $insertedCount . '/' . $count . ' transactions effectuées.');
    }

    public function history()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('/login/client');
        }

        $clientId = $this->session->get('client_id');

        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'type'      => $this->request->getGet('type'),
        ];

        $transactions = $this->transactionModel->getClientTransactions($clientId, null, $filters);

        return view('client/history', [
            'transactions' => $transactions,
            'filters'      => $filters,
        ]);
    }
}