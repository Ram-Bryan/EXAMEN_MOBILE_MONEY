<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: #0f172a;">Barèmes de Frais</h2>
        <p class="text-muted mb-0" style="font-size: 14px;">Configuration des tranches de frais par type d'opération et opérateur</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addFeeModal">
        <i class="fas fa-plus me-2"></i>Nouvelle tranche
    </button>
</div>

<!-- Group by type_operation -->
<?php 
$grouped = [];
foreach ($baremes ?? [] as $b) {
    $grouped[$b->type_nom][] = $b;
}
$typeColors = [
    'Dépôt' => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'badge' => '#dcfce7'],
    'Retrait' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'badge' => '#fee2e2'],
    'Transfert' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'badge' => '#dbeafe'],
];
foreach ($grouped as $typeNom => $rows):
    $colors = $typeColors[$typeNom] ?? ['bg' => '#f8fafc', 'color' => '#475569', 'badge' => '#e2e8f0'];
?>

<div class="premium-card mb-4">
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
                    <th>Opérateur</th>
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
                    <td>
                        <span style="background:#f8fafc; color:#334155; padding:3px 10px; border-radius:6px; font-weight:600; border:1px solid #e2e8f0;">
                            <?= esc($b->prefixe) ?>
                        </span>
                    </td>
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
                    <td style="color:#94a3b8; font-size:13px;"><?= esc($b->date_modif) ?></td>
                    <td style="text-align:right; padding-right:24px;">
                        <button class="btn btn-sm" style="background:#fff7ed; color:#ea580c; border:none; border-radius:6px;"
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
<div class="premium-card">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-percentage fa-3x mb-3 d-block" style="color:#e2e8f0;"></i>
        Aucun barème configuré.
    </div>
</div>
<?php endif; ?>

<!-- Modal Ajout Tranche -->
<div class="modal fade" id="addFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9;">
                <h5 class="modal-title fw-bold" style="color: #0f172a;"><i class="fas fa-plus me-2 text-primary"></i>Nouvelle tranche de frais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/fees/create') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Type d'opération</label>
                        <select name="type_operation_id" class="form-select" required>
                            <?php foreach ($types_operation ?? [] as $t): ?>
                            <option value="<?= $t->id ?>"><?= esc($t->nom) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Opérateur (préfixe)</label>
                        <select name="operateur_id" class="form-select" required>
                            <?php foreach ($operateurs ?? [] as $o): ?>
                            <option value="<?= $o->id ?>"><?= esc($o->prefixe) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Montant min (Ar)</label>
                            <input type="number" name="montant_min" class="form-control" min="0" step="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Montant max (Ar) <small class="text-muted">(vide = illimité)</small></label>
                            <input type="number" name="montant_max" class="form-control" min="1" step="1" placeholder="Laisser vide si illimité">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Frais fixe (Ar)</label>
                        <input type="number" name="frais_fixe" class="form-control" min="0" step="1" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9;">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modification Tranche (INSERT uniquement) -->
<div class="modal fade" id="editFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid #f1f5f9;">
                <h5 class="modal-title fw-bold" style="color: #0f172a;"><i class="fas fa-history me-2 text-warning"></i>Modifier la tranche <small class="text-muted fs-6">(historique préservé)</small></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFeeForm" method="POST">
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:13px;">
                        <i class="fas fa-info-circle me-1"></i> Une nouvelle version sera créée. L'ancienne est conservée pour l'historique.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Montant min (Ar)</label>
                            <input type="number" name="montant_min" id="editMontantMin" class="form-control" min="0" step="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Montant max (Ar)</label>
                            <input type="number" name="montant_max" id="editMontantMax" class="form-control" min="1" step="1" placeholder="Vide si illimité">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:14px; color:#475569;">Nouveau frais (Ar)</label>
                        <input type="number" name="frais_fixe" id="editFrais" class="form-control" min="0" step="1" required>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9;">
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
    document.getElementById('editFeeForm').action = '<?= base_url('admin/fees/update/') ?>' + baremeId;
    new bootstrap.Modal(document.getElementById('editFeeModal')).show();
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>