<?php

namespace App\Controllers;

use App\Models\OperateurPrefixeModel;
use App\Models\BaremeFraisModel;
use App\Models\BaremeFraisHistoriqueModel;
use App\Models\HistoriqueOperateurPrefixesModel;
use App\Models\CommissionModel;
use App\Models\CommissionHistoriqueModel;
use App\Models\TransactionModel;
use App\Models\ClientModel;
use App\Models\TypeOperationModel;

class AdminController extends BaseController
{
    protected $operateurPrefixeModel;
    protected $baremeFraisModel;
    protected $baremeFraisHistoriqueModel;
    protected $historiqueOperateurModel;
    protected $commissionModel;
    protected $commissionHistoriqueModel;
    protected $transactionModel;
    protected $clientModel;
    protected $typeOperationModel;

    public function __construct()
    {
        $this->operateurPrefixeModel      = new OperateurPrefixeModel();
        $this->baremeFraisModel           = new BaremeFraisModel();
        $this->baremeFraisHistoriqueModel = new BaremeFraisHistoriqueModel();
        $this->historiqueOperateurModel   = new HistoriqueOperateurPrefixesModel();
        $this->commissionModel            = new CommissionModel();
        $this->commissionHistoriqueModel  = new CommissionHistoriqueModel();
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
    // Opérateurs (préfixes)
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
            'prefixe'             => $this->request->getPost('prefixe'),
            'nom'                 => $this->request->getPost('nom') ?: null,
            'est_notre_operateur' => (int)$this->request->getPost('est_notre_operateur'),
        ]);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Opérateur ajouté.' : 'Erreur lors de l\'ajout.');
    }

    public function updateOperator($id)
    {
        $ok = $this->operateurPrefixeModel->update($id, [
            'prefixe'             => $this->request->getPost('prefixe'),
            'nom'                 => $this->request->getPost('nom') ?: null,
            'est_notre_operateur' => (int)$this->request->getPost('est_notre_operateur'),
        ]);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Opérateur mis à jour.' : 'Erreur lors de la mise à jour.');
    }

    public function deleteOperator($id)
    {
        $ok = $this->operateurPrefixeModel->delete($id);

        return redirect()->to('/admin/operators')
            ->with($ok ? 'success' : 'error', $ok ? 'Opérateur supprimé.' : 'Erreur lors de la suppression.');
    }

    // ----------------------------------------------------------------
    // Détail opérateur : barèmes + préfixes + commission
    // ----------------------------------------------------------------

    public function operatorDetail($id)
    {
        $operator = $this->operateurPrefixeModel->find($id);
        if (!$operator) {
            return redirect()->to('/admin/operators')->with('error', 'Opérateur introuvable.');
        }

        // Commission actuelle (uniquement pour les opérateurs externes)
        $commission = null;
        if (!$operator->est_notre_operateur) {
            $commission = $this->commissionModel->getCommissionByOperateur($id);
        }

        return view('admin/operator_detail', [
            'operator'         => $operator,
            'baremes'          => $this->baremeFraisModel->getBaremesByOperateur($id),
            'types_operation'  => $this->typeOperationModel->findAll(),
            'gains'            => $this->transactionModel->getGainsParOperateur($id),
            'prefixes'         => $this->historiqueOperateurModel->getByOperateur($id),
            'commission'       => $commission,
        ]);
    }

    // ----------------------------------------------------------------
    // Barèmes de frais
    // ----------------------------------------------------------------

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
    // Préfixes historiques d'un opérateur
    // ----------------------------------------------------------------

    public function addPrefixe($operateurId)
    {
        $prefixe = trim($this->request->getPost('prefixe'));
        if (!$prefixe) {
            return redirect()->to('/admin/operators/detail/' . $operateurId)
                ->with('error', 'Le préfixe est requis.');
        }

        $ok = $this->historiqueOperateurModel->addPrefixe($operateurId, $prefixe);

        return redirect()->to('/admin/operators/detail/' . $operateurId)
            ->with($ok ? 'success' : 'error', $ok ? 'Préfixe ajouté.' : 'Erreur lors de l\'ajout du préfixe.');
    }

    // ----------------------------------------------------------------
    // Commissions inter-opérateurs
    // ----------------------------------------------------------------

    public function commissions()
    {
        $commissions = $this->commissionModel->getAllCommissions();

        $db = \Config\Database::connect();
        $historiques = $db->query("
            SELECT ch.*, c.operateur_destination_id,
                   o.prefixe, o.nom
            FROM commissions_historique ch
            JOIN commissions c ON c.id = ch.commission_id
            JOIN operateur_prefixes o ON o.id = c.operateur_destination_id
            ORDER BY o.nom, ch.date_modif DESC
        ")->getResult();

        return view('admin/commissions', [
            'commissions'  => $commissions,
            'historiques'  => $historiques,
        ]);
    }

    public function updateCommission($operateurId)
    {
        $pourcentage = (float)$this->request->getPost('pourcentage');
        $redirectBack = $this->request->getPost('redirect_to') ?: '/admin/commissions';

        if ($pourcentage < 0 || $pourcentage > 100) {
            return redirect()->to($redirectBack)
                ->with('error', 'Le pourcentage doit être entre 0 et 100.');
        }

        $commissionId = $this->commissionModel->getOrCreate($operateurId);

        $ok = $this->commissionHistoriqueModel->addPourcentage($commissionId, $pourcentage);

        return redirect()->to($redirectBack)
            ->with($ok ? 'success' : 'error', $ok ? 'Commission mise à jour (' . $pourcentage . '%).' : 'Erreur.');
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
    // Gains (v2 : séparation nous / autres opérateurs)
    // ----------------------------------------------------------------

    public function gains()
    {
        // Vérifier si les vues v2 existent
        $gainsSepaRes = [];
        $montantsAEnvoyer = [];

        try {
            $gainsSepaRes = $this->transactionModel->getGainsSepares();
            $montantsAEnvoyer = $this->transactionModel->getMontantsAEnvoyer();
        } catch (\Exception $e) {
            // Fallback si les vues v2 ne sont pas encore créées
            $gainsSepaRes = [];
            $montantsAEnvoyer = [];
        }

        return view('admin/gains', [
            'gains'            => $this->transactionModel->getGainsParType(),
            'gainsSepares'     => $gainsSepaRes,
            'montantsAEnvoyer' => $montantsAEnvoyer,
        ]);
    }
}