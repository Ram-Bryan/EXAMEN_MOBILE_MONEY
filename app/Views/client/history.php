<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-history text-green"></i> Historique des Transactions</h2>
    <p class="text-muted">Consultez l'historique complet de vos opérations et transferts.</p>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list text-green me-2"></i> Toutes vos opérations</h5>
        <button class="btn btn-green-outline btn-sm" onclick="window.location.reload()">
            <i class="fas fa-sync"></i> Actualiser
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr style="background: var(--primary-bg);">
                        <th class="ps-4">Réf / Date</th>
                        <th>Type d'Opération</th>
                        <th>Détails</th>
                        <th class="text-end">Montant Brut</th>
                        <th class="text-end">Frais de Réseau</th>
                        <th class="text-end pe-4">Impact Solde</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3 text-green d-block"></i>
                                Aucune transaction enregistrée pour ce compte.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <?php
                                $isExpediteur = ($tx->expediteur_id == session()->get('client_id'));
                                $frais = (float)$tx->frais_applique;
                                $montant = (float)$tx->montant_brut;

                                if ($tx->type_code === 'DEPOT') {
                                    $badge = '<span class="badge bg-success px-3 py-2"><i class="fas fa-arrow-down me-1"></i> Dépôt</span>';
                                    $signe = '+';
                                    $color = 'text-success';
                                    $details = 'Dépôt direct (Automatique)';
                                    $total = $montant;
                                } elseif ($tx->type_code === 'RETRAIT') {
                                    $badge = '<span class="badge bg-danger px-3 py-2"><i class="fas fa-arrow-up me-1"></i> Retrait</span>';
                                    $signe = '-';
                                    $color = 'text-danger';
                                    $details = 'Retrait en guichet';
                                    $total = $montant + $frais;
                                } else {
                                    if ($isExpediteur) {
                                        $badge = '<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-paper-plane me-1"></i> Transfert Envoyé</span>';
                                        $signe = '-';
                                        $color = 'text-danger';
                                        $details = 'Vers : ' . esc($tx->destinataire_phone) . ' (' . esc($tx->destinataire_nom) . ')';
                                        $total = $montant + $frais;
                                    } else {
                                        $badge = '<span class="badge bg-info text-dark px-3 py-2"><i class="fas fa-hands-helping me-1"></i> Transfert Reçu</span>';
                                        $signe = '+';
                                        $color = 'text-success';
                                        $details = 'De : ' . esc($tx->expediteur_phone) . ' (' . esc($tx->expediteur_nom) . ')';
                                        $total = $montant;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark" style="font-size: 14px;">#TRX-<?= esc($tx->transaction_id) ?></div>
                                    <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($tx->date_transaction)) ?></div>
                                </td>
                                <td><?= $badge ?></td>
                                <td class="small text-muted"><?= esc($details) ?></td>
                                <td class="text-end fw-bold text-dark">
                                    <?= number_format($montant, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-muted small">
                                    <?= number_format($frais, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end pe-4 <?= $color ?> fw-bold" style="font-size: 16px;">
                                    <?= $signe ?><?= number_format($total, 0, ',', ' ') ?> Ar
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
