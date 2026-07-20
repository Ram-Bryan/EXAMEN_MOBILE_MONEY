<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #0f172a;">Commissions Inter-Opérateurs</h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Configuration du pourcentage de commission sur les transferts vers d'autres opérateurs</p>
    </div>
</div>

<!-- Info box -->
<div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background: rgba(37, 99, 235, 0.05);">
    <i class="fas fa-info-circle me-2" style="color: #2563eb;"></i>
    <strong>Fonctionnement :</strong> Le pourcentage de commission s'ajoute au frais fixe lors d'un transfert vers un opérateur externe.
    Frais total = frais fixe de la tranche + (montant brut × pourcentage / 100).
    <br>L'historique des modifications est préservé (INSERT-only, jamais d'UPDATE).
</div>

<!-- Liste des commissions -->
<div class="premium-card mb-4">
    <div class="card-header">
        <span><i class="fas fa-percent me-2 text-warning"></i> Commissions configurées</span>
        <span class="badge bg-green" style="font-size:13px;"><?= count($commissions ?? []) ?> opérateur(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Opérateur</th>
                        <th>Préfixe</th>
                        <th>Type</th>
                        <th style="text-align:right; padding-right:24px;">Commission (%)</th>
                        <th>Dernière modification</th>
                        <th style="text-align:right; padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($commissions)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-percentage fa-2x mb-3 d-block" style="color: #cbd5e1;"></i>
                                Aucune commission configurée.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($commissions as $c): ?>
                        <tr>
                            <td><span class="text-muted" style="font-size:13px;">#<?= esc($c->id) ?></span></td>
                            <td><strong><?= esc($c->nom ?? '—') ?></strong></td>
                            <td>
                                <span class="badge bg-green" style="padding:6px 14px; font-size:15px; border-radius:20px;">
                                    <?= esc($c->prefixe) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($c->est_notre_operateur ?? false): ?>
                                    <span style="background:#dcfce7; color:#16a34a; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                                        <i class="fas fa-star me-1"></i>Notre opérateur
                                    </span>
                                <?php else: ?>
                                    <span style="background:#f1f5f9; color:#64748b; padding:3px 12px; border-radius:20px; font-size:12px; font-weight:600;">
                                        <i class="fas fa-exchange-alt me-1"></i>Externe
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; padding-right:24px;">
                                <span style="background:#fef3c7; color:#d97706; padding:6px 16px; border-radius:20px; font-weight:700; font-size:16px;">
                                    <?= number_format($c->pourcentage, 2) ?> %
                                </span>
                            </td>
                            <td style="color: #94a3b8; font-size:13px;">
                                <?= esc($c->date_modif ?? '—') ?>
                            </td>
                            <td style="text-align:right; padding-right:24px;">
                                <button class="btn btn-sm btn-primary-custom"
                                    onclick="openEditCommission(<?= $c->id ?>, <?= $c->operateur_id ?>, '<?= esc($c->prefixe) ?>', '<?= esc($c->nom ?? '') ?>', <?= $c->pourcentage ?>)">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <a href="<?= base_url('admin/operators/detail/' . $c->operateur_id) ?>" class="btn btn-sm btn-outline-secondary ms-1">
                                    <i class="fas fa-eye"></i> Détail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Historique détaillé -->
<?php if (!empty($historiques)): ?>
<div class="premium-card mb-4">
    <div class="card-header">
        <span><i class="fas fa-history me-2 text-info"></i> Historique des modifications</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-custom w-100">
                <thead>
                    <tr>
                        <th>Opérateur</th>
                        <th>Préfixe</th>
                        <th style="text-align:right; padding-right:24px;">Pourcentage</th>
                        <th>Date de modification</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historiques as $h): ?>
                    <tr>
                        <td><strong><?= esc($h->nom ?? '—') ?></strong></td>
                        <td>
                            <span style="background:#dbeafe; color:#2563eb; padding:3px 12px; border-radius:20px; font-weight:700; font-size:13px;">
                                <?= esc($h->prefixe) ?>
                            </span>
                        </td>
                        <td style="text-align:right; padding-right:24px;">
                            <span style="background:#fef3c7; color:#d97706; padding:4px 12px; border-radius:20px; font-weight:700;">
                                <?= number_format($h->pourcentage, 2) ?> %
                            </span>
                        </td>
                        <td style="color: #94a3b8; font-size:13px;"><?= esc($h->date_modif) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Modification Commission -->
<div class="modal fade" id="editCommissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">
                    <i class="fas fa-percent me-2 text-warning"></i>Modifier la commission — <span id="modalOperateurLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCommissionForm" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:13px; border-radius:8px;">
                        <i class="fas fa-info-circle me-1"></i> Une nouvelle version sera créée dans l'historique. L'ancienne est conservée.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Pourcentage de commission (%)</label>
                        <div class="input-group">
                            <input type="number" name="pourcentage" id="editPourcentage" class="form-control"
                                   min="0" max="100" step="0.01" required style="font-size:18px; font-weight:700;">
                            <span class="input-group-text" style="font-size:18px; font-weight:700;">%</span>
                        </div>
                        <small class="text-muted mt-1 d-block">Valeur entre 0 et 100 (ex: 1.5 pour 1.5%)</small>
                    </div>
                    <div class="p-3 bg-light rounded-3" style="border-radius:8px;">
                        <p class="mb-1" style="font-size:13px; color:#64748b;">
                            <i class="fas fa-calculator me-1"></i> <strong>Exemple de calcul :</strong>
                        </p>
                        <p class="mb-0" style="font-size:14px; color:#334155;">
                            Pour un transfert de <strong>10 000 Ar</strong> avec <strong id="examplePct">1.5</strong>% de commission :
                            <br>Frais commission = <strong id="exampleResult">150</strong> Ar
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function openEditCommission(id, operateurId, prefixe, nom, pourcentage) {
    document.getElementById('modalOperateurLabel').textContent = nom + ' (' + prefixe + ')';
    document.getElementById('editPourcentage').value = pourcentage;
    document.getElementById('editCommissionForm').action = '<?= base_url('admin/commissions/update/') ?>' + operateurId;
    updateExample(pourcentage);
    new bootstrap.Modal(document.getElementById('editCommissionModal')).show();
}

document.getElementById('editPourcentage').addEventListener('input', function() {
    updateExample(parseFloat(this.value) || 0);
});

function updateExample(pct) {
    document.getElementById('examplePct').textContent = pct;
    document.getElementById('exampleResult').textContent = new Intl.NumberFormat('fr-MG').format(10000 * pct / 100);
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
