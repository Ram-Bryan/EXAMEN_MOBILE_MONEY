<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h2 class="fw-bold" style="color: var(--text-dark);"><i class="fas fa-exchange-alt text-green"></i> Historique des Transactions</h2>
    <p class="text-muted">Consultez toutes les transactions effectuées sur la plateforme.</p>
</div>

<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <form method="GET" action="<?= base_url('admin/transactions') ?>" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Date début</label>
                <input type="date" name="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Date fin</label>
                <input type="date" name="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Type d'opération</label>
                <select name="type" class="form-select">
                    <option value="">Tous</option>
                    <option value="DEPOT" <?= ($filters['type'] ?? '') === 'DEPOT' ? 'selected' : '' ?>>Dépôt</option>
                    <option value="RETRAIT" <?= ($filters['type'] ?? '') === 'RETRAIT' ? 'selected' : '' ?>>Retrait</option>
                    <option value="TRANSFERT" <?= ($filters['type'] ?? '') === 'TRANSFERT' ? 'selected' : '' ?>>Transfert</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-muted">Rechercher client</label>
                <input type="text" name="client" class="form-control" placeholder="Nom ou téléphone..." value="<?= esc($filters['client'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-green flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Filtrer
                </button>
                <a href="<?= base_url('admin/transactions') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center" style="border-radius: 16px 16px 0 0;">
        <h5 class="mb-0 fw-bold"><i class="fas fa-list text-green me-2"></i> Résultats <span class="text-muted small fw-normal">(<?= count($transactions) ?>)</span></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr style="background: var(--primary-bg);">
                        <th class="ps-4">Réf / Date</th>
                        <th>Type</th>
                        <th>Expéditeur</th>
                        <th>Destinataire</th>
                        <th class="text-end">Montant</th>
                        <th class="text-end">Frais</th>
                        <th class="text-end pe-4">Gains</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3 text-green d-block"></i>
                                <?php if (!empty($filters['date_from']) || !empty($filters['date_to']) || !empty($filters['type']) || !empty($filters['client'])): ?>
                                    Aucune transaction ne correspond aux filtres sélectionnés.
                                <?php else: ?>
                                    Aucune transaction enregistrée.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <?php
                                $frais = (float)$tx->frais_applique;
                                $montant = (float)$tx->montant_brut;

                                if ($tx->type_code === 'DEPOT') {
                                    $badge = '<span class="badge bg-success px-3 py-2"><i class="fas fa-arrow-down me-1"></i> Dépôt</span>';
                                } elseif ($tx->type_code === 'RETRAIT') {
                                    $badge = '<span class="badge bg-danger px-3 py-2"><i class="fas fa-arrow-up me-1"></i> Retrait</span>';
                                } else {
                                    $badge = '<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-exchange-alt me-1"></i> Transfert</span>';
                                }
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark" style="font-size: 14px;">#TRX-<?= esc($tx->transaction_id) ?></div>
                                    <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($tx->date_transaction)) ?></div>
                                </td>
                                <td><?= $badge ?></td>
                                <td class="small">
                                    <div class="fw-semibold text-dark"><?= esc($tx->expediteur_nom ?? '—') ?></div>
                                    <div class="text-muted"><?= esc($tx->expediteur_phone ?? '—') ?></div>
                                </td>
                                <td class="small">
                                    <div class="fw-semibold text-dark"><?= esc($tx->destinataire_nom ?? '—') ?></div>
                                    <div class="text-muted"><?= esc($tx->destinataire_phone ?? '—') ?></div>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    <?= number_format($montant, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end text-muted">
                                    <?= number_format($frais, 0, ',', ' ') ?> Ar
                                </td>
                                <td class="text-end pe-4 fw-bold text-green">
                                    <?= number_format($frais, 0, ',', ' ') ?> Ar
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
