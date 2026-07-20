<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;

class Api extends BaseController
{
    protected $clientModel;
    protected $typeOperationModel;
    protected $baremeFraisModel;
    protected $session;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->session = session();
    }

    /**
     * Get the logged-in client's balance as JSON.
     */
    public function getBalance()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'client') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non authentifié'
            ])->setStatusCode(401);
        }

        $clientId = $this->session->get('client_id');
        $balance = $this->clientModel->getBalance($clientId);

        return $this->response->setJSON([
            'success' => true,
            'balance' => $balance
        ]);
    }

    /**
     * Calculate fee dynamically for preview.
     */
    public function calculateFees()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Non authentifié'
            ])->setStatusCode(401);
        }

        $typeCode = $this->request->getGet('type_code') ?? $this->request->getPost('type_code');
        $amount = (float)($this->request->getGet('amount') ?? $this->request->getPost('amount'));
        $clientId = $this->session->get('client_id');

        if (!$typeCode || $amount <= 0 || !$clientId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Paramètres invalides'
            ]);
        }

        // Fetch client details
        $client = $this->clientModel->find($clientId);
        if (!$client) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Client introuvable'
            ]);
        }

        // Fetch operation type
        $typeOp = $this->typeOperationModel->where('code', $typeCode)->first();
        if (!$typeOp) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type d\'opération invalide'
            ]);
        }

        // Calculate fee via model
        $fee = $this->baremeFraisModel->getFrais($typeOp->id, $client->operateur_id, $amount);

        return $this->response->setJSON([
            'success' => true,
            'fee'     => $fee
        ]);
    }
}
