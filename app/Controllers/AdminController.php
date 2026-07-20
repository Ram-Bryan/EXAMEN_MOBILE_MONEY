<?php

namespace App\Controllers;

use App\Models\OperateurPrefixeModel;
use App\Models\BaremeFraisModel;
use App\Models\BaremeFraisHistoriqueModel;
use App\Models\TransactionModel;
use App\Models\ClientModel;
use App\Models\TypeOperationModel;

class AdminController extends BaseController
{
    protected $operateurPrefixeModel;
    protected $baremeFraisModel;
    protected $baremeFraisHistoriqueModel;
    protected $transactionModel;
    protected $clientModel;
    protected $typeOperationModel;

    public function __construct()
    {
        $this->operateurPrefixeModel      = new OperateurPrefixeModel();
        $this->baremeFraisModel           = new BaremeFraisModel();
        $this->baremeFraisHistoriqueModel = new BaremeFraisHistoriqueModel();
        $this->transactionModel           = new TransactionModel();
        $this->clientModel                = new ClientModel();
        $this->typeOperationModel         = new TypeOperationModel();
    }

    // ----------------------------------------------------------------
    // Dashboard
    // ----------------------------------------------------------------

    public function dashboard()
    {
        return view('admin/dashboard', [
            'totalOperators'    => $this->operateurPrefixeModel->countAll(),
            'totalClients'      => $this->clientModel->countAll(),
            'totalTransactions' => $this->transactionModel->countAll(),
        ]);
    }

    // ----------------------------------------------------------------
    // Préfixes opérateurs
    // ----------------------------------------------------------------

    public function operators()
    {
        return view('admin/operators', [
            'operators' => $this->operateurPrefixeModel->findAll(),
        ]);
    }

    public function createOperator()
    {
        $ok = $this->operateurPrefixeModel->insert([
            'prefixe' => $this->request->getPost('prefixe'),
        ]);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Préfixe ajouté.' : 'Erreur lors de l\'ajout.');
    }

    public function updateOperator($id)
    {
        $ok = $this->operateurPrefixeModel->update($id, [
            'prefixe' => $this->request->getPost('prefixe'),
        ]);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Préfixe mis à jour.' : 'Erreur lors de la mise à jour.');
    }

    public function deleteOperator($id)
    {
        $ok = $this->operateurPrefixeModel->delete($id);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Préfixe supprimé.' : 'Erreur lors de la suppression.');
    }

    // ----------------------------------------------------------------
    // Barèmes de frais par opérateur
    // ----------------------------------------------------------------

    public function operatorDetail($id)
    {
        $operator = $this->operateurPrefixeModel->find($id);
        if (!$operator) {
            return redirect()->to('/admin/operators')->with('error', 'Opérateur introuvable.');
        }

        return view('admin/operator_detail', [
            'operator'       => $operator,
            'baremes'        => $this->baremeFraisModel->getBaremesByOperateur($id),
            'types_operation' => $this->typeOperationModel->findAll(),
        ]);
    }

    public function createFee($operateurId)
    {
        $ok = $this->baremeFraisModel->addTranche(
            $this->request->getPost('type_operation_id'),
            $operateurId,
            $this->request->getPost('montant_min'),
            $this->request->getPost('montant_max'),
            $this->request->getPost('frais_fixe')
        );

        return redirect()->to('/admin/operators/detail/' . $operateurId)
            ->with($ok ? 'success' : 'error', $ok ? 'Tranche créée.' : 'Erreur lors de la création.');
    }

    public function updateFee($baremeId, $operateurId)
    {
        $ok = $this->baremeFraisHistoriqueModel->addHistorique(
            $baremeId,
            $this->request->getPost('montant_min'),
            $this->request->getPost('montant_max'),
            $this->request->getPost('frais_fixe')
        );

        return redirect()->to('/admin/operators/detail/' . $operateurId)
            ->with($ok ? 'success' : 'error', $ok ? 'Tranche mise à jour (historique préservé).' : 'Erreur.');
    }

    // ----------------------------------------------------------------
    // Comptes clients
    // ----------------------------------------------------------------

    public function clients()
    {
        return view('admin/clients', [
            'clients' => $this->clientModel->getClientsSoldes(),
        ]);
    }

    // ----------------------------------------------------------------
    // Gains
    // ----------------------------------------------------------------

    public function gains()
    {
        return view('admin/gains', [
            'gains' => $this->transactionModel->getGainsParType(),
        ]);
    }
}