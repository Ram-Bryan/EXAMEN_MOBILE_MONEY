<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0" style="color: #0f172a;"><i class="fas fa-percent text-warning me-2"></i> Commissions inter-opérateurs</h2>
    <a href="<?= base_url('admin/operators') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 16px;">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Taux de commission par opérateur externe</h5>
        <p class="text-muted small mb-0">Pourcentage appliqué en plus du frais fixe lors d'un transfert vers cet opérateur.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom w-100 mb-0">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th>Préfixe</th>
                        <th class="text-end">Commission</th>
                        <th>Dernière mise à jour</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-1"></i> Aucune commission configurée.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commissions as $c): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($c->nom) ?></strong>
                                </td>
                                <td>
                                    <span style="background:#dbeafe; color:#2563eb; padding:3px 12px; border-radius:20px; font-weight:700; font-size:14px;">
                                        <?= esc($c->prefixe) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-warning" style="font-size: 16px;"><?= number_format($c->pourcentage, 2) ?> %</span>
                                </td>
                                <td style="color: var(--text-muted); font-size:13px;"><?= esc($c->date_modif) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-warning"
                                        onclick="openEditCommission(<?= $c->operateur_id ?>, <?= $c->pourcentage ?>)">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Modifier Commission -->
<div class="modal fade" id="editCommissionModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--text-dark);"><i class="fas fa-percent me-2 text-warning"></i>Modifier la commission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCommissionForm" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:13px;">
                        <i class="fas fa-info-circle me-1"></i> La commission s'applique en plus du frais fixe pour les transferts vers cet opérateur.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouveau taux (%)</label>
                        <div class="input-group" style="max-width:200px;">
                            <input type="number" name="pourcentage" id="editCommissionPourcentage" class="form-control" min="0" max="100" step="0.01" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <input type="hidden" name="operateur_id" id="editCommissionOperateurId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-warning">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function openEditCommission(operateurId, pourcentage) {
    document.getElementById('editCommissionOperateurId').value = operateurId;
    document.getElementById('editCommissionPourcentage').value = pourcentage;
    document.getElementById('editCommissionForm').action = '<?= base_url('admin/operators/') ?>' + operateurId + '/commission/update';
    new bootstrap.Modal(document.getElementById('editCommissionModal')).show();
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>