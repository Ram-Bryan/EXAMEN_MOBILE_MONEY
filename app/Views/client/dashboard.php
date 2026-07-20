<?= $this->extend('layout/client') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-mobile-alt text-green"></i> Mon Espace Client</h2>
    <p class="text-muted">Bienvenue, <strong><?= esc($phone) ?></strong></p>
</div>

<!-- Balance Card -->
<div class="row mb-4 animate-fade-in">
    <div class="col-12">
        <div class="text-center p-4" style="background: var(--primary-gradient); border-radius: 16px; color: white;">
            <i class="fas fa-wallet mb-2" style="font-size: 48px; opacity: 0.9;"></i>
            <div style="font-size: 42px; font-weight: 700; letter-spacing: -1px; margin: 10px 0;">
                <?= number_format($balance, 0, ',', ' ') ?> Ar
            </div>
            <div class="text-uppercase" style="font-size: 14px; opacity: 0.85; letter-spacing: 1px;">Solde disponible</div>
            <button class="btn btn-light btn-sm mt-3 px-4 py-2" onclick="refreshBalance()" style="border-radius: 20px; font-weight: 600; color: var(--primary);">
                <i class="fas fa-sync me-1"></i> Actualiser
            </button>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100 text-center border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <i class="fas fa-plus-circle mb-3" style="font-size: 40px; color: var(--primary);"></i>
                <h5 class="card-title fw-bold">Dépôt</h5>
                <p class="card-text text-muted small">Ajouter des fonds sur votre compte</p>
                <a href="<?= base_url('client/deposit') ?>" class="btn btn-green mt-auto">Dépôt</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100 text-center border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <i class="fas fa-minus-circle mb-3" style="font-size: 40px; color: var(--primary);"></i>
                <h5 class="card-title fw-bold">Retrait</h5>
                <p class="card-text text-muted small">Retirer de l'argent de votre compte</p>
                <a href="<?= base_url('client/withdraw') ?>" class="btn btn-green mt-auto">Retrait</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100 text-center border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <i class="fas fa-exchange-alt mb-3" style="font-size: 40px; color: var(--primary);"></i>
                <h5 class="card-title fw-bold">Transfert</h5>
                <p class="card-text text-muted small">Envoyer des fonds à un destinataire</p>
                <a href="<?= base_url('client/transfer') ?>" class="btn btn-green mt-auto">Transfert</a>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card h-100 text-center border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <i class="fas fa-history mb-3" style="font-size: 40px; color: var(--primary);"></i>
                <h5 class="card-title fw-bold">Historique</h5>
                <p class="card-text text-muted small">Consulter vos transactions passées</p>
                <a href="<?= base_url('client/history') ?>" class="btn btn-green mt-auto">Historique</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list text-green me-2"></i> Dernières transactions</h5>
        <a href="<?= base_url('client/history') ?>" class="btn btn-link text-green p-0 small">Voir tout</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Type / Détails</th>
                        <th class="text-end">Montant</th>
                        <th class="text-end">Frais</th>
                        <th class="text-end pe-4">Total impacté</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-1"></i> Aucune transaction récente.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $tx): ?>
                            <?php
                                $isExpediteur = ($tx->expediteur_id == session()->get('client_id'));
                                $frais = (float)$tx->frais_applique;
                                $montant = (float)$tx->montant_brut;

                                if ($tx->type_code === 'DEPOT') {
                                    $badge = '<span class="badge bg-success">Dépôt</span>';
                                    $signe = '+';
                                    $color = 'text-success';
                                    $details = 'De : Automatique';
                                    $total = $montant;
                                } elseif ($tx->type_code === 'RETRAIT') {
                                    $badge = '<span class="badge bg-danger">Retrait</span>';
                                    $signe = '-';
                                    $color = 'text-danger';
                                    $details = 'Vers : Guichet';
                                    $total = $montant + $frais;
                                } else {
                                    if ($isExpediteur) {
                                        $badge = '<span class="badge bg-warning text-dark">Transfert Envoyé</span>';
                                        $signe = '-';
                                        $color = 'text-danger';
                                        $details = 'Vers : ' . esc($tx->destinataire_phone) . ' (' . esc($tx->destinataire_nom) . ')';
                                        $total = $montant + $frais;
                                    } else {
                                        $badge = '<span class="badge bg-info text-dark">Transfert Reçu</span>';
                                        $signe = '+';
                                        $color = 'text-success';
                                        $details = 'De : ' . esc($tx->expediteur_phone) . ' (' . esc($tx->expediteur_nom) . ')';
                                        $total = $montant;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small">
                                    <?= date('d/m/Y H:i', strtotime($tx->date_transaction)) ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2"><?= $badge ?></div>
                                        <div class="small text-muted"><?= esc($details) ?></div>
                                    </div>
                                </td>
                                <td class="text-end fw-bold">
                                    <?= number_format($montant, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-muted small">
                                    <?= number_format($frais, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end pe-4 <?= $color ?> fw-bold">
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

<script>
function refreshBalance() {
    window.location.reload();
}
</script>
<?= $this->endSection() ?>
