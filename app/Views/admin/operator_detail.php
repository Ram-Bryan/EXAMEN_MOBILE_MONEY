<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--text-dark);">
            <span class="badge bg-green" style="padding:8px 18px; border-radius:20px; font-weight:700; margin-right:12px; font-size:18px;">
                <?= esc($operator->prefixe) ?>
            </span>
            Barèmes de l'opérateur
        </h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Tranches de frais pour l'opérateur <?= esc($operator->prefixe) ?> (ajouté le <?= esc($operator->created_at ?? '') ?>)</p>
    </div>
    <div>
        <a href="<?= base_url('admin/operators') ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
        <button class="btn btn-primary-custom ms-2" data-bs-toggle="modal" data-bs-target="#addFeeModal">
            <i class="fas fa-plus me-2"></i>Nouvelle tranche
        </button>
    </div>
</div>

<!-- Situation des gains -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-success"></i> Situation des gains (<?= esc($operator->prefixe) ?>)</h5>
                <p class="text-muted small mb-0">Total des frais collectés pour cet opérateur</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table-custom w-100">
                        <thead>
                            <tr>
                                <th>Type d'opération</th>
                                <th>Nombre d'opérations</th>
                                <th style="text-align:right; padding-right:24px;">Total des frais collectés</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalNb = 0;
                            $totalAmount = 0;
                            if (empty($gains)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-1"></i> Aucun gain enregistré pour cet opérateur.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($gains as $g):
                                    $totalNb += $g->nb_operations;
                                    $totalAmount += $g->total_gains;
                                ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold" style="color: #334155;"><?= esc($g->nom) ?></span>
                                    </td>
                                    <td><?= number_format($g->nb_operations, 0, ',', ' ') ?></td>
                                    <td style="text-align:right; padding-right:24px;">
                                        <span class="fw-bold text-success"><?= number_format($g->total_gains, 0, ',', ' ') ?> Ar</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <tr style="background-color: var(--primary-bg); border-top: 2px solid var(--primary-border);">
                                    <td><strong style="color: var(--text-dark); font-size: 15px;">Total Général</strong></td>
                                    <td><strong style="font-size: 15px;"><?= number_format($totalNb, 0, ',', ' ') ?></strong></td>
                                    <td style="text-align:right; padding-right:24px;">
                                        <strong class="text-success" style="font-size: 18px;"><?= number_format($totalAmount, 0, ',', ' ') ?> Ar</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$grouped = [];
foreach ($baremes ?? [] as $b) {
    $grouped[$b->type_nom][] = $b;
}
$typeColors = [
    'Dépôt' => ['bg' => 'var(--primary-bg)', 'color' => 'var(--primary)', 'badge' => 'var(--primary-badge-bg)'],
    'Retrait' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'badge' => '#fee2e2'],
    'Transfert' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'badge' => '#dbeafe'],
];
foreach ($grouped as $typeNom => $rows):
    $colors = $typeColors[$typeNom] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'badge' => '#e2e8f0'];
?>

<div class="card mb-4">
    <div class="card-header">
        <span>
            <span style="background:<?= $colors['badge'] ?>; color:<?= $colors['color'] ?>; padding:4px 14px; border-radius:20px; font-weight:600; margin-right:10px;">
                <?= esc($typeNom) ?>
            </span>
            Tranches de frais
        </span>
        <span class="text-muted" style="font-size:13px;"><?= count($rows) ?> tranche(s)</span>
    </div>
    <div class="card-body p-0">
        <table class="table-custom w-100">
            <thead>
                <tr>
                    <th>Montant min (Ar)</th>
                    <th>Montant max (Ar)</th>
                    <th>Frais (Ar)</th>
                    <th>Dernière modification</th>
                    <th style="text-align:right; padding-right:24px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $b): ?>
                <tr>
                    <td><strong><?= number_format($b->montant_min, 0, ',', ' ') ?></strong></td>
                    <td>
                        <?php if ($b->montant_max === null): ?>
                            <span class="text-muted fst-italic">Illimité</span>
                        <?php else: ?>
                            <strong><?= number_format($b->montant_max, 0, ',', ' ') ?></strong>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span style="background:<?= $colors['badge'] ?>; color:<?= $colors['color'] ?>; padding:4px 12px; border-radius:20px; font-weight:700;">
                            <?= number_format($b->frais_fixe, 0, ',', ' ') ?> Ar
                        </span>
                    </td>
                    <td style="color: var(--text-muted); font-size:13px;"><?= esc($b->date_modif) ?></td>
                    <td style="text-align:right; padding-right:24px;">
                        <button class="btn btn-sm btn-primary-custom"
                            onclick="openEditFee(<?= $b->bareme_id ?>, <?= $b->montant_min ?>, <?= $b->montant_max ?? 'null' ?>, <?= $b->frais_fixe ?>)">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endforeach; ?>

<?php if (empty($grouped)): ?>
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-percentage fa-3x mb-3 d-block" style="color: var(--primary-border);"></i>
        Aucun barème configuré pour cet opérateur.
    </div>
</div>
<?php endif; ?>

<!-- Modal Ajout Tranche -->
<div class="modal fade" id="addFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--text-dark);"><i class="fas fa-plus me-2 text-success"></i>Nouvelle tranche de frais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/operators/' . $operator->id . '/fees/create') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type d'opération</label>
                        <select name="type_operation_id" class="form-select" required>
                            <?php foreach ($types_operation ?? [] as $t): ?>
                            <option value="<?= $t->id ?>"><?= esc($t->nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant min (Ar)</label>
                            <input type="number" name="montant_min" class="form-control" min="0" step="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant max (Ar) <small class="text-muted">(vide = illimité)</small></label>
                            <input type="number" name="montant_max" class="form-control" min="1" step="1" placeholder="Laisser vide si illimité">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frais fixe (Ar)</label>
                        <input type="number" name="frais_fixe" class="form-control" min="0" step="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modification Tranche -->
<div class="modal fade" id="editFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--text-dark);"><i class="fas fa-history me-2 text-warning"></i>Modifier la tranche <small class="text-muted fs-6">(historique préservé)</small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFeeForm" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:13px;">
                        <i class="fas fa-info-circle me-1"></i> Une nouvelle version sera créée. L'ancienne est conservée pour l'historique.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant min (Ar)</label>
                            <input type="number" name="montant_min" id="editMontantMin" class="form-control" min="0" step="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant max (Ar)</label>
                            <input type="number" name="montant_max" id="editMontantMax" class="form-control" min="1" step="1" placeholder="Vide si illimité">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nouveau frais (Ar)</label>
                        <input type="number" name="frais_fixe" id="editFrais" class="form-control" min="0" step="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">Valider la modification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function openEditFee(baremeId, montantMin, montantMax, frais) {
    document.getElementById('editMontantMin').value = montantMin;
    document.getElementById('editMontantMax').value = montantMax !== null ? montantMax : '';
    document.getElementById('editFrais').value = frais;
    document.getElementById('editFeeForm').action = '<?= base_url('admin/operators/' . $operator->id . '/fees/update/') ?>' + baremeId;
    new bootstrap.Modal(document.getElementById('editFeeModal')).show();
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
